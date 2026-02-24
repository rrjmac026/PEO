<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\WorkRequest;
use App\Models\WorkRequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewerWorkRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkRequest::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name_of_project', 'LIKE', "%{$request->search}%")
                  ->orWhere('project_location', 'LIKE', "%{$request->search}%")
                  ->orWhere('contractor_name', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('requested_work_start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('requested_work_start_date', '<=', $request->date_to);
        }

        if ($request->filled('inspected')) {
            $request->input('inspected') === 'pending'
                ? $query->whereNull('inspected_by_site_inspector')
                : $query->whereNotNull('inspected_by_site_inspector');
        }

        if ($request->filled('surveyed')) {
            $request->input('surveyed') === 'pending'
                ? $query->whereNull('surveyor_name')
                : $query->whereNotNull('surveyor_name');
        }

        if ($request->filled('reviewed')) {
            $request->input('reviewed') === 'pending'
                ? $query->whereNull('resident_engineer_name')
                : $query->whereNotNull('resident_engineer_name');
        }

        if ($request->filled('noted')) {
            $request->input('noted') === 'pending'
                ? $query->whereNull('approved_notes')
                : $query->whereNotNull('approved_notes');
        }

        $workRequests = $query->latest()->paginate(15)->withQueryString();

        return view('reviewer.work-requests.index', compact('workRequests'));
    }

    public function show(WorkRequest $workRequest)
    {
        $role = Auth::user()->role;
        return view('reviewer.work-requests.show', compact('workRequest', 'role'));
    }

    public function storeInspection(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'findings_comments' => 'nullable|string',
            'recommendation'    => 'nullable|string',
        ]);

        $workRequest->update([
            'inspected_by_site_inspector' => Auth::user()->name,
            'site_inspector_signature'    => $request->site_inspector_signature, // from form now
            'findings_comments'           => $request->findings_comments,
            'recommendation'              => $request->recommendation,
            'status'                      => WorkRequest::STATUS_INSPECTED,
        ]);

        $workRequest->addLog(WorkRequestLog::EVENT_INSPECTED, [
            'description' => 'Inspection findings submitted by ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        return back()->with('success', 'Inspection submitted successfully.');
    }

    public function storeSurvey(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'findings_surveyor'       => 'nullable|string',
            'recommendation_surveyor' => 'nullable|string',
        ]);

        $workRequest->update([
            'surveyor_name'           => Auth::user()->name,
            'surveyor_signature'      => Auth::user()->signature_path,
            'findings_surveyor'       => $request->findings_surveyor,
            'recommendation_surveyor' => $request->recommendation_surveyor,
        ]);

        $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
            'description' => 'Survey findings submitted by ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        return back()->with('success', 'Survey submitted successfully.');
    }

    public function storeEngineerReview(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'findings_engineer'       => 'nullable|string',
            'recommendation_engineer' => 'nullable|string',
        ]);

        $workRequest->update([
            'resident_engineer_name'      => Auth::user()->name,
            'resident_engineer_signature' => Auth::user()->signature_path,
            'findings_engineer'           => $request->findings_engineer,
            'recommendation_engineer'     => $request->recommendation_engineer,
            'status'                      => WorkRequest::STATUS_REVIEWED,
        ]);

        $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
            'description' => 'Resident engineer review submitted by ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        return back()->with('success', 'Engineer review submitted successfully.');
    }

    public function storeProvincialNote(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'approved_notes' => 'required|string',
        ]);

        $workRequest->update([
            'approved_notes' => $request->approved_notes,
            'status'         => WorkRequest::STATUS_APPROVED,
        ]);

        $workRequest->addLog(WorkRequestLog::EVENT_APPROVED, [
            'description' => 'Provincial engineer note added by ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        return back()->with('success', 'Note added successfully.');
    }
}