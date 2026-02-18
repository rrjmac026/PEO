<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\WorkRequestController;
use App\Http\Controllers\Admin\WorkRequestLogController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\User\UserWorkRequestController;
use App\Http\Controllers\Admin\EmployeeManagementController;
use App\Http\Controllers\User\UserConcretePouringController;
use App\Http\Controllers\Admin\AdminConcretePouringController;
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
    
    Route::get('work-requests/{workRequest}/export-excel', [WorkRequestController::class, 'exportToExcel'])
    ->name('work-requests.export-excel');

    Route::resource('users', UserManagementController::class);
    Route::resource('employees', EmployeeManagementController::class);

    /*
    |--------------------------------------------------------------------------
    | Concrete Pouring Routes - Admin
    |--------------------------------------------------------------------------
    */

    
    // Concrete Pouring Reports
    Route::get('/concrete-pouring', [AdminConcretePouringController::class, 'index'])
        ->name('concrete-pouring.index');
    Route::get('/concrete-pouring/reports', [AdminConcretePouringController::class, 'reports'])
        ->name('concrete-pouring.reports');
    Route::get('/concrete-pouring/print-report', [AdminConcretePouringController::class, 'printReport'])
        ->name('concrete-pouring.print-report');
    
    // Concrete Pouring Calendar
    Route::get('/concrete-pouring/calendar', [AdminConcretePouringController::class, 'calendar'])
        ->name('concrete-pouring.calendar');
    
    // Bulk Actions
    Route::post('/concrete-pouring/bulk-approve', [AdminConcretePouringController::class, 'bulkApprove'])
        ->name('concrete-pouring.bulk-approve');
    Route::post('/concrete-pouring/bulk-disapprove', [AdminConcretePouringController::class, 'bulkDisapprove'])
        ->name('concrete-pouring.bulk-disapprove');
    
    // Concrete Pouring Resource Routes
    Route::resource('concrete-pouring', AdminConcretePouringController::class)
        ->except(['create', 'store', 'edit', 'update']);
    
    // ME/MTQA Review Routes
    Route::get('/concrete-pouring/{concretePouring}/me-mtqa-review', [AdminConcretePouringController::class, 'meMtqaReviewForm'])
        ->name('concrete-pouring.me-mtqa-review-form');
    Route::post('/concrete-pouring/{concretePouring}/me-mtqa-review', [AdminConcretePouringController::class, 'storeMeMtqaReview'])
        ->name('concrete-pouring.store-me-mtqa-review');
    
    // Resident Engineer Review Routes
    Route::get('/concrete-pouring/{concretePouring}/re-review', [AdminConcretePouringController::class, 'residentEngineerReviewForm'])
        ->name('concrete-pouring.re-review-form');
    Route::post('/concrete-pouring/{concretePouring}/re-review', [AdminConcretePouringController::class, 'storeResidentEngineerReview'])
        ->name('concrete-pouring.store-re-review');
    
    // Approval/Disapproval Routes
    Route::get('/concrete-pouring/{concretePouring}/approval', [AdminConcretePouringController::class, 'approvalForm'])
        ->name('concrete-pouring.approval-form');
    Route::post('/concrete-pouring/{concretePouring}/approve', [AdminConcretePouringController::class, 'approve'])
        ->name('concrete-pouring.approve');
    Route::post('/concrete-pouring/{concretePouring}/disapprove', [AdminConcretePouringController::class, 'disapprove'])
        ->name('concrete-pouring.disapprove');
    
    // Provincial Engineer Note
    Route::post('/concrete-pouring/{concretePouring}/add-note', [AdminConcretePouringController::class, 'addNote'])
        ->name('concrete-pouring.add-note');
    
    // Print Single Form
    Route::get('/concrete-pouring/{concretePouring}/print', [AdminConcretePouringController::class, 'print'])
        ->name('concrete-pouring.print');
});

// User Routes
Route::prefix('user')->name('user.')->middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\User\UserController::class, 'dashboard'])
        ->name('dashboard');
    
    // Work Requests
    Route::resource('work-requests', UserWorkRequestController::class);
    Route::get('work-requests/{workRequest}/print', [UserWorkRequestController::class, 'print'])
        ->name('work-requests.print');
    Route::get('work-requests/{workRequest}/download', [UserWorkRequestController::class, 'download'])
        ->name('work-requests.download');
    Route::get('employee-details', [UserWorkRequestController::class, 'getEmployeeDetails'])
        ->name('employee-details');

    /*
    |--------------------------------------------------------------------------
    | Concrete Pouring Routes - User
    |--------------------------------------------------------------------------
    */
    
    // Concrete Pouring Resource Routes
    Route::resource('concrete-pouring', UserConcretePouringController::class);
    
    // Print Concrete Pouring Form
    Route::get('/concrete-pouring/{concretePouring}/print', [UserConcretePouringController::class, 'print'])
        ->name('concrete-pouring.print');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';