<?php

namespace App\Services;

use App\Mail\WorkRequestAssignedMail;
use App\Mail\WorkRequestDecisionMadeMail;
use App\Mail\WorkRequestReadyForDecisionMail;
use App\Mail\WorkRequestStepAdvancedMail;
use App\Mail\WorkRequestSubmittedMail;
use App\Models\Notification;
use App\Models\WorkRequest;
use App\Models\ConcretePouring;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    // ══════════════════════════════════════════════════════════════
    //  WORK REQUEST NOTIFICATIONS
    // ══════════════════════════════════════════════════════════════

    /**
     * Contractor submitted a new Work Request → notify all admins.
     */
    public static function workRequestSubmitted(WorkRequest $wr): void
    {
        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) return;

        Notification::send(
            $admins->pluck('id')->toArray(),
            'work_request',
            '📋 New Work Request Submitted',
            "Contractor \"{$wr->contractor_name}\" submitted a new work request for \"{$wr->name_of_project}\".",
            route('admin.work-requests.show', $wr),
            $wr
        );

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new WorkRequestSubmittedMail($wr));
            } catch (\Throwable $e) {
                Log::error('WorkRequestSubmittedMail failed', [
                    'to'    => $admin->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Admin assigned engineers → notify each assigned engineer.
     */
    public static function workRequestAssigned(WorkRequest $wr): void
    {
        $steps = [
            'assigned_site_inspector_id'      => ['role' => 'Site Inspector',      'step' => 'site_inspector'],
            'assigned_surveyor_id'             => ['role' => 'Surveyor',            'step' => 'surveyor'],
            'assigned_resident_engineer_id'    => ['role' => 'Resident Engineer',   'step' => 'resident_engineer'],
            'assigned_mtqa_id'                 => ['role' => 'MTQA',                'step' => 'mtqa'],
            'assigned_engineer_iv_id'          => ['role' => 'Engineer IV',         'step' => 'engineer_iv'],
            'assigned_engineer_iii_id'         => ['role' => 'Engineer III',        'step' => 'engineer_iii'],
            'assigned_provincial_engineer_id'  => ['role' => 'Provincial Engineer', 'step' => 'provincial_engineer'],
        ];

        foreach ($steps as $col => $info) {
            $userId = $wr->$col;
            if (!$userId) continue;

            $reviewer = User::find($userId);
            if (!$reviewer) continue;

            $isFirst = $wr->current_review_step === $info['step'];

            if ($isFirst) {
                Notification::send(
                    $userId,
                    'work_request',
                    '🔔 Work Request Assigned to You',
                    "You have been assigned as {$info['role']} for the work request \"{$wr->name_of_project}\". Please review it.",
                    route('reviewer.work-requests.show', $wr),
                    $wr
                );
            } else {
                Notification::send(
                    $userId,
                    'work_request',
                    '📌 You Are in the Review Queue',
                    "You have been queued as {$info['role']} for work request \"{$wr->name_of_project}\". You'll be notified when it's your turn.",
                    route('reviewer.work-requests.show', $wr),
                    $wr
                );
            }

            try {
                Mail::to($reviewer->email)->send(
                    new WorkRequestAssignedMail($wr, $info['role'], $isFirst)
                );
            } catch (\Throwable $e) {
                Log::error('WorkRequestAssignedMail failed', [
                    'to'    => $reviewer->email,
                    'role'  => $info['role'],
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * A reviewer completed their step → notify the next reviewer.
     * Call this AFTER $wr->advanceReviewStep() has been saved.
     */
    public static function workRequestStepAdvanced(WorkRequest $wr, string $completedByName, string $completedStep): void
    {
        $stepLabels = [
            'site_inspector'      => 'Site Inspector',
            'surveyor'            => 'Surveyor',
            'resident_engineer'   => 'Resident Engineer',
            'mtqa'                => 'MTQA',
            'engineer_iv'         => 'Engineer IV',
            'engineer_iii'        => 'Engineer III',
            'provincial_engineer' => 'Provincial Engineer',
            'admin_final'         => 'Admin Final Decision',
        ];

        $nextStep = $wr->current_review_step;

        if ($nextStep === 'admin_final') {
            $admins = User::where('role', 'admin')->get();

            Notification::send(
                $admins->pluck('id')->toArray(),
                'work_request',
                '✅ Work Request Ready for Final Decision',
                "Work request \"{$wr->name_of_project}\" has completed all reviews. Please make a final decision.",
                route('admin.work-requests.decision-form', $wr),
                $wr
            );

            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new WorkRequestReadyForDecisionMail($wr));
                } catch (\Throwable $e) {
                    Log::error('WorkRequestReadyForDecisionMail failed', [
                        'to'    => $admin->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return;
        }

        $col = WorkRequest::REVIEW_STEPS[$nextStep]['assigned_col'] ?? null;
        if (!$col || !$wr->$col) return;

        $nextReviewer  = User::find($wr->$col);
        if (!$nextReviewer) return;

        $nextStepLabel = $stepLabels[$nextStep]      ?? $nextStep;
        $prevLabel     = $stepLabels[$completedStep] ?? $completedStep;

        Notification::send(
            $wr->$col,
            'work_request',
            "🔔 It's Your Turn to Review",
            "{$prevLabel} \"{$completedByName}\" completed their review of \"{$wr->name_of_project}\". It's now your turn as {$nextStepLabel}.",
            route('reviewer.work-requests.show', $wr),
            $wr
        );

        try {
            Mail::to($nextReviewer->email)->send(
                new WorkRequestStepAdvancedMail($wr, $completedByName, $completedStep, $nextStepLabel)
            );
        } catch (\Throwable $e) {
            Log::error('WorkRequestStepAdvancedMail failed', [
                'to'    => $nextReviewer->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Provincial Engineer made the final decision → notify contractor (and MTQA if approved).
     */
    public static function workRequestDecisionMade(WorkRequest $wr): void
    {
        $contractor = User::where('name', $wr->contractor_name)
            ->where('role', 'contractor')
            ->first();

        if (!$contractor) return;

        $isApproved = $wr->status === WorkRequest::STATUS_APPROVED;
        $decision   = $isApproved ? 'Approved ✅' : 'Rejected ❌';
        $emoji      = $isApproved ? '🎉' : '😔';
        $statusWord = $isApproved ? 'approved' : 'rejected';

        // Notify assigned MTQA when approved — they can now print
        if ($isApproved && $wr->assigned_mtqa_id) {
            $mtqaUser = User::find($wr->assigned_mtqa_id);
            if ($mtqaUser) {
                Notification::send(
                    $mtqaUser->id,
                    'work_request',
                    '🖨️ Work Request Ready to Print',
                    "Work request \"{$wr->name_of_project}\" has been approved by the Provincial Engineer and is ready to print.",
                    route('reviewer.work-requests.show', $wr),
                    $wr
                );
            }
        }

        // Notify contractor of the outcome
        Notification::send(
            $contractor->id,
            'work_request',
            "{$emoji} Work Request {$decision}",
            "Your work request for \"{$wr->name_of_project}\" has been {$statusWord}." .
            ($wr->approved_recommendation_action ? " Remarks: {$wr->approved_recommendation_action}" : ''),
            route('user.work-requests.show', $wr),
            $wr
        );

        try {
            Mail::to($contractor->email)->send(new WorkRequestDecisionMadeMail($wr));
        } catch (\Throwable $e) {
            Log::error('WorkRequestDecisionMadeMail failed', [
                'to'    => $contractor->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // ══════════════════════════════════════════════════════════════
    //  CONCRETE POURING NOTIFICATIONS
    // ══════════════════════════════════════════════════════════════

    /**
     * Contractor submitted a new Concrete Pouring request.
     * → Notify: contractor (confirmation) + all admins (action needed).
     */
    public static function concretePouringSubmitted(ConcretePouring $cp): void
    {
        // Contractor confirmation
        Notification::send(
            $cp->requested_by_user_id,
            'concrete_pouring',
            '🏗️ Concrete Pouring Request Submitted',
            "Your concrete pouring request {$cp->reference_number} for \"{$cp->project_name}\" has been submitted and is awaiting admin assignment.",
            route('user.concrete-pouring.show', $cp->id),
            $cp
        );

        // All admins
        $adminIds = User::where('role', 'admin')->pluck('id')->toArray();
        if (empty($adminIds)) return;

        Notification::send(
            $adminIds,
            'concrete_pouring',
            '🏗️ New Concrete Pouring Request',
            "A new concrete pouring request {$cp->reference_number} for \"{$cp->project_name}\" has been submitted by {$cp->contractor} and is awaiting reviewer assignment.",
            route('admin.concrete-pouring.show', $cp->id),
            $cp
        );
    }

    /**
     * Contractor updated their Concrete Pouring request.
     * → Notify: contractor (confirmation) only.
     */
    public static function concretePouringUpdated(ConcretePouring $cp): void
    {
        Notification::send(
            $cp->requested_by_user_id,
            'concrete_pouring',
            '✏️ Concrete Pouring Request Updated',
            "Your concrete pouring request {$cp->reference_number} ({$cp->project_name}) has been updated successfully.",
            route('user.concrete-pouring.show', $cp->id),
            $cp
        );
    }

    /**
     * Contractor deleted their Concrete Pouring request.
     * → Notify: contractor (confirmation) only.
     *
     * Pass $contractorId separately since the record will be deleted before this runs.
     */
    public static function concretePouringDeleted(int $contractorId, string $referenceNumber, string $projectName): void
    {
        Notification::send(
            $contractorId,
            'concrete_pouring',
            '🗑️ Concrete Pouring Request Deleted',
            "Your concrete pouring request {$referenceNumber} ({$projectName}) has been deleted.",
            route('user.concrete-pouring.index'),
            null
        );
    }

    /**
     * Admin assigned reviewers to a Concrete Pouring request.
     * → Notify: contractor (under review) + each assigned reviewer (queued or active turn).
     */
    public static function concretePouringAssigned(ConcretePouring $cp): void
    {
        // Notify contractor that review has started
        Notification::send(
            $cp->requested_by_user_id,
            'concrete_pouring',
            '🔍 Concrete Pouring Request Under Review',
            "Your concrete pouring request {$cp->reference_number} ({$cp->project_name}) has been picked up by admin and reviewers have been assigned. The review process has begun.",
            route('user.concrete-pouring.show', $cp->id),
            $cp
        );

        $reviewerMeta = [
            'mtqa'                => ['col' => 'me_mtqa_user_id',           'label' => 'ME/MTQA'],
            'resident_engineer'   => ['col' => 'resident_engineer_user_id', 'label' => 'Resident Engineer'],
            'provincial_engineer' => ['col' => 'noted_by_user_id',          'label' => 'Provincial Engineer'],
        ];

        foreach ($reviewerMeta as $step => $meta) {
            $userId = $cp->{$meta['col']};
            if (!$userId) continue;

            $isFirst = $cp->current_review_step === $step;

            if ($isFirst) {
                Notification::send(
                    $userId,
                    'concrete_pouring',
                    '🔔 Action Required — Concrete Pouring Review',
                    "You have been assigned as {$meta['label']} for concrete pouring request {$cp->reference_number} ({$cp->project_name}). It is now your turn to review.",
                    route('reviewer.concrete-pouring.show', $cp->id),
                    $cp
                );
            } else {
                Notification::send(
                    $userId,
                    'concrete_pouring',
                    '📌 You Are in the Review Queue',
                    "You have been queued as {$meta['label']} for concrete pouring request {$cp->reference_number} ({$cp->project_name}). You'll be notified when it's your turn.",
                    route('reviewer.concrete-pouring.show', $cp->id),
                    $cp
                );
            }
        }
    }

    /**
     * A reviewer signed and submitted their step.
     * → Notify: all admins + every OTHER assigned reviewer (not the signer).
     * The next reviewer will be notified separately via concretePouringStepAdvanced().
     */
    public static function concretePouringSignatureSubmitted(
        ConcretePouring $cp,
        string          $roleLabel,
        int             $signerId
    ): void {
        $message = "{$roleLabel} has signed and submitted their review for concrete pouring request {$cp->reference_number} ({$cp->project_name}).";

        // All admins
        $adminIds = User::where('role', 'admin')->pluck('id')->toArray();
        if (!empty($adminIds)) {
            Notification::send(
                $adminIds,
                'concrete_pouring',
                "✍️ Signature Submitted — {$roleLabel}",
                $message,
                route('admin.concrete-pouring.show', $cp->id),
                $cp
            );
        }

        // Every OTHER assigned reviewer
        $reviewerCols = [
            'me_mtqa_user_id'           => 'ME/MTQA',
            'resident_engineer_user_id' => 'Resident Engineer',
            'noted_by_user_id'          => 'Provincial Engineer',
        ];

        foreach ($reviewerCols as $col => $label) {
            $reviewerId = $cp->$col;
            if ($reviewerId && $reviewerId != $signerId) {
                Notification::send(
                    $reviewerId,
                    'concrete_pouring',
                    "✍️ Signature Submitted — {$roleLabel}",
                    $message,
                    route('reviewer.concrete-pouring.show', $cp->id),
                    $cp
                );
            }
        }
    }

    /**
     * Review step advanced to the next reviewer.
     * → Notify: next reviewer (it's their turn).
     * If the next step is admin_final, notify all admins instead.
     */
    public static function concretePouringStepAdvanced(ConcretePouring $cp, string $completedStep): void
    {
        $nextStep = $cp->current_review_step;

        if ($nextStep === 'admin_final') {
            $adminIds = User::where('role', 'admin')->pluck('id')->toArray();
            if (!empty($adminIds)) {
                Notification::send(
                    $adminIds,
                    'concrete_pouring',
                    '✅ Concrete Pouring Ready for Final Decision',
                    "Concrete pouring request {$cp->reference_number} ({$cp->project_name}) has completed all reviews and is awaiting your final decision.",
                    route('admin.concrete-pouring.show', $cp->id),
                    $cp
                );
            }
            return;
        }

        $stepToCol = [
            'mtqa'                => 'me_mtqa_user_id',
            'resident_engineer'   => 'resident_engineer_user_id',
            'provincial_engineer' => 'noted_by_user_id',
        ];

        $stepLabels = [
            'mtqa'                => 'ME/MTQA',
            'resident_engineer'   => 'Resident Engineer',
            'provincial_engineer' => 'Provincial Engineer',
        ];

        $col = $stepToCol[$nextStep] ?? null;
        if (!$col || !$cp->$col) return;

        $nextLabel = $stepLabels[$nextStep] ?? $nextStep;

        Notification::send(
            $cp->$col,
            'concrete_pouring',
            '🔔 Action Required — Concrete Pouring Review',
            "It is now your turn as {$nextLabel} to review concrete pouring request {$cp->reference_number} ({$cp->project_name}).",
            route('reviewer.concrete-pouring.show', $cp->id),
            $cp
        );
    }

    /**
     * Provincial Engineer submitted their note — pipeline goes to admin_final.
     * → Notify: all admins + MTQA reviewer only.
     */
    public static function concretePouringReadyForDecision(ConcretePouring $cp): void
    {
        // All admins
        $adminIds = User::where('role', 'admin')->pluck('id')->toArray();
        if (!empty($adminIds)) {
            Notification::send(
                $adminIds,
                'concrete_pouring',
                '✅ Concrete Pouring Ready for Final Decision',
                "Concrete pouring request {$cp->reference_number} ({$cp->project_name}) has completed all reviews and is awaiting your final decision.",
                route('admin.concrete-pouring.show', $cp->id),
                $cp
            );
        }

        // MTQA reviewer (if assigned)
        if ($cp->me_mtqa_user_id) {
            Notification::send(
                $cp->me_mtqa_user_id,
                'concrete_pouring',
                '📋 Concrete Pouring Awaiting Final Decision',
                "Concrete pouring request {$cp->reference_number} ({$cp->project_name}) has been fully reviewed and is now awaiting admin final decision.",
                route('reviewer.concrete-pouring.show', $cp->id),
                $cp
            );
        }
    }

    /**
     * Admin approved the Concrete Pouring request.
     * → Notify: contractor + all assigned reviewers.
     */
    public static function concretePouringApproved(ConcretePouring $cp): void
    {
        $remarksNote = $cp->approval_remarks ? " Remarks: {$cp->approval_remarks}" : '';

        // Contractor
        Notification::send(
            $cp->requested_by_user_id,
            'concrete_pouring',
            '✅ Concrete Pouring Request Approved',
            "Your concrete pouring request {$cp->reference_number} ({$cp->project_name}) has been approved.{$remarksNote}",
            route('user.concrete-pouring.show', $cp->id),
            $cp
        );

        // All assigned reviewers
        self::notifyAllReviewers(
            $cp,
            '✅ Concrete Pouring Request Approved',
            "Concrete pouring request {$cp->reference_number} ({$cp->project_name}) that you reviewed has been approved by admin.{$remarksNote}"
        );
    }

    /**
     * Admin disapproved the Concrete Pouring request.
     * → Notify: contractor + all assigned reviewers.
     */
    public static function concretePouringDisapproved(ConcretePouring $cp): void
    {
        $remarksNote = $cp->approval_remarks ? " Remarks: {$cp->approval_remarks}" : '';

        // Contractor
        Notification::send(
            $cp->requested_by_user_id,
            'concrete_pouring',
            '❌ Concrete Pouring Request Disapproved',
            "Your concrete pouring request {$cp->reference_number} ({$cp->project_name}) has been disapproved.{$remarksNote}",
            route('user.concrete-pouring.show', $cp->id),
            $cp
        );

        // All assigned reviewers
        self::notifyAllReviewers(
            $cp,
            '❌ Concrete Pouring Request Disapproved',
            "Concrete pouring request {$cp->reference_number} ({$cp->project_name}) that you reviewed has been disapproved by admin.{$remarksNote}"
        );
    }

    // ── Private helper ────────────────────────────────────────────────────────

    /**
     * Send a notification to every assigned reviewer on a ConcretePouring.
     */
    private static function notifyAllReviewers(ConcretePouring $cp, string $title, string $message): void
    {
        $reviewerIds = collect([
            $cp->me_mtqa_user_id,
            $cp->resident_engineer_user_id,
            $cp->noted_by_user_id,
        ])->filter()->unique()->values()->toArray();

        if (empty($reviewerIds)) return;

        Notification::send(
            $reviewerIds,
            'concrete_pouring',
            $title,
            $message,
            route('reviewer.concrete-pouring.show', $cp->id),
            $cp
        );
    }
}