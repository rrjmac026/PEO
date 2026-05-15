{{-- resources/views/reviewer/concrete-pouring/partials/_step-mtqa.blade.php --}}
{{-- Variables expected: $concretePouring, $isMyTurn --}}

@php
    $mtqaDone    = !is_null($concretePouring->me_mtqa_date);
    $mtqaActive  = $concretePouring->current_review_step === 'mtqa';
    $isMyMtqa    = $isMyTurn && $mtqaActive;
    $isFinalised = in_array($concretePouring->status, ['approved', 'disapproved']);
    $sig         = $concretePouring->me_mtqa_signature;
    $mtqaSigUrl  = $sig
        ? (str_starts_with($sig, 'http') || str_starts_with($sig, 'data:')
            ? $sig
            : asset('storage/' . $sig))
        : null;
    $showMtqaSig = !is_null($mtqaSigUrl);
@endphp

<div class="cp-timeline-item">
    <div class="cp-tl-icon-wrap">
        <div class="cp-tl-icon {{ $isFinalised ? 'done' : ($mtqaActive ? 'active' : 'waiting') }}">
            @if($isFinalised)<i class="fas fa-check"></i>
            @elseif($mtqaActive)<i class="fas fa-clock"></i>
            @else<i class="fas fa-circle"></i>@endif
        </div>
    </div>

    <div style="flex:1">
        <div class="cp-tl-label" style="display:flex;align-items:center;gap:8px;">
            Step 3 — ME/MTQA Final Decision
            <span style="font-size:10px;background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0;border-radius:20px;padding:1px 8px;font-weight:700;letter-spacing:0.3px;">FINAL</span>
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
            <div class="rv-decision-box">
                <div class="rv-decision-title">
                    <i class="fas fa-gavel text-green-600"></i>
                    Submit Final Decision & Signature
                </div>

                <form action="{{ route('reviewer.concrete-pouring.store-mtqa-review', $concretePouring) }}"
                      method="POST" id="mtqa-review-form">
                    @csrf

                    {{-- Decision radio --}}
                    <div class="mb-4">
                        <p style="font-size:13px;font-weight:600;color:var(--cp-text);margin-bottom:10px;">
                            Decision <span style="color:#ef4444;">*</span>
                        </p>
                        <div class="rv-decision-radios">
                            <label class="rv-decision-radio">
                                <input type="radio" name="decision" value="approved"
                                       {{ old('decision') === 'approved' ? 'checked' : '' }}
                                       class="accent-green-600" required>
                                <span class="rv-decision-approve">✓ Approve</span>
                            </label>
                            <label class="rv-decision-radio">
                                <input type="radio" name="decision" value="disapproved"
                                       {{ old('decision') === 'disapproved' ? 'checked' : '' }}
                                       class="accent-red-600">
                                <span class="rv-decision-disapprove">✗ Disapprove</span>
                            </label>
                        </div>
                        @error('decision')
                            <p style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remarks --}}
                    <div class="mb-3">
                        <label class="cp-label">
                            Remarks
                            <span style="color:var(--cp-muted);font-weight:normal;font-size:12px;">(required if disapproving)</span>
                        </label>
                        <textarea name="me_mtqa_remarks" rows="3" class="cp-textarea"
                                  placeholder="Enter your remarks or reasons for this decision…">{{ old('me_mtqa_remarks') }}</textarea>
                    </div>

                    @include('reviewer.concrete-pouring.partials._signature-pad', [
                        'cp_prefix'     => 'mtqa',
                        'cp_radioName'  => 'mtqa_sig_mode',
                        'cp_hiddenName' => 'me_mtqa_signature',
                    ])

                    <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;">
                        <button type="submit" name="decision_submit" value="approved"
                                onclick="document.querySelector('input[name=decision][value=approved]').checked=true"
                                class="px-6 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition inline-flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Approve
                        </button>
                        <button type="submit" name="decision_submit" value="disapproved"
                                onclick="document.querySelector('input[name=decision][value=disapproved]').checked=true"
                                class="px-6 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition inline-flex items-center gap-2">
                            <i class="fas fa-times-circle"></i> Disapprove
                        </button>
                    </div>
                </form>
            </div>
        @elseif(!$mtqaDone && !$mtqaActive)
            <div class="rv-readonly-box">Waiting for previous reviewers to complete their steps.</div>
        @endif
    </div>

    <div>
        @if($concretePouring->status === 'approved')
            <span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Approved</span>
        @elseif($concretePouring->status === 'disapproved')
            <span class="cp-badge disapproved" style="font-size:11px;padding:3px 8px">Disapproved</span>
        @elseif($mtqaActive)
            <span class="cp-badge requested" style="font-size:11px;padding:3px 8px">Pending Decision</span>
        @else
            <span style="font-size:11px;color:var(--cp-muted)">Waiting</span>
        @endif
    </div>
</div>
