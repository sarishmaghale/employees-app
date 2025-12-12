<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function getAll(int $employeeId): Collection
    {
        $tasks = Task::with('employee', 'taskCategory')
            ->where('employee_id', $employeeId)
            ->get();
        foreach ($tasks as $task) {
            $task->priority = match ($task->isImportant) {
                1 => 'Important',
                2 => 'Moderate',
                default => 'Normal',
            };
        };
        foreach ($tasks as $task) {
            if ($task->category_id == 1) {
                $task->color = 'green';
            } elseif ($task->category_id == 2) {
                $task->color = 'purple';
            } else {
                $task->color = 'black';
            }
        };

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
