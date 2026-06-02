<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user');

        if ($request->search) {
            $query->search($request->search);
        }

        $employees = $query->latest()->paginate(10);

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $users = User::doesntHave('employee')->get();
        return view('admin.employees.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'        => 'nullable|exists:users,id|unique:employees,user_id',
            'id_number'      => 'required|string|unique:employees,id_number',
            'position_title' => 'required|string|max:255',
            'department'     => 'nullable|string|max:255',
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'email_address'  => 'nullable|email|max:255',
            'date_of_birth'  => 'nullable|date',
            'blood_type'     => 'nullable|string|max:10',
            'height_cm'      => 'nullable|numeric',
            'weight_kg'      => 'nullable|numeric',
            'home_address'   => 'nullable|string',
            'phone_number'   => 'nullable|string|max:20',
            'emergency_contact_no' => 'nullable|string|max:20',
            'tin'            => 'nullable|string|max:50',
            'pagibig_no'     => 'nullable|string|max:50',
            'philhealth'     => 'nullable|string|max:50',
            'gsis_no'        => 'nullable|string|max:50',
            'hmo_organization' => 'nullable|string|max:255',
            'hmo_number'     => 'nullable|string|max:100',
            'eligibility'    => 'nullable|string',
            'licence_number' => 'nullable|string|max:100',
            'office'         => 'nullable|string|max:255',
            'signature_path' => 'nullable|string',
        ]);

        Employee::create($request->all());

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load('user');
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $users = User::doesntHave('employee')
                    ->orWhere('id', $employee->user_id)
                    ->get();

        return view('admin.employees.edit', compact('employee', 'users'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'user_id'        => 'nullable|exists:users,id|unique:employees,user_id,' . $employee->id,
            'id_number'      => 'required|string|unique:employees,id_number,' . $employee->id,
            'position_title' => 'required|string|max:255',
            'department'     => 'nullable|string|max:255',
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'email_address'  => 'nullable|email|max:255',
            'date_of_birth'  => 'nullable|date',
            'blood_type'     => 'nullable|string|max:10',
            'height_cm'      => 'nullable|numeric',
            'weight_kg'      => 'nullable|numeric',
            'home_address'   => 'nullable|string',
            'phone_number'   => 'nullable|string|max:20',
            'emergency_contact_no' => 'nullable|string|max:20',
            'tin'            => 'nullable|string|max:50',
            'pagibig_no'     => 'nullable|string|max:50',
            'philhealth'     => 'nullable|string|max:50',
            'gsis_no'        => 'nullable|string|max:50',
            'hmo_organization' => 'nullable|string|max:255',
            'hmo_number'     => 'nullable|string|max:100',
            'eligibility'    => 'nullable|string',
            'licence_number' => 'nullable|string|max:100',
            'office'         => 'nullable|string|max:255',
            'signature_path' => 'nullable|string',
        ]);

        $employee->update($request->all());

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Employee deleted successfully.');
    }
}