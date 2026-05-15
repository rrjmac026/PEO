{{-- resources/views/reviewer/concrete-pouring/partials/_step-provincial-engineer.blade.php --}}
{{-- Variables expected: $concretePouring, $isMyTurn --}}

@php
    $peDone   = !is_null($concretePouring->noted_date);
    $peActive = $concretePouring->current_review_step === 'provincial_engineer';
    $isMyPe   = $isMyTurn && $peActive;
    $sig      = $concretePouring->noted_by_signature;

    $peSigUrl = null;
    if ($sig) {
        if (str_starts_with($sig, 'data:image')) {
            $peSigUrl = $sig;
        } elseif (str_starts_with($sig, 'http://') || str_starts_with($sig, 'https://')) {
            $peSigUrl = $sig;
        } elseif (str_starts_with($sig, '/storage/')) {
            $peSigUrl = asset(ltrim($sig, '/'));
        } elseif (str_starts_with($sig, 'storage/')) {
            $peSigUrl = asset($sig);
        } else {
            $peSigUrl = asset('storage/' . $sig);
        }
    }

    $showPeSig = !is_null($peSigUrl);
@endphp

<div class="cp-timeline-item">
    <div class="cp-tl-icon-wrap">
        <div class="cp-tl-icon {{ $peDone ? 'done' : ($peActive ? 'active' : 'waiting') }}">
            @if($peDone)<i class="fas fa-check"></i>
            @elseif($peActive)<i class="fas fa-clock"></i>
            @else<i class="fas fa-circle"></i>@endif
        </div>
    </div>

    <div style="flex:1">
        <div class="cp-tl-label">Step 2 — Noted by Provincial Engineer</div>
        <div class="cp-tl-name">{{ $concretePouring->notedByEngineer?->name ?? 'Not assigned' }}</div>

        @if($concretePouring->noted_date)
            <div class="cp-tl-date">Noted: {{ $concretePouring->noted_date->format('M d, Y') }}</div>
        @endif

        @if($concretePouring->approval_remarks && $peDone && !in_array($concretePouring->status, ['approved','disapproved']))
            <div class="cp-tl-remark">"{{ $concretePouring->approval_remarks }}"</div>
        @endif

        @if($showPeSig)
            <div class="cp-sig-display">
                <div>
                    <div class="cp-sig-display-label"><i class="fas fa-pen-nib mr-1"></i> Signed by {{ $concretePouring->notedByEngineer?->name }}</div>
                    <img src="{{ $peSigUrl }}" alt="Provincial Engineer Signature">
                </div>
            </div>
        @endif

        @if($isMyPe)
            <div class="rv-form-box">
                <div class="rv-form-title">
                    <i class="fas fa-pen text-orange-500"></i>
                    Submit Your Note & Signature
                </div>
                <form action="{{ route('reviewer.concrete-pouring.store-provincial-note', $concretePouring) }}"
                      method="POST" id="pe-review-form">
                    @csrf
                    <div class="mb-3">
                        <label class="cp-label">Provincial Remarks <span style="color:var(--cp-muted)">(optional)</span></label>
                        <textarea name="provincial_remarks" rows="3" class="cp-textarea"
                                  placeholder="Enter your note or observations as Provincial Engineer…">{{ old('provincial_remarks') }}</textarea>
                    </div>

                    @include('reviewer.concrete-pouring.partials._signature-pad', [
                        'cp_prefix'     => 'pe',
                        'cp_radioName'  => 'pe_sig_mode',
                        'cp_hiddenName' => 'noted_by_signature',
                    ])

                    <div style="margin-top:16px;">
                        <button type="submit"
                                class="px-6 py-2.5 bg-orange-500 text-white text-sm font-semibold rounded-lg hover:bg-orange-600 transition inline-flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Submit Note
                        </button>
                    </div>
                </form>
            </div>
        @elseif(!$peDone && !$peActive)
            <div class="rv-readonly-box">Waiting for Provincial Engineer step to become active.</div>
        @endif
    </div>

    <div>
        @if($peDone)
            <span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Noted</span>
        @elseif($peActive)
            <span class="cp-badge requested" style="font-size:11px;padding:3px 8px">In Progress</span>
        @else
            <span style="font-size:11px;color:var(--cp-muted)">Waiting</span>
        @endif
    </div>
</div>