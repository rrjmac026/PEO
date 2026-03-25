<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConcretePouring;
use App\Models\Notification;
use App\Models\User;
use App\Services\ConcretePouringPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminConcretePouringController extends Controller
{
    const REVIEW_STEPS = [
        'mtqa'                => 'me_mtqa_user_id',
        'resident_engineer'   => 'resident_engineer_user_id',
        'provincial_engineer' => 'noted_by_user_id',
        'admin_final'         => null,
    ];

    // =========================================================================
    // INDEX
    // =========================================================================

    public function index(Request $request)
    {
        $query = ConcretePouring::with([
            'workRequest',
            'requestedBy',
            'meMtqaChecker',
            'residentEngineer',
            'notedByEngineer',
            'approver',
            'disapprover',
        ]);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('review_step')) {
            $query->where('current_review_step', $request->review_step);
        }

        if ($request->filled('contractor')) {
            $query->where('contractor', $request->contractor);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('pouring_datetime', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('pouring_datetime', '<=', $request->date_to);
        }

        $sortBy    = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $concretePourings = $query->paginate(20)->withQueryString();

        $contractors = ConcretePouring::select('contractor')->distinct()->orderBy('contractor')->pluck('contractor');

        $pendingAssignment = ConcretePouring::whereNull('current_review_step')
            ->where('status', 'requested')->count();
        $inReview          = ConcretePouring::whereNotNull('current_review_step')
            ->whereNotIn('current_review_step', ['admin_final'])->count();
        $awaitingDecision  = ConcretePouring::where('current_review_step', 'admin_final')->count();

        return view('admin.concrete-pouring.index', compact(
            'concretePourings',
            'contractors',
            'pendingAssignment',
            'inReview',
            'awaitingDecision'
        ));
    }

    // =========================================================================
    // SHOW
    // =========================================================================

    public function show(ConcretePouring $concretePouring)
    {
        $concretePouring->load([
            'workRequest',
            'requestedBy',
            'meMtqaChecker',
            'residentEngineer',
            'notedByEngineer',
            'approver',
            'disapprover',
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
            'concretePouring',
            'mtqas',
            'residentEngineers',
            'provincialEngineers'
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
            'mtqa'                => 'me_mtqa_user_id',
            'resident_engineer'   => 'resident_engineer_user_id',
            'provincial_engineer' => 'noted_by_user_id',
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

        $reviewerMeta = [
            'mtqa'                => ['col' => 'me_mtqa_user_id',           'label' => 'ME/MTQA'],
            'resident_engineer'   => ['col' => 'resident_engineer_user_id', 'label' => 'Resident Engineer'],
            'provincial_engineer' => ['col' => 'noted_by_user_id',          'label' => 'Provincial Engineer'],
        ];

        foreach ($reviewerMeta as $step => $meta) {
            $userId = $concretePouring->{$meta['col']};
            if ($userId) {
                Notification::send(
                    $userId,
                    'concrete_pouring',
                    'Concrete Pouring Review Assigned',
                    "You have been assigned as {$meta['label']} reviewer for concrete pouring request {$concretePouring->reference_number} ({$concretePouring->project_name}).",
                    route('reviewer.concrete-pouring.show', $concretePouring->id),
                    $concretePouring
                );
            }
        }

        if ($firstStep && isset($reviewerMeta[$firstStep])) {
            $firstUserId = $concretePouring->{$reviewerMeta[$firstStep]['col']};
            $firstLabel  = $reviewerMeta[$firstStep]['label'];
            if ($firstUserId) {
                Notification::send(
                    $firstUserId,
                    'concrete_pouring',
                    'Action Required — Concrete Pouring Review',
                    "The review pipeline for {$concretePouring->reference_number} ({$concretePouring->project_name}) has started. It is now your turn as {$firstLabel} to review this request.",
                    route('reviewer.concrete-pouring.show', $concretePouring->id),
                    $concretePouring
                );
            }
        }

        Notification::send(
            $concretePouring->requested_by_user_id,
            'concrete_pouring',
            'Concrete Pouring Request Now Under Review 🔍',
            "Your concrete pouring request {$concretePouring->reference_number} ({$concretePouring->project_name}) has been picked up by admin and reviewers have been assigned. The review process has begun.",
            route('user.concrete-pouring.show', $concretePouring->id),
            $concretePouring
        );

        return redirect()
            ->route('admin.concrete-pouring.show', $concretePouring)
            ->with('success', 'Reviewers assigned successfully! The first reviewer has been notified.');
    }

    // =========================================================================
    // FINAL DECISION
    // =========================================================================

    public function decisionForm(ConcretePouring $concretePouring)
    {
        if ($concretePouring->current_review_step !== 'admin_final') {
            return redirect()
                ->route('admin.concrete-pouring.show', $concretePouring)
                ->with('error', 'This request is not yet ready for a final decision.');
        }

        return view('admin.concrete-pouring.decision', compact('concretePouring'));
    }

    public function storeDecision(Request $request, ConcretePouring $concretePouring)
    {
        $request->validate([
            'decision'         => 'required|in:approved,disapproved',
            'approval_remarks' => 'nullable|string|max:2000',
        ]);

        if ($concretePouring->current_review_step !== 'admin_final') {
            return back()->with('error', 'This request is not ready for a final decision yet.');
        }

        if ($request->decision === 'approved') {
            $concretePouring->approve(Auth::user(), $request->approval_remarks);
        } else {
            $concretePouring->disapprove(Auth::user(), $request->approval_remarks);
        }

        $concretePouring->update(['current_review_step' => null]);

        $decisionLabel = $request->decision === 'approved' ? 'Approved ✅' : 'Disapproved ❌';
        $remarksNote   = $request->approval_remarks ? " Remarks: {$request->approval_remarks}" : '';

        Notification::send(
            $concretePouring->requested_by_user_id,
            'concrete_pouring',
            "Concrete Pouring Request {$decisionLabel}",
            "Your concrete pouring request {$concretePouring->reference_number} ({$concretePouring->project_name}) has been {$request->decision}.{$remarksNote}",
            route('user.concrete-pouring.show', $concretePouring->id),
            $concretePouring
        );

        $reviewerCols = ['me_mtqa_user_id', 'resident_engineer_user_id', 'noted_by_user_id'];
        $reviewerIds  = collect($reviewerCols)
            ->map(fn ($col) => $concretePouring->$col)
            ->filter()->unique()->values()->toArray();

        if (!empty($reviewerIds)) {
            Notification::send(
                $reviewerIds,
                'concrete_pouring',
                "Concrete Pouring Request {$decisionLabel}",
                "Concrete pouring request {$concretePouring->reference_number} ({$concretePouring->project_name}) that you reviewed has been {$request->decision} by admin.{$remarksNote}",
                route('reviewer.concrete-pouring.show', $concretePouring->id),
                $concretePouring
            );
        }

        return redirect()
            ->route('admin.concrete-pouring.show', $concretePouring)
            ->with('success', 'Decision recorded. Concrete pouring request has been ' . $request->decision . '.');
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
            if ($cp && $cp->current_review_step === 'admin_final') {
                $cp->approve(Auth::user(), $validated['approval_remarks'] ?? null);
                $cp->update(['current_review_step' => null]);

                Notification::send(
                    $cp->requested_by_user_id,
                    'concrete_pouring',
                    'Concrete Pouring Request Approved ✅',
                    "Your concrete pouring request {$cp->reference_number} ({$cp->project_name}) has been approved." .
                        (!empty($validated['approval_remarks']) ? " Remarks: {$validated['approval_remarks']}" : ''),
                    route('user.concrete-pouring.show', $cp->id),
                    $cp
                );

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
            if ($cp && $cp->current_review_step === 'admin_final') {
                $cp->disapprove(Auth::user(), $validated['approval_remarks']);
                $cp->update(['current_review_step' => null]);

                Notification::send(
                    $cp->requested_by_user_id,
                    'concrete_pouring',
                    'Concrete Pouring Request Disapproved ❌',
                    "Your concrete pouring request {$cp->reference_number} ({$cp->project_name}) has been disapproved. Remarks: {$validated['approval_remarks']}",
                    route('user.concrete-pouring.show', $cp->id),
                    $cp
                );

                $count++;
            }
        }

        return back()->with('success', "{$count} request(s) disapproved.");
    }

    // =========================================================================
    // REPORTS
    // =========================================================================

    public function reports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date',   now()->endOfMonth()->format('Y-m-d'));

        $query = ConcretePouring::with(['requestedBy', 'approver', 'disapprover'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('contractor')) {
            $query->where('contractor', $request->contractor);
        }

        $concretePourings = $query->get();

        $summary = [
            'total_requests'           => $concretePourings->count(),
            'approved'                 => $concretePourings->where('status', 'approved')->count(),
            'disapproved'              => $concretePourings->where('status', 'disapproved')->count(),
            'pending'                  => $concretePourings->where('status', 'requested')->count(),
            'total_volume'             => round($concretePourings->sum('estimated_volume'), 2),
            'avg_volume'               => round($concretePourings->avg('estimated_volume'), 2),
            'avg_checklist_completion' => round(
                $concretePourings->avg(fn ($p) => $p->checklist_progress), 2
            ),
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

    // =========================================================================
    // CALENDAR
    // =========================================================================

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

    // =========================================================================
    // PRINT / DOWNLOAD  — uses ConcretePouringPdf (with embedded signatures)
    // =========================================================================

    /**
     * Stream the official Concrete Pouring Form PDF inline in the browser.
     * Reviewer signatures are automatically embedded via ConcretePouringPdf.
     */
    public function print(ConcretePouring $concretePouring)
    {
        $concretePouring->load([
            'workRequest',
            'requestedBy',
            'meMtqaChecker',
            'residentEngineer',
            'notedByEngineer',
            'approver',
            'disapprover',
        ]);

        $pdf      = new ConcretePouringPdf($concretePouring);
        $filename = 'concrete-pouring-' . ($concretePouring->reference_number ?? $concretePouring->id) . '.pdf';

        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Force-download the Concrete Pouring Form PDF.
     */
    public function download(ConcretePouring $concretePouring)
    {
        $concretePouring->load([
            'workRequest',
            'requestedBy',
            'meMtqaChecker',
            'residentEngineer',
            'notedByEngineer',
            'approver',
            'disapprover',
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
        $concretePouring->delete();

        return redirect()
            ->route('admin.concrete-pouring.index')
            ->with('success', 'Concrete pouring request deleted successfully!');
    }
}