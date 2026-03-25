<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ConcretePouring;
use App\Models\Notification;
use App\Models\User;
use App\Models\WorkRequest;
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
     *
     * Notifications fired:
     *  1. Contractor  — confirmation that their request was received
     *  2. All admins  — a new request is waiting for reviewer assignment
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

        // If linked to a work request, verify the contractor owns it
        if (!empty($validated['work_request_id'])) {
            $wr = WorkRequest::where('id', $validated['work_request_id'])
                ->where('contractor_name', Auth::user()->name)
                ->first();

            if (!$wr) {
                return back()->with('error', 'Invalid work request selected.');
            }
        }

        // Normalise checkboxes — unchecked inputs are simply absent from POST
        foreach ($this->checklistFields() as $field) {
            $validated[$field] = $request->boolean($field);
        }

        // Force owner and initial status
        $validated['requested_by_user_id'] = Auth::id();
        $validated['status']               = 'requested';

        $concretePouring = ConcretePouring::create($validated);

        // ── 1. Notify the contractor: submission received ──────────────────
        Notification::send(
            Auth::id(),
            'concrete_pouring',
            'Concrete Pouring Request Submitted ✅',
            "Your concrete pouring request {$concretePouring->reference_number} for {$concretePouring->project_name} has been successfully submitted and is awaiting admin assignment.",
            route('user.concrete-pouring.show', $concretePouring->id),
            $concretePouring
        );

        // ── 2. Notify all admins: new request needs reviewer assignment ─────
        $adminIds = User::where('role', 'admin')->pluck('id')->toArray();
        if (!empty($adminIds)) {
            Notification::send(
                $adminIds,
                'concrete_pouring',
                'New Concrete Pouring Request Submitted',
                "A new concrete pouring request {$concretePouring->reference_number} ({$concretePouring->project_name}) has been submitted by {$concretePouring->contractor} and is awaiting reviewer assignment.",
                route('admin.concrete-pouring.show', $concretePouring->id),
                $concretePouring
            );
        }

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
     *
     * Notification fired:
     *  1. Contractor — confirmation that their edit was saved
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

        // ── Notify the contractor: their edit was saved ────────────────────
        Notification::send(
            Auth::id(),
            'concrete_pouring',
            'Concrete Pouring Request Updated',
            "Your concrete pouring request {$concretePouring->reference_number} ({$concretePouring->project_name}) has been updated successfully.",
            route('user.concrete-pouring.show', $concretePouring->id),
            $concretePouring
        );

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

        // Capture details before deletion for the notification message
        $refNumber   = $concretePouring->reference_number;
        $projectName = $concretePouring->project_name;
        $contractorId = Auth::id();

        $concretePouring->delete();

        // ── Notify the contractor: deletion confirmed ──────────────────────
        Notification::send(
            $contractorId,
            'concrete_pouring',
            'Concrete Pouring Request Deleted',
            "Your concrete pouring request {$refNumber} ({$projectName}) has been deleted successfully.",
            route('user.concrete-pouring.index'),
            null   // notifiable is gone, pass null
        );

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

    // ─── Helpers ─────────────────────────────────────────────────────────────

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