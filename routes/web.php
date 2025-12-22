<?php

use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\NotificationController;
use PharIo\Manifest\Author;

Route::get('/login', [AuthenticateController::class, 'index'])->name('login');
Route::post('/login', [AuthenticateController::class, 'login'])->name('login.request');
Route::post('/verify', [AuthenticateController::class, 'verifyLogin'])->name('login.verify');
Route::get('/set-new-password', [AuthenticateController::class, 'newPassword'])->name('password.new');
Route::post('/set-new-password', [AuthenticateController::class, 'saveNewPassword'])->name('password.save-new');
Route::post('/initiate-reset', [AuthenticateController::class, 'initiatePasswordReset'])->name('password.initiate');
Route::get('/reset-password', [AuthenticateController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [AuthenticateController::class, 'resetPassword'])->name('password.reset');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');

    Route::middleware([IsAdmin::class])->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employeesList', [EmployeeController::class, 'employeesList'])->name('employees.list');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::post('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::patch('/employees/{id}', [EmployeeController::class, 'delete'])->name('employees.delete');
        Route::get('/employee/task/{id}', [EmployeeController::class, 'task'])->name('employees.task');
    });

    Route::get('/profile', [EmployeeController::class, 'profileView'])->name('profile.show');
    Route::post('/profile/{id}', [EmployeeController::class, 'modifyProfile'])->name('profile.update');
    Route::post('/loggedOut', [AuthenticateController::class, 'logout'])->name('logout');

    Route::get('/calendar', [CalendarController::class, 'calendar'])->name('calendar.index');
    Route::get('/calendar/show', [CalendarController::class, 'show'])->name('calendar.show');

    Route::get('/tasks/all', [TaskController::class, 'index'])->name('tasks.all');
    Route::get('/tasks', [TaskController::class, 'allTasks'])->name('tasks.lists');
    Route::get('/tasks/recents', [TaskController::class, 'recentTasks'])->name('tasks.recent');
    Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('task.show');
    Route::post('/tasks', [TaskController::class, 'store'])->name('task.store');

    Route::post('/task/update/{id}', [TaskController::class, 'update'])->name('task.update');
    Route::delete('/task/{id}', [TaskController::class, 'delete'])->name('task.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.all');
    Route::post('/notification/{id}', [NotificationController::class, 'markAsRead'])->name('notification.read');
    Route::post('/notification/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications-all-read');
});
