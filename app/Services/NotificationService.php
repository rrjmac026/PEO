<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\WorkRequest;
use App\Models\ConcretePouring;
use App\Models\User;

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
        $admins = User::where('role', 'admin')->pluck('id')->toArray();

        if (empty($admins)) return;

        Notification::send(
            $admins,
            'work_request',
            '📋 New Work Request Submitted',
            "Contractor \"{$wr->contractor_name}\" submitted a new work request for \"{$wr->name_of_project}\".",
            route('admin.work-requests.show', $wr),
            $wr
        );
    }

    /**
     * Admin assigned engineers → notify each assigned engineer.
     */
    public static function workRequestAssigned(WorkRequest $wr): void
    {
        $steps = [
            'assigned_site_inspector_id'     => ['role' => 'Site Inspector',      'step' => 'site_inspector'],
            'assigned_surveyor_id'            => ['role' => 'Surveyor',            'step' => 'surveyor'],
            'assigned_resident_engineer_id'   => ['role' => 'Resident Engineer',   'step' => 'resident_engineer'],
            'assigned_mtqa_id'                => ['role' => 'MTQA',                'step' => 'mtqa'],
            'assigned_engineer_iv_id'         => ['role' => 'Engineer IV',         'step' => 'engineer_iv'],
            'assigned_engineer_iii_id'        => ['role' => 'Engineer III',        'step' => 'engineer_iii'],
            'assigned_provincial_engineer_id' => ['role' => 'Provincial Engineer', 'step' => 'provincial_engineer'],
        ];

        foreach ($steps as $col => $info) {
            $userId = $wr->$col;
            if (!$userId) continue;

            // Only notify the FIRST assigned reviewer right now (it's their turn).
            // Others will be notified when the step advances to them.
            if ($wr->current_review_step === $info['step']) {
                Notification::send(
                    $userId,
                    'work_request',
                    '🔔 Work Request Assigned to You',
                    "You have been assigned as {$info['role']} for the work request \"{$wr->name_of_project}\". Please review it.",
                    route('reviewer.work-requests.show', $wr),
                    $wr
                );
            } else {
                // Notify others that they are in the queue (informational)
                Notification::send(
                    $userId,
                    'work_request',
                    '📌 You Are in the Review Queue',
                    "You have been queued as {$info['role']} for work request \"{$wr->name_of_project}\". You'll be notified when it's your turn.",
                    route('reviewer.work-requests.show', $wr),
                    $wr
                );
            }
        }
    }

    /**
     * A reviewer completed their step → notify the next reviewer.
     * Also notify admin if it has reached admin_final.
     *
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
            // Notify admins for final decision
            $admins = User::where('role', 'admin')->pluck('id')->toArray();
            Notification::send(
                $admins,
                'work_request',
                '✅ Work Request Ready for Final Decision',
                "Work request \"{$wr->name_of_project}\" has completed all reviews. Please make a final decision.",
                route('admin.work-requests.decision-form', $wr),
                $wr
            );
            return;
        }

        // Find the assigned user for the next step
        $col = WorkRequest::REVIEW_STEPS[$nextStep]['assigned_col'] ?? null;
        if (!$col || !$wr->$col) return;

        $stepLabel = $stepLabels[$nextStep] ?? $nextStep;
        $prevLabel = $stepLabels[$completedStep] ?? $completedStep;

        Notification::send(
            $wr->$col,
            'work_request',
            "🔔 It's Your Turn to Review",
            "{$prevLabel} \"{$completedByName}\" completed their review of \"{$wr->name_of_project}\". It's now your turn as {$stepLabel}.",
            route('reviewer.work-requests.show', $wr),
            $wr
        );
    }

    /**
     * Admin made a final decision (approved/rejected) → notify contractor.
     */
    public static function workRequestDecisionMade(WorkRequest $wr): void
    {
        // Find the contractor user by name
        $contractor = User::where('name', $wr->contractor_name)
            ->where('role', 'contractor')
            ->first();

        if (!$contractor) return;

        $decision = $wr->admin_decision === 'approved' ? 'Approved ✅' : 'Rejected ❌';
        $emoji    = $wr->admin_decision === 'approved' ? '🎉' : '😔';

        Notification::send(
            $contractor->id,
            'work_request',
            "{$emoji} Work Request {$decision}",
            "Your work request for \"{$wr->name_of_project}\" has been {$wr->admin_decision}." .
            ($wr->admin_decision_remarks ? " Remarks: {$wr->admin_decision_remarks}" : ''),
            route('user.work-requests.show', $wr),
            $wr
        );
    }

    // ══════════════════════════════════════════════════════════════
    //  CONCRETE POURING NOTIFICATIONS
    // ══════════════════════════════════════════════════════════════

    /**
     * Contractor submitted a concrete pouring request → notify admins.
     */
    public static function concretePouringSubmitted(ConcretePouring $cp): void
    {
        $admins = User::where('role', 'admin')->pluck('id')->toArray();
        if (empty($admins)) return;

        Notification::send(
            $admins,
            'concrete_pouring',
            '🏗️ New Concrete Pouring Request',
            "A new concrete pouring request has been submitted for \"{$cp->project_name}\" ({$cp->contractor}).",
            route('admin.concrete-pouring.show', $cp),
            $cp
        );
    }

    /**
     * Admin approved concrete pouring → notify requester.
     */
    public static function concretePouringApproved(ConcretePouring $cp): void
    {
        $requester = $cp->requestedBy?->user;
        if (!$requester) return;

        Notification::send(
            $requester->id,
            'concrete_pouring',
            '✅ Concrete Pouring Request Approved',
            "Your concrete pouring request for \"{$cp->project_name}\" has been approved." .
            ($cp->approval_remarks ? " Remarks: {$cp->approval_remarks}" : ''),
            route('user.concrete-pouring.show', $cp),
            $cp
        );
    }

    /**
     * Admin disapproved concrete pouring → notify requester.
     */
    public static function concretePouringDisapproved(ConcretePouring $cp): void
    {
        $requester = $cp->requestedBy?->user;
        if (!$requester) return;

        Notification::send(
            $requester->id,
            'concrete_pouring',
            '❌ Concrete Pouring Request Disapproved',
            "Your concrete pouring request for \"{$cp->project_name}\" has been disapproved." .
            ($cp->approval_remarks ? " Reason: {$cp->approval_remarks}" : ''),
            route('user.concrete-pouring.show', $cp),
            $cp
        );
    }
}