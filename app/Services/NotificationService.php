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

        // In-app
        Notification::send(
            $admins->pluck('id')->toArray(),
            'work_request',
            '📋 New Work Request Submitted',
            "Contractor \"{$wr->contractor_name}\" submitted a new work request for \"{$wr->name_of_project}\".",
            route('admin.work-requests.show', $wr),
            $wr
        );

        // Email
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

            // In-app
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

            // Email
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
            $admins = User::where('role', 'admin')->get();

            // In-app
            Notification::send(
                $admins->pluck('id')->toArray(),
                'work_request',
                '✅ Work Request Ready for Final Decision',
                "Work request \"{$wr->name_of_project}\" has completed all reviews. Please make a final decision.",
                route('admin.work-requests.decision-form', $wr),
                $wr
            );

            // Email
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

        // Find the assigned user for the next step
        $col = WorkRequest::REVIEW_STEPS[$nextStep]['assigned_col'] ?? null;
        if (!$col || !$wr->$col) return;

        $nextReviewer = User::find($wr->$col);
        if (!$nextReviewer) return;

        $nextStepLabel = $stepLabels[$nextStep]      ?? $nextStep;
        $prevLabel     = $stepLabels[$completedStep] ?? $completedStep;

        // In-app
        Notification::send(
            $wr->$col,
            'work_request',
            "🔔 It's Your Turn to Review",
            "{$prevLabel} \"{$completedByName}\" completed their review of \"{$wr->name_of_project}\". It's now your turn as {$nextStepLabel}.",
            route('reviewer.work-requests.show', $wr),
            $wr
        );

        // Email
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
     * Admin made a final decision (approved/rejected) → notify contractor.
     */
    public static function workRequestDecisionMade(WorkRequest $wr): void
    {
        $contractor = User::where('name', $wr->contractor_name)
            ->where('role', 'contractor')
            ->first();

        if (!$contractor) return;

        $decision = $wr->admin_decision === 'approved' ? 'Approved ✅' : 'Rejected ❌';
        $emoji    = $wr->admin_decision === 'approved' ? '🎉' : '😔';

        // In-app
        Notification::send(
            $contractor->id,
            'work_request',
            "{$emoji} Work Request {$decision}",
            "Your work request for \"{$wr->name_of_project}\" has been {$wr->admin_decision}." .
            ($wr->admin_decision_remarks ? " Remarks: {$wr->admin_decision_remarks}" : ''),
            route('user.work-requests.show', $wr),
            $wr
        );

        // Email
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
    //  CONCRETE POURING NOTIFICATIONS  (unchanged — not touched)
    // ══════════════════════════════════════════════════════════════

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