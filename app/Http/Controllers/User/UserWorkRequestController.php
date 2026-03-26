<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WorkRequest;
use App\Models\WorkRequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWorkRequestController extends Controller
{
    /**
     * Display a listing of user's own work requests
     */
    public function index(Request $request)
    {
        $query = WorkRequest::query()
            ->where('contractor_name', Auth::user()->name);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name_of_project', 'LIKE', "%{$request->search}%")
                  ->orWhere('project_location', 'LIKE', "%{$request->search}%")
                  ->orWhere('reference_number', 'LIKE', "%{$request->search}%");
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
            'total'     => WorkRequest::where('contractor_name', $contractorName)->count(),
            'draft'     => WorkRequest::where('contractor_name', $contractorName)->where('status', 'draft')->count(),
            'submitted' => WorkRequest::where('contractor_name', $contractorName)->where('status', 'submitted')->count(),
            'approved'  => WorkRequest::where('contractor_name', $contractorName)->where('status', 'approved')->count(),
            'rejected'  => WorkRequest::where('contractor_name', $contractorName)->where('status', 'rejected')->count(),
        ];

        return view('user.work-requests.index', compact('workRequests', 'stats'));
    }

    /**
     * Show form to create new work request
     */
    public function create()
    {
        $referenceNumbers = WorkRequest::whereNotNull('reference_number')
            ->where('reference_number', '!=', '')
            ->distinct()
            ->pluck('reference_number')
            ->sort()
            ->values();

        return view('user.work-requests.create', compact('referenceNumbers'));
    }

    /**
     * Store new work request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Reference Number
            'reference_number'              => 'nullable|string|max:100',

            // Project Information
            'name_of_project'               => 'required|string|max:255',
            'project_location'              => 'required|string|max:255',
            'for_office'                    => 'nullable|string|max:255',
            'from_requester'                => 'nullable|string|max:255',

            // Schedule
            'requested_work_start_date'     => 'required|date',
            'requested_work_start_time'     => 'nullable|string|max:20',

            // Pay Item Details
            'item_no'                       => 'nullable|string|max:100',
            'description'                   => 'nullable|string|max:255',
            'quantity'                      => 'nullable|numeric|min:0',
            'estimated_quantity'            => 'nullable|numeric|min:0',
            'unit'                          => 'nullable|string|max:50',
            'equipment_to_be_used'          => 'nullable|string|max:255',
            'description_of_work_requested' => 'required|string',

            // Submission
            'contractor_name'               => 'nullable|string|max:255',
        ]);

        // Force contractor_name to logged-in user's name
        $validated['contractor_name'] = Auth::user()->name;

        // Force for_office
        $validated['for_office'] = 'PROVINCIAL ENGINEERS OFFICE';

        // Set initial status
        $validated['status'] = WorkRequest::STATUS_SUBMITTED;

        $workRequest = WorkRequest::create($validated);

        $workRequest->addLog(WorkRequestLog::EVENT_SUBMITTED, [
            'description' => 'Work request submitted by contractor',
            'user_id'     => Auth::id(),
        ]);
        
        \App\Services\NotificationService::workRequestSubmitted($workRequest);

        return redirect()
            ->route('user.work-requests.show', $workRequest)
            ->with('success', 'Work request submitted successfully!');
    }

    /**
     * Display specific work request
     */
    public function show(WorkRequest $workRequest)
    {
        if ($workRequest->contractor_name !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        return view('user.work-requests.show', compact('workRequest'));
    }

    /**
     * Show edit form — only allowed fields, only if status allows
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
     * Update work request — only allowed fields
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
            // Reference Number
            'reference_number'              => 'nullable|string|max:100',

            // Project Information
            'name_of_project'               => 'required|string|max:255',
            'project_location'              => 'required|string|max:255',
            'for_office'                    => 'nullable|string|max:255',
            'from_requester'                => 'nullable|string|max:255',

            // Schedule
            'requested_work_start_date'     => 'required|date',
            'requested_work_start_time'     => 'nullable|string|max:20',

            // Pay Item Details
            'item_no'                       => 'nullable|string|max:100',
            'description'                   => 'nullable|string|max:255',
            'quantity'                      => 'nullable|numeric|min:0',
            'estimated_quantity'            => 'nullable|numeric|min:0',
            'unit'                          => 'nullable|string|max:50',
            'equipment_to_be_used'          => 'nullable|string|max:255',
            'description_of_work_requested' => 'required|string',
        ]);

        // Prevent contractor_name from being changed
        $validated['contractor_name'] = Auth::user()->name;

        // Force for_office
        $validated['for_office'] = 'PROVINCIAL ENGINEERS OFFICE';

        $changes = $workRequest->buildChanges($validated);

        $workRequest->update($validated);

        $workRequest->addLog(WorkRequestLog::EVENT_UPDATED, [
            'description' => 'Work request updated by contractor',
            'changes'     => $changes,
            'user_id'     => Auth::id(),
        ]);

        return redirect()
            ->route('user.work-requests.show', $workRequest)
            ->with('success', 'Work request updated successfully!');
    }

    /**
     * Delete work request — only if status allows
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
     * Print work request — read only view
     */
    public function print(WorkRequest $workRequest)
    {
        if ($workRequest->contractor_name !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        return view('user.work-requests.print', compact('workRequest'));
    }

    /**
     * Get employee details for autofill
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
}