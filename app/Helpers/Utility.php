<?php

use App\Models\Task;
use App\Models\Employee;
use App\Models\EmployeeKanbanStatusLink;
use App\Models\PmsBoard;
use App\Models\TaskCategory;
use App\Models\TaskSubCategory;
use App\Models\TaskCategoryLink;
use Illuminate\Support\Facades\Auth;

if (!function_exists('getTaskCategories')) {
    function getTaskCategories()
    {
        return TaskCategory::all();
    }
}

if (!function_exists('getTaskSubCategories')) {
    function getTaskSubCategories(int $id)
    {
        $category = TaskCategory::find($id);
        return $category->subCategories;
    }
}

if (!function_exists('getEmployeesCount')) {
    function getEmployeesCount()
    {
        return Employee::where('isDeleted', 0)->count();
    }
}

if (!function_exists('getEmployees')) {
    function getEmployees()
    {
        return  Employee::select('username', 'id', 'role')
            ->where('isDeleted', 0)->get();
    }
}

if (!function_exists('countTasksEndingToday')) {
    function countTasksEndingToday()
    {
        $today = date('Y-m-d');
        if (Auth::user()->role === 'admin') $taskCount = Task::where('end', $today)->count();
        else $taskCount = Task::where('employee_id', Auth::user()->id)
            ->where('end', $today)->count();
        return $taskCount;
    }
}
if (!function_exists('countTasksStartingToday')) {
    function countTasksStartingToday()
    {
        $today = date('Y-m-d');
        if (Auth::user()->role === 'admin') $taskCount = Task::where('start', $today)->count();
        else $taskCount = Task::where('employee_id', Auth::user()->id)
            ->where('start', $today)->count();
        return $taskCount;
    }
}
if (!function_exists('latest_notifications')) {
    function latest_notifications(int $limit = 5)
    {
        if (!Auth::check()) {
            return collect();
        }

        return Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }
}
if (!function_exists('unread_notifications_count')) {
    function unread_notifications_count(): int
    {
        if (!Auth::check()) {
            return 0;
        }

        return Auth::user()
            ->unreadNotifications()
            ->count();
    }
}
