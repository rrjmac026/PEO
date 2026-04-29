<?php

namespace App\Services;

use App\Models\ConcretePouring;
use App\Models\WorkRequest;

/**
 * Backward-compatible facade.
 *
 * All new code should call the dedicated services directly:
 *   - WorkRequestNotificationService::submitted($wr)
 *   - ConcretePouringNotificationService::submitted($cp)
 *
 * This class simply delegates to those services so existing call
 * sites continue to work without any changes.
 */
class NotificationService
{
    // ══════════════════════════════════════════════════════════════
    //  WORK REQUEST — delegates to WorkRequestNotificationService
    // ══════════════════════════════════════════════════════════════

    public static function workRequestSubmitted(WorkRequest $wr): void
    {
        WorkRequestNotificationService::submitted($wr);
    }

    public static function workRequestAssigned(WorkRequest $wr): void
    {
        WorkRequestNotificationService::assigned($wr);
    }

    public static function workRequestStepAdvanced(WorkRequest $wr, string $completedByName, string $completedStep): void
    {
        WorkRequestNotificationService::stepAdvanced($wr, $completedByName, $completedStep);
    }

    public static function workRequestDecisionMade(WorkRequest $wr): void
    {
        WorkRequestNotificationService::decisionMade($wr);
    }

    // ══════════════════════════════════════════════════════════════
    //  CONCRETE POURING — delegates to ConcretePouringNotificationService
    // ══════════════════════════════════════════════════════════════

    public static function concretePouringSubmitted(ConcretePouring $cp): void
    {
        ConcretePouringNotificationService::submitted($cp);
    }

    public static function concretePouringUpdated(ConcretePouring $cp): void
    {
        ConcretePouringNotificationService::updated($cp);
    }

    public static function concretePouringDeleted(int $contractorId, string $referenceNumber, string $projectName): void
    {
        ConcretePouringNotificationService::deleted($contractorId, $referenceNumber, $projectName);
    }

    public static function concretePouringAssigned(ConcretePouring $cp): void
    {
        ConcretePouringNotificationService::assigned($cp);
    }

    public static function concretePouringSignatureSubmitted(ConcretePouring $cp, string $roleLabel, int $signerId): void
    {
        ConcretePouringNotificationService::signatureSubmitted($cp, $roleLabel, $signerId);
    }

    public static function concretePouringStepAdvanced(ConcretePouring $cp, string $completedStep = ''): void
    {
        ConcretePouringNotificationService::stepAdvanced($cp, $completedStep);
    }

    public static function concretePouringReadyForDecision(ConcretePouring $cp): void
    {
        ConcretePouringNotificationService::readyForDecision($cp);
    }

    public static function concretePouringApproved(ConcretePouring $cp): void
    {
        ConcretePouringNotificationService::approved($cp);
    }

    public static function concretePouringDisapproved(ConcretePouring $cp): void
    {
        ConcretePouringNotificationService::disapproved($cp);
    }
}