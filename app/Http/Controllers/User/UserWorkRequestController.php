<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WorkRequest;
use App\Models\WorkRequestLog;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class UserWorkRequestController extends Controller
{
    /**
     * Display a listing of user's work requests with search and filter
     */
    public function index(Request $request)
    {
        $query = WorkRequest::query()
            ->where('requested_by', Auth::user()->name); // Only show user's own requests

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_of_project', 'LIKE', "%{$search}%")
                  ->orWhere('project_location', 'LIKE', "%{$search}%")
                  ->orWhere('contractor_name', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $workRequests = $query->latest()->paginate(15)->withQueryString();
            
        return view('user.work-requests.index', compact('workRequests'));
    }

    /**
     * Show form to create new work request
     */
    public function create()
    {
        // Pre-fill with current user's information
        $currentUser = Auth::user();
        $employee = $currentUser->employee;

        return view('user.work-requests.create', compact('employee'));
    }

    /**
     * Store new work request
     */
    public function store(Request $request)
    {
        $validated = $request->validate(WorkRequest::validationRules());
        
        // Ensure the request is created by the current user
        $validated['requested_by'] = Auth::user()->name;
        
        $workRequest = WorkRequest::create($validated);
        
        // Log the creation
        $workRequest->addLog(WorkRequestLog::EVENT_CREATED, [
            'description' => 'Work request created by user',
            'user_id' => Auth::id(),
        ]);
        
        return redirect()
            ->route('user.work-requests.show', $workRequest)
            ->with('success', 'Work request submitted successfully!');
    }

    /**
     * Display specific work request
     */
    public function show(WorkRequest $workRequest)
    {
        // Ensure user can only view their own requests
        if ($workRequest->requested_by !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        return view('user.work-requests.show', compact('workRequest'));
    }

    /**
     * Edit work request (only if pending/draft)
     */
    public function edit(WorkRequest $workRequest)
    {
        // Ensure user can only edit their own requests
        if ($workRequest->requested_by !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        // Check if the request can be edited (e.g., only if status is 'pending' or 'draft')
        if (!$workRequest->canEdit()) {
            return redirect()
                ->route('user.work-requests.show', $workRequest)
                ->with('error', 'This work request can no longer be edited.');
        }
        
        return view('user.work-requests.edit', compact('workRequest'));
    }

    /**
     * Update work request
     */
    public function update(Request $request, WorkRequest $workRequest)
    {
        // Ensure user can only update their own requests
        if ($workRequest->requested_by !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        if (!$workRequest->canEdit()) {
            return redirect()
                ->route('user.work-requests.show', $workRequest)
                ->with('error', 'This work request can no longer be edited.');
        }
        
        $validated = $request->validate(WorkRequest::validationRules($workRequest->id));
        
        $workRequest->update($validated);
        
        // Log the update
        $workRequest->addLog(WorkRequestLog::EVENT_UPDATED, [
            'description' => 'Work request updated by user',
            'user_id' => Auth::id(),
        ]);
        
        return redirect()
            ->route('user.work-requests.show', $workRequest)
            ->with('success', 'Work request updated successfully!');
    }

    /**
     * Delete work request (only if pending/draft)
     */
    public function destroy(WorkRequest $workRequest)
    {
        // Ensure user can only delete their own requests
        if ($workRequest->requested_by !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        // Only allow deletion if status permits (e.g., draft or pending)
        if (!$workRequest->canEdit()) {
            return redirect()
                ->route('user.work-requests.show', $workRequest)
                ->with('error', 'This work request can no longer be deleted.');
        }

        $workRequest->delete();
        
        return redirect()
            ->route('user.work-requests.index')
            ->with('success', 'Work request deleted successfully!');
    }

    /**
     * Print work request as PDF
     */
    public function print(WorkRequest $workRequest)
    {
        // Ensure user can only print their own requests
        if ($workRequest->requested_by !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        $pdf = Pdf::loadView('user.work-requests.print', compact('workRequest'))
            ->setPaper('a4', 'portrait');
            
        return $pdf->stream('work-request-' . $workRequest->id . '.pdf');
    }

    /**
     * Download work request as PDF
     */
    public function download(WorkRequest $workRequest)
    {
        // Ensure user can only download their own requests
        if ($workRequest->requested_by !== Auth::user()->name) {
            abort(403, 'Unauthorized access to this work request.');
        }

        $pdf = Pdf::loadView('user.work-requests.print', compact('workRequest'))
            ->setPaper('a4', 'portrait');
            
        return $pdf->download('work-request-' . $workRequest->id . '.pdf');
    }

    /**
     * Get employee details for autofill (current user only)
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
            'employee_id' => $employee->employee_id,
            'position'    => $employee->position,
            'department'  => $employee->department,
            'office'      => $employee->office,
            'email'       => Auth::user()->email,
            'phone'       => $employee->phone,
        ]);
    }
}