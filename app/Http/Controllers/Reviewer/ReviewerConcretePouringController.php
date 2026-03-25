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

        // Also surface already-completed items for this reviewer (read-only)
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

    public function storeMtqaReview(Request $request, ConcretePouring $concretePouring)
    {
        $this->authorizeStep($concretePouring, 'mtqa');

        $request->validate([
            'me_mtqa_remarks' => 'nullable|string|max:2000',
        ]);

        $concretePouring->update([
            'me_mtqa_remarks' => $request->me_mtqa_remarks,
            'me_mtqa_date'    => now(),
        ]);

        $this->advanceStep($concretePouring, 'mtqa');

        return back()->with('success', 'ME/MTQA review submitted. Request forwarded to next reviewer.');
    }

    public function storeResidentEngineerReview(Request $request, ConcretePouring $concretePouring)
    {
        $this->authorizeStep($concretePouring, 'resident_engineer');

        $request->validate([
            're_remarks' => 'nullable|string|max:2000',
        ]);

        $concretePouring->update([
            're_remarks' => $request->re_remarks,
            're_date'    => now(),
        ]);

        $this->advanceStep($concretePouring, 'resident_engineer');

        return back()->with('success', 'Resident Engineer review submitted. Request forwarded to next reviewer.');
    }

    public function storeProvincialNote(Request $request, ConcretePouring $concretePouring)
    {
        $this->authorizeStep($concretePouring, 'provincial_engineer');

        $request->validate([
            'provincial_remarks' => 'nullable|string|max:2000',
        ]);

        $concretePouring->update([
            'noted_date'       => now(),
            'approval_remarks' => $request->provincial_remarks,
        ]);

        // Advance to admin_final — the terminal step
        $this->advanceStep($concretePouring, 'provincial_engineer');

        return back()->with('success', 'Note submitted. Request forwarded to admin for final decision.');
    }

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
        $steps    = array_keys(self::REVIEW_STEPS);           // mtqa, resident_engineer, provincial_engineer
        $allSteps = array_merge($steps, ['admin_final']);      // add terminal step
        $idx      = array_search($completedStep, $allSteps);

        // Find the next occupied step
        for ($i = $idx + 1; $i < count($allSteps); $i++) {
            $nextStep = $allSteps[$i];

            if ($nextStep === 'admin_final') {
                $concretePouring->update(['current_review_step' => 'admin_final']);

                // ── Notify all admins the request is ready for final decision ──
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

                // ── Notify the next reviewer it is now their turn ──────────
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

        // ── Notify all admins ──────────────────────────────────────────────
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

    /**
     * True when the user is the assigned reviewer for the current step.
     */
    private function isCurrentReviewer(ConcretePouring $concretePouring, $user): bool
    {
        $step = $concretePouring->current_review_step;

        if (!$step || !isset(self::REVIEW_STEPS[$step])) {
            return false;
        }

        $col = self::REVIEW_STEPS[$step];
        return $concretePouring->$col == $user->id;
    }

    /**
     * True when the user is assigned to ANY step (allows read-only access).
     */
    private function userIsAssignedAnywhere(ConcretePouring $concretePouring, $user): bool
    {
        foreach (self::REVIEW_STEPS as $col) {
            if ($concretePouring->$col == $user->id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Base query: requests currently waiting for this user at their assigned step.
     */
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

    /**
     * Query requests this user has already reviewed (any completed step).
     */
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