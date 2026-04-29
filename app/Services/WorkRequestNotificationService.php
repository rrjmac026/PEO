<?php

namespace App\Services;

use App\Mail\WorkRequestAssignedMail;
use App\Mail\WorkRequestDecisionMadeMail;
use App\Mail\WorkRequestReadyForDecisionMail;
use App\Mail\WorkRequestStepAdvancedMail;
use App\Mail\WorkRequestSubmittedMail;
use App\Models\Notification;
use App\Models\WorkRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WorkRequestNotificationService
{
    /**
     * Contractor submitted a new Work Request → notify all admins.
     */
    public static function submitted(WorkRequest $wr): void
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
    public static function assigned(WorkRequest $wr): void
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
    public static function stepAdvanced(WorkRequest $wr, string $completedByName, string $completedStep): void
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
    public static function decisionMade(WorkRequest $wr): void
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
}
