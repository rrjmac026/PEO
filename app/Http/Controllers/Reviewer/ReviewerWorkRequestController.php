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

    // ─────────────────────────────────────────────────────────────────────────
    // Site Inspector
    // ─────────────────────────────────────────────────────────────────────────
    public function storeInspection(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'findings_comments'        => 'nullable|string',
            'recommendation'           => 'nullable|string',
            // The hidden input contains either a base64 data-URI (drawn) or
            // the public URL of the saved signature (selected from profile).
            'site_inspector_signature' => 'nullable|string',
        ]);

        $workRequest->update([
            'inspected_by_site_inspector' => Auth::user()->name,
            'site_inspector_signature'    => $this->resolveSignatureValue(
                                                $request->input('site_inspector_signature')
                                            ),
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

    // ─────────────────────────────────────────────────────────────────────────
    // Surveyor
    // ─────────────────────────────────────────────────────────────────────────
    public function storeSurvey(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'findings_surveyor'       => 'nullable|string',
            'recommendation_surveyor' => 'nullable|string',
            'surveyor_signature'      => 'nullable|string',
        ]);

        $workRequest->update([
            'surveyor_name'           => Auth::user()->name,
            'surveyor_signature'      => $this->resolveSignatureValue(
                                            $request->input('surveyor_signature')
                                        ),
            'findings_surveyor'       => $request->findings_surveyor,
            'recommendation_surveyor' => $request->recommendation_surveyor,
        ]);

        $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
            'description' => 'Survey findings submitted by ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        return back()->with('success', 'Survey submitted successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MTQA
    // ─────────────────────────────────────────────────────────────────────────
    public function storeMtqaCheck(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'recommended_action' => 'nullable|string',
            'mtqa_signature'     => 'nullable|string',
        ]);

        $workRequest->update([
            'checked_by_mtqa'    => Auth::user()->name,
            'mtqa_signature'     => $this->resolveSignatureValue(
                                        $request->input('mtqa_signature')
                                    ),
            'recommended_action' => $request->recommended_action,
        ]);

        $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
            'description' => 'MTQA check submitted by ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        return back()->with('success', 'MTQA check submitted successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Resident Engineer
    // ─────────────────────────────────────────────────────────────────────────
    public function storeEngineerReview(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'findings_engineer'           => 'nullable|string',
            'recommendation_engineer'     => 'nullable|string',
            'resident_engineer_signature' => 'nullable|string',
        ]);

        $workRequest->update([
            'resident_engineer_name'      => Auth::user()->name,
            'resident_engineer_signature' => $this->resolveSignatureValue(
                                                $request->input('resident_engineer_signature')
                                            ),
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

    // ─────────────────────────────────────────────────────────────────────────
    // Provincial Engineer
    // ─────────────────────────────────────────────────────────────────────────
    public function storeProvincialNote(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'approved_notes'     => 'required|string',
            'approved_signature' => 'nullable|string',
        ]);

        $workRequest->update([
            'approved_by'        => Auth::user()->name,
            'approved_notes'     => $request->approved_notes,
            'approved_signature' => $this->resolveSignatureValue(
                                        $request->input('approved_signature')
                                    ),
            'status'             => WorkRequest::STATUS_APPROVED,
        ]);

        $workRequest->addLog(WorkRequestLog::EVENT_APPROVED, [
            'description' => 'Provincial engineer note added by ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        return back()->with('success', 'Note added successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Normalise the value coming from the signature hidden input so that the
     * database always holds something the PDF generator can consume:
     *
     *  - If the user drew a signature  → the hidden input holds a base64
     *    data-URI ("data:image/png;base64,…"). Store it as-is.
     *
     *  - If the user selected their saved profile signature → the hidden input
     *    holds the full public URL (asset('storage/…')).  Convert it back to
     *    a storage-relative path ("signatures/file.png") so the PDF can
     *    resolve it via storage_path().
     *
     *  - If empty / null → return null.
     */
    private function resolveSignatureValue(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Already a base64 data-URI — store as-is
        if (str_starts_with($value, 'data:image')) {
            return $value;
        }

        // Full URL pointing to our own storage — convert to relative path
        $storageUrl = url('storage') . '/';
        if (str_starts_with($value, $storageUrl)) {
            return ltrim(substr($value, strlen($storageUrl)), '/');
        }

        // Also handle /storage/... relative URLs
        if (str_starts_with($value, '/storage/')) {
            return ltrim(substr($value, strlen('/storage/')), '/');
        }

        // Fallback: store whatever was sent (handles already-relative paths)
        return $value;
    }
}