<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WorkRequest;
use App\Models\ConcretePouring;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Work Request stats
        $workRequestStats = [
            'total'     => WorkRequest::where('contractor_name', $user->name)->count(),
            'submitted' => WorkRequest::where('contractor_name', $user->name)->where('status', 'submitted')->count(),
            'approved'  => WorkRequest::where('contractor_name', $user->name)->where('status', 'approved')->count(),
            'rejected'  => WorkRequest::where('contractor_name', $user->name)->where('status', 'rejected')->count(),
        ];

        // Concrete Pouring stats
        $concretePouringStats = [
            'total'       => ConcretePouring::where('requested_by_employee_id', $user->employee?->id)->count(),
            'pending'     => ConcretePouring::where('requested_by_employee_id', $user->employee?->id)->where('status', 'requested')->count(),
            'approved'    => ConcretePouring::where('requested_by_employee_id', $user->employee?->id)->where('status', 'approved')->count(),
            'disapproved' => ConcretePouring::where('requested_by_employee_id', $user->employee?->id)->where('status', 'disapproved')->count(),
        ];

        // Recent Work Requests
        $recentWorkRequests = WorkRequest::where('contractor_name', $user->name)
            ->latest()
            ->take(5)
            ->get();

        // Recent Concrete Pourings
        $recentConcretePourings = ConcretePouring::where('requested_by_employee_id', $user->employee?->id)
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact(
            'workRequestStats',
            'concretePouringStats',
            'recentWorkRequests',
            'recentConcretePourings'
        ));
    }
}