<?php

namespace App\Repositories;

use App\Models\KanbanStatus;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeKanbanStatusLink;
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
}
