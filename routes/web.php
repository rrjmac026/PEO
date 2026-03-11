<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\WorkRequestController;
use App\Http\Controllers\Admin\WorkRequestLogController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\User\UserWorkRequestController;
use App\Http\Controllers\Admin\EmployeeManagementController;
use App\Http\Controllers\User\UserConcretePouringController;
use App\Http\Controllers\Admin\AdminConcretePouringController;
use App\Http\Controllers\Reviewer\ReviewerController;
use App\Http\Controllers\Reviewer\ReviewerWorkRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/',              [NotificationController::class, 'index'])        ->name('index');
    Route::get('/unread-count',  [NotificationController::class, 'unreadCount'])  ->name('unread-count');
    Route::post('/mark-all-read',[NotificationController::class, 'markAllRead'])  ->name('mark-all-read');
    Route::post('/{notification}/read', [NotificationController::class, 'markRead'])->name('mark-read');
});

// ─── Admin Routes ──────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])
        ->name('dashboard');

    // Work Requests
    Route::resource('work-requests', WorkRequestController::class);

    // Employee search autocomplete
    Route::get('/work-requests/api/search-employee', [WorkRequestController::class, 'searchEmployee'])
        ->name('work-requests.search-employee');
    Route::get('/work-requests/api/employee/{id}', [WorkRequestController::class, 'getEmployee'])
        ->name('work-requests.get-employee');

    // Print / Download PDF
    Route::get('/work-requests/{workRequest}/print', [WorkRequestController::class, 'print'])
        ->name('work-requests.print');
    Route::get('/work-requests/{workRequest}/download', [WorkRequestController::class, 'download'])
        ->name('work-requests.download');

    // Admin: assign engineers to a work request
    Route::get('/work-requests/{workRequest}/assign', [WorkRequestController::class, 'assignForm'])
        ->name('work-requests.assign-form');
    Route::post('/work-requests/{workRequest}/assign', [WorkRequestController::class, 'assign'])
        ->name('work-requests.assign');

    // Admin: final approve / reject after all reviewers done
    Route::get('/work-requests/{workRequest}/decision', [WorkRequestController::class, 'decisionForm'])
        ->name('work-requests.decision-form');
    Route::post('/work-requests/{workRequest}/decision', [WorkRequestController::class, 'storeDecision'])
        ->name('work-requests.store-decision');

    // Manual status override
    Route::patch('/work-requests/{workRequest}/status', [WorkRequestController::class, 'updateStatus'])
        ->name('work-requests.update-status');

    // Logs
    Route::get('work-requests/{workRequest}/logs', [WorkRequestController::class, 'logs'])
        ->name('work-requests.logs');
    Route::get('/work-request-logs', [WorkRequestLogController::class, 'index'])
        ->name('work-request-logs.index');

    // Users & Employees
    Route::resource('users', UserManagementController::class);
    Route::resource('employees', EmployeeManagementController::class);

    // ── Concrete Pouring ──────────────────────────────────────────────────────
    Route::get('/concrete-pouring', [AdminConcretePouringController::class, 'index'])
        ->name('concrete-pouring.index');
    Route::get('/concrete-pouring/reports', [AdminConcretePouringController::class, 'reports'])
        ->name('concrete-pouring.reports');
    Route::get('/concrete-pouring/print-report', [AdminConcretePouringController::class, 'printReport'])
        ->name('concrete-pouring.print-report');
    Route::get('/concrete-pouring/calendar', [AdminConcretePouringController::class, 'calendar'])
        ->name('concrete-pouring.calendar');
    Route::post('/concrete-pouring/bulk-approve', [AdminConcretePouringController::class, 'bulkApprove'])
        ->name('concrete-pouring.bulk-approve');
    Route::post('/concrete-pouring/bulk-disapprove', [AdminConcretePouringController::class, 'bulkDisapprove'])
        ->name('concrete-pouring.bulk-disapprove');
    Route::resource('concrete-pouring', AdminConcretePouringController::class)
        ->except(['create', 'store', 'edit', 'update']);
    Route::get('/concrete-pouring/{concretePouring}/me-mtqa-review', [AdminConcretePouringController::class, 'meMtqaReviewForm'])
        ->name('concrete-pouring.me-mtqa-review-form');
    Route::post('/concrete-pouring/{concretePouring}/me-mtqa-review', [AdminConcretePouringController::class, 'storeMeMtqaReview'])
        ->name('concrete-pouring.store-me-mtqa-review');
    Route::get('/concrete-pouring/{concretePouring}/re-review', [AdminConcretePouringController::class, 'residentEngineerReviewForm'])
        ->name('concrete-pouring.re-review-form');
    Route::post('/concrete-pouring/{concretePouring}/re-review', [AdminConcretePouringController::class, 'storeResidentEngineerReview'])
        ->name('concrete-pouring.store-re-review');
    Route::get('/concrete-pouring/{concretePouring}/approval', [AdminConcretePouringController::class, 'approvalForm'])
        ->name('concrete-pouring.approval-form');
    Route::post('/concrete-pouring/{concretePouring}/approve', [AdminConcretePouringController::class, 'approve'])
        ->name('concrete-pouring.approve');
    Route::post('/concrete-pouring/{concretePouring}/disapprove', [AdminConcretePouringController::class, 'disapprove'])
        ->name('concrete-pouring.disapprove');
    Route::post('/concrete-pouring/{concretePouring}/add-note', [AdminConcretePouringController::class, 'addNote'])
        ->name('concrete-pouring.add-note');
    Route::get('/concrete-pouring/{concretePouring}/print', [AdminConcretePouringController::class, 'print'])
        ->name('concrete-pouring.print');
});

// ─── Contractor (User) Routes ──────────────────────────────────────────────────
Route::prefix('user')->name('user.')->middleware(['auth', 'role:contractor'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\User\UserController::class, 'dashboard'])
        ->name('dashboard');

    Route::resource('work-requests', UserWorkRequestController::class);
    Route::get('employee-details', [UserWorkRequestController::class, 'getEmployeeDetails'])
        ->name('employee-details');

    Route::resource('concrete-pouring', UserConcretePouringController::class);
    Route::get('/concrete-pouring/{concretePouring}/print', [UserConcretePouringController::class, 'print'])
        ->name('concrete-pouring.print');
});

// ─── Profile ───────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Reviewer Routes ───────────────────────────────────────────────────────────
Route::prefix('reviewer')->name('reviewer.')
    ->middleware(['auth', 'role:provincial_engineer,site_inspector,surveyor,resident_engineer,engineeriii,engineeriv,mtqa'])
    ->group(function () {

    Route::get('/dashboard', [ReviewerController::class, 'dashboard'])
        ->name('dashboard');

    // List: only shows requests currently waiting for this user
    Route::get('/work-requests', [ReviewerWorkRequestController::class, 'index'])
        ->name('work-requests.index');

    Route::get('/work-requests/{workRequest}', [ReviewerWorkRequestController::class, 'show'])
        ->name('work-requests.show');

    // Each role can only POST when it's their assigned step
    Route::post('/work-requests/{workRequest}/inspection', [ReviewerWorkRequestController::class, 'storeInspection'])
        ->name('work-requests.store-inspection');

    Route::post('/work-requests/{workRequest}/survey', [ReviewerWorkRequestController::class, 'storeSurvey'])
        ->name('work-requests.store-survey');

    Route::post('/work-requests/{workRequest}/engineer-review', [ReviewerWorkRequestController::class, 'storeEngineerReview'])
        ->name('work-requests.store-engineer-review');

    Route::post('/work-requests/{workRequest}/mtqa-check', [ReviewerWorkRequestController::class, 'storeMtqaCheck'])
        ->name('work-requests.store-mtqa-check');

    Route::post('/work-requests/{workRequest}/engineer-iv-review', [ReviewerWorkRequestController::class, 'storeEngineerIvReview'])
        ->name('work-requests.store-engineer-iv-review');

    Route::post('/work-requests/{workRequest}/recommending-approval', [ReviewerWorkRequestController::class, 'storeRecommendingApproval'])
        ->name('work-requests.store-recommending-approval');

    Route::post('/work-requests/{workRequest}/provincial-note', [ReviewerWorkRequestController::class, 'storeProvincialNote'])
        ->name('work-requests.store-provincial-note');
});

require __DIR__ . '/auth.php';