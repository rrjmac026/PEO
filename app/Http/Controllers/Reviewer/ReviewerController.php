<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\WorkRequest;
use App\Models\ConcretePouring;
use Illuminate\Support\Facades\Auth;

class ReviewerController extends Controller
{
    public function dashboard()
    {
        $role  = Auth::user()->role;
        $stats = $this->getStatsForRole($role);

        return view('reviewer.dashboard', compact('role', 'stats'));
    }

    private function getStatsForRole(string $role): array
    {
        return match($role) {
            'site_inspector' => [
                'title'   => 'Site Inspector Dashboard',
                'pending' => WorkRequest::whereNull('inspected_by_site_inspector')->count(),
                'done'    => WorkRequest::whereNotNull('inspected_by_site_inspector')->count(),
                'total'   => WorkRequest::count(),
                'recent'  => WorkRequest::whereNull('inspected_by_site_inspector')
                                ->latest()->take(5)->get(),
            ],

            'surveyor' => [
                'title'   => 'Surveyor Dashboard',
                'pending' => WorkRequest::whereNull('surveyor_name')->count(),
                'done'    => WorkRequest::whereNotNull('surveyor_name')->count(),
                'total'   => WorkRequest::count(),
                'recent'  => WorkRequest::whereNull('surveyor_name')
                                ->latest()->take(5)->get(),
            ],

            'resident_engineer' => [
                'title'   => 'Resident Engineer Dashboard',
                'pending' => WorkRequest::whereNull('resident_engineer_name')->count(),
                'done'    => WorkRequest::whereNotNull('resident_engineer_name')->count(),
                'total'   => WorkRequest::count(),
                'recent'  => WorkRequest::whereNull('resident_engineer_name')
                                ->latest()->take(5)->get(),
            ],

            'provincial_engineer' => [
                'title'      => 'Provincial Engineer Dashboard',
                'total'      => WorkRequest::count(),
                'approved'   => WorkRequest::where('status', 'approved')->count(),
                'pending'    => WorkRequest::where('status', 'submitted')->count(),
                'rejected'   => WorkRequest::where('status', 'rejected')->count(),
                'recent'     => WorkRequest::latest()->take(5)->get(),
                // Concrete Pouring stats
                'cp_total'      => ConcretePouring::count(),
                'cp_approved'   => ConcretePouring::where('status', 'approved')->count(),
                'cp_pending'    => ConcretePouring::where('status', 'requested')->count(),
            ],

            default => [],
        };
    }
}