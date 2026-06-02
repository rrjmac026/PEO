<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ConcretePouring;
use App\Models\ConcretePouringLog;
use App\Models\WorkRequest;
use App\Services\ConcretePouringNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserConcretePouringController extends Controller
{
    public function index(Request $request)
    {
        $query = ConcretePouring::with(['workRequest'])
            ->where('requested_by_user_id', Auth::id());

        if ($request->filled('search'))    $query->search($request->search);
        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('date_from')) $query->whereDate('pouring_datetime', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('pouring_datetime', '<=', $request->date_to);

        $concretePourings = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'       => ConcretePouring::where('requested_by_user_id', Auth::id())->count(),
            'pending'     => ConcretePouring::where('requested_by_user_id', Auth::id())->where('status', 'requested')->count(),
            'approved'    => ConcretePouring::where('requested_by_user_id', Auth::id())->where('status', 'approved')->count(),
            'disapproved' => ConcretePouring::where('requested_by_user_id', Auth::id())->where('status', 'disapproved')->count(),
        ];

        return view('user.concrete-pouring.index', compact('concretePourings', 'stats'));
    }

    public function create(Request $request)
    {
        $workRequest = null;
        if ($request->filled('work_request_id')) {
            $workRequest = WorkRequest::where('id', $request->work_request_id)
                ->where('contractor_name', Auth::user()->name)
                ->where('status', WorkRequest::STATUS_APPROVED)
                ->first();
        }

        $approvedWorkRequests = WorkRequest::where('contractor_name', Auth::user()->name)
            ->where('status', WorkRequest::STATUS_APPROVED)
            ->orderByDesc('created_at')
            ->get();

        // ── Existing contract numbers for the combobox ──────────────
        $contractNumbers = ConcretePouring::whereNotNull('contract_number')
            ->where('contract_number', '!=', '')
            ->distinct()
            ->orderBy('contract_number')
            ->pluck('contract_number')
            ->values();

        $residentEngineers   = \App\Models\User::where('role', 'resident_engineer')->orderBy('name')->get();
        $provincialEngineers = \App\Models\User::where('role', 'provincial_engineer')->orderBy('name')->get();
        $mtqas               = \App\Models\User::where('role', 'mtqa')->orderBy('name')->get();

        return view('user.concrete-pouring.create', compact(
            'workRequest', 'approvedWorkRequests', 'contractNumbers',
            'residentEngineers', 'provincialEngineers', 'mtqas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_request_id'           => 'nullable|exists:work_requests,id',
            'contract_number'           => 'nullable|string|max:50',
            'project_name'              => 'required|string|max:255',
            'location'                  => 'required|string|max:255',
            'contractor'                => 'required|string|max:255',
            'part_of_structure'         => 'required|string|max:255',
            'estimated_volume'          => 'required|numeric|min:0|max:9999.99',
            'station_limits_section'    => 'nullable|string|max:255',
            'pouring_datetime'          => 'required|date|after:now',
            // Reviewer assignments
            'resident_engineer_user_id' => 'nullable|exists:users,id',
            'noted_by_user_id'          => 'nullable|exists:users,id',
            'me_mtqa_user_id'           => 'nullable|exists:users,id',
        ]);

        $validated['requested_by_user_id'] = Auth::id();
        $validated['status']               = 'requested';

        // Determine first review step (same logic as AdminConcretePouringController::assign)
        $stepToCol = [
            'resident_engineer'   => 'resident_engineer_user_id',
            'mtqa'                => 'me_mtqa_user_id',           // ← step 2
            'provincial_engineer' => 'noted_by_user_id',          // ← step 3 / final
        ];

        $firstStep = null;
        foreach ($stepToCol as $step => $col) {
            if (!empty($validated[$col])) {
                $firstStep = $step;
                break;
            }
        }

        if ($firstStep) {
            $validated['current_review_step']  = $firstStep;
            $validated['assigned_by_admin_id'] = Auth::id(); // contractor acts as assigner
            $validated['assigned_at']          = now();
        }

        $concretePouring = ConcretePouring::create($validated);

        // Build assignment log description
        $assignedLabels = [];
        if (!empty($validated['resident_engineer_user_id'])) {
            $u = \App\Models\User::find($validated['resident_engineer_user_id']);
            if ($u) $assignedLabels[] = "Resident Engineer: {$u->name}";
        }
        if (!empty($validated['noted_by_user_id'])) {
            $u = \App\Models\User::find($validated['noted_by_user_id']);
            if ($u) $assignedLabels[] = "Provincial Engineer: {$u->name}";
        }
        if (!empty($validated['me_mtqa_user_id'])) {
            $u = \App\Models\User::find($validated['me_mtqa_user_id']);
            if ($u) $assignedLabels[] = "ME/MTQA: {$u->name}";
        }

        $concretePouring->addLog(ConcretePouringLog::EVENT_SUBMITTED, [
            'description' => 'Concrete pouring request submitted by contractor.'
                . ($assignedLabels ? ' Reviewers: ' . implode(', ', $assignedLabels) . '.' : ''),
            'status_to'   => 'requested',
        ]);

        if ($firstStep) {
            $concretePouring->addLog(ConcretePouringLog::EVENT_ASSIGNED, [
                'description' => 'Reviewers assigned by contractor. ' . implode(', ', $assignedLabels) . '. First step: ' . $firstStep . '.',
                'review_step' => $firstStep,
            ]);
            ConcretePouringNotificationService::assigned($concretePouring);
        } else {
            ConcretePouringNotificationService::submitted($concretePouring);
        }

        return redirect()
            ->route('user.concrete-pouring.show', $concretePouring)
            ->with('success', 'Concrete pouring request submitted successfully!'
                . ($firstStep ? ' Reviewers have been notified.' : ' Awaiting reviewer assignment.'));
    }

    public function show(ConcretePouring $concretePouring)
    {
        $this->authorizeOwner($concretePouring);

        $concretePouring->load([
            'workRequest', 'requestedBy', 'meMtqaChecker',
            'residentEngineer', 'approver', 'disapprover', 'notedByEngineer',
            'checklistFilledBy',
            'checklistLogs.user', // ← add this
        ]);

        return view('user.concrete-pouring.show', compact('concretePouring'));
    }

    public function edit(ConcretePouring $concretePouring)
    {
        $this->authorizeOwner($concretePouring);

        if ($concretePouring->status !== 'requested' || !is_null($concretePouring->re_date)) {
            return redirect()
                ->route('user.concrete-pouring.show', $concretePouring)
                ->with('error', 'This request can no longer be edited once a review has been submitted.');
        }

        $residentEngineers   = \App\Models\User::where('role', 'resident_engineer')->orderBy('name')->get();
        $provincialEngineers = \App\Models\User::where('role', 'provincial_engineer')->orderBy('name')->get();
        $mtqas               = \App\Models\User::where('role', 'mtqa')->orderBy('name')->get();

        $approvedWorkRequests = WorkRequest::where('contractor_name', Auth::user()->name)
            ->where('status', WorkRequest::STATUS_APPROVED)
            ->orderByDesc('created_at')
            ->get();

        return view('user.concrete-pouring.edit', compact(
            'concretePouring', 'approvedWorkRequests',
            'residentEngineers', 'provincialEngineers', 'mtqas'
        ));
    }

    public function update(Request $request, ConcretePouring $concretePouring)
    {
        $this->authorizeOwner($concretePouring);

        // Block edit if already in review (any reviewer has acted)
        if ($concretePouring->status !== 'requested' || !is_null($concretePouring->re_date)) {
            return back()->with('error', 'This request can no longer be edited once a review has been submitted.');
        }

        $validated = $request->validate([
            'work_request_id'           => 'nullable|exists:work_requests,id',
            'contract_number'           => 'nullable|string|max:50,' . $concretePouring->id,
            'project_name'              => 'required|string|max:255',
            'location'                  => 'required|string|max:255',
            'contractor'                => 'required|string|max:255',
            'part_of_structure'         => 'required|string|max:255',
            'estimated_volume'          => 'required|numeric|min:0|max:9999.99',
            'station_limits_section'    => 'nullable|string|max:255',
            'pouring_datetime'          => 'required|date',
            // Reviewer assignments
            'resident_engineer_user_id' => 'nullable|exists:users,id',
            'noted_by_user_id'          => 'nullable|exists:users,id',
            'me_mtqa_user_id'           => 'nullable|exists:users,id',
        ]);

        // Recalculate first step
        $stepToCol = [
            'resident_engineer'   => 'resident_engineer_user_id',
            'mtqa'                => 'me_mtqa_user_id',           // ← step 2
            'provincial_engineer' => 'noted_by_user_id',          // ← step 3 / final
        ];

        $firstStep = null;
        foreach ($stepToCol as $step => $col) {
            if (!empty($validated[$col])) {
                $firstStep = $step;
                break;
            }
        }

        $validated['current_review_step'] = $firstStep;
        if ($firstStep && is_null($concretePouring->assigned_at)) {
            $validated['assigned_by_admin_id'] = Auth::id();
            $validated['assigned_at']          = now();
        }

        $changes = $concretePouring->buildChanges($validated);
        $concretePouring->update($validated);

        $concretePouring->addLog(ConcretePouringLog::EVENT_UPDATED, [
            'description' => 'Concrete pouring request updated by contractor.',
            'changes'     => $changes,
        ]);

        ConcretePouringNotificationService::updated($concretePouring);

        return redirect()
            ->route('user.concrete-pouring.show', $concretePouring)
            ->with('success', 'Concrete pouring request updated successfully!');
    }

    public function destroy(ConcretePouring $concretePouring)
    {
        $this->authorizeOwner($concretePouring);

        if ($concretePouring->status !== 'requested' || !is_null($concretePouring->me_mtqa_user_id)) {
            return back()->with('error', 'Cannot delete a request that has already been assigned or reviewed.');
        }

        $contractorId    = Auth::id();
        $contractNumber = $concretePouring->contract_number;
        $projectName     = $concretePouring->project_name;

        // Log before delete so the FK still exists
        $concretePouring->addLog(ConcretePouringLog::EVENT_DELETED, [
            'description' => 'Concrete pouring request deleted by contractor.',
            'status_from' => $concretePouring->status,
        ]);

        $concretePouring->delete();

        ConcretePouringNotificationService::deleted($contractorId, $contractNumber, $projectName);

        return redirect()
            ->route('user.concrete-pouring.index')
            ->with('success', 'Concrete pouring request deleted successfully!');
    }

    public function print(ConcretePouring $concretePouring)
    {
        $this->authorizeOwner($concretePouring);

        $concretePouring->load([
            'workRequest', 'requestedBy', 'meMtqaChecker',
            'residentEngineer', 'approver', 'disapprover', 'notedByEngineer',
        ]);

        return view('user.concrete-pouring.print', compact('concretePouring'));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function authorizeOwner(ConcretePouring $concretePouring): void
    {
        if ($concretePouring->requested_by_user_id !== Auth::id()) {
            abort(403, 'You do not have access to this concrete pouring request.');
        }
    }

    private function checklistRules(): array
    {
        return collect($this->checklistFields())
            ->mapWithKeys(fn ($f) => [$f => 'nullable|boolean'])
            ->toArray();
    }

    private function checklistFields(): array
    {
        return [
            'concrete_vibrator', 'field_density_test', 'protective_covering_materials',
            'beam_cylinder_molds', 'warning_signs_barricades', 'curing_materials',
            'concrete_saw', 'slump_cones', 'concrete_block_spacer', 'plumbness',
            'finishing_tools_equipment', 'quality_of_materials', 'line_grade_alignment',
            'lighting_system', 'required_construction_equipment', 'electrical_layout',
            'rebar_sizes_spacing', 'plumbing_layout', 'rebars_installation',
            'falseworks_formworks',
        ];
    }
}
