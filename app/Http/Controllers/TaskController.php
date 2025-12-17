<?php

namespace App\Http\Controllers;


use App\Helpers\JsonResponse;
use App\Http\Requests\StoreTaskRequest;
use App\Repositories\EmailRepository;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct(
        protected TaskRepository $taskRepo,
        protected EmailRepository $emailService
    ) {}

    public function index()
    {
        return view('all-tasks');
    }

    public function allTasks(Request $request)
    {
        $filters = $request->only(['start', 'end', 'category_id', 'employee_id']);
        $userRole = Auth::user()->role;
        if ($userRole === 'admin')   $result = $this->taskRepo->getAll($filters);
        else $result = $this->taskRepo->getAllById(Auth::user()->id, $filters);

        return response()->json($result);
    }

    public function store(StoreTaskRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['employee_id'] = $request->employee_id ?: Auth::user()->id;
        $task = $this->taskRepo->addTask($validatedData);
        if ($task !== null) {
            $mailSent = $this->emailService->sendTaskAssignedMail(
                toEmail: $task->employee->email,
                subject: 'New Task assigned',
                taskInfo: $task
            );
            if (!$mailSent) return JsonResponse::error(message: 'Task added but failed to send email');
            return JsonResponse::success(message: 'Task added successfully', data: $task);
        }
        return JsonResponse::error(message: 'Failed to add');
    }

    public function show(int $id)
    {
        $task = $this->taskRepo->getById($id);
        if ($task !== null) {
            return JsonResponse::success(data: $task);
        }
        return JsonResponse::error(message: "Task doesn't exist");
    }

    public function recentTasks()
    {
        if (Auth::user()->role === 'admin') $task = $this->taskRepo->getRecentlyAddedTasks();
        else $task = $this->taskRepo->getRecentlyAddedTasksForStaff(Auth::user()->id);
        if ($task !== null) return response()->json($task);
        else   return JsonResponse::error(message: "No tasks");
    }
    public function update(StoreTaskRequest $request, int $id)
    {
        $task = $this->taskRepo->getById($id);
        $validatedData = $request->validated();
        $validatedData['employee_id'] = $request->employee_id ?: Auth::user()->id;
        $isUpdated = $this->taskRepo->updateTask($validatedData, $task);
        if ($isUpdated) {
            $updatedtask = $this->taskRepo->getById($id);
            $mailSent = $this->emailService->sendTaskAssignedMail(
                toEmail: $task->employee->email,
                subject: 'Task updated',
                taskInfo: $updatedtask,
            );

            if (!$mailSent) return JsonResponse::error(message: 'Task added but failed to send email');

            return JsonResponse::success(message: 'Task updated successfully');
        }
        return JsonResponse::error(message: 'Faied to update');
    }

    public function delete(int $id)
    {
        $task = $this->taskRepo->getById($id);
        if ($task === null) {
            return JsonResponse::error(message: 'Task not found');
        }
        $isDeleted = $this->taskRepo->deleteTask($task);
        if ($isDeleted) return JsonResponse::success(message: 'Deleted successfully');
        return JsonResponse::error(message: 'Failed to delete');
    }
}
