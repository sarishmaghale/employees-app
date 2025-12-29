<?php

namespace App\Repositories;

use App\Models\KanbanStatus;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeKanbanStatusLink;
use App\Models\PmsBoard;
use App\Models\PmsCard;
use App\Models\PmsTask;
use Illuminate\Database\Eloquent\Collection;


class KanbanRepository
{
    public function fetchStatusByCategory(int $categoryId, int $employeeId): Collection
    {
        $columns = EmployeeKanbanStatusLink::with('status', 'tasks')
            ->where('employee_id', $employeeId)
            ->where('category_id', $categoryId)
            ->orderBy('position')
            ->get();
        return $columns;
    }

    public function addNewBoard(array $board): ?KanbanStatus
    {
        return DB::transaction(function () use ($board) {
            $addedStatus = KanbanStatus::firstOrCreate([
                'name' => $board['name']
            ]);
            $latestPosition = EmployeeKanbanStatusLink::where('employee_id', $board['employee_id'])
                ->where('category_id', $board['category_id'])->max('position');
            $newPosition = $latestPosition ? $latestPosition + 1 : 1;
            $addedStatus->kanbanLinks = EmployeeKanbanStatusLink::create([
                'employee_id' => $board['employee_id'],
                'category_id' => $board['category_id'],
                'status_id' => $addedStatus->id,
                'position' => $newPosition
            ]);
            return $addedStatus;
        });
    }


    // Project Management System

    public function getCreatedBoards(int $employeeId): Collection
    {
        return PmsBoard::with('cards', 'members')
            ->where('created_by', $employeeId)->get();
    }

    public function getAssociatedBoards(int $employeeId): Collection
    {
        $boards = PmsBoard::select('pms_boards.*')
            ->join('pms_board_members', 'pms_boards.id', '=', 'pms_board_members.board_id')
            ->where('pms_board_members.employee_id', $employeeId)
            ->where('pms_boards.created_by', '!=', $employeeId)
            ->with('cards', 'members')
            ->get();
        return $boards;
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
        return PmsTask::with('employees')->where('card_id', $cardId)->get();
    }
}
