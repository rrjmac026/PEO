<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkRequest;
use App\Models\WorkRequestLog;
use App\Models\Employee;
use App\Models\User;
use App\Imports\EmployeesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Services\WorkRequestPdf;

class WorkRequestController extends Controller
{
    /**
     * Display listing of work requests with search and filter.
     */
    public function index(Request $request)
    {
        $query = WorkRequest::query()
            ->with(['assignedSiteInspector', 'assignedSurveyor', 'assignedByAdmin']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_of_project', 'LIKE', "%{$search}%")
                  ->orWhere('project_location', 'LIKE', "%{$search}%")
                  ->orWhere('contractor_name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $workRequests = $query->latest()->paginate(15)->withQueryString();

        // Count for quick-stats banner
        $pendingAssignment = WorkRequest::where('status', WorkRequest::STATUS_SUBMITTED)->count();
        $inReview          = WorkRequest::whereIn('status', [WorkRequest::STATUS_ASSIGNED, WorkRequest::STATUS_IN_REVIEW])->count();
        $awaitingDecision  = WorkRequest::where('current_review_step', 'admin_final')->count();

        return view('admin.work-requests.index', compact(
            'workRequests',
            'pendingAssignment',
            'inReview',
            'awaitingDecision'
        ));
    }

    /**
     * Show form to create new work request (admin-created on behalf of contractor).
     */
    public function create()
    {
        return view('admin.work-requests.create', $this->reviewerLists());
    }

    /**
     * Store new work request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(WorkRequest::validationRules());

        $workRequest = WorkRequest::create($validated);

        $logData = ['description' => 'Work request created by admin'];
        if (Auth::user()->employee) {
            $logData['employee_id'] = Auth::user()->employee->id;
        }

        $workRequest->addLog(WorkRequestLog::EVENT_CREATED, $logData);

        return redirect()
            ->route('admin.work-requests.show', $workRequest)
            ->with('success', 'Work request created successfully!');
    }

    /**
     * Display specific work request.
     */
    public function show(WorkRequest $workRequest)
    {
        $workRequest->load([
            'assignedSiteInspector',
            'assignedSurveyor',
            'assignedResidentEngineer',
            'assignedMtqa',
            'assignedEngineerIv',
            'assignedEngineerIii',
            'assignedProvincialEngineer',
            'assignedByAdmin',
            'adminDecisionBy',
            'logs',
        ]);

        return view('admin.work-requests.show', compact('workRequest'));
    }

    /**
     * Show the assign-engineers form.
     */
    public function assignForm(WorkRequest $workRequest)
    {
        if (!in_array($workRequest->status, [WorkRequest::STATUS_SUBMITTED, WorkRequest::STATUS_ASSIGNED])) {
            return redirect()
                ->route('admin.work-requests.show', $workRequest)
                ->with('error', 'This request cannot be (re-)assigned at its current status.');
        }

        $siteInspectors      = User::where('role', 'site_inspector')->orderBy('name')->get();
        $surveyors           = User::where('role', 'surveyor')->orderBy('name')->get();
        $residentEngineers   = User::where('role', 'resident_engineer')->orderBy('name')->get();
        $mtqas               = User::where('role', 'mtqa')->orderBy('name')->get();
        $engineersIv         = User::where('role', 'engineeriv')->orderBy('name')->get();
        $engineersIii        = User::where('role', 'engineeriii')->orderBy('name')->get();
        $provincialEngineers = User::where('role', 'provincial_engineer')->orderBy('name')->get();

        return view('admin.work-requests.assign', compact(
            'workRequest',
            'siteInspectors',
            'surveyors',
            'residentEngineers',
            'mtqas',
            'engineersIv',
            'engineersIii',
            'provincialEngineers'
        ));
    }

    /**
     * Save engineer assignments and move status to 'assigned'.
     * At least one engineer must be assigned.
     */
    public function assign(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'assigned_site_inspector_id'      => 'nullable|exists:users,id',
            'assigned_surveyor_id'             => 'nullable|exists:users,id',
            'assigned_resident_engineer_id'    => 'nullable|exists:users,id',
            'assigned_mtqa_id'                 => 'nullable|exists:users,id',
            'assigned_engineer_iv_id'          => 'nullable|exists:users,id',
            'assigned_engineer_iii_id'         => 'nullable|exists:users,id',
            'assigned_provincial_engineer_id'  => 'nullable|exists:users,id',
        ]);

        // At least one assignment required
        $anyAssigned = collect([
            $request->assigned_site_inspector_id,
            $request->assigned_surveyor_id,
            $request->assigned_resident_engineer_id,
            $request->assigned_mtqa_id,
            $request->assigned_engineer_iv_id,
            $request->assigned_engineer_iii_id,
            $request->assigned_provincial_engineer_id,
        ])->filter()->isNotEmpty();

        if (!$anyAssigned) {
            return back()->with('error', 'Please assign at least one engineer before proceeding.');
        }

        // Determine the first step that has someone assigned
        $stepsInOrder = [
            'site_inspector'      => 'assigned_site_inspector_id',
            'surveyor'            => 'assigned_surveyor_id',
            'resident_engineer'   => 'assigned_resident_engineer_id',
            'mtqa'                => 'assigned_mtqa_id',
            'engineer_iv'         => 'assigned_engineer_iv_id',
            'engineer_iii'        => 'assigned_engineer_iii_id',
            'provincial_engineer' => 'assigned_provincial_engineer_id',
        ];

        $firstStep = null;
        foreach ($stepsInOrder as $step => $col) {
            if (!empty($request->$col)) {
                $firstStep = $step;
                break;
            }
        }

        $workRequest->update([
            'assigned_site_inspector_id'     => $request->assigned_site_inspector_id,
            'assigned_surveyor_id'            => $request->assigned_surveyor_id,
            'assigned_resident_engineer_id'   => $request->assigned_resident_engineer_id,
            'assigned_mtqa_id'                => $request->assigned_mtqa_id,
            'assigned_engineer_iv_id'         => $request->assigned_engineer_iv_id,
            'assigned_engineer_iii_id'        => $request->assigned_engineer_iii_id,
            'assigned_provincial_engineer_id' => $request->assigned_provincial_engineer_id,
            'assigned_by_admin_id'            => Auth::id(),
            'assigned_at'                     => now(),
            'current_review_step'             => $firstStep,
            'status'                          => WorkRequest::STATUS_ASSIGNED,
        ]);

        $workRequest->addLog(WorkRequestLog::EVENT_UPDATED, [
            'description' => 'Engineers assigned by admin. First step: ' . $firstStep,
            'user_id'     => Auth::id(),
        ]);

        return redirect()
            ->route('admin.work-requests.show', $workRequest)
            ->with('success', 'Engineers assigned successfully! The first reviewer has been notified.');
    }

    /**
     * Show the final admin decision form (approve / reject).
     * Only available when current_review_step = 'admin_final'.
     */
    public function decisionForm(WorkRequest $workRequest)
    {
        if ($workRequest->current_review_step !== 'admin_final') {
            return redirect()
                ->route('admin.work-requests.show', $workRequest)
                ->with('error', 'This request is not yet ready for a final decision.');
        }

        return view('admin.work-requests.decision', compact('workRequest'));
    }

    /**
     * Store admin final decision (approve or reject).
     */
    public function storeDecision(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'decision'         => 'required|in:approved,rejected',
            'decision_remarks' => 'nullable|string|max:2000',
        ]);

        if ($workRequest->current_review_step !== 'admin_final') {
            return back()->with('error', 'This request is not ready for a final decision yet.');
        }

        $newStatus = $request->decision === 'approved'
            ? WorkRequest::STATUS_APPROVED
            : WorkRequest::STATUS_REJECTED;

        $workRequest->update([
            'admin_decision'         => $request->decision,
            'admin_decision_remarks' => $request->decision_remarks,
            'admin_decision_by'      => Auth::id(),
            'admin_decision_at'      => now(),
            'status'                 => $newStatus,
            'current_review_step'    => null,   // done
        ]);

        $event = $request->decision === 'approved'
            ? WorkRequestLog::EVENT_APPROVED
            : WorkRequestLog::EVENT_REJECTED;

        $workRequest->addLog($event, [
            'description' => 'Admin final decision: ' . $request->decision,
            'user_id'     => Auth::id(),
            'status_from' => WorkRequest::STATUS_REVIEWED,
            'status_to'   => $newStatus,
        ]);

        return redirect()
            ->route('admin.work-requests.show', $workRequest)
            ->with('success', 'Decision recorded. Work request has been ' . $request->decision . '.');
    }

    /**
     * Edit work request.
     */
    public function edit(WorkRequest $workRequest)
    {
        if (!$workRequest->canEdit()) {
            return redirect()
                ->route('admin.work-requests.show', $workRequest)
                ->with('error', 'This work request cannot be edited at its current status.');
        }

        return view('admin.work-requests.edit', array_merge(
            ['workRequest' => $workRequest],
            $this->reviewerLists()
        ));
    }

    /**
     * Update work request.
     */
    public function update(Request $request, WorkRequest $workRequest)
    {
        if (!$workRequest->canEdit()) {
            return redirect()
                ->route('admin.work-requests.show', $workRequest)
                ->with('error', 'This work request cannot be edited.');
        }

        $validated = $request->validate(WorkRequest::validationRules($workRequest->id));
        $changes   = $workRequest->buildChanges($validated);
        $workRequest->update($validated);

        $logData = [
            'description' => 'Work request updated',
            'changes'     => $changes,
            'user_id'     => Auth::id(),
        ];

        $workRequest->addLog(WorkRequestLog::EVENT_UPDATED, $logData);

        return redirect()
            ->route('admin.work-requests.show', $workRequest)
            ->with('success', 'Work request updated successfully!');
    }

    /**
     * Delete work request.
     */
    public function destroy(WorkRequest $workRequest)
    {
        $workRequest->addLog(WorkRequestLog::EVENT_DELETED, [
            'description' => 'Work request deleted by admin',
            'user_id'     => Auth::id(),
        ]);

        $workRequest->delete();

        return redirect()
            ->route('admin.work-requests.index')
            ->with('success', 'Work request deleted successfully!');
    }

    /**
     * Print work request PDF — inline.
     */
    public function print(WorkRequest $workRequest)
    {
        $pdf = new WorkRequestPdf($workRequest);
        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="work-request-' . $workRequest->id . '.pdf"',
        ]);
    }

    /**
     * Download work request PDF.
     */
    public function download(WorkRequest $workRequest)
    {
        $pdf = new WorkRequestPdf($workRequest);
        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="work-request-' . $workRequest->id . '.pdf"',
        ]);
    }

    /**
     * Update status manually (admin override).
     */
    public function updateStatus(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', WorkRequest::getStatuses()),
        ]);

        $workRequest->update(['status' => $request->status]);

        return redirect()
            ->route('admin.work-requests.show', $workRequest)
            ->with('success', 'Status updated successfully!');
    }

    /**
     * Employee search autocomplete.
     */
    public function searchEmployee(Request $request)
    {
        $term = $request->get('term');
        $employees = Employee::with('user')
            ->search($term)
            ->limit(10)
            ->get()
            ->map(fn ($e) => [
                'id'          => $e->id,
                'name'        => $e->user->name,
                'employee_id' => $e->employee_id,
                'position'    => $e->position,
                'department'  => $e->department,
                'office'      => $e->office,
                'email'       => $e->user->email,
                'phone'       => $e->phone,
            ]);

        return response()->json($employees);
    }

    public function getEmployee($id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        return response()->json([
            'id'          => $employee->id,
            'name'        => $employee->user->name,
            'employee_id' => $employee->employee_id,
            'position'    => $employee->position,
            'department'  => $employee->department,
            'office'      => $employee->office,
            'email'       => $employee->user->email,
            'phone'       => $employee->phone,
        ]);
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function reviewerLists(): array
    {
        return [
            'site_inspectors'      => User::where('role', 'site_inspector')->get(),
            'surveyors'            => User::where('role', 'surveyor')->get(),
            'resident_engineers'   => User::where('role', 'resident_engineer')->get(),
            'mtqas'                => User::where('role', 'mtqa')->get(),
            'engineers_iv'         => User::where('role', 'engineeriv')->get(),
            'engineers_iii'        => User::where('role', 'engineeriii')->get(),
            'provincial_engineers' => User::where('role', 'provincial_engineer')->get(),
            'contractors'          => User::where('role', 'contractor')->get(),
        ];
    }
}