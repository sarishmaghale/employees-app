<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\StorePmsTasKRequest;
use App\Models\PmsBoard;
use Illuminate\Http\Request;
use App\Repositories\PmsRepository;
use Illuminate\Support\Facades\Auth;

class PmsController extends Controller
{
    public function __construct(protected PmsRepository $pmsHelper) {}

    public function index()
    {
        $createdBoards = $this->pmsHelper->getCreatedBoardList(Auth::id());
        $associatedBoards = $this->pmsHelper->getAssociatedBoardList(Auth::id());
        return view('pms.index', compact('createdBoards', 'associatedBoards'));
    }

    public function showBoard(int $id)
    {
        $board = $this->pmsHelper->getBoardDetails($id);
        if ($board) {
            return view('pms.created-boards', compact('board'));
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
            $request->position
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
}
