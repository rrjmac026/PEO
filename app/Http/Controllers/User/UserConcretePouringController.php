<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ConcretePouring;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConcretePouringController extends Controller
{
    /**
     * Display a listing of concrete pouring requests for the authenticated user
     */
    public function index(Request $request)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You must be registered as an employee to view concrete pouring requests.');
        }

        $query = ConcretePouring::with([
            'requestedBy.user',
            'meMtqaChecker.user',
            'residentEngineer.user',
            'approver.user',
            'disapprover.user'
        ])->where('requested_by_employee_id', $employee->id);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('pouring_datetime', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('pouring_datetime', '<=', $request->date_to);
        }

        $concretePourings = $query->latest()->paginate(15);

        // Statistics for the user
        $stats = [
            'total' => ConcretePouring::where('requested_by_employee_id', $employee->id)->count(),
            'pending' => ConcretePouring::where('requested_by_employee_id', $employee->id)
                ->where('status', 'requested')->count(),
            'approved' => ConcretePouring::where('requested_by_employee_id', $employee->id)
                ->where('status', 'approved')->count(),
            'disapproved' => ConcretePouring::where('requested_by_employee_id', $employee->id)
                ->where('status', 'disapproved')->count(),
        ];

        return view('user.concrete-pouring.index', compact('concretePourings', 'stats'));
    }

    /**
     * Show the form for creating a new concrete pouring request
     */
    public function create()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You must be registered as an employee to submit a request.');
        }

        return view('user.concrete-pouring.create');
    }

    /**
     * Store a newly created concrete pouring request
     */
    public function store(Request $request)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return back()->with('error', 'You must be registered as an employee to submit a request.');
        }

        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contractor' => 'required|string|max:255',
            'part_of_structure' => 'required|string|max:255',
            'estimated_volume' => 'required|numeric|min:0|max:9999.99',
            'station_limits_section' => 'nullable|string|max:255',
            'pouring_datetime' => 'required|date|after:now',
            
            // Checklist items
            'concrete_vibrator' => 'nullable|boolean',
            'field_density_test' => 'nullable|boolean',
            'protective_covering_materials' => 'nullable|boolean',
            'beam_cylinder_molds' => 'nullable|boolean',
            'warning_signs_barricades' => 'nullable|boolean',
            'curing_materials' => 'nullable|boolean',
            'concrete_saw' => 'nullable|boolean',
            'slump_cones' => 'nullable|boolean',
            'concrete_block_spacer' => 'nullable|boolean',
            'plumbness' => 'nullable|boolean',
            'finishing_tools_equipment' => 'nullable|boolean',
            'quality_of_materials' => 'nullable|boolean',
            'line_grade_alignment' => 'nullable|boolean',
            'lighting_system' => 'nullable|boolean',
            'required_construction_equipment' => 'nullable|boolean',
            'electrical_layout' => 'nullable|boolean',
            'rebar_sizes_spacing' => 'nullable|boolean',
            'plumbing_layout' => 'nullable|boolean',
            'rebars_installation' => 'nullable|boolean',
            'falseworks_formworks' => 'nullable|boolean',
        ]);

        // Convert null checkboxes to false
        $checklistFields = [
            'concrete_vibrator', 'field_density_test', 'protective_covering_materials',
            'beam_cylinder_molds', 'warning_signs_barricades', 'curing_materials',
            'concrete_saw', 'slump_cones', 'concrete_block_spacer', 'plumbness',
            'finishing_tools_equipment', 'quality_of_materials', 'line_grade_alignment',
            'lighting_system', 'required_construction_equipment', 'electrical_layout',
            'rebar_sizes_spacing', 'plumbing_layout', 'rebars_installation', 'falseworks_formworks'
        ];

        foreach ($checklistFields as $field) {
            $validated[$field] = $request->has($field) ? true : false;
        }

        // Add employee ID and status
        $validated['requested_by_employee_id'] = $employee->id;
        $validated['status'] = 'requested';

        $concretePouring = ConcretePouring::create($validated);

        return redirect()
            ->route('user.concrete-pouring.show', $concretePouring)
            ->with('success', 'Concrete pouring request submitted successfully! Your request is now pending review.');
    }

    /**
     * Display the specified concrete pouring request
     */
    public function show(ConcretePouring $concretePouring)
    {
        $employee = Auth::user()->employee;

        // Check if user owns this request
        if (!$employee || $concretePouring->requested_by_employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to this concrete pouring request.');
        }

        $concretePouring->load([
            'requestedBy.user',
            'meMtqaChecker.user',
            'residentEngineer.user',
            'approver.user',
            'disapprover.user',
            'notedByEngineer.user'
        ]);

        return view('user.concrete-pouring.show', compact('concretePouring'));
    }

    /**
     * Show the form for editing the specified concrete pouring request
     */
    public function edit(ConcretePouring $concretePouring)
    {
        $employee = Auth::user()->employee;

        // Check if user owns this request
        if (!$employee || $concretePouring->requested_by_employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to this concrete pouring request.');
        }

        // Only allow editing if not yet approved
        if ($concretePouring->status === 'approved') {
            return redirect()
                ->route('user.concrete-pouring.show', $concretePouring)
                ->with('error', 'Cannot edit an approved concrete pouring request.');
        }

        return view('user.concrete-pouring.edit', compact('concretePouring'));
    }

    /**
     * Update the specified concrete pouring request
     */
    public function update(Request $request, ConcretePouring $concretePouring)
    {
        $employee = Auth::user()->employee;

        // Check if user owns this request
        if (!$employee || $concretePouring->requested_by_employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to this concrete pouring request.');
        }

        // Only allow updating if not yet approved
        if ($concretePouring->status === 'approved') {
            return back()->with('error', 'Cannot update an approved concrete pouring request.');
        }

        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contractor' => 'required|string|max:255',
            'part_of_structure' => 'required|string|max:255',
            'estimated_volume' => 'required|numeric|min:0|max:9999.99',
            'station_limits_section' => 'nullable|string|max:255',
            'pouring_datetime' => 'required|date',
            
            // Checklist items
            'concrete_vibrator' => 'nullable|boolean',
            'field_density_test' => 'nullable|boolean',
            'protective_covering_materials' => 'nullable|boolean',
            'beam_cylinder_molds' => 'nullable|boolean',
            'warning_signs_barricades' => 'nullable|boolean',
            'curing_materials' => 'nullable|boolean',
            'concrete_saw' => 'nullable|boolean',
            'slump_cones' => 'nullable|boolean',
            'concrete_block_spacer' => 'nullable|boolean',
            'plumbness' => 'nullable|boolean',
            'finishing_tools_equipment' => 'nullable|boolean',
            'quality_of_materials' => 'nullable|boolean',
            'line_grade_alignment' => 'nullable|boolean',
            'lighting_system' => 'nullable|boolean',
            'required_construction_equipment' => 'nullable|boolean',
            'electrical_layout' => 'nullable|boolean',
            'rebar_sizes_spacing' => 'nullable|boolean',
            'plumbing_layout' => 'nullable|boolean',
            'rebars_installation' => 'nullable|boolean',
            'falseworks_formworks' => 'nullable|boolean',
        ]);

        // Convert null checkboxes to false
        $checklistFields = [
            'concrete_vibrator', 'field_density_test', 'protective_covering_materials',
            'beam_cylinder_molds', 'warning_signs_barricades', 'curing_materials',
            'concrete_saw', 'slump_cones', 'concrete_block_spacer', 'plumbness',
            'finishing_tools_equipment', 'quality_of_materials', 'line_grade_alignment',
            'lighting_system', 'required_construction_equipment', 'electrical_layout',
            'rebar_sizes_spacing', 'plumbing_layout', 'rebars_installation', 'falseworks_formworks'
        ];

        foreach ($checklistFields as $field) {
            $validated[$field] = $request->has($field) ? true : false;
        }

        $concretePouring->update($validated);

        return redirect()
            ->route('user.concrete-pouring.show', $concretePouring)
            ->with('success', 'Concrete pouring request updated successfully!');
    }

    /**
     * Remove the specified concrete pouring request
     */
    public function destroy(ConcretePouring $concretePouring)
    {
        $employee = Auth::user()->employee;

        // Check if user owns this request
        if (!$employee || $concretePouring->requested_by_employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to this concrete pouring request.');
        }

        // Only allow deletion if status is 'requested'
        if ($concretePouring->status !== 'requested') {
            return back()->with('error', 'Cannot delete a concrete pouring request that has been reviewed.');
        }

        $concretePouring->delete();

        return redirect()
            ->route('user.concrete-pouring.index')
            ->with('success', 'Concrete pouring request deleted successfully!');
    }

    /**
     * Print/Download PDF of the concrete pouring form
     */
    public function print(ConcretePouring $concretePouring)
    {
        $employee = Auth::user()->employee;

        // Check if user owns this request
        if (!$employee || $concretePouring->requested_by_employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to this concrete pouring request.');
        }

        $concretePouring->load([
            'requestedBy.user',
            'meMtqaChecker.user',
            'residentEngineer.user',
            'approver.user',
            'disapprover.user',
            'notedByEngineer.user'
        ]);

        return view('user.concrete-pouring.print', compact('concretePouring'));
    }
}