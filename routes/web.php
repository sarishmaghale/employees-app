<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AuthenticateController;



Route::get('/login', [AuthenticateController::class, 'index'])->name('login.index');
Route::post('/login', [AuthenticateController::class, 'login'])->name('login.request');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    Route::put('/employees/profile/{id}', [EmployeeController::class, 'modifyProfile'])->name('profile.update');
    Route::post('/logout', [AuthenticateController::class, 'logout'])->name('logout');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::post('/employees', [EmployeeController::class, 'create'])->name('employees.create');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::patch('/employees/{id}', [EmployeeController::class, 'delete'])->name('employees.delete');
});
