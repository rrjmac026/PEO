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
use App\Http\Controllers\Reviewer\ReviewerConcretePouringController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/',               [NotificationController::class, 'index'])       ->name('index');
    Route::get('/unread-count',   [NotificationController::class, 'unreadCount']) ->name('unread-count');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']) ->name('mark-all-read');
    Route::post('/{notification}/read', [NotificationController::class, 'markRead'])->name('mark-read');
    Route::get('/notifications', [NotificationController::class, 'page'])->name('page');
});

// =============================================================================
// ADMIN
// Admin receives work requests and assigns reviewers ONLY.
// Final decision is made by the Provincial Engineer (reviewer role).
// =============================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])
        ->name('dashboard');

    // ── Work Requests ─────────────────────────────────────────────────────────
    Route::resource('work-requests', WorkRequestController::class);

    Route::get('/work-requests/api/search-employee', [WorkRequestController::class, 'searchEmployee'])
        ->name('work-requests.search-employee');
    Route::get('/work-requests/api/employee/{id}', [WorkRequestController::class, 'getEmployee'])
        ->name('work-requests.get-employee');

    Route::get('/work-requests/{workRequest}/print',    [WorkRequestController::class, 'print'])   ->name('work-requests.print');
    Route::get('/work-requests/{workRequest}/download', [WorkRequestController::class, 'download'])->name('work-requests.download');

    // Assign reviewers (admin's only action beyond CRUD)
    Route::get( '/work-requests/{workRequest}/assign', [WorkRequestController::class, 'assignForm'])->name('work-requests.assign-form');
    Route::post('/work-requests/{workRequest}/assign', [WorkRequestController::class, 'assign'])    ->name('work-requests.assign');

    // NOTE: /decision routes have been REMOVED.
    // The Provincial Engineer now makes the final decision via the reviewer routes.

    Route::patch('/work-requests/{workRequest}/status', [WorkRequestController::class, 'updateStatus'])->name('work-requests.update-status');

    Route::get('/work-requests/{workRequest}/logs', [WorkRequestController::class, 'logs'])
        ->name('work-requests.logs');
    Route::get('/work-request-logs', [WorkRequestLogController::class, 'index'])
        ->name('work-request-logs.index');

    // ── Users & Employees ─────────────────────────────────────────────────────
    Route::resource('users', UserManagementController::class);
    Route::resource('employees', EmployeeManagementController::class);

    // ── Concrete Pouring ──────────────────────────────────────────────────────
    Route::prefix('concrete-pouring')->name('concrete-pouring.')->group(function () {

        Route::get('/',             [AdminConcretePouringController::class, 'index'])       ->name('index');
        Route::get('/reports',      [AdminConcretePouringController::class, 'reports'])     ->name('reports');
        Route::get('/print-report', [AdminConcretePouringController::class, 'printReport']) ->name('print-report');
        Route::get('/calendar',     [AdminConcretePouringController::class, 'calendar'])    ->name('calendar');

        Route::post('/bulk-approve',    [AdminConcretePouringController::class, 'bulkApprove'])    ->name('bulk-approve');
        Route::post('/bulk-disapprove', [AdminConcretePouringController::class, 'bulkDisapprove']) ->name('bulk-disapprove');

        Route::get(   '/{concretePouring}',         [AdminConcretePouringController::class, 'show'])    ->name('show');
        Route::delete('/{concretePouring}',         [AdminConcretePouringController::class, 'destroy']) ->name('destroy');
        Route::get(   '/{concretePouring}/print',   [AdminConcretePouringController::class, 'print'])   ->name('print');

        Route::get( '/{concretePouring}/assign', [AdminConcretePouringController::class, 'assignForm']) ->name('assign-form');
        Route::post('/{concretePouring}/assign', [AdminConcretePouringController::class, 'assign'])     ->name('assign');

        Route::get( '/{concretePouring}/decision', [AdminConcretePouringController::class, 'decisionForm'])  ->name('decision-form');
        Route::post('/{concretePouring}/decision', [AdminConcretePouringController::class, 'storeDecision']) ->name('store-decision');
    });

    Route::get('/dev/pdf-grid', function () {
        $pdf = new \setasign\Fpdi\Fpdi('P', 'mm', 'A4');
        $pdf->AddPage();

        $templatePath = storage_path('app/pdf-templates/concrete-pouring-template.pdf');
        $pdf->setSourceFile($templatePath);
        $tplId = $pdf->importPage(1);
        $pdf->useTemplate($tplId, 0, 0, 210, 297);

        // Draw a 10mm grid with labels
        $pdf->SetFont('Arial', '', 5);
        $pdf->SetDrawColor(200, 0, 0);
        $pdf->SetTextColor(200, 0, 0);
        $pdf->SetLineWidth(0.1);

        for ($x = 0; $x <= 210; $x += 10) {
            $pdf->Line($x, 0, $x, 297);
            $pdf->SetXY($x + 0.5, 2);
            $pdf->Cell(8, 3, (string)$x);
        }
        for ($y = 0; $y <= 297; $y += 10) {
            $pdf->Line(0, $y, 210, $y);
            $pdf->SetXY(1, $y + 0.5);
            $pdf->Cell(8, 3, (string)$y);
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="grid.pdf"',
        ]);
    })->middleware('auth');

    Route::get('/dev/pdf-debug', function () {
        $path = storage_path('app/pdf-templates/concrete-pouring-template.pdf');
        
        return response()->json([
            'resolved_path'  => $path,
            'file_exists'    => file_exists($path),
            'storage_path'   => storage_path('app'),
            'directory_exists' => is_dir(storage_path('app/pdf-templates')),
            'files_in_dir'   => is_dir(storage_path('app/pdf-templates')) 
                                ? scandir(storage_path('app/pdf-templates')) 
                                : 'directory not found',
        ]);
    });
});

// =============================================================================
// CONTRACTOR (User)
// =============================================================================
Route::prefix('user')->name('user.')->middleware(['auth', 'role:contractor'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\User\UserController::class, 'dashboard'])
        ->name('dashboard');

    Route::resource('work-requests', UserWorkRequestController::class);
    Route::get('employee-details', [UserWorkRequestController::class, 'getEmployeeDetails'])
        ->name('employee-details');

    Route::get('/concrete-pouring/{concretePouring}/print', [UserConcretePouringController::class, 'print'])
        ->name('concrete-pouring.print');

    Route::resource('concrete-pouring', UserConcretePouringController::class);
});

// =============================================================================
// PROFILE
// =============================================================================
Route::middleware('auth')->group(function () {
    Route::get(   '/profile', [ProfileController::class, 'edit'])   ->name('profile.edit');
    Route::patch( '/profile', [ProfileController::class, 'update']) ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =============================================================================
// REVIEWER
// Covers all reviewer roles.
// Provincial Engineer makes the FINAL decision (approve/reject).
// MTQA is notified after approval and can print.
// =============================================================================
Route::prefix('reviewer')->name('reviewer.')
    ->middleware([
        'auth',
        'role:provincial_engineer,site_inspector,surveyor,resident_engineer,engineeriii,engineeriv,mtqa',
    ])
    ->group(function () {

    Route::get('/dashboard', [ReviewerController::class, 'dashboard'])->name('dashboard');

    // ── Work Requests ─────────────────────────────────────────────────────────
    Route::get('/work-requests/approved',      [ReviewerWorkRequestController::class, 'approvedIndex'])
    ->name('work-requests.approved');
    Route::get('/work-requests',               [ReviewerWorkRequestController::class, 'index'])->name('work-requests.index');
    Route::get('/work-requests/{workRequest}', [ReviewerWorkRequestController::class, 'show']) ->name('work-requests.show');
    

    // Reviewer step submissions
    Route::post('/work-requests/{workRequest}/inspection',
        [ReviewerWorkRequestController::class, 'storeInspection'])
        ->name('work-requests.store-inspection');

    Route::post('/work-requests/{workRequest}/survey',
        [ReviewerWorkRequestController::class, 'storeSurvey'])
        ->name('work-requests.store-survey');

    Route::post('/work-requests/{workRequest}/engineer-review',
        [ReviewerWorkRequestController::class, 'storeEngineerReview'])
        ->name('work-requests.store-engineer-review');

    Route::post('/work-requests/{workRequest}/mtqa-check',
        [ReviewerWorkRequestController::class, 'storeMtqaCheck'])
        ->name('work-requests.store-mtqa-check');

    Route::post('/work-requests/{workRequest}/engineer-iv-review',
        [ReviewerWorkRequestController::class, 'storeEngineerIvReview'])
        ->name('work-requests.store-engineer-iv-review');

    Route::post('/work-requests/{workRequest}/recommending-approval',
        [ReviewerWorkRequestController::class, 'storeRecommendingApproval'])
        ->name('work-requests.store-recommending-approval');

    // Provincial Engineer: FINAL DECISION (approve or reject) — replaces old provincial-note
    Route::post('/work-requests/{workRequest}/provincial-decision',
        [ReviewerWorkRequestController::class, 'storeProvincialDecision'])
        ->name('work-requests.store-provincial-decision');

    // MTQA print/download (accessible when status = approved)
    Route::get('/work-requests/{workRequest}/print',
        [ReviewerWorkRequestController::class, 'printApproved'])
        ->name('work-requests.print');

    Route::get('/work-requests/{workRequest}/download',
        [ReviewerWorkRequestController::class, 'downloadApproved'])
        ->name('work-requests.download');

    // ── Concrete Pouring ──────────────────────────────────────────────────────
    Route::prefix('concrete-pouring')->name('concrete-pouring.')->group(function () {

        Route::get('/',                  [ReviewerConcretePouringController::class, 'index'])->name('index');
        Route::get('/{concretePouring}', [ReviewerConcretePouringController::class, 'show'])->name('show');

        Route::post('/{concretePouring}/mtqa-review',
            [ReviewerConcretePouringController::class, 'storeMtqaReview'])
            ->name('store-mtqa-review');

        Route::post('/{concretePouring}/engineer-review',
            [ReviewerConcretePouringController::class, 'storeResidentEngineerReview'])
            ->name('store-engineer-review');

        Route::post('/{concretePouring}/provincial-note',
            [ReviewerConcretePouringController::class, 'storeProvincialNote'])
            ->name('store-provincial-note');
    });
});

require __DIR__ . '/auth.php';