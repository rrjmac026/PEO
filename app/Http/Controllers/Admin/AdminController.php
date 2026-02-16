<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WorkRequest;
use App\Models\Employee;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users           = User::all();
        $totalUsers      = $users->count();
        $totalRequests   = WorkRequest::count();
        $totalEmployees  = Employee::count();

        return view('admin.dashboard', compact(
            'users',
            'totalUsers',
            'totalRequests',
            'totalEmployees'
        ));
    }
}