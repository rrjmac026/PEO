<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeManagementController extends Controller
{
    // 📌 Display all employees
    public function index(Request $request)
    {
        $query = Employee::with('user');

        if ($request->search) {
            $query->search($request->search);
        }

        $employees = $query->latest()->paginate(10);

        return view('admin.employees.index', compact('employees'));
    }

    // 📌 Show create form
    public function create()
    {
        $users = User::doesntHave('employee')->get(); 
        // prevents assigning multiple employees to one user

        return view('admin.employees.create', compact('users'));
    }

    // 📌 Store new employee
    public function store(Request $request)
    {
        $request->validate([
            'user_id'         => 'required|exists:users,id|unique:employees,user_id',
            'employee_number' => 'required|string|unique:employees,employee_number',
            'position'        => 'required|string|max:255',
            'department'      => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'office'          => 'nullable|string|max:255',
            'signature_path'  => 'nullable|string',
        ]);

        Employee::create($request->all());

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Employee created successfully.');
    }

    // 📌 Show single employee
    public function show(Employee $employee)
    {
        $employee->load('user', 'workRequests');

        return view('admin.employees.show', compact('employee'));
    }

    // 📌 Show edit form
    public function edit(Employee $employee)
    {
        $users = User::doesntHave('employee')
                    ->orWhere('id', $employee->user_id)
                    ->get();

        return view('admin.employees.edit', compact('employee', 'users'));
    }

    // 📌 Update employee
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'user_id'         => 'required|exists:users,id|unique:employees,user_id,' . $employee->id,
            'employee_number' => 'required|string|unique:employees,employee_number,' . $employee->id,
            'position'        => 'required|string|max:255',
            'department'      => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'office'          => 'nullable|string|max:255',
            'signature_path'  => 'nullable|string',
        ]);

        $employee->update($request->all());

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Employee updated successfully.');
    }

    // 📌 Delete employee
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Employee deleted successfully.');
    }
}