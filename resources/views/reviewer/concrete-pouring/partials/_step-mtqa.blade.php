{{-- resources/views/reviewer/concrete-pouring/partials/_step-mtqa.blade.php --}}
{{-- Variables expected: $concretePouring, $isMyTurn --}}

@php
    $mtqaDone    = !is_null($concretePouring->me_mtqa_date);
    $mtqaActive  = $concretePouring->current_review_step === 'mtqa';
    $isMyMtqa    = $isMyTurn && $mtqaActive;
    $sig         = $concretePouring->me_mtqa_signature;

    $mtqaSigUrl = null;
    if ($sig) {
        if (str_starts_with($sig, 'data:image')) {
            $mtqaSigUrl = $sig;
        } elseif (str_starts_with($sig, 'http://') || str_starts_with($sig, 'https://')) {
            $mtqaSigUrl = $sig;
        } elseif (str_starts_with($sig, '/storage/')) {
            $mtqaSigUrl = asset(ltrim($sig, '/'));
        } elseif (str_starts_with($sig, 'storage/')) {
            $mtqaSigUrl = asset($sig);
        } else {
            $mtqaSigUrl = asset('storage/' . $sig);
        }
    }

    $showMtqaSig = !is_null($mtqaSigUrl);
@endphp

<div class="cp-timeline-item">
    <div class="cp-tl-icon-wrap">
        {{-- Icon state uses $mtqaDone, same pattern as RE ($reDone) and PE ($peDone) --}}
        <div class="cp-tl-icon {{ $mtqaDone ? 'done' : ($mtqaActive ? 'active' : 'waiting') }}">
            @if($mtqaDone)<i class="fas fa-check"></i>
            @elseif($mtqaActive)<i class="fas fa-clock"></i>
            @else<i class="fas fa-circle"></i>@endif
        </div>
    </div>

    <div style="flex:1">
        <div class="cp-tl-label" style="display:flex;align-items:center;gap:8px;">
            Step 2 — ME/MTQA Review
        </div>
        <div class="cp-tl-name">{{ $concretePouring->meMtqaChecker?->name ?? 'Not assigned' }}</div>

        @if($concretePouring->me_mtqa_date)
            <div class="cp-tl-date">Decision: {{ $concretePouring->me_mtqa_date->format('M d, Y') }}</div>
        @endif

        @if($concretePouring->me_mtqa_remarks)
            <div class="cp-tl-remark">"{{ $concretePouring->me_mtqa_remarks }}"</div>
        @endif

        @if($showMtqaSig)
            <div class="cp-sig-display">
                <div>
                    <div class="cp-sig-display-label"><i class="fas fa-pen-nib mr-1"></i> Signed by {{ $concretePouring->meMtqaChecker?->name }}</div>
                    <img src="{{ $mtqaSigUrl }}" alt="ME/MTQA Signature">
                </div>
            </div>
        @endif

        @if($isMyMtqa)
            <div class="rv-form-box">
                <div class="rv-form-title">
                    <i class="fas fa-clipboard-check text-orange-500"></i>
                    Submit Your ME/MTQA Review & Signature
                </div>

                <form action="{{ route('reviewer.concrete-pouring.store-mtqa-review', $concretePouring) }}"
                    method="POST" id="mtqa-review-form">
                    @csrf

                    <div class="mb-3">
                        <label class="cp-label">Remarks <span style="color:var(--cp-muted)">(optional)</span></label>
                        <textarea name="me_mtqa_remarks" rows="3" class="cp-textarea"
                                placeholder="Enter your ME/MTQA review remarks…">{{ old('me_mtqa_remarks') }}</textarea>
                    </div>

                    @include('reviewer.concrete-pouring.partials._signature-pad', [
                        'cp_prefix'     => 'mtqa',
                        'cp_radioName'  => 'mtqa_sig_mode',
                        'cp_hiddenName' => 'me_mtqa_signature',
                    ])

                    <div style="margin-top:16px;">
                        <button type="submit"
                                class="px-6 py-2.5 bg-orange-500 text-white text-sm font-semibold rounded-lg hover:bg-orange-600 transition inline-flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Submit Review
                        </button>
                    </div>
                </form>
            </div>
        @elseif(!$mtqaDone && !$mtqaActive)
            <div class="rv-readonly-box">Waiting for previous reviewers to complete their steps.</div>
        @endif
    </div>

    <div>
        @if($mtqaDone)
            <span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Done</span>
        @elseif($mtqaActive)
            <span class="cp-badge requested" style="font-size:11px;padding:3px 8px">In Progress</span>
        @else
            <span style="font-size:11px;color:var(--cp-muted)">Waiting</span>
        @endif
    </div>
</div>