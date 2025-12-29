<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\PmsBoard;
use App\Models\PmsCard;
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
        $card_id = $data['card_id'];
        $latestPosition = PmsTask::where('card_id', $card_id)->max('position');
        $position = $latestPosition ? $latestPosition + 1 : 1;
        $task = PmsTask::create([
            'title' => $data['title'],
            'card_id' => $card_id,
            'position' => $position,
        ]);
        return $task;
    }

    public function addCard(array $data): PmsCard
    {
        $board_id = $data['board_id'];
        $latestPosition = PmsCard::where('board_id', $board_id)->max('position');
        $position = $latestPosition ? $latestPosition + 1 : 1;
        $card = PmsCard::create([
            'title' => $data['title'],
            'board_id' => $board_id,
            'position' => $latestPosition
        ]);
        return $card;
    }

    public function updateTaskOrder(int $taskId,  int $newCardId, ?int $position = null): ?PmsTask
    {
        $task = PmsTask::find($taskId);
        if (!$task) return null;
        if (!$position) {
            $latest = PmsTask::where('card_id', $newCardId)->max('position');
            $position = $latest ? $latest + 1 : 1;
        }
        $task->update([
            'card_id' => $newCardId,
            'position' => $position
        ]);
        return $task;
    }

    public function getTaskDetails(int $id)
    {
        return PmsTask::with('card', 'employees')->find($id);
    }
}
