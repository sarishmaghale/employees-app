<?php

namespace App\Repositories;

use App\Models\PmsCard;
use App\Models\PmsTask;
use App\Models\PmsBoard;
use App\Models\PmsComment;
use App\Models\PmsTaskFile;
use App\Models\PmsChecklist;
use App\Models\PmsChecklistItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class PmsRepository
{

    // Project Management System

    public function getCreatedBoardList(int $employeeId): Collection
    {
        return PmsBoard::with('members')
            ->where('created_by', $employeeId)
            ->select('id', 'board_name', 'image')
            ->get();
    }

    public function getAssociatedBoardList(int $employeeId): Collection
    {
        $boards = PmsBoard::with('members', 'creator')
            ->whereRelation('members', 'employee_id', $employeeId)
            ->where('created_by', '!=', $employeeId)
            ->select('id', 'board_name', 'created_by', 'image')
            ->get();
        return $boards;
    }

    public function getBoardDetails(int $boardId): ?PmsBoard
    {
        $employeeId = Auth::id();

        return PmsBoard::with([
            'members',
            'cards.tasks.checklists.items',
            'cards.tasks' => function ($query) {
                $query->with([
                    'assignedEmployees:id,username',
                    'assignedEmployees.detail:id,employee_id,profile_image'
                ])->withCount([
                    'checklistItems as total_items',
                    'checklistItems as completed_items' => fn($q) => $q->where('isCompleted', true)
                ]);
            }
        ])->find($boardId);
    }


    public function saveNewCard(array $data): PmsCard
    {
        return DB::transaction(function () use ($data) {
            $result = PmsCard::create([
                'title' => $data['title'],
                'board_id' => $data['board_id']
            ]);
            return $result;
        });
    }

    public function getTasksByCard(int $cardId): Collection
    {
        return PmsTask::with('assignedEmployees')
            ->where('card_id', $cardId)
            ->whereRelation('assignedEmployees', 'employee_id', Auth::id())
            ->orderBy('position', 'asc')->get();
    }

    public function saveTaskForCard(array $data): PmsTask
    {
        return DB::transaction(function () use ($data) {
            $card_id = $data['card_id'];
            $latestPosition = PmsTask::where('card_id', $card_id)->max('position');
            $position = $latestPosition ? $latestPosition + 1 : 1;
            $userId = $data['created_by'];

            $task = PmsTask::create([
                'title' => $data['title'],
                'card_id' => $card_id,
                'position' => $position,
                'created_by' => $userId
            ]);

            $card = PmsCard::where('id', $card_id)->value('title') ?? 'Unknown';

            $comment = PmsComment::create([
                'comment' => ' created this task to ' . $card,
                'employee_id' => $userId,
                'task_id' => $task->id
            ]);

            return $task;
        });
    }

    public function saveCard(array $data): PmsCard
    {
        $board_id = $data['board_id'];
        $latestPosition = PmsCard::where('board_id', $board_id)->max('position');
        $position = $latestPosition ? $latestPosition + 1 : 1;
        $card = PmsCard::create([
            'title' => $data['title'],
            'board_id' => $board_id,
            'position' => $position
        ]);
        return $card;
    }

    public function saveBoard(array $data): PmsBoard
    {
        return DB::transaction(function () use ($data) {
            $board = PmsBoard::create([
                'board_name' => $data['board_name'],
                'created_by' => $data['employee_id'],
            ]);
            $board->members()->attach($data['employee_id']);
            return $board;
        });
    }

    public function updateTaskOrder(
        int $cardId,
        array $positions,
        int $employeeId
    ): bool {
        return DB::transaction(function () use ($cardId, $positions, $employeeId) {
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
                    'employee_id' => $employeeId,
                    'comment'     => "moved this task from {$oldCardTitle} to {$newCardTitle}",
                    'comment_type' => 1
                ]);
            }
            return true;
        });
    }

    public function getTaskDetails(int $id): ?PmsTask
    {
        return PmsTask::with(
            'card',
            'comments.employee',
            'checklists.items',
            'assignedEmployees.detail',
            'labels',
            'files'
        )->find($id);
    }

    public function saveBoardMember(int $boardId, int $employeeId): array
    {
        $board = PmsBoard::find($boardId);
        if ($board->members()->where('employee_id', $employeeId)->exists()) {
            return [
                'success' => false,
                'message' => 'This member is already part of this board.'
            ];
        }

        $board->members()->attach($employeeId);
        $member = $board->members()
            ->where('employee_id', $employeeId)
            ->first();
        return [
            'success' => true,
            'message' => 'Member added successfully.',
            'board' => $member
        ];
    }

    public function saveTaskMember(int $taskId, int $employeeId): array
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

    public function updateTaskDetails(array $data, int $taskId): ?PmsTask
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
                                    'comment' => "{$statusText} '{$checkbox->item_title}' of this task",
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

    public function saveCommentToTask(array $data): ?PmsComment
    {
        $comment = PmsComment::create([
            'comment' => $data['comment'],
            'task_id' => $data['task_id'],
            'employee_id' => $data['employee_id'],
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
                'employee_id' => $data['employee_id'],
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

    public function removeChecklistItem(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $checklist = PmsChecklist::find($id);
            if ($checklist) {
                $authUser = Auth::id();
                PmsComment::create([
                    'comment' => "deleted checklist: '{$checklist['title']}' from this card",
                    'employee_id' => $authUser,
                    'task_id' => $checklist['task_id']
                ]);
                PmsChecklistItem::where('checklist_id', $id)->delete();
                $checklist->delete();
                return true;
            }
        });
    }

    public function removeCard(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $card = PmsCard::with([
                'tasks.comments',
                'tasks.checklists',
                'tasks.labels',
                'tasks.files',
                'tasks'
            ])->find($id);
            if (!$card) return false;
            foreach ($card->tasks as $task) {
                $task->comments()->delete();
                foreach ($task->checklists as $checklist) {
                    $checklist->items()->delete();
                    $checklist->delete();
                }
                foreach ($task->files as $file) {
                    $file->delete();
                }
                $task->labels()->detach();
                $task->delete();
            }
            $card->delete();
            return true;
        });
    }

    public function saveTaskFile(array $data): PmsTaskFile
    {
        return DB::transaction(function () use ($data) {
            $file = $data['file'];
            $originalName = $file->getClientOriginalName();
            $filePath = $file->store('taskFiles', 'public');

            $addedFile = PmsTaskFile::create([
                'task_id'     => $data['task_id'],
                'employee_id' => $data['employee_id'],
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

    public function removeTaskFile(int $fileId): bool
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

    public function removeTask(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $task = PmsTask::with([
                'comments',
                'checklists',
                'files',
                'labels',
                'assignedEmployees'
            ])->find($id);
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

    public function updateCoverImage(int $boardId, UploadedFile $file): ?PmsBoard
    {
        $board = PmsBoard::find($boardId);
        if (!$board) return null;

        // Delete old cover if exists
        if ($board->image && Storage::disk('public')->exists($board->image)) {
            Storage::disk('public')->delete($board->image);
        }

        // Store new image
        $filePath = $file->store('boards', 'public');

        // Update board record
        $board->update(['image' => $filePath]);

        return $board;
    }
}
