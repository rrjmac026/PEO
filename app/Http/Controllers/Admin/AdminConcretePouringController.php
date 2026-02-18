<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConcretePouring;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminConcretePouringController extends Controller
{
    /**
     * Display a listing of all concrete pouring requests
     */
    public function index(Request $request)
    {
        $query = ConcretePouring::with([
            'requestedBy.user',
            'meMtqaChecker.user',
            'residentEngineer.user',
            'approver.user',
            'disapprover.user'
        ]);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by contractor
        if ($request->filled('contractor')) {
            $query->where('contractor', $request->contractor);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('pouring_datetime', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('pouring_datetime', '<=', $request->date_to);
        }

        // Filter by project
        if ($request->filled('project')) {
            $query->where('project_name', 'LIKE', "%{$request->project}%");
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $concretePourings = $query->paginate(20);
        
        // Get unique contractors for filter dropdown
        $contractors = ConcretePouring::select('contractor')
            ->distinct()
            ->orderBy('contractor')
            ->pluck('contractor');

        return view('admin.concrete-pouring.index', compact('concretePourings', 'contractors'));
    }

    /**
     * Display the specified concrete pouring request
     */
    public function show(ConcretePouring $concretePouring)
    {
        $concretePouring->load([
            'requestedBy.user',
            'meMtqaChecker.user',
            'residentEngineer.user',
            'approver.user',
            'disapprover.user',
            'notedByEngineer.user'
        ]);

        return view('admin.concrete-pouring.show', compact('concretePouring'));
    }

    /**
     * Show the form for ME/MTQA review
     */
    public function meMtqaReviewForm(ConcretePouring $concretePouring)
    {
        return view('admin.concrete-pouring.me-mtqa-review', compact('concretePouring'));
    }

    /**
     * Store ME/MTQA review
     */
    public function storeMeMtqaReview(Request $request, ConcretePouring $concretePouring)
    {
        $validated = $request->validate([
            'me_mtqa_remarks' => 'nullable|string|max:1000',
        ]);

        $employee = Auth::user()->employee;

        if (!$employee) {
            return back()->with('error', 'You must be registered as an employee to perform this action.');
        }

        $concretePouring->update([
            'me_mtqa_checked_by' => $employee->id,
            'me_mtqa_date' => now(),
            'me_mtqa_remarks' => $validated['me_mtqa_remarks'],
        ]);

        return redirect()
            ->route('admin.concrete-pouring.show', $concretePouring)
            ->with('success', 'ME/MTQA review submitted successfully!');
    }

    /**
     * Show the form for Resident Engineer review
     */
    public function residentEngineerReviewForm(ConcretePouring $concretePouring)
    {
        return view('admin.concrete-pouring.re-review', compact('concretePouring'));
    }

    /**
     * Store Resident Engineer review
     */
    public function storeResidentEngineerReview(Request $request, ConcretePouring $concretePouring)
    {
        $validated = $request->validate([
            're_remarks' => 'nullable|string|max:1000',
        ]);

        $employee = Auth::user()->employee;

        if (!$employee) {
            return back()->with('error', 'You must be registered as an employee to perform this action.');
        }

        $concretePouring->update([
            're_checked_by' => $employee->id,
            're_date' => now(),
            're_remarks' => $validated['re_remarks'],
        ]);

        return redirect()
            ->route('admin.concrete-pouring.show', $concretePouring)
            ->with('success', 'Resident Engineer review submitted successfully!');
    }

    /**
     * Show approval/disapproval form
     */
    public function approvalForm(ConcretePouring $concretePouring)
    {
        // Check if already approved or disapproved
        if ($concretePouring->status !== 'requested') {
            return redirect()
                ->route('admin.concrete-pouring.show', $concretePouring)
                ->with('info', 'This request has already been ' . $concretePouring->status . '.');
        }

        return view('admin.concrete-pouring.approval', compact('concretePouring'));
    }

    /**
     * Approve concrete pouring request
     */
    public function approve(Request $request, ConcretePouring $concretePouring)
    {
        $validated = $request->validate([
            'approval_remarks' => 'nullable|string|max:1000',
        ]);

        $employee = Auth::user()->employee;

        if (!$employee) {
            return back()->with('error', 'You must be registered as an employee to perform this action.');
        }

        // Check if already processed
        if ($concretePouring->status !== 'requested') {
            return back()->with('error', 'This request has already been ' . $concretePouring->status . '.');
        }

        $concretePouring->approve($employee, $validated['approval_remarks'] ?? null);

        return redirect()
            ->route('admin.concrete-pouring.show', $concretePouring)
            ->with('success', 'Concrete pouring request approved successfully!');
    }

    /**
     * Disapprove concrete pouring request
     */
    public function disapprove(Request $request, ConcretePouring $concretePouring)
    {
        $validated = $request->validate([
            'approval_remarks' => 'required|string|max:1000',
        ]);

        $employee = Auth::user()->employee;

        if (!$employee) {
            return back()->with('error', 'You must be registered as an employee to perform this action.');
        }

        // Check if already processed
        if ($concretePouring->status !== 'requested') {
            return back()->with('error', 'This request has already been ' . $concretePouring->status . '.');
        }

        $concretePouring->disapprove($employee, $validated['approval_remarks']);

        return redirect()
            ->route('admin.concrete-pouring.show', $concretePouring)
            ->with('success', 'Concrete pouring request disapproved. The contractor has been notified.');
    }

    /**
     * Add Provincial Engineer note
     */
    public function addNote(Request $request, ConcretePouring $concretePouring)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return back()->with('error', 'You must be registered as an employee to perform this action.');
        }

        $concretePouring->update([
            'noted_by' => $employee->id,
            'noted_date' => now(),
        ]);

        return redirect()
            ->route('admin.concrete-pouring.show', $concretePouring)
            ->with('success', 'Provincial Engineer note added successfully!');
    }

    /**
     * Generate reports
     */
    public function reports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query = ConcretePouring::with([
            'requestedBy.user',
            'approver.user',
            'disapprover.user'
        ])
        ->whereBetween('created_at', [$startDate, $endDate]);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by contractor if provided
        if ($request->filled('contractor')) {
            $query->where('contractor', $request->contractor);
        }

        $concretePourings = $query->get();

        // Summary statistics
        $summary = [
            'total_requests' => $concretePourings->count(),
            'approved' => $concretePourings->where('status', 'approved')->count(),
            'disapproved' => $concretePourings->where('status', 'disapproved')->count(),
            'pending' => $concretePourings->where('status', 'requested')->count(),
            'total_volume' => round($concretePourings->sum('estimated_volume'), 2),
            'avg_volume' => round($concretePourings->avg('estimated_volume'), 2),
            'avg_checklist_completion' => round($concretePourings->avg(function ($pouring) {
                return $pouring->checklist_progress;
            }), 2),
        ];

        // Get contractor breakdown
        $contractorBreakdown = $concretePourings->groupBy('contractor')->map(function ($group) {
            return [
                'total' => $group->count(),
                'approved' => $group->where('status', 'approved')->count(),
                'disapproved' => $group->where('status', 'disapproved')->count(),
                'pending' => $group->where('status', 'requested')->count(),
                'total_volume' => round($group->sum('estimated_volume'), 2),
            ];
        });

        $contractors = ConcretePouring::select('contractor')
            ->distinct()
            ->orderBy('contractor')
            ->pluck('contractor');

        return view('admin.concrete-pouring.reports', compact(
            'concretePourings',
            'summary',
            'contractors',
            'startDate',
            'endDate',
            'contractorBreakdown'
        ));
    }

    /**
     * Print report
     */
    public function printReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query = ConcretePouring::with([
            'requestedBy.user',
            'approver.user',
            'disapprover.user'
        ])
        ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('contractor')) {
            $query->where('contractor', $request->contractor);
        }

        $concretePourings = $query->get();

        $summary = [
            'total_requests' => $concretePourings->count(),
            'approved' => $concretePourings->where('status', 'approved')->count(),
            'disapproved' => $concretePourings->where('status', 'disapproved')->count(),
            'pending' => $concretePourings->where('status', 'requested')->count(),
            'total_volume' => round($concretePourings->sum('estimated_volume'), 2),
        ];

        return view('admin.concrete-pouring.print-report', compact(
            'concretePourings',
            'summary',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Calendar view of scheduled pourings
     */
    public function calendar(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $pourings = ConcretePouring::whereBetween('pouring_datetime', [$startDate, $endDate])
            ->with('requestedBy.user')
            ->get()
            ->groupBy(function ($pouring) {
                return $pouring->pouring_datetime->format('Y-m-d');
            });

        // Get calendar data
        $calendarData = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $calendarData[$dateKey] = $pourings->get($dateKey, collect());
            $currentDate->addDay();
        }

        return view('admin.concrete-pouring.calendar', compact('calendarData', 'month', 'year'));
    }

    /**
     * Bulk approve selected requests
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'selected' => 'required|array|min:1',
            'selected.*' => 'exists:concrete_pourings,id',
            'approval_remarks' => 'nullable|string|max:1000',
        ]);

        $employee = Auth::user()->employee;

        if (!$employee) {
            return back()->with('error', 'You must be registered as an employee to perform this action.');
        }

        $count = 0;
        foreach ($validated['selected'] as $id) {
            $concretePouring = ConcretePouring::find($id);
            if ($concretePouring && $concretePouring->status === 'requested') {
                $concretePouring->approve($employee, $validated['approval_remarks'] ?? null);
                $count++;
            }
        }

        return back()->with('success', "{$count} concrete pouring request(s) approved successfully!");
    }

    /**
     * Bulk disapprove selected requests
     */
    public function bulkDisapprove(Request $request)
    {
        $validated = $request->validate([
            'selected' => 'required|array|min:1',
            'selected.*' => 'exists:concrete_pourings,id',
            'approval_remarks' => 'required|string|max:1000',
        ]);

        $employee = Auth::user()->employee;

        if (!$employee) {
            return back()->with('error', 'You must be registered as an employee to perform this action.');
        }

        $count = 0;
        foreach ($validated['selected'] as $id) {
            $concretePouring = ConcretePouring::find($id);
            if ($concretePouring && $concretePouring->status === 'requested') {
                $concretePouring->disapprove($employee, $validated['approval_remarks']);
                $count++;
            }
        }

        return back()->with('success', "{$count} concrete pouring request(s) disapproved.");
    }

    /**
     * Delete a concrete pouring request (admin only)
     */
    public function destroy(ConcretePouring $concretePouring)
    {
        $concretePouring->delete();

        return redirect()
            ->route('admin.concrete-pouring.index')
            ->with('success', 'Concrete pouring request deleted successfully!');
    }

    /**
     * Print/View single concrete pouring form
     */
    public function print(ConcretePouring $concretePouring)
    {
        $concretePouring->load([
            'requestedBy.user',
            'meMtqaChecker.user',
            'residentEngineer.user',
            'approver.user',
            'disapprover.user',
            'notedByEngineer.user'
        ]);

        return view('admin.concrete-pouring.print', compact('concretePouring'));
    }
}