<?php

namespace App\Http\Controllers;

use App\Helpers\JsonReponse;
use App\Http\Requests\StoreTaskRequest;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\TextUI\XmlConfiguration\FailedSchemaDetectionResult;

class TaskController extends Controller
{
    public function __construct(protected TaskRepository $taskRepo) {}

    public function index()
    {
        return view('calendar');
    }
    public function allTasks()
    {
        $result = $this->taskRepo->getAll(Auth::user()->id);
        foreach ($result as $task) {
            if ($task->isImportant == 1) $task->color = 'red';
        };
        return response()->json($result);
    }

    public function store(StoreTaskRequest $request)
    {
        $validatedData = $request->validated();
        if (!$request->employee_id) $validatedData['employee_id'] = Auth::user()->id;
        else $validatedData['employee_id'] = $request->employee_id;
        $task = $this->taskRepo->addTask($validatedData);
        if ($task !== null) {
            return JsonReponse::success(message: 'Task added successfully', data: $task);
        }
        return JsonReponse::error(message: 'Failed to add');
    }

    public function show(int $id)
    {
        $task = $this->taskRepo->getById($id);
        if ($task !== null) {
            return JsonReponse::success(data: $task);
        }
        return JsonReponse::error(message: "Task doesn't exist");
    }

    public function update(StoreTaskRequest $request, int $id)
    {
        $task = $this->taskRepo->getById($id);
        $validatedData = $request->validated();
        $validatedData['employee_id'] = Auth::user()->id;
        $isUpdated = $this->taskRepo->updateTask($validatedData, $task);
        if ($isUpdated) {
            return JsonReponse::success(message: 'Task updated successfully');
        }
        return JsonReponse::error(message: 'Faied to update');
    }

    public function delete(int $id)
    {
        $task = $this->taskRepo->getById($id);
        if ($task === null) {
            return JsonReponse::error(message: 'Task not found');
        }
        $isDeleted = $this->taskRepo->deleteTask($task);
        if ($isDeleted) return JsonReponse::success(message: 'Deleted successfully');
        return JsonReponse::error(message: 'Failed to delete');
    }
}
