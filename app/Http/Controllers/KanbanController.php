<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\StoreBoardRequest;
use App\Repositories\KanbanRepository;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KanbanController extends Controller
{
    public function __construct(
        protected KanbanRepository $boardHelper,
        protected TaskRepository $taskHelper
    ) {}

    public function index()
    {
        // $employee_id = Auth::id();
        // $category_id = 1;
        // $columns = $this->boardHelper->getUserColumns(categoryId: $category_id, employeeId: $employee_id);
        // foreach ($columns as $column) {
        //     dd($column->status->name);
        // }

        return view('kanban-board.index');
    }
    public function show(int $categoryId)
    {
        $employee_id = Auth::id();
        $columns = $this->boardHelper->fetchStatusByCategory(categoryId: $categoryId, employeeId: $employee_id);
        return response()->json([
            'success' => true,
            'data' => $columns
        ]);
    }
    public function storeBoard(StoreBoardRequest $request)
    {
        $data = $request->validated();
        $result = $this->boardHelper->addNewBoard($data);
        if ($result) return JsonResponse::success(message: 'New Board added');
        else return JsonResponse::error(message: 'Failed to add board');
    }

    public function showTasks(int $categoryId)
    {
        $employee_id = Auth::id();
        $tasks = $this->taskHelper->getTaskForBoard(employeeId: $employee_id, categoryId: $categoryId);
        return JsonResponse::success(message: 'Tasks fetched', data: $tasks);
    }


    public function addTasks(Request $request)
    {
        $response = $this->taskHelper->assignTaskToBoard(
            taskId: $request->task_id,
            statusLinkId: $request->status_id
        );
        if ($response) return JsonResponse::success(message: 'Added');
        else return JsonResponse::error(message: 'Failed to add');
    }

    public function moveTask(Request $request)
    {
        $task = $this->taskHelper->getById($request->taskId);
        $result = $this->taskHelper->updateTaskStatus(task: $task, boardId: $request->statusId);
        if ($result) return JsonResponse::success(message: 'Updated');
        else return JsonResponse::error(message: 'Failed to save');
    }
}
