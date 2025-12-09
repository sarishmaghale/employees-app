<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\TaskController;

Route::get('/login', [AuthenticateController::class, 'index'])->name('login');
Route::post('/login', [AuthenticateController::class, 'login'])->name('login.request');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');

    Route::get('/profile', [EmployeeController::class, 'profileView'])->name('profile.show');
    Route::post('/profile/{id}', [EmployeeController::class, 'modifyProfile'])->name('profile.update');
    Route::post('/loggedOut', [AuthenticateController::class, 'logout'])->name('logout');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employeesList', [EmployeeController::class, 'employeesList'])->name('employees.list');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::post('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::patch('/employees/{id}', [EmployeeController::class, 'delete'])->name('employees.delete');

    Route::get('/calendar', [TaskController::class, 'index'])->name('calendar.index');
    Route::get('/tasks', [TaskController::class, 'allTasks'])->name('calendar.lists');
    Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('calendar.show');
    Route::post('/tasks', [TaskController::class, 'store'])->name('calendar.store');
    Route::post('/task/update/{id}', [TaskController::class, 'update'])->name('calendar.update');
    Route::delete('/task/{id}', [TaskController::class, 'delete'])->name('calendar.destroy');
});
