<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\WorkRequestController;
use App\Http\Controllers\Admin\WorkRequestLogController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\User\UserWorkRequestController;
use App\Http\Controllers\Admin\EmployeeManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes with 'admin' prefix and name prefix
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])
        ->name('dashboard');

    // Work Request routes - now accessible as admin.work-requests.*
    Route::resource('work-requests', WorkRequestController::class);
    // Employee Search for Autofill
    Route::get('/work-requests/api/search-employee', [WorkRequestController::class, 'searchEmployee'])
        ->name('work-requests.search-employee');
    Route::get('/work-requests/api/employee/{id}', [WorkRequestController::class, 'getEmployee'])
        ->name('work-requests.get-employee');
    // CSV Import Routes
    Route::get('/work-requests/import/form', [WorkRequestController::class, 'importForm'])
        ->name('work-requests.import.form');
    Route::post('/work-requests/import/csv', [WorkRequestController::class, 'importCsv'])
        ->name('work-requests.import.csv');
    // Print/Download Routes
    Route::get('/work-requests/{workRequest}/print', [WorkRequestController::class, 'print'])
        ->name('work-requests.print');
    Route::get('/work-requests/{workRequest}/download', [WorkRequestController::class, 'download'])
        ->name('work-requests.download');
    // Status Update Route
    Route::patch('/work-requests/{workRequest}/status', [WorkRequestController::class, 'updateStatus'])
        ->name('work-requests.update-status');   
    Route::get('work-requests/{workRequest}/logs', [WorkRequestController::class, 'logs'])
        ->name('work-requests.logs');
    Route::get('/work-request-logs', [WorkRequestLogController::class, 'index'])
     ->name('work-request-logs.index');

    Route::resource('users', UserManagementController::class);
    Route::resource('employees', EmployeeManagementController::class);
});

// User Routes
Route::prefix('user')->name('user.')->middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\User\UserController::class, 'dashboard'])
        ->name('dashboard');
    Route::resource('work-requests', UserWorkRequestController::class);
    Route::get('work-requests/{workRequest}/print', [UserWorkRequestController::class, 'print'])
        ->name('work-requests.print');
    Route::get('work-requests/{workRequest}/download', [UserWorkRequestController::class, 'download'])
        ->name('work-requests.download');
    Route::get('employee-details', [UserWorkRequestController::class, 'getEmployeeDetails'])
        ->name('employee-details');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';