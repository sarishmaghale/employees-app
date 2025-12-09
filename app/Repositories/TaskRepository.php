<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function getAll(int $employeeId): Collection
    {
        return Task::with('employee')
            ->where('employee_id', $employeeId)
            ->get();
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
