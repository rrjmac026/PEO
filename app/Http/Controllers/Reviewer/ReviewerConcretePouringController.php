<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\ConcretePouring;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewerConcretePouringController extends Controller
{

    const REVIEW_STEPS = [
        'mtqa'                => 'me_mtqa_user_id',
        'resident_engineer'   => 'resident_engineer_user_id',
        'provincial_engineer' => 'noted_by_user_id',
    ];

    // ── Human-readable labels for each step ───────────────────────────────
    const REVIEW_STEP_LABELS = [
        'mtqa'                => 'ME/MTQA',
        'resident_engineer'   => 'Resident Engineer',
        'provincial_engineer' => 'Provincial Engineer',
    ];

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $this->assignedToUserQuery($user->id);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('project_name', 'LIKE', "%{$request->search}%")
                  ->orWhere('location',   'LIKE', "%{$request->search}%")
                  ->orWhere('contractor', 'LIKE', "%{$request->search}%");
            });
        }

        $concretePourings = $query->latest()->paginate(15)->withQueryString();

        $completed = $this->completedByUser($user)->latest()->limit(10)->get();

        return view('reviewer.concrete-pouring.index', compact('concretePourings', 'completed'));
    }

    public function show(ConcretePouring $concretePouring)
    {
        $user = Auth::user();

        if (!$this->userIsAssignedAnywhere($concretePouring, $user)) {
            abort(403, 'You are not assigned to this concrete pouring request.');
        }

        $concretePouring->load([
            'workRequest', 'requestedBy', 'meMtqaChecker',
            'residentEngineer', 'notedByEngineer', 'approver', 'disapprover',
        ]);

        $isMyTurn = $this->isCurrentReviewer($concretePouring, $user);

        return view('reviewer.concrete-pouring.show', compact('concretePouring', 'isMyTurn'));
    }

    // =========================================================================
    // REVIEW SUBMISSIONS
    // =========================================================================

    public function storeMtqaReview(Request $request, ConcretePouring $concretePouring)
    {
        $this->authorizeStep($concretePouring, 'mtqa');

        $request->validate([
            'me_mtqa_remarks'   => 'nullable|string|max:2000',
            'me_mtqa_signature' => 'nullable|string',
        ]);

        $concretePouring->update([
            'me_mtqa_remarks'   => $request->me_mtqa_remarks,
            'me_mtqa_date'      => now(),
            'me_mtqa_signature' => $this->resolveSignatureValue($request->me_mtqa_signature),
        ]);

        // ── Notify admin + all other assigned reviewers that ME/MTQA signed ──
        $this->notifySignatureSubmitted($concretePouring, 'ME/MTQA', Auth::user());

        $this->advanceStep($concretePouring, 'mtqa');

        return back()->with('success', 'ME/MTQA review & signature submitted. Request forwarded to next reviewer.');
    }

    public function storeResidentEngineerReview(Request $request, ConcretePouring $concretePouring)
    {
        $this->authorizeStep($concretePouring, 'resident_engineer');

        $request->validate([
            're_remarks'   => 'nullable|string|max:2000',
            're_signature' => 'nullable|string',
        ]);

        $concretePouring->update([
            're_remarks'   => $request->re_remarks,
            're_date'      => now(),
            're_signature' => $this->resolveSignatureValue($request->re_signature),
        ]);

        // ── Notify admin + all other assigned reviewers that RE signed ──────
        $this->notifySignatureSubmitted($concretePouring, 'Resident Engineer', Auth::user());

        $this->advanceStep($concretePouring, 'resident_engineer');

        return back()->with('success', 'Resident Engineer review & signature submitted. Request forwarded to next reviewer.');
    }

    public function storeProvincialNote(Request $request, ConcretePouring $concretePouring)
    {
        $this->authorizeStep($concretePouring, 'provincial_engineer');

        $request->validate([
            'provincial_remarks'   => 'nullable|string|max:2000',
            'noted_by_signature'   => 'nullable|string',
        ]);

        $concretePouring->update([
            'noted_date'         => now(),
            'approval_remarks'   => $request->provincial_remarks,
            'noted_by_signature' => $this->resolveSignatureValue($request->noted_by_signature),
        ]);

        // ── Notify admin + all other assigned reviewers that PE signed ───────
        $this->notifySignatureSubmitted($concretePouring, 'Provincial Engineer', Auth::user());

        $this->advanceStep($concretePouring, 'provincial_engineer');

        return back()->with('success', 'Note & signature submitted. Request forwarded to admin for final decision.');
    }

    // =========================================================================
    // PRIVATE — STEP CONTROL
    // =========================================================================

    /**
     * Abort 403 if it is not the current user's turn for $step.
     */
    private function authorizeStep(ConcretePouring $concretePouring, string $step): void
    {
        $user = Auth::user();

        if ($concretePouring->current_review_step !== $step) {
            abort(403, 'It is not your turn to review this request. Current step: ' . ($concretePouring->current_review_step ?? 'unassigned'));
        }

        $col = self::REVIEW_STEPS[$step] ?? null;
        if ($col && $concretePouring->$col != $user->id) {
            abort(403, 'You are not the assigned reviewer for this step.');
        }
    }

    private function advanceStep(ConcretePouring $concretePouring, string $completedStep): void
    {
        $steps    = array_keys(self::REVIEW_STEPS);
        $allSteps = array_merge($steps, ['admin_final']);
        $idx      = array_search($completedStep, $allSteps);

        for ($i = $idx + 1; $i < count($allSteps); $i++) {
            $nextStep = $allSteps[$i];

            if ($nextStep === 'admin_final') {
                $concretePouring->update(['current_review_step' => 'admin_final']);

                $adminIds = User::where('role', 'admin')->pluck('id')->toArray();
                if (!empty($adminIds)) {
                    Notification::send(
                        $adminIds,
                        'concrete_pouring',
                        'Concrete Pouring Ready for Final Decision',
                        "Concrete pouring request {$concretePouring->reference_number} ({$concretePouring->project_name}) has completed all reviews and is awaiting your final decision.",
                        route('admin.concrete-pouring.show', $concretePouring->id),
                        $concretePouring
                    );
                }

                return;
            }

            $col = self::REVIEW_STEPS[$nextStep];
            if (!empty($concretePouring->$col)) {
                $concretePouring->update(['current_review_step' => $nextStep]);

                $nextLabel = self::REVIEW_STEP_LABELS[$nextStep] ?? $nextStep;
                Notification::send(
                    $concretePouring->$col,
                    'concrete_pouring',
                    'Action Required — Concrete Pouring Review',
                    "It is now your turn as {$nextLabel} to review concrete pouring request {$concretePouring->reference_number} ({$concretePouring->project_name}).",
                    route('reviewer.concrete-pouring.show', $concretePouring->id),
                    $concretePouring
                );

                return;
            }
        }

        // All reviewer steps skipped — go straight to admin_final
        $concretePouring->update(['current_review_step' => 'admin_final']);

        $adminIds = User::where('role', 'admin')->pluck('id')->toArray();
        if (!empty($adminIds)) {
            Notification::send(
                $adminIds,
                'concrete_pouring',
                'Concrete Pouring Ready for Final Decision',
                "Concrete pouring request {$concretePouring->reference_number} ({$concretePouring->project_name}) has completed all reviews and is awaiting your final decision.",
                route('admin.concrete-pouring.show', $concretePouring->id),
                $concretePouring
            );
        }
    }

    // =========================================================================
    // PRIVATE — SIGNATURE NOTIFICATIONS
    // =========================================================================

    /**
     * Notify admins AND the other assigned reviewers that someone signed.
     * Each party can only see their OWN signature on their respective views,
     * but everyone is informed that a signature was placed.
     */
    private function notifySignatureSubmitted(
        ConcretePouring $concretePouring,
        string          $roleLabel,
        \App\Models\User $signer
    ): void {
        $message = "{$signer->name} ({$roleLabel}) has signed and submitted their review for concrete pouring request {$concretePouring->reference_number} ({$concretePouring->project_name}).";

        // ── Notify all admins ────────────────────────────────────────────────
        $adminIds = User::where('role', 'admin')->pluck('id')->toArray();
        if (!empty($adminIds)) {
            Notification::send(
                $adminIds,
                'concrete_pouring',
                "Signature Submitted — {$roleLabel}",
                $message,
                route('admin.concrete-pouring.show', $concretePouring->id),
                $concretePouring
            );
        }

        // ── Notify every OTHER assigned reviewer (not the signer themselves) ─
        $reviewerCols = [
            'me_mtqa_user_id'           => 'ME/MTQA',
            'resident_engineer_user_id' => 'Resident Engineer',
            'noted_by_user_id'          => 'Provincial Engineer',
        ];

        foreach ($reviewerCols as $col => $label) {
            $reviewerId = $concretePouring->$col;
            if ($reviewerId && $reviewerId != $signer->id) {
                Notification::send(
                    $reviewerId,
                    'concrete_pouring',
                    "Signature Submitted — {$roleLabel}",
                    $message,
                    route('reviewer.concrete-pouring.show', $concretePouring->id),
                    $concretePouring
                );
            }
        }
    }

    // =========================================================================
    // PRIVATE — SIGNATURE VALUE NORMALISER
    // =========================================================================

    /**
     * Normalise the signature hidden-input value before storing.
     * Mirrors the same helper in ReviewerWorkRequestController.
     */
    private function resolveSignatureValue(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Raw base64 data URI — store as-is
        if (str_starts_with($value, 'data:image')) {
            return $value;
        }

        // Full URL pointing at /storage/... — strip to relative path
        $storageUrl = url('storage') . '/';
        if (str_starts_with($value, $storageUrl)) {
            return ltrim(substr($value, strlen($storageUrl)), '/');
        }

        if (str_starts_with($value, '/storage/')) {
            return ltrim(substr($value, strlen('/storage/')), '/');
        }

        return $value;
    }

    // =========================================================================
    // PRIVATE — PREDICATES / QUERY HELPERS
    // =========================================================================

    private function isCurrentReviewer(ConcretePouring $concretePouring, $user): bool
    {
        $step = $concretePouring->current_review_step;

        if (!$step || !isset(self::REVIEW_STEPS[$step])) {
            return false;
        }

        $col = self::REVIEW_STEPS[$step];
        return $concretePouring->$col == $user->id;
    }

    private function userIsAssignedAnywhere(ConcretePouring $concretePouring, $user): bool
    {
        foreach (self::REVIEW_STEPS as $col) {
            if ($concretePouring->$col == $user->id) {
                return true;
            }
        }
        return false;
    }

    private function assignedToUserQuery(int $userId)
    {
        return ConcretePouring::where(function ($q) use ($userId) {
            $q->where(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'mtqa')
                   ->where('me_mtqa_user_id', $userId);
            })->orWhere(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'resident_engineer')
                   ->where('resident_engineer_user_id', $userId);
            })->orWhere(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'provincial_engineer')
                   ->where('noted_by_user_id', $userId);
            });
        });
    }

    private function completedByUser($user)
    {
        return ConcretePouring::where(function ($q) use ($user) {
            $q->where('me_mtqa_user_id', $user->id)->whereNotNull('me_mtqa_date');
        })->orWhere(function ($q) use ($user) {
            $q->where('resident_engineer_user_id', $user->id)->whereNotNull('re_date');
        })->orWhere(function ($q) use ($user) {
            $q->where('noted_by_user_id', $user->id)->whereNotNull('noted_date');
        });
    }
}