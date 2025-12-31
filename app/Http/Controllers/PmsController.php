<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\StorePmsTasKRequest;
use App\Models\PmsBoard;
use App\Notifications\BoardMemberAddedNotification;
use App\Repositories\EmployeeRepository;
use Illuminate\Http\Request;
use App\Repositories\PmsRepository;
use Illuminate\Support\Facades\Auth;

class PmsController extends Controller
{
    public function __construct(
        protected PmsRepository $pmsHelper,
        protected EmployeeRepository $empHelper
    ) {}

    public function index()
    {
        $createdBoards = $this->pmsHelper->getCreatedBoardList(Auth::id());
        $associatedBoards = $this->pmsHelper->getAssociatedBoardList(Auth::id());
        return view('pms.index', compact('createdBoards', 'associatedBoards'));
    }

    public function showBoard(int $id)
    {
        $employees = $this->empHelper->getEmployeeList();
        $board = $this->pmsHelper->getBoardDetails($id);
        if ($board) {
            return view('pms.created-boards', compact('board', 'employees'));
        } else  abort(404);
    }

    public function showTasks(int $cardId)
    {
        $tasks = $this->pmsHelper->getTasksByCard($cardId);
        return response()->json($tasks);
    }

    public function storeTask(StorePmsTasKRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $result = $this->pmsHelper->addTaskForCard($data);
        if ($result && $result->exists) return JsonResponse::success(message: 'Added', data: $result);
        else return JsonResponse::error(message: 'Failed to add');
    }

    public function storeCard(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'board_id' => 'required|exists:pms_boards,id'
        ]);
        $data = $request->only(['title', 'board_id']);
        $result = $this->pmsHelper->addCard($data);
        if ($result && $result->exists) return JsonResponse::success(data: $result);
        else return JsonResponse::error(message: 'failed to add');
    }

    public function moveTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:pms_tasks,id',
            'new_card_id' => 'required|exists:pms_cards,id',
            'position' => 'nullable|integer|min:1'
        ]);
        $result = $this->pmsHelper->updateTaskOrder(
            $request->task_id,
            $request->new_card_id,
            $request->position,
            Auth::id()
        );
        if (!$result) return JsonResponse::error(message: 'Failed to move task');
        else return JsonResponse::success(message: 'Task updated', data: $result);
    }

    public function showTaskDetail(int $id)
    {
        $detail = $this->pmsHelper->getTaskDetails($id);
        if ($detail && $detail->exists) return JsonResponse::success(data: $detail);
        else return JsonResponse::error(message: 'Failed to fetch details');
    }

    public function addMember(Request $request, $id)
    {

        $result = $this->pmsHelper->addBoardMember(boardId: $id, employeeId: $request->employee_id);
        if ($result['success']) {
            $employee = $this->empHelper->getById($request->employee_id);
            $employee->notify(new BoardMemberAddedNotification($result['board']));
            return JsonResponse::success(message: $result['message']);
        }

        return JsonResponse::error(message: $result['message']);
    }

    public function storeBoard(Request $request)
    {
        $request->validate([
            'board_name' => 'required',
        ]);
        $data = [
            'board_name' => $request->board_name,
            'employee_id' => Auth::id(),
        ];
        $result = $this->pmsHelper->addBoard($data);
        if ($result && $result->exists) return JsonResponse::success(data: $result);
        else return JsonResponse::error(message: 'failed to add');
    }

    public function updateTask(Request $request, int $id)
    {
        $data = $request->only(['description', 'start_date', 'end_date', 'checklist_items']);
        $result = $this->pmsHelper->updateTaskDetails($data, $id);
        if ($result) return JsonResponse::success(message: 'Saved successfully');
        else return JsonResponse::error(message: 'Failed to save');
    }

    public function storeComment(Request $request)
    {
        $request->validate([
            'comment' => 'required',
            'task_id' => 'required|exists:pms_tasks,id'
        ]);
        $data = [
            'comment' => $request->comment,
            'task_id' => $request->task_id,
            'employee_id' => Auth::id(),
        ];
        $comments = $this->pmsHelper->addCommentToTask($data);
        if ($comments->isNotEmpty()) return JsonResponse::success(data: $comments);
        else return JsonResponse::error(message: 'Failed to post comment');
    }

    public function createChecklist(Request $request)
    {
        $data = [
            'title' => $request->title,
            'task_id' => $request->task_id,
        ];
        $result = $this->pmsHelper->addChecklist($data);
        if ($result) return JsonResponse::success(data: $result);
        else return JsonResponse::error(message: 'Faild to add checklist');
    }

    public function createChecklistItem(Request $request)
    {
        $data = [
            'title' => $request->title,
            'checklist_id' => $request->checklist_id,

        ];
        $result = $this->pmsHelper->addChecklistItem($data);
        if ($result) return JsonResponse::success(data: $result);
        else return JsonResponse::error(message: 'Faild to add checklist');
    }
}
