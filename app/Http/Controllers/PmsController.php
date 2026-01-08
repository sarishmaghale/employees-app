<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\JsonResponse;
use App\Repositories\EmployeeRepository;
use App\Http\Requests\StorePmsTasKRequest;
use App\Jobs\SendTaskDueReminderMail;
use App\Notifications\TaskMemberAddedNotification;
use App\Notifications\BoardMemberAddedNotification;
use App\Repositories\Pms\BoardRepository;
use App\Repositories\Pms\CardRepository;
use App\Repositories\Pms\TaskRepository;

class PmsController extends Controller
{
    public function __construct(
        protected BoardRepository $boardRepo,
        protected CardRepository $cardRepo,
        protected TaskRepository $taskRepo,
        protected EmployeeRepository $empHelper
    ) {}

    //Board operationss

    public function index()
    {
        $createdBoards = $this->boardRepo->getCreatedBoardList();
        $associatedBoards = $this->boardRepo->getAssociatedBoardList();
        return view('pms.index', compact('createdBoards', 'associatedBoards'));
    }

    public function showBoard(int $id)
    {
        $employees = $this->empHelper->getEmployeeList();
        $board = $this->boardRepo->getBoardDetails($id);
        if ($board) {
            return view('pms.created-boards', compact('board', 'employees'));
        } else  abort(404);
    }

    public function storeBoard(Request $request)
    {
        $request->validate([
            'board_name' => 'required',
        ]);
        $result = $this->boardRepo->create($$request->input('board_name'));
        if ($result && $result->exists) return JsonResponse::success(data: $result);
        else return JsonResponse::error(message: 'failed to add');
    }

    public function addBoardMember(Request $request, $id)
    {

        $result = $this->boardRepo->addMember(boardId: $id, employeeId: $request->employee_id);
        if ($result['success']) {
            $employee = $this->empHelper->getById($request->employee_id);
            $member = $result['board'];
            $employee->notify(new BoardMemberAddedNotification($result['board']));
            return JsonResponse::success(message: $result['message'], data: $member);
        }

        return JsonResponse::error(message: $result['message']);
    }

    public function updateCover(Request $request)
    {
        $request->validate([
            'cover_image' => 'required|image',
            'board_id'    => 'required|integer|exists:pms_boards,id',
        ]);
        $board = $this->boardRepo->updateCoverImage(
            $request->board_id,
            $request->file('cover_image')
        );

        if (!$board) {
            return redirect()->back()->with('error', 'Board not found!');
        }

        return redirect()->back();
    }

    //card operations

    public function storeCard(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'board_id' => 'required|exists:pms_boards,id'
        ]);
        $data = $request->only(['title', 'board_id']);
        $result = $this->cardRepo->create($data);
        if ($result && $result->exists) return JsonResponse::success(data: $result);
        else return JsonResponse::error(message: 'failed to add');
    }

    public function deleteCard(int $taskId)
    {
        $result = $this->cardRepo->remove($taskId);
        if ($result) return JsonResponse::success(message: 'Card deleted successfully!');
        else return JsonResponse::error(message: 'Failed to delete');
    }


    //Task operations

    public function showTasks(int $cardId)
    {
        $tasks = $this->taskRepo->getTasks($cardId);
        return response()->json($tasks);
    }

    public function storeTask(StorePmsTasKRequest $request)
    {
        $data = $request->validated();
        $result = $this->taskRepo->create($data);
        if ($result && $result->exists) return JsonResponse::success(message: 'Added', data: $result);
        else return JsonResponse::error(message: 'Failed to add');
    }

    public function moveTask(Request $request)
    {
        $request->validate([
            'positions' => 'required|array|min:1',
            'card_id' => 'required|exists:pms_cards,id',
            'positions.*.task_id' => 'required|exists:pms_tasks,id',
            'positions.*.position' => 'required|integer|min:1',
        ]);
        $result = $this->taskRepo->updateTaskOrder(
            $request->card_id,
            $request->positions
        );
        if (!$result) return JsonResponse::error(message: 'Failed to move task');
        else return JsonResponse::success(message: 'Task updated', data: $result);
    }

    public function showTaskDetail(int $taskId)
    {
        $detail = $this->taskRepo->getDetails($taskId);
        if ($detail && $detail->exists) return JsonResponse::success(data: $detail);
        else return JsonResponse::error(message: 'Failed to fetch details');
    }

    public function deleteTask(int $taskId)
    {
        $result = $this->taskRepo->remove($taskId);
        if ($result) return JsonResponse::success(message: 'Task deleted successfully');
        else return JsonResponse::error(message: 'Failed to delete task');
    }

    public function addTaskMember(Request $request, $id)
    {

        $result = $this->taskRepo->addMember(taskId: $id, employeeId: $request->employee_id);
        if ($result['success']) {
            $employee = $result['member'];
            $task = $result['task'];
            $employee->notify(new TaskMemberAddedNotification($task));
            return JsonResponse::success(message: $result['message'], data: $employee);
        }

        return JsonResponse::error(message: $result['message']);
    }

    public function updateTask(Request $request, int $taskId)
    {
        $data = $request->only(['description', 'start_date', 'end_date', 'checklist_items', 'labels']);
        $result = $this->taskRepo->updateDetails($data, $taskId);
        if ($result) {
            if ($result->end_date) {
                $sendAt = Carbon::parse($result->end_date)->subDay();

                $job = new SendTaskDueReminderMail(
                    taskId: $result->id,

                    reminderForDate: $result->end_date
                );

                // Dispatch with delay if not past
                if ($sendAt->isPast())  dispatch($job);
                else  dispatch($job)->delay($sendAt);
            }
            return JsonResponse::success(message: 'Saved successfully', data: $result);
        } else return JsonResponse::error(message: 'Failed to save');
    }



    public function storeComment(Request $request)
    {
        $request->validate([
            'comment' => 'required',
            'task_id' => 'required|exists:pms_tasks,id'
        ]);
        $data = [
            'comment' => $request->comment,
            'task_id' => $request->task_id
        ];
        $comment = $this->taskRepo->saveComment($data);
        if ($comment) return JsonResponse::success(data: $comment);
        else return JsonResponse::error(message: 'Failed to post comment');
    }

    public function createChecklist(Request $request)
    {
        $data = [
            'title' => $request->title,
            'task_id' => $request->task_id
        ];
        $result = $this->taskRepo->saveChecklist($data);
        if ($result) return JsonResponse::success(data: $result);
        else return JsonResponse::error(message: 'Faild to add checklist');
    }

    public function createChecklistItem(Request $request)
    {
        $data = [
            'title' => $request->title,
            'checklist_id' => $request->checklist_id,

        ];
        $result = $this->taskRepo->saveChecklistItem($data);
        if ($result) return JsonResponse::success(data: $result);
        else return JsonResponse::error(message: 'Faild to add checklist');
    }

    public function deleteChecklist(int $id)
    {
        $result = $this->taskRepo->removeChecklistItem($id);
        if ($result) return JsonResponse::success(message: 'Deleted!');
        else return JsonResponse::error(message: 'Failed to delete!');
    }

    public function uploadTaskFile(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:pms_tasks,id',
            'file'    => 'required|file|max:10240',
        ]);
        $data = [
            'task_id' => $request->task_id,
            'file' => $request->file('file')
        ];
        $taskFile = $this->taskRepo->saveFile($data);
        if ($taskFile && $taskFile->exists) return JsonResponse::success(message: 'File uploaded', data: $taskFile);
        else return JsonResponse::error(message: 'Failed to upload file');
    }

    public function deleteTaskFile(int $fileId)
    {
        $result = $this->taskRepo->removeFile($fileId);
        if ($result) return JsonResponse::success(message: 'File deleted');
        else return JsonResponse::error(message: 'Failed to delete file');
    }
}
