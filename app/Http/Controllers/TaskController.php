<?php

namespace App\Http\Controllers;

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
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);
        $validatedData['employee_id'] = Auth::user()->id;
        $task = $this->taskRepo->addTask($validatedData);
        if ($task !== null) {
            return response()->json([
                'success' => true,
                'message' => 'Task added successfully',
                'data' => $task,
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Failed to add',
        ]);
    }

    public function show(int $id)
    {
        $task = $this->taskRepo->getById($id);
        if ($task !== null) {
            return response()->json([
                'success' => true,
                'data' => $task,
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Task doesnt exists',
        ]);
    }

    public function update(Request $request, int $id)
    {
        $task = $this->taskRepo->getById($id);
        $validatedData = $request->validate([
            'title' => 'required',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);
        $validatedData['employee_id'] = Auth::user()->id;
        $isUpdated = $this->taskRepo->updateTask($validatedData, $task);
        if ($isUpdated) {
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Failed to update',
        ]);
    }

    public function delete(int $id)
    {
        $task = $this->taskRepo->getById($id);
        if ($task === null) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
            ]);
        }
        $isDeleted = $this->taskRepo->deleteTask($task);
        if ($isDeleted) return response()->json(['success' => false, 'message' => 'deleted successfully']);
        return response()->json(['success' => false, 'message' => 'Failed to delete']);
    }
}
