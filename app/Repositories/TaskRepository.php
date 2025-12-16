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

    public function getById(int $id): Task
    {
        return Task::with('employee')
            ->find($id);
    }

    public function addTask(array $data): Task
    {
        return Task::create($data);
    }

    public function updateTask(array $data, Task $task): bool
    {
        return $task->update($data);
    }

    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }
}
