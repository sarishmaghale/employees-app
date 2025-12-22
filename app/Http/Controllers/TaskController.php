<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Helpers\JsonResponse;
use App\Mail\TaskAssignedMail;
use Illuminate\Support\Facades\Log;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Repositories\EmailRepository;
use App\Http\Requests\StoreTaskRequest;
use App\Repositories\EmployeeRepository;
use App\Notifications\TaskUpdateNotification;
use App\Notifications\TaskDeletedNotification;
use App\Notifications\TaskAssignedNotification;

class TaskController extends Controller
{
    public function __construct(
        protected TaskRepository $taskRepo,
        protected EmailRepository $emailService,
        protected EmployeeRepository $empRepo
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
        $user = $this->empRepo->getByEmail($task->employee->email);
        if ($task !== null) {
            try {
                if ($user) $user->notify(new TaskAssignedNotification($task));
                Mail::to($task->employee->email)->send(new TaskAssignedMail(type: 0, task: $task));
                return JsonResponse::success(message: 'Task added successfully', data: $task);
            } catch (\Throwable $e) {
                return JsonResponse::error(message: 'Failed to send mail');
            }
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
        $user = $this->empRepo->getByEmail($task->employee->email);
        if ($isUpdated) {
            $updatedTask = $this->taskRepo->getById($id);
            try {
                if ($user) $user->notify(new TaskUpdateNotification($updatedTask));
                Mail::to($task->employee->email)->send(new TaskAssignedMail(
                    type: 1,
                    task: $updatedTask
                ));
                return JsonResponse::success(message: 'Task updated successfully');
            } catch (\Throwable $e) {
                return JsonResponse::error(message: 'Task added but failed to send email');
            }
        }
        return JsonResponse::error(message: 'Faied to update');
    }

    public function delete(int $id)
    {
        $task = $this->taskRepo->getById($id);
        if ($task === null) {
            return JsonResponse::error(message: 'Task not found');
        }
        $taskTitle = $task->title;
        $employee = $this->empRepo->getById($task->employee_id);
        $employee->notify(new TaskDeletedNotification($taskTitle));
        $isDeleted = $this->taskRepo->deleteTask($task);
        if ($isDeleted) return JsonResponse::success(message: 'Deleted successfully');
        return JsonResponse::error(message: 'Failed to delete');
    }
}
