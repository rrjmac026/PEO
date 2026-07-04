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
        $user  = Auth::user();
        $role  = $user->role;
        $stats = $this->getStatsForRole($role, $user->id);

        return view('reviewer.dashboard', compact('role', 'stats'));
    }

    private function getStatsForRole(string $role, int $userId): array
    {
        return match($role) {
            'site_inspector' => [
                'title'   => 'Site Inspector Dashboard',
                'pending' => WorkRequest::where('assigned_site_inspector_id', $userId)
                                ->whereNull('inspected_by_site_inspector')->count(),
                'done'    => WorkRequest::where('assigned_site_inspector_id', $userId)
                                ->whereNotNull('inspected_by_site_inspector')->count(),
                'total'   => WorkRequest::where('assigned_site_inspector_id', $userId)->count(),
                'recent'  => WorkRequest::where('assigned_site_inspector_id', $userId)
                                ->whereNull('inspected_by_site_inspector')
                                ->latest()->take(5)->get(),
            ],

            'surveyor' => [
                'title'   => 'Surveyor Dashboard',
                'pending' => WorkRequest::where('assigned_surveyor_id', $userId)
                                ->whereNull('surveyor_name')->count(),
                'done'    => WorkRequest::where('assigned_surveyor_id', $userId)
                                ->whereNotNull('surveyor_name')->count(),
                'total'   => WorkRequest::where('assigned_surveyor_id', $userId)->count(),
                'recent'  => WorkRequest::where('assigned_surveyor_id', $userId)
                                ->whereNull('surveyor_name')
                                ->latest()->take(5)->get(),
            ],

            'resident_engineer' => [
                'title'   => 'Resident Engineer Dashboard',
                'pending' => WorkRequest::where('assigned_resident_engineer_id', $userId)
                                ->whereNull('resident_engineer_name')->count(),
                'done'    => WorkRequest::where('assigned_resident_engineer_id', $userId)
                                ->whereNotNull('resident_engineer_name')->count(),
                'total'   => WorkRequest::where('assigned_resident_engineer_id', $userId)->count(),
                'recent'  => WorkRequest::where('assigned_resident_engineer_id', $userId)
                                ->whereNull('resident_engineer_name')
                                ->latest()->take(5)->get(),
            ],

            'mtqa' => [
                'title'   => 'MTQA Dashboard',
                'total'   => WorkRequest::where('assigned_mtqa_id', $userId)->count(),
                'pending' => WorkRequest::where('assigned_mtqa_id', $userId)
                                ->whereNull('checked_by_mtqa')->count(),
                'done'    => WorkRequest::where('assigned_mtqa_id', $userId)
                                ->whereNotNull('checked_by_mtqa')->count(),
                'recent'  => WorkRequest::where('assigned_mtqa_id', $userId)
                                ->whereNull('checked_by_mtqa')
                                ->latest()->take(5)->get(),
            ],

            'engineeriv' => [
                'title'   => 'Engineer IV Dashboard',
                'total'   => WorkRequest::where('assigned_engineer_iv_id', $userId)->count(),
                'pending' => WorkRequest::where('assigned_engineer_iv_id', $userId)
                                ->whereNull('reviewed_by')->count(),
                'done'    => WorkRequest::where('assigned_engineer_iv_id', $userId)
                                ->whereNotNull('reviewed_by')->count(),
                'recent'  => WorkRequest::where('assigned_engineer_iv_id', $userId)
                                ->whereNull('reviewed_by')
                                ->latest()->take(5)->get(),
            ],

            'engineeriii' => [
                'title'   => 'Engineer III Dashboard',
                'total'   => WorkRequest::where('assigned_engineer_iii_id', $userId)->count(),
                'pending' => WorkRequest::where('assigned_engineer_iii_id', $userId)
                                ->whereNull('recommending_approval_by')->count(),
                'done'    => WorkRequest::where('assigned_engineer_iii_id', $userId)
                                ->whereNotNull('recommending_approval_by')->count(),
                'recent'  => WorkRequest::where('assigned_engineer_iii_id', $userId)
                                ->whereNull('recommending_approval_by')
                                ->latest()->take(5)->get(),
            ],

            'provincial_engineer' => [
                'title'      => 'Provincial Engineer Dashboard',
                'total'      => WorkRequest::where('assigned_provincial_engineer_id', $userId)->count(),
                'approved'   => WorkRequest::where('assigned_provincial_engineer_id', $userId)
                                    ->where('status', 'approved')->count(),
                'pending'    => WorkRequest::where('assigned_provincial_engineer_id', $userId)
                                    ->where('current_review_step', 'provincial_engineer')->count(),
                'rejected'   => WorkRequest::where('assigned_provincial_engineer_id', $userId)
                                    ->where('status', 'rejected')->count(),
                'recent'     => WorkRequest::where('assigned_provincial_engineer_id', $userId)
                                    ->latest()->take(5)->get(),

                // Concrete Pouring stats — also scoped to this PE
                'cp_total'    => ConcretePouring::where('noted_by_user_id', $userId)->count(),
                'cp_approved' => ConcretePouring::where('noted_by_user_id', $userId)
                                    ->where('status', 'approved')->count(),
                'cp_pending'  => ConcretePouring::where('noted_by_user_id', $userId)
                                    ->where('current_review_step', 'provincial_engineer')
                                    ->where('status', 'requested')->count(),
            ],

            default => [],
        };
    }
}