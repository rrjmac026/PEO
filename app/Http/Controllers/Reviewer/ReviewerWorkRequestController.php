<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\WorkRequest;
use App\Models\WorkRequestLog;
use App\Models\WorkRequestRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewerWorkRequestController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Index — show requests where THIS user is (or was) a reviewer
    // For MTQA: also shows approved requests they are assigned to (for printing)
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = WorkRequest::where(function($q) use ($user) {
            $q->where('assigned_site_inspector_id', $user->id)
            ->orWhere('assigned_surveyor_id', $user->id)
            ->orWhere('assigned_resident_engineer_id', $user->id)
            ->orWhere('assigned_mtqa_id', $user->id)
            ->orWhere('assigned_engineer_iv_id', $user->id)
            ->orWhere('assigned_engineer_iii_id', $user->id)
            ->orWhere('assigned_provincial_engineer_id', $user->id);
        });

        // MTQA also needs to see requests approved by the Provincial Engineer
        // (for printing), even if MTQA's own step is otherwise "done."
        // Since assigned_mtqa_id is already covered by the base query above,
        // this is effectively already included — no special-case needed.

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

        $workRequests = $query->latest()->paginate(15)->withQueryString();

        $completedQuery = $this->completedByUser($user)->latest()->limit(10)->get();

        return view('reviewer.work-requests.index', compact('workRequests', 'completedQuery'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Approved Work Requests — MTQA dedicated page
    // ─────────────────────────────────────────────────────────────────────────

    public function approvedIndex(Request $request)
    {
        $user = Auth::user();

        $query = WorkRequest::where('status', WorkRequest::STATUS_APPROVED);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name_of_project', 'LIKE', "%{$request->search}%")
                  ->orWhere('project_location', 'LIKE', "%{$request->search}%")
                  ->orWhere('contractor_name', 'LIKE', "%{$request->search}%");
            });
        }

        $workRequests = $query->latest()->paginate(15)->withQueryString();

        return view('reviewer.work-requests.approved', compact('workRequests'));
    }

    public function show(WorkRequest $workRequest)
    {
        $user = Auth::user();

        $isAssignedAnywhere = $this->userIsAssignedAnywhere($workRequest, $user);

        $isMtqaViewer = ($user->role === 'mtqa' && $workRequest->status === WorkRequest::STATUS_APPROVED);

        if (! $isAssignedAnywhere && ! $isMtqaViewer) {
            abort(403, 'You are not assigned to this work request.');
        }

        return view('reviewer.work-requests.show', [
            'workRequest' => $workRequest,
            'role'        => $user->role,
            'isMyTurn'    => $workRequest->isCurrentReviewer($user),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Site Inspector — unchanged (no recommendation-history / revision loop)
    // ─────────────────────────────────────────────────────────────────────────

    public function storeInspection(Request $request, WorkRequest $workRequest)
    {
        $this->authorizeStep($workRequest, 'site_inspector');

        $request->validate([
            'findings_comments'        => 'nullable|string',
            'recommendation'           => 'nullable|string',
            'site_inspector_signature' => 'nullable|string',
        ]);

        $workRequest->update([
            'inspected_by_site_inspector' => Auth::user()->name,
            'site_inspector_signature'    => $this->resolveSignatureValue($request->input('site_inspector_signature')),
            'findings_comments'           => $request->findings_comments,
            'recommendation'              => $request->recommendation,
            'status'                      => WorkRequest::STATUS_INSPECTED,
        ]);

        $workRequest->addLog(WorkRequestLog::EVENT_INSPECTED, [
            'description' => 'Site inspection submitted by ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        $workRequest->advanceReviewStep();
        $this->notifyNextReviewer($workRequest, 'site_inspector');

        return back()->with('success', 'Inspection submitted. Request forwarded to next reviewer.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Surveyor — unchanged (no recommendation-history / revision loop)
    // ─────────────────────────────────────────────────────────────────────────

    public function storeSurvey(Request $request, WorkRequest $workRequest)
    {
        $this->authorizeStep($workRequest, 'surveyor');

        $request->validate([
            'findings_surveyor'       => 'nullable|string',
            'recommendation_surveyor' => 'nullable|string',
            'surveyor_signature'      => 'nullable|string',
        ]);

        $workRequest->update([
            'surveyor_name'           => Auth::user()->name,
            'surveyor_signature'      => $this->resolveSignatureValue($request->input('surveyor_signature')),
            'findings_surveyor'       => $request->findings_surveyor,
            'recommendation_surveyor' => $request->recommendation_surveyor,
        ]);

        $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
            'description' => 'Survey submitted by ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        $workRequest->advanceReviewStep();
        $this->notifyNextReviewer($workRequest, 'surveyor');

        return back()->with('success', 'Survey submitted. Request forwarded to next reviewer.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Resident Engineer — NOW WITH RECOMMENDATION HISTORY + REVISION LOOP
    // ─────────────────────────────────────────────────────────────────────────

    public function storeEngineerReview(Request $request, WorkRequest $workRequest)
    {
        $this->authorizeStep($workRequest, 'resident_engineer');

        $request->validate([
            'findings_engineer'           => 'nullable|string',
            'recommendation_engineer'     => 'required|string',
            'resident_engineer_signature' => 'nullable|string',
        ]);

        $signature = $this->resolveSignatureValue($request->input('resident_engineer_signature'));
        $isSigned  = ! empty($signature);

        // Always record the recommendation in history
        WorkRequestRecommendation::create([
            'work_request_id'     => $workRequest->id,
            'step'                => 'resident_engineer',
            'user_id'             => Auth::id(),
            'recommendation_text' => $request->recommendation_engineer,
            'signature'           => $signature,
            'is_signed'           => $isSigned,
        ]);

        $workRequest->update([
            'resident_engineer_name'      => Auth::user()->name,
            'resident_engineer_signature' => $signature,
            'findings_engineer'           => $request->findings_engineer,
            'recommendation_engineer'     => $request->recommendation_engineer,
        ]);

        if ($isSigned) {
            $workRequest->update(['status' => WorkRequest::STATUS_REVIEWED]);

            $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
                'description' => 'Resident engineer review submitted and signed by ' . Auth::user()->name,
                'user_id'     => Auth::id(),
            ]);

            $workRequest->advanceReviewStep();
            $this->notifyNextReviewer($workRequest, 'resident_engineer');

            return back()->with('success', 'Engineer review submitted. Request forwarded to next reviewer.');
        }

        // Not signed — send back to contractor for revision
        $workRequest->update(['status' => WorkRequest::STATUS_NEEDS_REVISION]);

        $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
            'description' => 'Resident engineer requested revision (unsigned) — ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        \App\Services\NotificationService::workRequestNeedsRevision($workRequest, 'resident_engineer');

        return back()->with('success', 'Recommendation saved. Request sent back to contractor for revision.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MTQA — NOW WITH RECOMMENDATION HISTORY + REVISION LOOP
    // ─────────────────────────────────────────────────────────────────────────

    public function storeMtqaCheck(Request $request, WorkRequest $workRequest)
    {
        $this->authorizeStep($workRequest, 'mtqa');

        $request->validate([
            'recommended_action' => 'required|string',
            'mtqa_signature'     => 'nullable|string',
        ]);

        $signature = $this->resolveSignatureValue($request->input('mtqa_signature'));
        $isSigned  = ! empty($signature);

        WorkRequestRecommendation::create([
            'work_request_id'     => $workRequest->id,
            'step'                => 'mtqa',
            'user_id'             => Auth::id(),
            'recommendation_text' => $request->recommended_action,
            'signature'           => $signature,
            'is_signed'           => $isSigned,
        ]);

        $workRequest->update([
            'checked_by_mtqa'    => Auth::user()->name,
            'mtqa_signature'     => $signature,
            'recommended_action' => $request->recommended_action,
        ]);

        if ($isSigned) {
            $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
                'description' => 'MTQA check submitted and signed by ' . Auth::user()->name,
                'user_id'     => Auth::id(),
            ]);

            $workRequest->advanceReviewStep();
            $this->notifyNextReviewer($workRequest, 'mtqa');

            return back()->with('success', 'MTQA check submitted. Request forwarded to next reviewer.');
        }

        $workRequest->update(['status' => WorkRequest::STATUS_NEEDS_REVISION]);

        $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
            'description' => 'MTQA requested revision (unsigned) — ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        \App\Services\NotificationService::workRequestNeedsRevision($workRequest, 'mtqa');

        return back()->with('success', 'Recommendation saved. Request sent back to contractor for revision.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Engineer IV — NOW WITH RECOMMENDATION HISTORY + REVISION LOOP
    // ─────────────────────────────────────────────────────────────────────────

    public function storeEngineerIvReview(Request $request, WorkRequest $workRequest)
    {
        $this->authorizeStep($workRequest, 'engineer_iv');

        $request->validate([
            'reviewed_by_recommendation_action' => 'required|string',
            'reviewer_signature'                => 'nullable|string',
        ]);

        $signature = $this->resolveSignatureValue($request->input('reviewer_signature'));
        $isSigned  = ! empty($signature);

        WorkRequestRecommendation::create([
            'work_request_id'     => $workRequest->id,
            'step'                => 'engineer_iv',
            'user_id'             => Auth::id(),
            'recommendation_text' => $request->reviewed_by_recommendation_action,
            'signature'           => $signature,
            'is_signed'           => $isSigned,
        ]);

        $workRequest->update([
            'reviewed_by'                       => Auth::user()->name,
            'reviewer_signature'                => $signature,
            'reviewed_by_recommendation_action' => $request->reviewed_by_recommendation_action,
        ]);

        if ($isSigned) {
            $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
                'description' => 'Engineer IV review submitted and signed by ' . Auth::user()->name,
                'user_id'     => Auth::id(),
            ]);

            $workRequest->advanceReviewStep();
            $this->notifyNextReviewer($workRequest, 'engineer_iv');

            return back()->with('success', 'Engineer IV review submitted. Request forwarded to next reviewer.');
        }

        $workRequest->update(['status' => WorkRequest::STATUS_NEEDS_REVISION]);

        $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
            'description' => 'Engineer IV requested revision (unsigned) — ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        \App\Services\NotificationService::workRequestNeedsRevision($workRequest, 'engineer_iv');

        return back()->with('success', 'Recommendation saved. Request sent back to contractor for revision.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Engineer III — NOW WITH RECOMMENDATION HISTORY + REVISION LOOP
    // ─────────────────────────────────────────────────────────────────────────

    public function storeRecommendingApproval(Request $request, WorkRequest $workRequest)
    {
        $this->authorizeStep($workRequest, 'engineer_iii');

        $request->validate([
            'eiii_notes'     => 'required|string',
            'eiii_signature' => 'nullable|string',
        ]);

        $signature = $this->resolveSignatureValue($request->input('eiii_signature'));
        $isSigned  = ! empty($signature);

        WorkRequestRecommendation::create([
            'work_request_id'     => $workRequest->id,
            'step'                => 'engineer_iii',
            'user_id'             => Auth::id(),
            'recommendation_text' => $request->eiii_notes,
            'signature'           => $signature,
            'is_signed'           => $isSigned,
        ]);

        $workRequest->update([
            'recommending_approval_by'                    => Auth::user()->name,
            'recommending_approval_recommendation_action' => $request->eiii_notes,
            'recommending_approval_signature'             => $signature,
        ]);

        if ($isSigned) {
            $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
                'description' => 'Engineer III recommending approval submitted and signed by ' . Auth::user()->name,
                'user_id'     => Auth::id(),
            ]);

            $workRequest->advanceReviewStep();
            $this->notifyNextReviewer($workRequest, 'engineer_iii');

            return back()->with('success', 'Recommending approval submitted. Request forwarded to Provincial Engineer.');
        }

        $workRequest->update(['status' => WorkRequest::STATUS_NEEDS_REVISION]);

        $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
            'description' => 'Engineer III requested revision (unsigned) — ' . Auth::user()->name,
            'user_id'     => Auth::id(),
        ]);

        \App\Services\NotificationService::workRequestNeedsRevision($workRequest, 'engineer_iii');

        return back()->with('success', 'Recommendation saved. Request sent back to contractor for revision.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Provincial Engineer — FINAL DECISION (approve / reject / request revision)
    // ─────────────────────────────────────────────────────────────────────────

    public function storeProvincialDecision(Request $request, WorkRequest $workRequest)
    {
        $this->authorizeStep($workRequest, 'provincial_engineer');

        $request->validate([
            'decision'                       => 'required|in:approved,rejected,needs_revision',
            'approved_recommendation_action' => 'required|string|max:2000',
            'approved_signature'             => 'nullable|string',
        ]);

        $signature = $this->resolveSignatureValue($request->input('approved_signature'));
        $isSigned  = ! empty($signature);

        // Always record in history
        WorkRequestRecommendation::create([
            'work_request_id'     => $workRequest->id,
            'step'                => 'provincial_engineer',
            'user_id'             => Auth::id(),
            'recommendation_text' => $request->approved_recommendation_action,
            'signature'           => $signature,
            'is_signed'           => $isSigned,
        ]);

        // If PE explicitly picks "needs_revision", OR picks approve/reject but didn't sign,
        // treat it as a bounce-back to the contractor.
        if ($request->decision === 'needs_revision' || ! $isSigned) {
            $workRequest->update([
                'approved_by'                    => Auth::user()->name,
                'approved_recommendation_action' => $request->approved_recommendation_action,
                'approved_signature'             => $signature,
                'status'                         => WorkRequest::STATUS_NEEDS_REVISION,
            ]);

            $workRequest->addLog(WorkRequestLog::EVENT_REVIEWED, [
                'description' => 'Provincial Engineer requested revision (unsigned) — ' . Auth::user()->name,
                'user_id'     => Auth::id(),
            ]);

            \App\Services\NotificationService::workRequestNeedsRevision($workRequest, 'provincial_engineer');

            return back()->with('success', 'Recommendation saved. Request sent back to contractor for revision.');
        }

        // Signed AND a real decision was made — finalize
        $newStatus = $request->decision === 'approved'
            ? WorkRequest::STATUS_APPROVED
            : WorkRequest::STATUS_REJECTED;

        $workRequest->update([
            'approved_by'                    => Auth::user()->name,
            'approved_recommendation_action' => $request->approved_recommendation_action,
            'approved_signature'             => $signature,
            'status'                         => $newStatus,
            'current_review_step'            => null,
            'accepted_date' => $request->decision === 'approved' ? now()->toDateString() : null,
            'accepted_time' => $request->decision === 'approved' ? now()->format('H:i:s') : null,
        ]);

        \App\Services\NotificationService::workRequestDecisionMade($workRequest);

        $message = $request->decision === 'approved'
            ? 'Work request approved successfully. MTQA has been notified and can now print.'
            : 'Work request rejected.';

        return back()->with('success', $message);
    }

    public function printApproved(WorkRequest $workRequest)
    {
        $user = Auth::user();

        if (! $this->userIsAssignedAnywhere($workRequest, $user)) {
            abort(403, 'You are not assigned to this work request.');
        }

        if ($workRequest->status !== WorkRequest::STATUS_APPROVED) {
            abort(403, 'This work request has not been approved yet.');
        }

        $pdf = new \App\Services\WorkRequestPdf($workRequest);
        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="work-request-' . $workRequest->id . '.pdf"',
        ]);
    }

    public function downloadApproved(WorkRequest $workRequest)
    {
        $user = Auth::user();

        if (! $this->userIsAssignedAnywhere($workRequest, $user)) {
            abort(403, 'You are not assigned to this work request.');
        }

        if ($workRequest->status !== WorkRequest::STATUS_APPROVED) {
            abort(403, 'This work request has not been approved yet.');
        }

        $pdf = new \App\Services\WorkRequestPdf($workRequest);
        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="work-request-' . $workRequest->id . '.pdf"',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function authorizeStep(WorkRequest $workRequest, string $step): void
    {
        $user = Auth::user();

        if ($workRequest->current_review_step !== $step) {
            abort(403, 'It is not your turn to review this request. Current step: ' . $workRequest->current_step_label);
        }

        $col = WorkRequest::REVIEW_STEPS[$step]['assigned_col'] ?? null;

        if ($col && $workRequest->$col != $user->id) {
            abort(403, 'You are not the assigned reviewer for this step.');
        }
    }

    private function userIsAssignedAnywhere(WorkRequest $workRequest, $user): bool
    {
        $cols = [
            'assigned_site_inspector_id',
            'assigned_surveyor_id',
            'assigned_resident_engineer_id',
            'assigned_mtqa_id',
            'assigned_engineer_iv_id',
            'assigned_engineer_iii_id',
            'assigned_provincial_engineer_id',
        ];

        foreach ($cols as $col) {
            if ($workRequest->$col == $user->id) {
                return true;
            }
        }

        return false;
    }

    private function completedByUser($user)
    {
        return WorkRequest::where(function ($q) use ($user) {
            $q->where('assigned_site_inspector_id', $user->id)
              ->whereNotNull('inspected_by_site_inspector');
        })->orWhere(function ($q) use ($user) {
            $q->where('assigned_surveyor_id', $user->id)
              ->whereNotNull('surveyor_name');
        })->orWhere(function ($q) use ($user) {
            $q->where('assigned_resident_engineer_id', $user->id)
              ->whereNotNull('resident_engineer_name');
        })->orWhere(function ($q) use ($user) {
            $q->where('assigned_mtqa_id', $user->id)
              ->whereNotNull('checked_by_mtqa');
        })->orWhere(function ($q) use ($user) {
            $q->where('assigned_engineer_iv_id', $user->id)
              ->whereNotNull('reviewed_by');
        })->orWhere(function ($q) use ($user) {
            $q->where('assigned_engineer_iii_id', $user->id)
              ->whereNotNull('recommending_approval_by');
        })->orWhere(function ($q) use ($user) {
            $q->where('assigned_provincial_engineer_id', $user->id)
              ->whereNotNull('approved_by');
        });
    }

    private function resolveSignatureValue(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'data:image')) {
            return $value;
        }

        $storageUrl = url('storage') . '/';
        if (str_starts_with($value, $storageUrl)) {
            return ltrim(substr($value, strlen($storageUrl)), '/');
        }

        if (str_starts_with($value, '/storage/')) {
            return ltrim(substr($value, strlen('/storage/')), '/');
        }

        return $value;
    }

    private function notifyNextReviewer(WorkRequest $workRequest, string $completedStep): void
    {
        \App\Services\NotificationService::workRequestStepAdvanced(
            $workRequest,
            Auth::user()->name,
            $completedStep
        );
    }
}