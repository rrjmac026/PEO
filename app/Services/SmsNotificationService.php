<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * SmsNotificationService
 *
 * Mirrors the email notification pipeline for Work Requests, Concrete Pourings,
 * and Memos. Each static method corresponds 1-to-1 with its email counterpart.
 *
 * Phone numbers are pulled from users.employee->phone_number.
 * Users without a phone number are silently skipped (same behaviour as email
 * failing silently in the existing notification services).
 */
class SmsNotificationService
{
    /**
     * Microseconds to wait between SMS sends in a bulk loop.
     *
     * The Android modem can only have one SMS transaction in flight at a
     * time per SIM. Sending the next request before the modem has cleared
     * the previous one causes RESULT_ERROR_GENERIC_FAILURE on the device
     * (confirmed via the SMS Gateway app's own delivery log). 2 seconds
     * gives the modem enough margin to finish the prior send.
     */
    private const BULK_SEND_DELAY_US = 2000000; // 2s

    /**
     * Links are stripped from every outgoing SMS and replaced with a short
     * call-to-action telling the recipient to check the system.
     *
     * Confirmed root cause of RESULT_ERROR_GENERIC_FAILURE on delivery:
     * including the route() link pushed messages over the GSM-7 single-segment
     * limit (forcing multipart delivery, which failed) and/or the gateway
     * path was bouncing messages containing a URL outright. Removing links
     * entirely resolved delivery reliably, so this is the confirmed-working
     * behaviour going forward.
     */
    private const STRIP_LINKS = true;

    /** Phrase appended where a link used to be, telling the recipient what to do instead. */
    private const VISIT_PROMPT = 'Please visit the system to view details.';

    // ══════════════════════════════════════════════════════════════════════════
    //  WORK REQUESTS
    //  Mirrors: WorkRequestNotificationService
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Mirrors: WorkRequestNotificationService::submitted()
     * Recipient: all admins
     */
    public static function workRequestSubmitted(\App\Models\WorkRequest $wr): void
    {
        $admins = User::where('role', 'admin')->with('employee')->get();

        foreach ($admins as $admin) {
            self::send(
                $admin,
                '[Work Request] New submission: "' . self::truncate($wr->name_of_project, 40)
                    . '" by ' . self::truncate($wr->contractor_name, 25) . '. Log in to assign reviewers. '
                    . route('admin.work-requests.show', $wr)
            );
        }
    }

    /**
     * Mirrors: WorkRequestNotificationService::assigned()
     * Recipients: each assigned reviewer (active turn gets "Action Required", queued gets "Heads Up")
     */
    public static function workRequestAssigned(\App\Models\WorkRequest $wr): void
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

            $reviewer = User::with('employee')->find($userId);
            if (!$reviewer) continue;

            $isFirst = $wr->current_review_step === $info['step'];
            $link    = route('reviewer.work-requests.show', $wr);

            if ($isFirst) {
                self::send(
                    $reviewer,
                    '[Action Required] Work Request "' . self::truncate($wr->name_of_project, 35)
                        . '" assigned to you as ' . $info['role'] . '. Please log in to review. '
                        . $link
                );
            } else {
                self::send(
                    $reviewer,
                    '[Heads Up] You are queued as ' . $info['role'] . ' for Work Request "'
                        . self::truncate($wr->name_of_project, 35) . '". You will be notified when it\'s your turn. '
                        . $link
                );
            }
        }
    }

    /**
     * Mirrors: WorkRequestNotificationService::stepAdvanced()
     * Recipient: the next reviewer in the pipeline
     */
    public static function workRequestStepAdvanced(
        \App\Models\WorkRequest $wr,
        string                  $completedByName,
        string                  $completedStep
    ): void {
        $nextStep = $wr->current_review_step;
        if (is_null($nextStep)) return;

        $col = \App\Models\WorkRequest::REVIEW_STEPS[$nextStep]['assigned_col'] ?? null;
        if (!$col || !$wr->$col) return;

        $nextReviewer = User::with('employee')->find($wr->$col);
        if (!$nextReviewer) return;

        $stepLabels = [
            'site_inspector'      => 'Site Inspector',
            'surveyor'            => 'Surveyor',
            'resident_engineer'   => 'Resident Engineer',
            'mtqa'                => 'MTQA',
            'engineer_iv'         => 'Engineer IV',
            'engineer_iii'        => 'Engineer III',
            'provincial_engineer' => 'Provincial Engineer',
        ];

        $nextLabel = $stepLabels[$nextStep] ?? $nextStep;
        $link      = route('reviewer.work-requests.show', $wr);

        if ($nextStep === 'provincial_engineer') {
            self::send(
                $nextReviewer,
                '[Action Required] Work Request "' . self::truncate($wr->name_of_project, 30)
                    . '" is ready for your final decision as Provincial Engineer. Please log in. '
                    . $link
            );
        } else {
            self::send(
                $nextReviewer,
                '[Action Required] It\'s your turn as ' . $nextLabel . ' to review "'
                    . self::truncate($wr->name_of_project, 30) . '". Log in to proceed. '
                    . $link
            );
        }
    }

    /**
     * Mirrors: WorkRequestNotificationService::decisionMade()
     * Recipients: contractor (approved/rejected) + MTQA (ready to print, if approved)
     */
    public static function workRequestDecisionMade(\App\Models\WorkRequest $wr): void
    {
        $isApproved = $wr->status === \App\Models\WorkRequest::STATUS_APPROVED;

        // Notify contractor
        $contractor = User::with('employee')
            ->where('name', $wr->contractor_name)
            ->where('role', 'contractor')
            ->first();

        if ($contractor) {
            $decision = $isApproved ? 'APPROVED' : 'REJECTED';
            $remarks  = $wr->approved_recommendation_action
                ? ' Remarks: ' . self::truncate($wr->approved_recommendation_action, 40)
                : '';
            self::send(
                $contractor,
                '[Work Request ' . $decision . '] "'
                    . self::truncate($wr->name_of_project, 35) . '".' . $remarks . ' '
                    . route('user.work-requests.show', $wr)
            );
        }

        // Notify MTQA if approved
        if ($isApproved && $wr->assigned_mtqa_id) {
            $mtqa = User::with('employee')->find($wr->assigned_mtqa_id);
            if ($mtqa) {
                self::send(
                    $mtqa,
                    '[Ready to Print] Work Request "' . self::truncate($wr->name_of_project, 40)
                        . '" has been approved. Log in to print. '
                        . route('reviewer.work-requests.show', $wr)
                );
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  CONCRETE POURINGS
    //  Mirrors: ConcretePouringNotificationService
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Mirrors: ConcretePouringNotificationService::submitted()
     * Recipients: contractor (confirmation) + all admins
     */
    public static function concretePouringSubmitted(\App\Models\ConcretePouring $cp): void
    {
        // Contractor confirmation
        $contractor = User::with('employee')->find($cp->requested_by_user_id);
        if ($contractor) {
            self::send(
                $contractor,
                '[Concrete Pouring] Your request ' . $cp->contract_number
                    . ' for "' . self::truncate($cp->project_name, 35) . '" has been submitted. '
                    . route('user.concrete-pouring.show', $cp->id)
            );
        }

        // All admins
        $admins = User::where('role', 'admin')->with('employee')->get();
        foreach ($admins as $admin) {
            self::send(
                $admin,
                '[Concrete Pouring] New request ' . $cp->contract_number
                    . ' for "' . self::truncate($cp->project_name, 30) . '" by '
                    . self::truncate($cp->contractor, 25) . '. Awaiting reviewer assignment. '
                    . route('admin.concrete-pouring.show', $cp->id)
            );
        }
    }

    /**
     * Mirrors: ConcretePouringNotificationService::assigned()
     * Recipients: contractor (confirmation) + each assigned reviewer
     */
    public static function concretePouringAssigned(\App\Models\ConcretePouring $cp): void
    {
        // Contractor confirmation
        $contractor = User::with('employee')->find($cp->requested_by_user_id);
        if ($contractor) {
            self::send(
                $contractor,
                '[Concrete Pouring] Request ' . $cp->contract_number
                    . ' for "' . self::truncate($cp->project_name, 30) . '" is now under review. '
                    . route('user.concrete-pouring.show', $cp->id)
            );
        }

        $reviewerMeta = [
            'resident_engineer'   => ['col' => 'resident_engineer_user_id', 'label' => 'Resident Engineer'],
            'provincial_engineer' => ['col' => 'noted_by_user_id',          'label' => 'Provincial Engineer'],
            'mtqa'                => ['col' => 'me_mtqa_user_id',           'label' => 'ME/MTQA'],
        ];

        foreach ($reviewerMeta as $step => $meta) {
            $userId = $cp->{$meta['col']};
            if (!$userId) continue;

            $reviewer = User::with('employee')->find($userId);
            if (!$reviewer) continue;

            $isFirst = $cp->current_review_step === $step;
            $link    = route('reviewer.concrete-pouring.show', $cp->id);

            if ($isFirst) {
                self::send(
                    $reviewer,
                    '[Action Required] Concrete Pouring ' . $cp->contract_number
                        . ' assigned to you as ' . $meta['label'] . '. It is now your turn to review. '
                        . $link
                );
            } else {
                self::send(
                    $reviewer,
                    '[Heads Up] You are queued as ' . $meta['label'] . ' for Concrete Pouring '
                        . $cp->contract_number . '. You will be notified when it\'s your turn. '
                        . $link
                );
            }
        }
    }

    /**
     * Mirrors: ConcretePouringNotificationService::stepAdvanced()
     * Recipient: the next reviewer
     */
    public static function concretePouringStepAdvanced(
        \App\Models\ConcretePouring $cp,
        string                      $completedStep = ''
    ): void {
        $nextStep = $cp->current_review_step;
        if (is_null($nextStep)) return;

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

        $nextReviewer = User::with('employee')->find($cp->$col);
        if (!$nextReviewer) return;

        $nextLabel = $stepLabels[$nextStep] ?? $nextStep;
        $isFinal   = $nextStep === 'mtqa';
        $link      = route('reviewer.concrete-pouring.show', $cp->id);

        if ($isFinal) {
            self::send(
                $nextReviewer,
                '[Action Required] Concrete Pouring ' . $cp->contract_number
                    . ' for "' . self::truncate($cp->project_name, 30)
                    . '" is ready for your final decision as ' . $nextLabel . '. '
                    . $link
            );
        } else {
            self::send(
                $nextReviewer,
                '[Action Required] It\'s your turn as ' . $nextLabel
                    . ' to review Concrete Pouring ' . $cp->contract_number
                    . ' for "' . self::truncate($cp->project_name, 25) . '". '
                    . $link
            );
        }
    }

    /**
     * Mirrors: ConcretePouringNotificationService::approved()
     * Recipients: contractor + all assigned reviewers
     */
    public static function concretePouringApproved(\App\Models\ConcretePouring $cp): void
    {
        $remarks = $cp->approval_remarks
            ? ' Remarks: ' . self::truncate($cp->approval_remarks, 40)
            : '';

        // Contractor
        $contractor = User::with('employee')->find($cp->requested_by_user_id);
        if ($contractor) {
            self::send(
                $contractor,
                '[Concrete Pouring APPROVED] ' . $cp->contract_number
                    . ' for "' . self::truncate($cp->project_name, 35) . '".' . $remarks . ' '
                    . route('user.concrete-pouring.show', $cp->id)
            );
        }

        // All assigned reviewers
        self::notifyAllCpReviewers(
            $cp,
            '[Concrete Pouring APPROVED] ' . $cp->contract_number
                . ' for "' . self::truncate($cp->project_name, 30) . '" has been approved.' . $remarks,
            route('reviewer.concrete-pouring.show', $cp->id)
        );
    }

    /**
     * Mirrors: ConcretePouringNotificationService::disapproved()
     * Recipients: contractor + all assigned reviewers
     */
    public static function concretePouringDisapproved(\App\Models\ConcretePouring $cp): void
    {
        $remarks = $cp->approval_remarks
            ? ' Remarks: ' . self::truncate($cp->approval_remarks, 40)
            : '';

        // Contractor
        $contractor = User::with('employee')->find($cp->requested_by_user_id);
        if ($contractor) {
            self::send(
                $contractor,
                '[Concrete Pouring DISAPPROVED] ' . $cp->contract_number
                    . ' for "' . self::truncate($cp->project_name, 30) . '".' . $remarks . ' '
                    . route('user.concrete-pouring.show', $cp->id)
            );
        }

        // All assigned reviewers
        self::notifyAllCpReviewers(
            $cp,
            '[Concrete Pouring DISAPPROVED] ' . $cp->contract_number
                . ' for "' . self::truncate($cp->project_name, 25) . '" has been disapproved.' . $remarks,
            route('reviewer.concrete-pouring.show', $cp->id)
        );
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  MEMOS
    //  Mirrors: MemoController::dispatchNotifications()
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Mirrors: MemoController::dispatchNotifications()
     * Recipients: all resolved memo recipients
     *
     * Each recipient gets the role-appropriate link, exactly mirroring
     * the logic in MemoController::dispatchNotifications().
     *
     * NOTE: Memos can go out to many recipients at once (by_role / by_department /
     * all). A small delay is inserted between sends so a local gateway (e.g.
     * SMSGate on an Android phone, which can only process one outgoing SMS
     * at a time) doesn't drop or time out requests fired in rapid succession.
     */
    public static function memoDispatched(\App\Models\Memo $memo, array $userIds): void
    {
        if (empty($userIds)) return;

        $reviewerRoles = [
            'site_inspector', 'surveyor', 'resident_engineer',
            'provincial_engineer', 'mtqa', 'engineeriii', 'engineeriv',
        ];

        $recipients = User::whereIn('id', $userIds)->with('employee')->get();
        $count      = $recipients->count();
        $i          = 0;

        foreach ($recipients as $recipient) {
            $i++;

            $link = match (true) {
                $recipient->role === 'admin'               => route('admin.memos.show', $memo),
                $recipient->role === 'contractor'          => route('user.memos.show', $memo),
                in_array($recipient->role, $reviewerRoles) => \Illuminate\Support\Facades\Route::has('reviewer.memos.show')
                                                                ? route('reviewer.memos.show', $memo)
                                                                : route('user.memos.show', $memo),
                default                                    => route('user.memos.show', $memo),
            };

            self::send(
                $recipient,
                '[' . $memo->type_label . '] ' . self::truncate($memo->subject, 50)
                    . ' - from ' . ($memo->sender?->name ?? 'Admin') . '. '
                    . $link
            );

            // Throttle bulk sends — skip the delay after the last recipient
            if ($i < $count) {
                usleep(self::BULK_SEND_DELAY_US);
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Resolve a User's phone number (from employee record) and send SMS.
     * Silently skips if no phone number is found, mirroring the email
     * try/catch pattern used throughout the existing notification services.
     */
    private static function send(User $user, string $message): void
    {
        $phone = $user->employee?->phone_number ?? null;

        if (!$phone) {
            Log::debug('SmsNotificationService: skipped (no phone number)', [
                'user_id' => $user->id,
                'name'    => $user->name,
            ]);
            return;
        }

        if (self::STRIP_LINKS) {
            $message = self::stripLinks($message);
        }

        try {
            $sent = SmsService::send($phone, $message);

            if (!$sent) {
                Log::warning('SmsNotificationService: send returned false', [
                    'user_id' => $user->id,
                    'phone'   => $phone,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('SmsNotificationService: send failed', [
                'user_id' => $user->id,
                'phone'   => $phone,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove any http(s):// URL from the message text, clean up the
     * trailing punctuation left behind (e.g. a dangling ". " or ": "
     * where the link used to be), then append a short call-to-action
     * telling the recipient to check the system instead of tapping a link.
     */
    private static function stripLinks(string $message): string
    {
        $hadLink = (bool) preg_match('/https?:\/\/\S+/i', $message);

        $message = preg_replace('/https?:\/\/\S+/i', '', $message);
        $message = rtrim($message);
        // Drop a trailing period or colon left over from "...turn. " etc.
        $message = rtrim($message, '.:');
        $message = trim($message);

        if ($hadLink) {
            $message .= '. ' . self::VISIT_PROMPT;
        }

        return $message;
    }

    /**
     * Notify all three CP reviewer slots (RE, PE, MTQA) with the same message + link.
     */
    private static function notifyAllCpReviewers(
        \App\Models\ConcretePouring $cp,
        string                      $message,
        string                      $link
    ): void {
        $reviewerIds = collect([
            $cp->resident_engineer_user_id,
            $cp->noted_by_user_id,
            $cp->me_mtqa_user_id,
        ])->filter()->unique()->values()->toArray();

        if (empty($reviewerIds)) return;

        $reviewers = User::whereIn('id', $reviewerIds)->with('employee')->get();
        $count     = $reviewers->count();
        $i         = 0;

        foreach ($reviewers as $reviewer) {
            $i++;
            self::send($reviewer, $message . ' ' . $link);

            if ($i < $count) {
                usleep(self::BULK_SEND_DELAY_US);
            }
        }
    }

    /** Truncate a string to $max chars with ellipsis. */
    private static function truncate(string $text, int $max): string
    {
        return mb_strlen($text) > $max
            ? mb_substr($text, 0, $max - 1) . '...'
            : $text;
    }
}