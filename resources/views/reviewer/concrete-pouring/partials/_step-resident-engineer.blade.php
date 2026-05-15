{{-- resources/views/reviewer/concrete-pouring/partials/_step-resident-engineer.blade.php --}}
{{-- Variables expected: $concretePouring, $isMyTurn --}}

@php
    $reDone   = !is_null($concretePouring->re_date);
    $reActive = $concretePouring->current_review_step === 'resident_engineer';
    $isMyRe   = $isMyTurn && $reActive;
    $sig      = $concretePouring->re_signature;

    $reSigUrl = null;
    if ($sig) {
        if (str_starts_with($sig, 'data:image')) {
            // Raw base64 stored directly (legacy)
            $reSigUrl = $sig;
        } elseif (str_starts_with($sig, 'http://') || str_starts_with($sig, 'https://')) {
            // Already a full URL
            $reSigUrl = $sig;
        } elseif (str_starts_with($sig, '/storage/')) {
            // Absolute path: /storage/signatures/...
            $reSigUrl = asset(ltrim($sig, '/'));
        } elseif (str_starts_with($sig, 'storage/')) {
            // Relative with storage/ prefix
            $reSigUrl = asset($sig);
        } else {
            // Plain relative path: signatures/re_4_xxx.png
            $reSigUrl = asset('storage/' . $sig);
        }
    }

    $showReSig = !is_null($reSigUrl);
@endphp

<div class="cp-timeline-item">
    <div class="cp-tl-icon-wrap">
        <div class="cp-tl-icon {{ $reDone ? 'done' : ($reActive ? 'active' : 'waiting') }}">
            @if($reDone)<i class="fas fa-check"></i>
            @elseif($reActive)<i class="fas fa-clock"></i>
            @else<i class="fas fa-circle"></i>@endif
        </div>
    </div>

    <div style="flex:1">
        <div class="cp-tl-label">Step 1 — Resident Engineer Review</div>
        <div class="cp-tl-name">{{ $concretePouring->residentEngineer?->name ?? 'Not assigned' }}</div>

        @if($concretePouring->re_date)
            <div class="cp-tl-date">Reviewed: {{ $concretePouring->re_date->format('M d, Y') }}</div>
        @endif

        @if($concretePouring->re_remarks)
            <div class="cp-tl-remark">"{{ $concretePouring->re_remarks }}"</div>
        @endif

        @if($showReSig)
            <div class="cp-sig-display">
                <div>
                    <div class="cp-sig-display-label"><i class="fas fa-pen-nib mr-1"></i> Signed by {{ $concretePouring->residentEngineer?->name }}</div>
                    <img src="{{ $reSigUrl }}" alt="Resident Engineer Signature">
                </div>
            </div>
        @endif

        @if($isMyRe)
            <div class="rv-form-box">
                <div class="rv-form-title">
                    <i class="fas fa-pen text-blue-500"></i>
                    Submit Your Resident Engineer Review & Signature
                </div>
                <form action="{{ route('reviewer.concrete-pouring.store-engineer-review', $concretePouring) }}"
                      method="POST" id="re-review-form">
                    @csrf
                    <div class="mb-3">
                        <label class="cp-label">Remarks <span style="color:var(--cp-muted)">(optional)</span></label>
                        <textarea name="re_remarks" rows="3" class="cp-textarea"
                                  placeholder="Enter your engineering review remarks…">{{ old('re_remarks') }}</textarea>
                    </div>

                    @include('reviewer.concrete-pouring.partials._signature-pad', [
                        'cp_prefix'     => 're',
                        'cp_radioName'  => 're_sig_mode',
                        'cp_hiddenName' => 're_signature',
                    ])

                    <div style="margin-top:16px;">
                        <button type="submit"
                                class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition inline-flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Submit Engineer Review
                        </button>
                    </div>
                </form>
            </div>
        @elseif(!$reDone && !$reActive)
            <div class="rv-readonly-box">Waiting for Resident Engineer step to become active.</div>
        @endif
    </div>

    <div>
        @if($reDone)
            <span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Done</span>
        @elseif($reActive)
            <span class="cp-badge requested" style="font-size:11px;padding:3px 8px">In Progress</span>
        @else
            <span style="font-size:11px;color:var(--cp-muted)">Waiting</span>
        @endif
    </div>
</div>