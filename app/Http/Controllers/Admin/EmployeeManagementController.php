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
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
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
        ]);

        $fullName = trim($request->first_name . ' ' . ($request->middle_name ? $request->middle_name . ' ' : '') . $request->last_name);
        $email    = $request->email_address;

        // Auto-create or reuse a User account
        if ($email) {
            $user = \App\Models\User::firstOrCreate(
                ['email' => strtolower($email)],
                [
                    'name'     => $fullName,
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role'     => $this->resolveRole($request->position_title),
                ]
            );
        } else {
            $user = \App\Models\User::create([
                'name'     => $fullName,
                'email'    => 'manual.' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(8)) . '@placeholder.local',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role'     => $this->resolveRole($request->position_title),
            ]);
        }

        $data = $request->except(['email_address']);
        $data['user_id']       = $user->id;
        $data['email_address'] = $email;

        Employee::create($data);

        return redirect()->route('admin.employees.index')
                        ->with('success', 'Employee created and user account linked automatically.');
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

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:employees,id',
        ]);

        Employee::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.employees.index')
                        ->with('success', count($request->ids) . ' employee(s) deleted successfully.');
    }
}