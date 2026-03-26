<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ConcretePouring;
use App\Models\WorkRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserConcretePouringController extends Controller
{
    /**
     * List only the authenticated contractor's own concrete pouring requests.
     */
    public function index(Request $request)
    {
        $query = ConcretePouring::with(['workRequest'])
            ->where('requested_by_user_id', Auth::id());

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('pouring_datetime', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('pouring_datetime', '<=', $request->date_to);
        }

        $concretePourings = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'       => ConcretePouring::where('requested_by_user_id', Auth::id())->count(),
            'pending'     => ConcretePouring::where('requested_by_user_id', Auth::id())->where('status', 'requested')->count(),
            'approved'    => ConcretePouring::where('requested_by_user_id', Auth::id())->where('status', 'approved')->count(),
            'disapproved' => ConcretePouring::where('requested_by_user_id', Auth::id())->where('status', 'disapproved')->count(),
        ];

        return view('user.concrete-pouring.index', compact('concretePourings', 'stats'));
    }

    /**
     * Show the submission form.
     * Optionally pre-fill from an existing approved WorkRequest.
     */
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

    /**
     * Store a newly submitted concrete pouring request.
     */
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

            // Checklist
            'concrete_vibrator'               => 'nullable|boolean',
            'field_density_test'              => 'nullable|boolean',
            'protective_covering_materials'   => 'nullable|boolean',
            'beam_cylinder_molds'             => 'nullable|boolean',
            'warning_signs_barricades'        => 'nullable|boolean',
            'curing_materials'                => 'nullable|boolean',
            'concrete_saw'                    => 'nullable|boolean',
            'slump_cones'                     => 'nullable|boolean',
            'concrete_block_spacer'           => 'nullable|boolean',
            'plumbness'                       => 'nullable|boolean',
            'finishing_tools_equipment'       => 'nullable|boolean',
            'quality_of_materials'            => 'nullable|boolean',
            'line_grade_alignment'            => 'nullable|boolean',
            'lighting_system'                 => 'nullable|boolean',
            'required_construction_equipment' => 'nullable|boolean',
            'electrical_layout'               => 'nullable|boolean',
            'rebar_sizes_spacing'             => 'nullable|boolean',
            'plumbing_layout'                 => 'nullable|boolean',
            'rebars_installation'             => 'nullable|boolean',
            'falseworks_formworks'            => 'nullable|boolean',
        ]);

        if (!empty($validated['work_request_id'])) {
            $wr = WorkRequest::where('id', $validated['work_request_id'])
                ->where('contractor_name', Auth::user()->name)
                ->first();

            if (!$wr) {
                return back()->with('error', 'Invalid work request selected.');
            }
        }

        foreach ($this->checklistFields() as $field) {
            $validated[$field] = $request->boolean($field);
        }

        $validated['requested_by_user_id'] = Auth::id();
        $validated['status']               = 'requested';

        $concretePouring = ConcretePouring::create($validated);

        NotificationService::concretePouringSubmitted($concretePouring);

        return redirect()
            ->route('user.concrete-pouring.show', $concretePouring)
            ->with('success', 'Concrete pouring request submitted successfully! Awaiting admin assignment.');
    }

    /**
     * View a single request — only the owning contractor may see it.
     */
    public function show(ConcretePouring $concretePouring)
    {
        $this->authorizeOwner($concretePouring);

        $concretePouring->load([
            'workRequest',
            'requestedBy',
            'meMtqaChecker',
            'residentEngineer',
            'approver',
            'disapprover',
            'notedByEngineer',
        ]);

        return view('user.concrete-pouring.show', compact('concretePouring'));
    }

    /**
     * Edit form — only while still in 'requested' status (not yet assigned).
     */
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

    /**
     * Update — same restrictions as edit.
     */
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

            // Checklist
            'concrete_vibrator'               => 'nullable|boolean',
            'field_density_test'              => 'nullable|boolean',
            'protective_covering_materials'   => 'nullable|boolean',
            'beam_cylinder_molds'             => 'nullable|boolean',
            'warning_signs_barricades'        => 'nullable|boolean',
            'curing_materials'                => 'nullable|boolean',
            'concrete_saw'                    => 'nullable|boolean',
            'slump_cones'                     => 'nullable|boolean',
            'concrete_block_spacer'           => 'nullable|boolean',
            'plumbness'                       => 'nullable|boolean',
            'finishing_tools_equipment'       => 'nullable|boolean',
            'quality_of_materials'            => 'nullable|boolean',
            'line_grade_alignment'            => 'nullable|boolean',
            'lighting_system'                 => 'nullable|boolean',
            'required_construction_equipment' => 'nullable|boolean',
            'electrical_layout'               => 'nullable|boolean',
            'rebar_sizes_spacing'             => 'nullable|boolean',
            'plumbing_layout'                 => 'nullable|boolean',
            'rebars_installation'             => 'nullable|boolean',
            'falseworks_formworks'            => 'nullable|boolean',
        ]);

        foreach ($this->checklistFields() as $field) {
            $validated[$field] = $request->boolean($field);
        }

        $concretePouring->update($validated);

        NotificationService::concretePouringUpdated($concretePouring);

        return redirect()
            ->route('user.concrete-pouring.show', $concretePouring)
            ->with('success', 'Concrete pouring request updated successfully!');
    }

    /**
     * Delete — only while still unassigned and in 'requested' status.
     */
    public function destroy(ConcretePouring $concretePouring)
    {
        $this->authorizeOwner($concretePouring);

        if ($concretePouring->status !== 'requested' || !is_null($concretePouring->me_mtqa_user_id)) {
            return back()->with('error', 'Cannot delete a request that has already been assigned or reviewed.');
        }

        // Capture before deletion — model will be gone after delete()
        $contractorId    = Auth::id();
        $referenceNumber = $concretePouring->reference_number;
        $projectName     = $concretePouring->project_name;

        $concretePouring->delete();

        NotificationService::concretePouringDeleted($contractorId, $referenceNumber, $projectName);

        return redirect()
            ->route('user.concrete-pouring.index')
            ->with('success', 'Concrete pouring request deleted successfully!');
    }

    /**
     * Read-only print view.
     */
    public function print(ConcretePouring $concretePouring)
    {
        $this->authorizeOwner($concretePouring);

        $concretePouring->load([
            'workRequest',
            'requestedBy',
            'meMtqaChecker',
            'residentEngineer',
            'approver',
            'disapprover',
            'notedByEngineer',
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