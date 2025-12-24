<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function getAllById(int $employeeId, array $filters = []): Collection
    {
        $query = Task::with('employee', 'taskCategory')
            ->where('employee_id', $employeeId);
        if (!empty($filters['start'])) {
            $query->whereDate('start', '>=', $filters['start']);
        }
        if (!empty($filters['end'])) {
            $query->whereDate('end', '<=', $filters['end']);
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        $tasks = $query->orderBy('id', 'desc')->get();
        foreach ($tasks as $task) {
            $task->color = $task->taskCategory->color ?? null;
        };
        return $tasks;
    }

    public function getAll(array $filters = []): Collection
    {
        $query = Task::with('employee', 'taskCategory');
        if (!empty($filters['start'])) {
            $query->whereDate('start', '>=', $filters['start']);
        }
        if (!empty($filters['end'])) {
            $query->whereDate('end', '<=', $filters['end']);
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
        $tasks = $query->orderBy('id', 'desc')->get();
        foreach ($tasks as $task) {
            $task->color = $task->taskCategory->color ?? null;
        };
        return $tasks;
    }

    public function getRecentlyAddedTasks(): Collection
    {
        $tasks = Task::with('employee', 'taskCategory')
            ->orderBy('id', 'desc')->take(5)->get();
        return $tasks;
    }
    public function getRecentlyAddedTasksForStaff(int $id): Collection
    {
        $tasks = Task::with('employee', 'taskCategory')
            ->where('employee_id', $id)
            ->orderBy('id', 'desc')->take(5)->get();
        return $tasks;
    }

    public function getById(int $id): ?Task
    {
        return Task::with(['employee', 'taskCategory'])
            ->find($id);
    }

    public function addTask(array $data): Task
    {
        $task = Task::create($data);
        return Task::with('employee')->find($task->id);
    }

    public function updateTask(array $data, Task $task): bool
    {
        return $task->update($data);
    }

    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }

    public function getTaskForBoard(int $categoryId, int $employeeId): Collection
    {
        return Task::where('employee_id', $employeeId)->where('category_id', $categoryId)
            ->where('status_link_id', Null)->get();
    }

    public function assignTaskToBoard(int $taskId, int $statusLinkId): bool
    {
        $task = Task::find($taskId);
        if ($task) {
            $task->status_link_id = $statusLinkId;
            $task->save();
            return true;
        }
        return false;
    }

    public function updateTaskStatus(Task $task, int $boardId): bool
    {
        $task->status_link_id = $boardId;
        $task->save();
        return true;
    }
}
