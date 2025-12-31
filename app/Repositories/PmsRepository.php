<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use App\Models\PmsBoard;
use App\Models\PmsCard;
use App\Models\PmsChecklist;
use App\Models\PmsChecklistItem;
use App\Models\PmsComment;
use App\Models\PmsTask;
use Illuminate\Database\Eloquent\Collection;


class PmsRepository
{

    // Project Management System

    public function getCreatedBoardList(int $employeeId): Collection
    {
        return PmsBoard::with('members')
            ->where('created_by', $employeeId)
            ->select('id', 'board_name')
            ->get();
    }

    public function getAssociatedBoardList(int $employeeId): Collection
    {
        $boards = PmsBoard::with('members')
            ->whereHas('members', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            })->where('created_by', '!=', $employeeId)
            ->select('id', 'board_name')
            ->get();
        return $boards;
    }

    public function getBoardDetails(int $boardId): PmsBoard
    {
        return PmsBoard::with('cards.tasks', 'members')->find($boardId);
    }

    public function createNewCard(array $data): PmsCard
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
        return PmsTask::with('employees')->where('card_id', $cardId)
            ->orderBy('position', 'asc')->get();
    }

    public function addTaskForCard(array $data): PmsTask
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
                'comment' => ' added this task to ' . $card,
                'employee_id' => $userId,
                'task_id' => $task->id
            ]);

            return $task;
        });
    }

    public function addCard(array $data): PmsCard
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
    public function addBoard(array $data): PmsBoard
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
        int $taskId,
        int $newCardId,
        ?int $position = null,
        int $employeeId
    ): ?PmsTask {
        return DB::transaction(function () use ($taskId, $newCardId, $position, $employeeId) {
            $task = PmsTask::find($taskId);
            $oldCard = PmsCard::where('id', $task->card_id)->value('title');
            if (!$task) return null;
            if (!$position) {
                $latest = PmsTask::where('card_id', $newCardId)->max('position');
                $position = $latest ? $latest + 1 : 1;
            }
            $task->update([
                'card_id' => $newCardId,
                'position' => $position
            ]);
            $newCard = PmsCard::where('id', $task->card_id)->value('title');
            $comment = PmsComment::create([
                'comment' => "moved this card from {$oldCard} to {$newCard}",
                'employee_id' => $employeeId,
                'task_id' => $task->id
            ]);
            return $task;
        });
    }

    public function getTaskDetails(int $id): ?PmsTask
    {
        return PmsTask::with('card', 'comments.employee', 'checklists.items')->find($id);
    }

    public function addBoardMember(int $boardId, int $employeeId): array
    {
        $board = PmsBoard::find($boardId);
        if ($board->members()->where('employee_id', $employeeId)->exists()) {
            return [
                'success' => false,
                'message' => 'This member is already part of this board.'
            ];
        }

        $board->members()->attach($employeeId);
        return [
            'success' => true,
            'message' => 'Member added successfully.',
            'board' => $board
        ];
    }

    public function updateTaskDetails(array $data, int $taskId): bool
    {
        return DB::transaction(function () use ($data, $taskId) {
            $existingDetail = PmsTask::find($taskId);
            $taskData = collect($data)->except('checklist_items')->toArray();
            $existingDetail->fill($taskData);
            $existingDetail->save();

            $items = $data['checklist_items'] ?? [];

            if (!empty($items) && is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['id'], $item['completed'])) {
                        PmsChecklistItem::where('id', $item['id'])
                            ->update(['isCompleted' => $item['completed']]);
                    }
                }
            }
            return true;
        });
    }

    public function addCommentToTask(array $data): ?Collection
    {
        $comment = PmsComment::create([
            'comment' => $data['comment'],
            'task_id' => $data['task_id'],
            'employee_id' => $data['employee_id'],
            'comment_type' => 1
        ]);
        if ($comment->exists()) {
            $fetchComment = PmsComment::with('employee')
                ->where('task_id', $data['task_id'])
                ->get();
            return $fetchComment;
        } else return null;
    }

    public function addChecklist(array $data): ?PmsChecklist
    {
        $result = PmsChecklist::create([
            'title' => $data['title'],
            'task_id' => $data['task_id'],
        ]);
        if ($result && $result->exists()) return $result;
        else return null;
    }
    public function addChecklistItem(array $data)
    {
        $item = PmsChecklistItem::create([
            'checklist_id' => $data['checklist_id'],
            'item_title' => $data['title'],
            'isCompleted' => 0
        ]);
        if ($item && $item->exists()) return $item;
        else return null;
    }
}
