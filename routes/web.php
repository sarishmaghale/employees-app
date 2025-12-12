<?php

use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AuthenticateController;

Route::get('/login', [AuthenticateController::class, 'index'])->name('login');
Route::post('/login', [AuthenticateController::class, 'login'])->name('login.request');

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

    Route::get('/calendar', [TaskController::class, 'calendar'])->name('calendar.index');
    Route::get('/tasks/all', [TaskController::class, 'index'])->name('tasks.all');
    Route::get('/tasks', [TaskController::class, 'allTasks'])->name('calendar.lists');
    Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('calendar.show');
    Route::post('/tasks', [TaskController::class, 'store'])->name('task.store');
    Route::post('/task/update/{id}', [TaskController::class, 'update'])->name('task.update');
    Route::delete('/task/{id}', [TaskController::class, 'delete'])->name('task.destroy');
});
