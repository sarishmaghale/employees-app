<?php

namespace App\Repositories\Pms;

use App\Models\Pms\PmsCard;
use App\Models\Pms\PmsTask;
use App\Models\Pms\PmsComment;
use App\Models\Pms\PmsTaskFile;
use App\Models\Pms\PmsChecklist;
use App\Models\Pms\PmsChecklistItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;


class TaskRepository
{

    public function getTasks(int $cardId): Collection
    {
        return PmsTask::with('assignedEmployees')
            ->where('card_id', $cardId)
            ->whereRelation('assignedEmployees', 'employee_id', Auth::id())
            ->orderBy('position', 'asc')->get();
    }

    public function create(array $data): PmsTask
    {
        return DB::transaction(function () use ($data) {
            $card_id = $data['card_id'];
            $latestPosition = PmsTask::where('card_id', $card_id)->max('position');
            $position = $latestPosition ? $latestPosition + 1 : 1;

            $task = PmsTask::create([
                'title' => $data['title'],
                'card_id' => $card_id,
                'position' => $position,
                'created_by' => Auth::id()
            ]);
            $cardTitle =  PmsCard::where('id', $card_id)->value('title');

            PmsComment::create([
                'comment' => ' created this task to ' . $cardTitle,
                'employee_id' => Auth::id(),
                'task_id' => $task->id
            ]);

            return $task;
        });
    }

    public function updateTaskOrder(
        int $cardId,
        array $positions
    ): bool {
        return DB::transaction(function () use ($cardId, $positions) {
            $movedTaskId = $positions[0]['task_id'] ?? null;
            $task = PmsTask::find($movedTaskId);
            $oldCardTitle = null;

            if ($task && $task->card_id !== $cardId) {
                $oldCardTitle = PmsCard::where('id', $task->card_id)->value('title');
            }

            foreach ($positions as $item) {
                PmsTask::where('id', $item['task_id'])->update([
                    'card_id'    => $cardId,
                    'position'   => $item['position'],
                ]);
            }
            if ($task && $oldCardTitle) {
                $newCardTitle = PmsCard::where('id', $cardId)->value('title');

                PmsComment::create([
                    'task_id'     => $task->id,
                    'employee_id' => Auth::id(),
                    'comment'     => "moved this task from {$oldCardTitle} to {$newCardTitle}",
                    'comment_type' => 1
                ]);
            }
            return true;
        });
    }

    public function getDetails(int $taskId): ?PmsTask
    {
        return PmsTask::with(
            'card',
            'comments.employee',
            'checklists.items',
            'assignedEmployees.detail',
            'labels',
            'files'
        )->find($taskId);
    }

    public function addMember(int $taskId, int $employeeId): array
    {
        $task = PmsTask::find($taskId);
        if ($task->assignedEmployees()->where('employee_id', $employeeId)->exists()) {
            return [
                'success' => false,
                'message' => 'This member is already part of this task.'
            ];
        }

        return DB::transaction(function () use ($task, $employeeId) {
            $task->assignedEmployees()->attach($employeeId);
            $member = $task->assignedEmployees()
                ->with('detail')->where('employee_id', $employeeId)->first();
            PmsComment::create([
                'comment' => "added {$member->username} as a member to this task",
                'employee_id' => Auth::id(),
                'task_id' => $task->id
            ]);
            return [
                'success' => true,
                'message' => 'Member added successfully.',
                'member' => $member,
                'task' => $task
            ];
        });
    }

    public function updateDetails(array $data, int $taskId): ?PmsTask
    {
        return DB::transaction(function () use ($data, $taskId) {
            $task = PmsTask::find($taskId);
            $taskData = collect($data)->except(['checklist_items', 'labels'])
                ->toArray();
            $task->fill($taskData);
            $task->save();

            $items = $data['checklist_items'] ?? [];

            if (!empty($items) && is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['id'], $item['completed'])) {
                        $checkbox = PmsChecklistItem::find($item['id']);
                        if ($checkbox) {
                            $oldStatus = (bool) $checkbox->isCompleted;
                            $newStatus = (bool)($item['completed']);
                            if ($oldStatus !== $newStatus) {
                                $checkbox->update([
                                    'isCompleted' => $newStatus
                                ]);
                                $statusText = $newStatus ? 'completed' : 'unchecked';
                                PmsComment::create([
                                    'comment' => "{$statusText} '{$checkbox->item_title}' on this task",
                                    'employee_id' => Auth::id(),
                                    'task_id' => $taskId
                                ]);
                            }
                        }
                    }
                }
            }
            $labels = $data['labels'] ?? [];
            $task->labels()->sync($labels);
            if ($task->end_date) {
                $task->update([
                    'reminder_for_date' => $task->end_date,
                    'reminder_sent_at' => null
                ]);
            }
            return $task;
        });
    }

    public function saveComment(array $data): ?PmsComment
    {
        $comment = PmsComment::create([
            'comment' => $data['comment'],
            'task_id' => $data['task_id'],
            'employee_id' => Auth::id(),
            'comment_type' => 1
        ]);
        if ($comment->exists()) {
            return $comment->load('employee');
        } else return null;
    }

    public function saveChecklist(array $data): ?PmsChecklist
    {
        return DB::transaction(function () use ($data) {
            $result = PmsChecklist::create([
                'title' => $data['title'],
                'task_id' => $data['task_id'],
            ]);
            PmsComment::create([
                'comment' => "added checklist: '{$result['title']}' to this card",
                'employee_id' => Auth::id(),
                'task_id' => $result['task_id']
            ]);

            if ($result && $result->exists()) return $result;
        });
    }

    public function saveChecklistItem(array $data): ?PmsChecklistItem
    {
        return DB::transaction(function () use ($data) {
            $item = PmsChecklistItem::create([
                'checklist_id' => $data['checklist_id'],
                'item_title' => $data['title'],
                'isCompleted' => 0
            ]);
            if ($item && $item->exists()) return $item;
        });
    }

    public function removeChecklistItem(int $checklistId): bool
    {
        return DB::transaction(function () use ($checklistId) {
            $checklist = PmsChecklist::find($checklistId);
            if ($checklist) {
                $authUser = Auth::id();
                PmsChecklistItem::where('checklist_id', $checklistId)->delete();
                PmsComment::create([
                    'comment' => "deleted checklist: '{$checklist['title']}' from this card",
                    'employee_id' => $authUser,
                    'task_id' => $checklist['task_id']
                ]);
                $checklist->delete();
                return true;
            }
        });
    }

    public function saveFile(array $data): PmsTaskFile
    {
        return DB::transaction(function () use ($data) {
            $file = $data['file'];
            $originalName = $file->getClientOriginalName();
            $filePath = $file->store('taskFiles', 'public');

            $addedFile = PmsTaskFile::create([
                'task_id'     => $data['task_id'],
                'employee_id' => Auth::id(),
                'file_path'   => $filePath,
                'file_name' => $originalName
            ]);
            PmsComment::create([
                'comment' => "attached '{$originalName}' to this card",
                'employee_id' => $addedFile['employee_id'],
                'task_id' => $addedFile['task_id']
            ]);
            return $addedFile;
        });
    }

    public function removeFile(int $fileId): bool
    {
        $taskFile = PmsTaskFile::findOrFail($fileId);
        $fileName = $taskFile->file_name;

        if (Storage::disk('public')->exists($taskFile->file_path)) {
            Storage::disk('public')->delete($taskFile->file_path);
        }
        PmsComment::create([
            'comment' => 'removed file- ' . $fileName . ' from this card',
            'employee_id' => Auth::id(),
            'task_id' => $taskFile['task_id']
        ]);

        return $taskFile->delete();
    }

    public function remove(int $taskId): bool
    {
        return DB::transaction(function () use ($taskId) {
            $task = PmsTask::with([
                'comments',
                'checklists',
                'files',
                'labels',
                'assignedEmployees'
            ])->find($taskId);
            if (!$task) return false;

            $task->checklists->each(function ($checklist) {
                $checklist->items()->delete();
                $checklist->delete();
            });

            $task->files->each->delete();
            $task->comments->each->delete();

            if ($task->assignedEmployees) {
                $task->assignedEmployees()->detach();
            }
            $task->labels()->detach();

            $task->delete();
            return true;
        });
    }
}
