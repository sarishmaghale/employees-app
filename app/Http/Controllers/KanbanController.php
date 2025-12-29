<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\StoreBoardRequest;
use App\Models\PmsBoard;
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
        return view('kanban-board.index');
    }
    public function show(int $categoryId, Request $request)
    {
        $employee_id = $request->userId ?: Auth::id();
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

    public function showTasks(int $categoryId, Request $request)
    {
        $employee_id = $request->userId ?: Auth::id();
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

    // Project Management System

    public function pmsIndex()
    {
        $createdBoards = $this->boardHelper->getCreatedBoards(Auth::id());
        $associatedBoards = $this->boardHelper->getAssociatedBoards(Auth::id());
        return view('pms.index', compact('createdBoards', 'associatedBoards'));
    }

    public function pmsBoardIndex(PmsBoard $board)
    {
        return view('pms.created-boards', compact('board'));
    }
    public function pmsShowTasks(int $cardId)
    {
        $tasks = $this->boardHelper->getTasksByCard($cardId);
        return response()->json($tasks);
    }

    public function pmsShowAssociatedBoards()
    {
        $userId = Auth::id();
        $boards = $this->boardHelper->getAssociatedBoards($userId);
        if ($boards) return JsonResponse::success(message: 'Loaded', data: $boards);
        else return JsonResponse::error(message: 'Failed to load boards');
    }

    public function pmsShowCards(PmsBoard $board)
    {
        $cards = $board->cards;
        return response()->json($cards);
    }
}
