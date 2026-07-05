<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WorkRequest;
use App\Models\WorkRequestLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWorkRequestController extends Controller
{
    /**
     * Display a listing of user's own work requests.
     */
    public function index(Request $request)
    {
        $query = WorkRequest::query()
            ->where('contractor_name', Auth::user()->name);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name_of_project', 'LIKE', "%{$request->search}%")
                  ->orWhere('project_location', 'LIKE', "%{$request->search}%")
                  ->orWhere('contract_number', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('requested_work_start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('requested_work_start_date', '<=', $request->date_to);
        }

        $workRequests = $query->latest()->paginate(15)->withQueryString();

        $contractorName = Auth::user()->name;

        $stats = [
            'total'          => WorkRequest::where('contractor_name', $contractorName)->count(),
            'draft'          => WorkRequest::where('contractor_name', $contractorName)->where('status', 'draft')->count(),
            'submitted'      => WorkRequest::where('contractor_name', $contractorName)->where('status', 'submitted')->count(),
            'needs_revision' => WorkRequest::where('contractor_name', $contractorName)->where('status', WorkRequest::STATUS_NEEDS_REVISION)->count(),
            'approved'       => WorkRequest::where('contractor_name', $contractorName)->where('status', 'approved')->count(),
            'rejected'       => WorkRequest::where('contractor_name', $contractorName)->where('status', 'rejected')->count(),
        ];

        return view('user.work-requests.index', compact('workRequests', 'stats'));
    }

    /**
     * Show the creation form.
     * Passes only Resident Engineers so the contractor can choose one.
     */
    public function create()
    {
        $contractNumbers = WorkRequest::whereNotNull('contract_number')
            ->where('contract_number', '!=', '')
            ->distinct()
            ->pluck('contract_number')
            ->merge(
                \App\Models\ConcretePouring::whereNotNull('contract_number')
                    ->where('contract_number', '!=', '')
                    ->distinct()
                    ->pluck('contract_number')
            )
            ->unique()
            ->sort()
            ->values();

        $residentEngineers = User::where('role', 'resident_engineer')
            ->orderBy('name')
            ->get();

        return view('user.work-requests.create', compact(
            'contractNumbers',
            'residentEngineers'
        ));
    }

    /**
     * Store a new work request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_number'               => 'nullable|string|max:100',
            'name_of_project'               => 'required|string|max:255',
            'project_location'              => 'required|string|max:255',
            'for_office'                    => 'nullable|string|max:255',
            'from_requester'                => 'nullable|string|max:255',
            'requested_work_start_date'     => 'required|date',
            'requested_work_start_time'     => 'nullable|string|max:20',
            'assigned_resident_engineer_id' => 'nullable|exists:users,id',
            'item_no'                       => 'nullable|string|max:100',
            'description'                   => 'nullable|string|max:255',
            'quantity'                      => 'nullable|numeric|min:0',
            'estimated_quantity'            => 'nullable|numeric|min:0',
            'unit'                          => 'nullable|string|max:50',
            'equipment_to_be_used'          => 'nullable|string|max:255',
            'description_of_work_requested' => 'required|string',
            'contractor_name'               => 'nullable|string|max:255',
            'contractor_signature'          => 'nullable|string',
        ]);

        $engineersExist = User::where('role', 'resident_engineer')->exists();

        if ($engineersExist && empty($validated['assigned_resident_engineer_id'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'assigned_resident_engineer_id' => 'Please select a Resident Engineer.',
                ]);
        }

        $validated['contractor_name'] = Auth::user()->name;
        $validated['for_office']      = 'PROVINCIAL ENGINEERS OFFICE';
        $validated['assigned_resident_engineer_id'] = $validated['assigned_resident_engineer_id'] ?: null;
        $validated['status']              = WorkRequest::STATUS_SUBMITTED;
        $validated['current_review_step'] = null;

        $workRequest = WorkRequest::create($validated);

        $logDescription = !empty($validated['assigned_resident_engineer_id'])
            ? 'Work request submitted by contractor with preferred Resident Engineer pre-selected. Awaiting admin to complete full reviewer assignment.'
            : 'Work request submitted by contractor. Awaiting admin assignment.';

        $workRequest->addLog(WorkRequestLog::EVENT_SUBMITTED, [
            'description' => $logDescription,
            'user_id'     => Auth::id(),
        ]);

        \App\Services\NotificationService::workRequestSubmitted($workRequest);

        if (!empty($validated['assigned_resident_engineer_id'])) {
            \App\Services\NotificationService::workRequestAssigned($workRequest);
        }

        return redirect()
            ->route('user.work-requests.show', $workRequest)
            ->with('success', 'Work request submitted successfully!');
    }

    /**
     * Display a specific work request.
     */
    public function show(WorkRequest $workRequest)
    {
        if ($workRequest->contractor_name !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        return view('user.work-requests.show', compact('workRequest'));
    }

    /**
     * Show the edit form — only if status still allows editing.
     */
    public function edit(WorkRequest $workRequest)
    {
        if ($workRequest->contractor_name !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        if (!$workRequest->canEdit()) {
            return redirect()
                ->route('user.work-requests.show', $workRequest)
                ->with('error', 'This work request can no longer be edited.');
        }

        return view('user.work-requests.edit', compact('workRequest'));
    }

    /**
     * Update a work request.
     *
     * If the request was in `needs_revision` status (a reviewer left an
     * unsigned recommendation asking for changes), resubmitting sends it
     * back to `in_review` and returns it to the SAME reviewer/step that
     * requested the revision — no admin reassignment needed.
     */
    public function update(Request $request, WorkRequest $workRequest)
    {
        if ($workRequest->contractor_name !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        if (!$workRequest->canEdit()) {
            return redirect()
                ->route('user.work-requests.show', $workRequest)
                ->with('error', 'This work request can no longer be edited.');
        }

        $validated = $request->validate([
            'contract_number'               => 'nullable|string|max:100',
            'name_of_project'               => 'required|string|max:255',
            'project_location'              => 'required|string|max:255',
            'for_office'                    => 'nullable|string|max:255',
            'from_requester'                => 'nullable|string|max:255',
            'requested_work_start_date'     => 'required|date',
            'requested_work_start_time'     => 'nullable|string|max:20',
            'item_no'                       => 'nullable|string|max:100',
            'description'                   => 'nullable|string|max:255',
            'quantity'                      => 'nullable|numeric|min:0',
            'estimated_quantity'            => 'nullable|numeric|min:0',
            'unit'                          => 'nullable|string|max:50',
            'equipment_to_be_used'          => 'nullable|string|max:255',
            'description_of_work_requested' => 'required|string',
        ]);

        $validated['contractor_name'] = Auth::user()->name;
        $validated['for_office']      = 'PROVINCIAL ENGINEERS OFFICE';

        $wasNeedsRevision = $workRequest->status === WorkRequest::STATUS_NEEDS_REVISION;
        $revisionStep     = $workRequest->current_review_step; // preserved, same reviewer resumes

        $changes = $workRequest->buildChanges($validated);

        if ($wasNeedsRevision) {
            // Resubmitting after a revision request — send it back to the
            // SAME step/reviewer, no admin reassignment, no restart of pipeline.
            $validated['status'] = WorkRequest::STATUS_IN_REVIEW;
            // current_review_step is untouched — Eloquent won't reset it since
            // it's not part of $validated, so it stays exactly where it was.
        }

        $workRequest->update($validated);

        $logDescription = $wasNeedsRevision
            ? 'Work request revised and resubmitted by contractor. Returned to ' . ($workRequest->current_step_label ?? 'reviewer') . '.'
            : 'Work request updated by contractor';

        $workRequest->addLog(WorkRequestLog::EVENT_UPDATED, [
            'description' => $logDescription,
            'changes'     => $changes,
            'user_id'     => Auth::id(),
        ]);

        if ($wasNeedsRevision && $revisionStep) {
            $this->notifyReviewerOfResubmission($workRequest, $revisionStep);
        }

        $successMessage = $wasNeedsRevision
            ? 'Work request revised and resubmitted for review!'
            : 'Work request updated successfully!';

        return redirect()
            ->route('user.work-requests.show', $workRequest)
            ->with('success', $successMessage);
    }

    /**
     * Delete a work request — only if status allows.
     */
    public function destroy(WorkRequest $workRequest)
    {
        if ($workRequest->contractor_name !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        if (!$workRequest->canEdit()) {
            return redirect()
                ->route('user.work-requests.show', $workRequest)
                ->with('error', 'This work request can no longer be deleted.');
        }

        $workRequest->addLog(WorkRequestLog::EVENT_DELETED, [
            'description' => 'Work request deleted by contractor',
            'user_id'     => Auth::id(),
        ]);

        $workRequest->delete();

        return redirect()
            ->route('user.work-requests.index')
            ->with('success', 'Work request deleted successfully!');
    }

    /**
     * Print work request — read-only view.
     */
    public function print(WorkRequest $workRequest)
    {
        if ($workRequest->contractor_name !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        return view('user.work-requests.print', compact('workRequest'));
    }

    

    /**
     * Get employee details for autofill.
     */
    public function getEmployeeDetails()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found'], 404);
        }

        return response()->json([
            'id'          => $employee->id,
            'name'        => Auth::user()->name,
            'employee_id' => $employee->employee_number,
            'position'    => $employee->position,
            'department'  => $employee->department,
            'office'      => $employee->office,
            'email'       => Auth::user()->email,
            'phone'       => $employee->phone,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Notify the reviewer (same one who requested the revision) that the
     * contractor has resubmitted the work request for their review again.
     */
    private function notifyReviewerOfResubmission(WorkRequest $workRequest, string $step): void
    {
        \App\Services\NotificationService::workRequestResubmitted($workRequest, $step);
    }
}