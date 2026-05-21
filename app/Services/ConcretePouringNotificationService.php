<?php

namespace App\Services;

use App\Mail\ConcretePouringApprovedMail;
use App\Mail\ConcretePouringAssignedMail;
use App\Mail\ConcretePouringDisapprovedMail;
use App\Mail\ConcretePouringStepAdvancedMail;
use App\Mail\ConcretePouringSubmittedMail;
use App\Models\Notification;
use App\Models\ConcretePouring;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ConcretePouringNotificationService
{
    /**
     * Contractor submitted a new Concrete Pouring request.
     * → Notify: contractor (confirmation) + all admins (action needed).
     */
    public static function submitted(ConcretePouring $cp): void
    {
        // Contractor confirmation
        Notification::send(
            $cp->requested_by_user_id,
            'concrete_pouring',
            '🏗️ Concrete Pouring Request Submitted',
            "Your concrete pouring request {$cp->contract_number} for \"{$cp->project_name}\" has been submitted and is awaiting admin assignment.",
            route('user.concrete-pouring.show', $cp->id),
            $cp
        );

        // All admins — in-app + email
        $admins = User::where('role', 'admin')->get();
        if ($admins->isEmpty()) return;

        Notification::send(
            $admins->pluck('id')->toArray(),
            'concrete_pouring',
            '🏗️ New Concrete Pouring Request',
            "A new concrete pouring request {$cp->contract_number} for \"{$cp->project_name}\" has been submitted by {$cp->contractor} and is awaiting reviewer assignment.",
            route('admin.concrete-pouring.show', $cp->id),
            $cp
        );

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->queue(new ConcretePouringSubmittedMail($cp));
            } catch (\Throwable $e) {
                Log::error('ConcretePouringSubmittedMail failed', [
                    'to'    => $admin->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Contractor updated their Concrete Pouring request.
     * → Notify: contractor (confirmation) only.
     */
    public static function updated(ConcretePouring $cp): void
    {
        Notification::send(
            $cp->requested_by_user_id,
            'concrete_pouring',
            '✏️ Concrete Pouring Request Updated',
            "Your concrete pouring request {$cp->contract_number} ({$cp->project_name}) has been updated successfully.",
            route('user.concrete-pouring.show', $cp->id),
            $cp
        );
        // No email on update — in-app only, same as before.
    }

    /**
     * Contractor deleted their Concrete Pouring request.
     * → Notify: contractor (confirmation) only.
     *
     * Pass $contractorId separately since the record will be deleted before this runs.
     */
    public static function deleted(int $contractorId, string $contractNumber, string $projectName): void
    {
        Notification::send(
            $contractorId,
            'concrete_pouring',
            '🗑️ Concrete Pouring Request Deleted',
            "Your concrete pouring request {$contractNumber} ({$projectName}) has been deleted.",
            route('user.concrete-pouring.index'),
            null
        );
        // No email on delete — record is gone, no model to pass to a Mailable.
    }

    /**
     * Admin assigned reviewers to a Concrete Pouring request.
     * → Notify: contractor (under review) + each assigned reviewer (queued or active turn).
     */
    public static function assigned(ConcretePouring $cp): void
    {
        // Notify contractor that review has started
        Notification::send(
            $cp->requested_by_user_id,
            'concrete_pouring',
            '🔍 Concrete Pouring Request Under Review',
            "Your concrete pouring request {$cp->contract_number} ({$cp->project_name}) has been picked up by admin and reviewers have been assigned. The review process has begun.",
            route('user.concrete-pouring.show', $cp->id),
            $cp
        );

        $reviewerMeta = [
            'resident_engineer'   => ['col' => 'resident_engineer_user_id', 'label' => 'Resident Engineer'],
            'provincial_engineer' => ['col' => 'noted_by_user_id',          'label' => 'Provincial Engineer'],
            'mtqa'                => ['col' => 'me_mtqa_user_id',           'label' => 'ME/MTQA'],
        ];

        foreach ($reviewerMeta as $step => $meta) {
            $userId = $cp->{$meta['col']};
            if (!$userId) continue;

            $reviewer = User::find($userId);
            if (!$reviewer) continue;

            $isFirst = $cp->current_review_step === $step;

            // In-app notification
            if ($isFirst) {
                Notification::send(
                    $userId,
                    'concrete_pouring',
                    '🔔 Action Required — Concrete Pouring Review',
                    "You have been assigned as {$meta['label']} for concrete pouring request {$cp->contract_number} ({$cp->project_name}). It is now your turn to review.",
                    route('reviewer.concrete-pouring.show', $cp->id),
                    $cp
                );
            } else {
                Notification::send(
                    $userId,
                    'concrete_pouring',
                    '📌 You Are in the Review Queue',
                    "You have been queued as {$meta['label']} for concrete pouring request {$cp->contract_number} ({$cp->project_name}). You'll be notified when it's your turn.",
                    route('reviewer.concrete-pouring.show', $cp->id),
                    $cp
                );
            }

            // Email notification
            try {
                Mail::to($reviewer->email)->queue(
                    new ConcretePouringAssignedMail($cp, $meta['label'], $isFirst)
                );
            } catch (\Throwable $e) {
                Log::error('ConcretePouringAssignedMail failed', [
                    'to'    => $reviewer->email,
                    'role'  => $meta['label'],
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * A reviewer signed and submitted their step.
     * → Notify: all admins + every OTHER assigned reviewer (not the signer).
     */
    public static function signatureSubmitted(
        ConcretePouring $cp,
        string          $roleLabel,
        int             $signerId
    ): void {
        $message = "{$roleLabel} has signed and submitted their review for concrete pouring request {$cp->contract_number} ({$cp->project_name}).";

        // All admins — in-app only (no dedicated mail for this event)
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

        // Every OTHER assigned reviewer — in-app only
        $reviewerCols = [
            'resident_engineer_user_id' => 'Resident Engineer',
            'noted_by_user_id'          => 'Provincial Engineer',
            'me_mtqa_user_id'           => 'ME/MTQA',
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
     * → Notify next reviewer: in-app + email.
     */
    public static function stepAdvanced(ConcretePouring $cp, string $completedStep = ''): void
    {
        $nextStep = $cp->current_review_step;

        // No next step — MTQA has made the final decision; nothing to notify here.
        if (is_null($nextStep)) {
            return;
        }

        $stepToCol = [
            'resident_engineer'   => 'resident_engineer_user_id',
            'provincial_engineer' => 'noted_by_user_id',
            'mtqa'                => 'me_mtqa_user_id',
        ];

        $stepLabels = [
            'resident_engineer'   => 'Resident Engineer',
            'provincial_engineer' => 'Provincial Engineer',
            'mtqa'                => 'ME/MTQA (Final Decision)',
        ];

        $col = $stepToCol[$nextStep] ?? null;
        if (!$col || !$cp->$col) return;

        $nextReviewer = User::find($cp->$col);
        if (!$nextReviewer) return;

        $nextLabel      = $stepLabels[$nextStep]      ?? $nextStep;
        $completedLabel = $stepLabels[$completedStep] ?? $completedStep;

        $isFinal = $nextStep === 'mtqa';
        $title   = $isFinal
            ? '✅ Concrete Pouring Ready for Final Decision'
            : '🔔 Action Required — Concrete Pouring Review';
        $body    = $isFinal
            ? "Concrete pouring request {$cp->contract_number} ({$cp->project_name}) has completed all reviews and is awaiting your final decision as {$nextLabel}."
            : "It is now your turn as {$nextLabel} to review concrete pouring request {$cp->contract_number} ({$cp->project_name}).";

        // In-app notification
        Notification::send(
            $cp->$col,
            'concrete_pouring',
            $title,
            $body,
            route('reviewer.concrete-pouring.show', $cp->id),
            $cp
        );

        // Email notification
        try {
            Mail::to($nextReviewer->email)->queue(
                new ConcretePouringStepAdvancedMail(
                    $cp,
                    // completedByName — resolve from the completed step's assigned user
                    self::resolveReviewerName($cp, $completedStep),
                    $completedStep,
                    $nextLabel
                )
            );
        } catch (\Throwable $e) {
            Log::error('ConcretePouringStepAdvancedMail failed', [
                'to'    => $nextReviewer->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Provincial Engineer submitted their note — pipeline goes to MTQA.
     * → Notify: all admins + MTQA reviewer.
     * (This is now handled by stepAdvanced(); kept for backwards compat.)
     */
    public static function readyForDecision(ConcretePouring $cp): void
    {
        // All admins — in-app only
        $adminIds = User::where('role', 'admin')->pluck('id')->toArray();
        if (!empty($adminIds)) {
            Notification::send(
                $adminIds,
                'concrete_pouring',
                '✅ Concrete Pouring Ready for Final Decision',
                "Concrete pouring request {$cp->contract_number} ({$cp->project_name}) has completed all reviews and is awaiting your final decision.",
                route('admin.concrete-pouring.show', $cp->id),
                $cp
            );
        }

        // MTQA reviewer — in-app only (stepAdvanced() already sends the email)
        if ($cp->me_mtqa_user_id) {
            Notification::send(
                $cp->me_mtqa_user_id,
                'concrete_pouring',
                '📋 Concrete Pouring Awaiting Final Decision',
                "Concrete pouring request {$cp->contract_number} ({$cp->project_name}) has been fully reviewed and is now awaiting admin final decision.",
                route('reviewer.concrete-pouring.show', $cp->id),
                $cp
            );
        }
    }

    /**
     * Concrete Pouring request approved.
     * → Notify: contractor + all assigned reviewers (in-app + email).
     */
    public static function approved(ConcretePouring $cp): void
    {
        $remarksNote = $cp->approval_remarks ? " Remarks: {$cp->approval_remarks}" : '';

        // Contractor — in-app
        Notification::send(
            $cp->requested_by_user_id,
            'concrete_pouring',
            '✅ Concrete Pouring Request Approved',
            "Your concrete pouring request {$cp->contract_number} ({$cp->project_name}) has been approved.{$remarksNote}",
            route('user.concrete-pouring.show', $cp->id),
            $cp
        );

        // Contractor — email
        $contractor = User::find($cp->requested_by_user_id);
        if ($contractor) {
            try {
                Mail::to($contractor->email)->queue(new ConcretePouringApprovedMail($cp));
            } catch (\Throwable $e) {
                Log::error('ConcretePouringApprovedMail (contractor) failed', [
                    'to'    => $contractor->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // All assigned reviewers — in-app + email
        self::notifyAllReviewers(
            $cp,
            '✅ Concrete Pouring Request Approved',
            "Concrete pouring request {$cp->contract_number} ({$cp->project_name}) that you reviewed has been approved.{$remarksNote}",
            new ConcretePouringApprovedMail($cp)
        );
    }

    /**
     * Concrete Pouring request disapproved.
     * → Notify: contractor + all assigned reviewers (in-app + email).
     */
    public static function disapproved(ConcretePouring $cp): void
    {
        $remarksNote = $cp->approval_remarks ? " Remarks: {$cp->approval_remarks}" : '';

        // Contractor — in-app
        Notification::send(
            $cp->requested_by_user_id,
            'concrete_pouring',
            '❌ Concrete Pouring Request Disapproved',
            "Your concrete pouring request {$cp->contract_number} ({$cp->project_name}) has been disapproved.{$remarksNote}",
            route('user.concrete-pouring.show', $cp->id),
            $cp
        );

        // Contractor — email
        $contractor = User::find($cp->requested_by_user_id);
        if ($contractor) {
            try {
                Mail::to($contractor->email)->queue(new ConcretePouringDisapprovedMail($cp));
            } catch (\Throwable $e) {
                Log::error('ConcretePouringDisapprovedMail (contractor) failed', [
                    'to'    => $contractor->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // All assigned reviewers — in-app + email
        self::notifyAllReviewers(
            $cp,
            '❌ Concrete Pouring Request Disapproved',
            "Concrete pouring request {$cp->contract_number} ({$cp->project_name}) that you reviewed has been disapproved.{$remarksNote}",
            new ConcretePouringDisapprovedMail($cp)
        );
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    /**
     * Send in-app + email to all assigned reviewers (RE, PE, MTQA).
     * $mailInstance is already constructed so all three share one object.
     */
    private static function notifyAllReviewers(
        ConcretePouring $cp,
        string          $title,
        string          $message,
        object          $mailInstance
    ): void {
        $reviewerIds = collect([
            $cp->resident_engineer_user_id,
            $cp->noted_by_user_id,
            $cp->me_mtqa_user_id,
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

        $reviewers = User::whereIn('id', $reviewerIds)->get();
        foreach ($reviewers as $reviewer) {
            try {
                Mail::to($reviewer->email)->queue($mailInstance);
            } catch (\Throwable $e) {
                Log::error('ConcretePouringReviewerMail failed', [
                    'to'    => $reviewer->email,
                    'mail'  => get_class($mailInstance),
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Resolve the display name of the user assigned to a given review step.
     */
    private static function resolveReviewerName(ConcretePouring $cp, string $step): string
    {
        $stepToCol = [
            'resident_engineer'   => 'resident_engineer_user_id',
            'provincial_engineer' => 'noted_by_user_id',
            'mtqa'                => 'me_mtqa_user_id',
        ];

        $col = $stepToCol[$step] ?? null;
        if (!$col || !$cp->$col) return 'Reviewer';

        return User::find($cp->$col)?->name ?? 'Reviewer';
    }
}