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

        return view('user.concrete-pouring.create', compact('workRequest', 'approvedWorkRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_request_id'        => 'nullable|exists:work_requests,id',
            'reference_number'       => 'nullable|string|max:50|unique:concrete_pourings,reference_number',
            'project_name'           => 'required|string|max:255',
            'location'               => 'required|string|max:255',
            'contractor'             => 'required|string|max:255',
            'part_of_structure'      => 'required|string|max:255',
            'estimated_volume'       => 'required|numeric|min:0|max:9999.99',
            'station_limits_section' => 'nullable|string|max:255',
            'pouring_datetime'       => 'required|date|after:now',
            ...$this->checklistRules(),
        ]);

        foreach ($this->checklistFields() as $field) {
            $validated[$field] = $request->boolean($field);
        }

        $validated['requested_by_user_id'] = Auth::id();
        $validated['status']               = 'requested';

        $concretePouring = ConcretePouring::create($validated);

        $concretePouring->addLog(ConcretePouringLog::EVENT_SUBMITTED, [
            'description' => 'Concrete pouring request submitted by contractor.',
            'status_to'   => 'requested',
        ]);

        ConcretePouringNotificationService::submitted($concretePouring);

        return redirect()
            ->route('user.concrete-pouring.show', $concretePouring)
            ->with('success', 'Concrete pouring request submitted successfully! Awaiting admin assignment.');
    }

    public function show(ConcretePouring $concretePouring)
    {
        $this->authorizeOwner($concretePouring);

        $concretePouring->load([
            'workRequest', 'requestedBy', 'meMtqaChecker',
            'residentEngineer', 'approver', 'disapprover', 'notedByEngineer',
        ]);

        return view('user.concrete-pouring.show', compact('concretePouring'));
    }

    public function edit(ConcretePouring $concretePouring)
    {
        $this->authorizeOwner($concretePouring);

        if ($concretePouring->status !== 'requested' || !is_null($concretePouring->me_mtqa_user_id)) {
            return redirect()
                ->route('user.concrete-pouring.show', $concretePouring)
                ->with('error', 'This request can no longer be edited once reviewers have been assigned.');
        }

        $approvedWorkRequests = WorkRequest::where('contractor_name', Auth::user()->name)
            ->where('status', WorkRequest::STATUS_APPROVED)
            ->orderByDesc('created_at')
            ->get();

        return view('user.concrete-pouring.edit', compact('concretePouring', 'approvedWorkRequests'));
    }

    public function update(Request $request, ConcretePouring $concretePouring)
    {
        $this->authorizeOwner($concretePouring);

        if ($concretePouring->status !== 'requested' || !is_null($concretePouring->me_mtqa_user_id)) {
            return back()->with('error', 'This request can no longer be edited once reviewers have been assigned.');
        }

        $validated = $request->validate([
            'work_request_id'        => 'nullable|exists:work_requests,id',
            'reference_number'       => 'nullable|string|max:50|unique:concrete_pourings,reference_number,' . $concretePouring->id,
            'project_name'           => 'required|string|max:255',
            'location'               => 'required|string|max:255',
            'contractor'             => 'required|string|max:255',
            'part_of_structure'      => 'required|string|max:255',
            'estimated_volume'       => 'required|numeric|min:0|max:9999.99',
            'station_limits_section' => 'nullable|string|max:255',
            'pouring_datetime'       => 'required|date',
            ...$this->checklistRules(),
        ]);

        foreach ($this->checklistFields() as $field) {
            $validated[$field] = $request->boolean($field);
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
        $referenceNumber = $concretePouring->reference_number;
        $projectName     = $concretePouring->project_name;

        // Log before delete so the FK still exists
        $concretePouring->addLog(ConcretePouringLog::EVENT_DELETED, [
            'description' => 'Concrete pouring request deleted by contractor.',
            'status_from' => $concretePouring->status,
        ]);

        $concretePouring->delete();

        ConcretePouringNotificationService::deleted($contractorId, $referenceNumber, $projectName);

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
