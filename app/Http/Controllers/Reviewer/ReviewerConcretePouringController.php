<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\ConcretePouring;
use App\Models\ConcretePouringLog;
use App\Services\ConcretePouringNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewerConcretePouringController extends Controller
{
    const REVIEW_STEPS = [
        'resident_engineer'   => 'resident_engineer_user_id',
        'provincial_engineer' => 'noted_by_user_id',
        'mtqa'                => 'me_mtqa_user_id',
    ];

    const REVIEW_STEP_LABELS = [
        'resident_engineer'   => 'Resident Engineer',
        'provincial_engineer' => 'Provincial Engineer',
        'mtqa'                => 'ME/MTQA (Final Decision)',
    ];

    // =========================================================================
    // INDEX
    // =========================================================================

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

        $allPending = ConcretePouring::where('status', 'requested')
            ->whereNotNull('current_review_step')
            ->where(function ($q) use ($user) {
                $q->where('resident_engineer_user_id', $user->id)
                  ->orWhere('noted_by_user_id', $user->id)
                  ->orWhere('me_mtqa_user_id', $user->id);
            })
            ->with(['meMtqaChecker', 'residentEngineer', 'notedByEngineer'])
            ->latest()->get();

        $allApproved = ConcretePouring::where('status', 'approved')
            ->where(function ($q) use ($user) {
                $q->where('resident_engineer_user_id', $user->id)
                  ->orWhere('noted_by_user_id', $user->id)
                  ->orWhere('me_mtqa_user_id', $user->id);
            })
            ->with(['meMtqaChecker'])->latest()->get();

        $allDisapproved = ConcretePouring::where('status', 'disapproved')
            ->where(function ($q) use ($user) {
                $q->where('resident_engineer_user_id', $user->id)
                  ->orWhere('noted_by_user_id', $user->id)
                  ->orWhere('me_mtqa_user_id', $user->id);
            })
            ->with(['meMtqaChecker'])->latest()->get();

        $completed = $this->completedByUser($user)->latest()->get();

        return view('reviewer.concrete-pouring.index', compact(
            'concretePourings', 'allPending', 'allApproved', 'allDisapproved', 'completed'
        ));
    }

    // =========================================================================
    // SHOW
    // =========================================================================

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

    public function storeEngineerReview(Request $request, ConcretePouring $concretePouring)
    {
        $this->authorizeStep($concretePouring, 'resident_engineer');

        $request->validate([
            're_remarks'   => 'nullable|string|max:2000',
            're_signature' => 'nullable|string',
        ]);

        $concretePouring->update([
            're_checked_by' => Auth::id(),
            're_date'       => now(),
            're_remarks'    => $request->re_remarks,
            're_signature'  => $this->resolveSignatureValue($request->re_signature, 're_' . $concretePouring->id),
        ]);

        $nextStep = $concretePouring->noted_by_user_id ? 'provincial_engineer' : 'mtqa';
        $concretePouring->update(['current_review_step' => $nextStep]);

        $concretePouring->addLog(ConcretePouringLog::EVENT_RE_REVIEWED, [
            'description' => 'Resident Engineer submitted review. Forwarded to ' . self::REVIEW_STEP_LABELS[$nextStep] . '.',
            'note'        => $request->re_remarks,
            'review_step' => 'resident_engineer',
            'status_from' => $concretePouring->status,
            'status_to'   => $concretePouring->status,
        ]);

        ConcretePouringNotificationService::stepAdvanced($concretePouring, 'resident_engineer');

        return back()->with('success', 'Your Resident Engineer review has been submitted. Request forwarded to next reviewer.');
    }

    public function storeProvincialNote(Request $request, ConcretePouring $concretePouring)
    {
        $this->authorizeStep($concretePouring, 'provincial_engineer');

        $request->validate([
            'provincial_remarks' => 'nullable|string|max:2000',
            'noted_by_signature' => 'nullable|string',
        ]);

        $concretePouring->update([
            'noted_by'           => Auth::id(),
            'noted_date'         => now(),
            'approval_remarks'   => $request->provincial_remarks,
            'noted_by_signature' => $this->resolveSignatureValue($request->noted_by_signature, 'pe_' . $concretePouring->id),
        ]);

        $concretePouring->update(['current_review_step' => 'mtqa']);

        $concretePouring->addLog(ConcretePouringLog::EVENT_PE_NOTED, [
            'description' => 'Provincial Engineer submitted note. Forwarded to ME/MTQA for final decision.',
            'note'        => $request->provincial_remarks,
            'review_step' => 'provincial_engineer',
            'status_from' => $concretePouring->status,
            'status_to'   => $concretePouring->status,
        ]);

        ConcretePouringNotificationService::stepAdvanced($concretePouring, 'provincial_engineer');

        return back()->with('success', 'Your Provincial Engineer note has been submitted. Request forwarded to ME/MTQA for final decision.');
    }

    public function storeMtqaReview(Request $request, ConcretePouring $concretePouring)
    {
        $this->authorizeStep($concretePouring, 'mtqa');

        $request->validate([
            'decision'          => 'required|in:approved,disapproved',
            'me_mtqa_remarks'   => 'nullable|string|max:2000',
            'me_mtqa_signature' => 'nullable|string',
        ]);

        $oldStatus = $concretePouring->status;

        $concretePouring->update([
            'me_mtqa_checked_by' => Auth::id(),
            'me_mtqa_date'       => now(),
            'me_mtqa_remarks'    => $request->me_mtqa_remarks,
            'me_mtqa_signature'  => $this->resolveSignatureValue($request->me_mtqa_signature, 'mtqa_' . $concretePouring->id),
        ]);

        if ($request->decision === 'approved') {
            $concretePouring->approve(Auth::user(), $request->me_mtqa_remarks);
            ConcretePouringNotificationService::approved($concretePouring);
        } else {
            $concretePouring->disapprove(Auth::user(), $request->me_mtqa_remarks);
            ConcretePouringNotificationService::disapproved($concretePouring);
        }

        $concretePouring->update(['current_review_step' => null]);

        $concretePouring->addLog(ConcretePouringLog::EVENT_MTQA_DECIDED, [
            'description' => 'ME/MTQA made final decision: ' . ucfirst($request->decision) . '.',
            'note'        => $request->me_mtqa_remarks,
            'review_step' => 'mtqa',
            'status_from' => $oldStatus,
            'status_to'   => $request->decision,
        ]);

        $label = $request->decision === 'approved' ? 'approved' : 'disapproved';

        return redirect()
            ->route('reviewer.concrete-pouring.index')
            ->with('success', "Concrete pouring request has been {$label} successfully.");
    }

    // =========================================================================
    // PRIVATE — STEP CONTROL
    // =========================================================================

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

    // =========================================================================
    // PRIVATE — SIGNATURE VALUE NORMALISER
    // =========================================================================

    private function resolveSignatureValue(?string $value, string $prefix = 'sig'): ?string
    {
        if (empty($value)) return null;

        if (str_starts_with($value, 'data:image')) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $value));
            $filename  = 'signatures/' . $prefix . '_' . time() . '.png';
            Storage::disk('public')->put($filename, $imageData);
            return $filename;
        }

        $storageUrl = url('storage') . '/';
        if (str_starts_with($value, $storageUrl)) {
            return ltrim(substr($value, strlen($storageUrl)), '/');
        }

        if (str_starts_with($value, '/storage/')) {
            return ltrim(substr($value, strlen('/storage/')), '/');
        }

        return Auth::user()->signature_path ?? null;
    }

    // =========================================================================
    // PRIVATE — PREDICATES / QUERY HELPERS
    // =========================================================================

    private function isCurrentReviewer(ConcretePouring $concretePouring, $user): bool
    {
        $step = $concretePouring->current_review_step;
        if (!$step || !isset(self::REVIEW_STEPS[$step])) return false;
        $col = self::REVIEW_STEPS[$step];
        return $concretePouring->$col == $user->id;
    }

    private function userIsAssignedAnywhere(ConcretePouring $concretePouring, $user): bool
    {
        foreach (self::REVIEW_STEPS as $col) {
            if ($concretePouring->$col == $user->id) return true;
        }
        return false;
    }

    private function assignedToUserQuery(int $userId)
    {
        return ConcretePouring::where(function ($q) use ($userId) {
            $q->where(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'resident_engineer')
                   ->where('resident_engineer_user_id', $userId);
            })->orWhere(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'provincial_engineer')
                   ->where('noted_by_user_id', $userId);
            })->orWhere(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'mtqa')
                   ->where('me_mtqa_user_id', $userId);
            });
        });
    }

    private function completedByUser($user)
    {
        return ConcretePouring::where(function ($q) use ($user) {
            $q->where('resident_engineer_user_id', $user->id)->whereNotNull('re_date');
        })->orWhere(function ($q) use ($user) {
            $q->where('noted_by_user_id', $user->id)->whereNotNull('noted_date');
        })->orWhere(function ($q) use ($user) {
            $q->where('me_mtqa_user_id', $user->id)->whereNotNull('me_mtqa_date');
        });
    }
}
