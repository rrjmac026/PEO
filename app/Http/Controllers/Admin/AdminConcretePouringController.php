<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConcretePouring;
use App\Models\ConcretePouringLog;
use App\Models\User;
use App\Services\ConcretePouringNotificationService;
use App\Services\ConcretePouringPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminConcretePouringController extends Controller
{
    const REVIEW_STEPS = [
        'resident_engineer'   => 'resident_engineer_user_id',
        'provincial_engineer' => 'noted_by_user_id',
        'mtqa'                => 'me_mtqa_user_id',
    ];

    // =========================================================================
    // INDEX
    // =========================================================================

    public function index(Request $request)
    {
        $query = ConcretePouring::with([
            'workRequest', 'requestedBy', 'meMtqaChecker',
            'residentEngineer', 'notedByEngineer', 'approver', 'disapprover',
        ]);

        if ($request->filled('search'))     $query->search($request->search);
        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('review_step')) $query->where('current_review_step', $request->review_step);
        if ($request->filled('contractor')) $query->where('contractor', $request->contractor);
        if ($request->filled('date_from'))  $query->whereDate('pouring_datetime', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('pouring_datetime', '<=', $request->date_to);

        $sortBy    = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $concretePourings = $query->paginate(20)->withQueryString();
        $contractors      = ConcretePouring::select('contractor')->distinct()->orderBy('contractor')->pluck('contractor');

        $pendingAssignment = ConcretePouring::whereNull('current_review_step')->where('status', 'requested')->count();
        $inReview          = ConcretePouring::whereNotNull('current_review_step')->whereNotIn('current_review_step', ['mtqa'])->count();
        $awaitingDecision  = ConcretePouring::where('current_review_step', 'mtqa')->count();

        return view('admin.concrete-pouring.index', compact(
            'concretePourings', 'contractors',
            'pendingAssignment', 'inReview', 'awaitingDecision'
        ));
    }

    // =========================================================================
    // SHOW
    // =========================================================================

    public function show(ConcretePouring $concretePouring)
    {
        $concretePouring->load([
            'workRequest', 'requestedBy', 'meMtqaChecker',
            'residentEngineer', 'notedByEngineer', 'approver', 'disapprover',
            'logs.user',   // ← eager-load logs with their actor
        ]);

        return view('admin.concrete-pouring.show', compact('concretePouring'));
    }

    // =========================================================================
    // ASSIGN REVIEWERS
    // =========================================================================

    public function assignForm(ConcretePouring $concretePouring)
    {
        if (!in_array($concretePouring->status, ['requested'])) {
            return redirect()
                ->route('admin.concrete-pouring.show', $concretePouring)
                ->with('error', 'Reviewers can only be assigned to pending requests.');
        }

        $mtqas               = User::where('role', 'mtqa')->orderBy('name')->get();
        $residentEngineers   = User::where('role', 'resident_engineer')->orderBy('name')->get();
        $provincialEngineers = User::where('role', 'provincial_engineer')->orderBy('name')->get();

        return view('admin.concrete-pouring.assign', compact(
            'concretePouring', 'mtqas', 'residentEngineers', 'provincialEngineers'
        ));
    }

    public function assign(Request $request, ConcretePouring $concretePouring)
    {
        $request->validate([
            'me_mtqa_user_id'           => 'nullable|exists:users,id',
            'resident_engineer_user_id' => 'nullable|exists:users,id',
            'noted_by_user_id'          => 'nullable|exists:users,id',
        ]);

        $assignments = [
            'me_mtqa_user_id'           => $request->me_mtqa_user_id           ?: null,
            'resident_engineer_user_id' => $request->resident_engineer_user_id ?: null,
            'noted_by_user_id'          => $request->noted_by_user_id          ?: null,
        ];

        if (collect($assignments)->filter()->isEmpty()) {
            return back()->with('error', 'Please assign at least one reviewer before proceeding.');
        }

        $stepToCol = [
            'resident_engineer'   => 'resident_engineer_user_id',
            'provincial_engineer' => 'noted_by_user_id',
            'mtqa'                => 'me_mtqa_user_id',
        ];

        $firstStep = null;
        foreach ($stepToCol as $step => $col) {
            if (!is_null($assignments[$col])) {
                $firstStep = $step;
                break;
            }
        }

        $concretePouring->update(array_merge($assignments, [
            'current_review_step'  => $firstStep,
            'assigned_by_admin_id' => Auth::id(),
            'assigned_at'          => now(),
        ]));

        // Build a readable description of who was assigned
        $assignedLabels = [];
        if ($assignments['resident_engineer_user_id']) {
            $u = User::find($assignments['resident_engineer_user_id']);
            if ($u) $assignedLabels[] = "Resident Engineer: {$u->name}";
        }
        if ($assignments['noted_by_user_id']) {
            $u = User::find($assignments['noted_by_user_id']);
            if ($u) $assignedLabels[] = "Provincial Engineer: {$u->name}";
        }
        if ($assignments['me_mtqa_user_id']) {
            $u = User::find($assignments['me_mtqa_user_id']);
            if ($u) $assignedLabels[] = "ME/MTQA: {$u->name}";
        }

        $concretePouring->addLog(ConcretePouringLog::EVENT_ASSIGNED, [
            'description' => 'Reviewers assigned by admin. ' . implode(', ', $assignedLabels) . '. First step: ' . $firstStep . '.',
            'review_step' => $firstStep,
        ]);

        ConcretePouringNotificationService::assigned($concretePouring);

        return redirect()
            ->route('admin.concrete-pouring.show', $concretePouring)
            ->with('success', 'Reviewers assigned successfully! The first reviewer has been notified.');
    }

    // =========================================================================
    // BULK ACTIONS
    // =========================================================================

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'selected'         => 'required|array|min:1',
            'selected.*'       => 'exists:concrete_pourings,id',
            'approval_remarks' => 'nullable|string|max:1000',
        ]);

        $count = 0;
        foreach ($validated['selected'] as $id) {
            $cp = ConcretePouring::find($id);
            if ($cp && in_array($cp->status, ['requested']) && is_null($cp->current_review_step)) {
                $oldStatus = $cp->status;
                $cp->approve(Auth::user(), $validated['approval_remarks'] ?? null);
                $cp->addLog(ConcretePouringLog::EVENT_APPROVED, [
                    'description' => 'Bulk approved by admin.',
                    'status_from' => $oldStatus,
                    'status_to'   => 'approved',
                    'note'        => $validated['approval_remarks'] ?? null,
                ]);
                ConcretePouringNotificationService::approved($cp);
                $count++;
            }
        }

        return back()->with('success', "{$count} request(s) approved successfully!");
    }

    public function bulkDisapprove(Request $request)
    {
        $validated = $request->validate([
            'selected'         => 'required|array|min:1',
            'selected.*'       => 'exists:concrete_pourings,id',
            'approval_remarks' => 'required|string|max:1000',
        ]);

        $count = 0;
        foreach ($validated['selected'] as $id) {
            $cp = ConcretePouring::find($id);
            if ($cp && in_array($cp->status, ['requested']) && is_null($cp->current_review_step)) {
                $oldStatus = $cp->status;
                $cp->disapprove(Auth::user(), $validated['approval_remarks']);
                $cp->addLog(ConcretePouringLog::EVENT_DISAPPROVED, [
                    'description' => 'Bulk disapproved by admin.',
                    'status_from' => $oldStatus,
                    'status_to'   => 'disapproved',
                    'note'        => $validated['approval_remarks'],
                ]);
                ConcretePouringNotificationService::disapproved($cp);
                $count++;
            }
        }

        return back()->with('success', "{$count} request(s) disapproved.");
    }

    // =========================================================================
    // REPORTS / CALENDAR / PRINT / DOWNLOAD  (unchanged — no logging needed)
    // =========================================================================

    public function reports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date',   now()->endOfMonth()->format('Y-m-d'));

        $query = ConcretePouring::with(['requestedBy', 'approver', 'disapprover'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('contractor')) $query->where('contractor', $request->contractor);

        $concretePourings = $query->get();

        $summary = [
            'total_requests'           => $concretePourings->count(),
            'approved'                 => $concretePourings->where('status', 'approved')->count(),
            'disapproved'              => $concretePourings->where('status', 'disapproved')->count(),
            'pending'                  => $concretePourings->where('status', 'requested')->count(),
            'total_volume'             => round($concretePourings->sum('estimated_volume'), 2),
            'avg_volume'               => round($concretePourings->avg('estimated_volume'), 2),
            'avg_checklist_completion' => round($concretePourings->avg(fn ($p) => $p->checklist_progress), 2),
        ];

        $contractorBreakdown = $concretePourings->groupBy('contractor')->map(fn ($g) => [
            'total'        => $g->count(),
            'approved'     => $g->where('status', 'approved')->count(),
            'disapproved'  => $g->where('status', 'disapproved')->count(),
            'pending'      => $g->where('status', 'requested')->count(),
            'total_volume' => round($g->sum('estimated_volume'), 2),
        ]);

        $contractors = ConcretePouring::select('contractor')->distinct()->orderBy('contractor')->pluck('contractor');

        return view('admin.concrete-pouring.reports', compact(
            'concretePourings', 'summary', 'contractors',
            'startDate', 'endDate', 'contractorBreakdown'
        ));
    }

    public function printReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date',   now()->endOfMonth()->format('Y-m-d'));

        $concretePourings = ConcretePouring::with(['requestedBy', 'approver', 'disapprover'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($request->filled('status'),     fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('contractor'), fn ($q) => $q->where('contractor', $request->contractor))
            ->get();

        $summary = [
            'total_requests' => $concretePourings->count(),
            'approved'       => $concretePourings->where('status', 'approved')->count(),
            'disapproved'    => $concretePourings->where('status', 'disapproved')->count(),
            'pending'        => $concretePourings->where('status', 'requested')->count(),
            'total_volume'   => round($concretePourings->sum('estimated_volume'), 2),
        ];

        return view('admin.concrete-pouring.print-report', compact(
            'concretePourings', 'summary', 'startDate', 'endDate'
        ));
    }

    public function calendar(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year',  now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $month, 1)->endOfMonth();

        $pourings = ConcretePouring::whereBetween('pouring_datetime', [$startDate, $endDate])
            ->with('requestedBy')
            ->get()
            ->groupBy(fn ($p) => $p->pouring_datetime->format('Y-m-d'));

        $calendarData = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $key = $current->format('Y-m-d');
            $calendarData[$key] = $pourings->get($key, collect());
            $current->addDay();
        }

        return view('admin.concrete-pouring.calendar', compact('calendarData', 'month', 'year'));
    }

    public function print(ConcretePouring $concretePouring)
    {
        $concretePouring->load([
            'workRequest', 'requestedBy', 'meMtqaChecker',
            'residentEngineer', 'notedByEngineer', 'approver', 'disapprover',
        ]);

        $pdf      = new ConcretePouringPdf($concretePouring);
        $filename = 'concrete-pouring-' . ($concretePouring->reference_number ?? $concretePouring->id) . '.pdf';

        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function download(ConcretePouring $concretePouring)
    {
        $concretePouring->load([
            'workRequest', 'requestedBy', 'meMtqaChecker',
            'residentEngineer', 'notedByEngineer', 'approver', 'disapprover',
        ]);

        $pdf      = new ConcretePouringPdf($concretePouring);
        $filename = 'concrete-pouring-' . ($concretePouring->reference_number ?? $concretePouring->id) . '.pdf';

        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // =========================================================================
    // DELETE
    // =========================================================================

    public function destroy(ConcretePouring $concretePouring)
    {
        $concretePouring->addLog(ConcretePouringLog::EVENT_DELETED, [
            'description' => 'Concrete pouring request deleted by admin.',
            'status_from' => $concretePouring->status,
        ]);

        $concretePouring->delete();

        return redirect()
            ->route('admin.concrete-pouring.index')
            ->with('success', 'Concrete pouring request deleted successfully!');
    }
}
