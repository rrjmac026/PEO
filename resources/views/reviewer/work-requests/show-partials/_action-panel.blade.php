{{-- ── Role-specific Action Form ── --}}
@if($isMyTurn)
    @if($role === 'site_inspector')
        @include('reviewer.work-requests.partials._site-inspector-form')
    @elseif($role === 'surveyor')
        @include('reviewer.work-requests.partials._surveyor-form')
    @elseif($role === 'resident_engineer')
        @include('reviewer.work-requests.partials._resident-engineer-form')
    @elseif($role === 'mtqa')
        @include('reviewer.work-requests.partials._mtqa-form')
    @elseif($role === 'engineeriv')
        @include('reviewer.work-requests.partials._engineer-iv-form')
    @elseif($role === 'engineeriii')
        @include('reviewer.work-requests.partials._recommending-approval-form')
    @elseif($role === 'provincial_engineer')
        {{-- Provincial Engineer: FINAL DECISION --}}
        @include('reviewer.work-requests.partials._provincial-engineer-form')
    @endif

@elseif($role === 'mtqa' && $workRequest->status === 'approved')
    {{--
        MTQA special case:
        Their review step is done, but once the Provincial Engineer approves,
        MTQA can see the print/download panel.
    --}}
    <div class="wrd-card" style="border-top: 3px solid #f59e0b;">
        <div class="wrd-card-head">
            <div class="wrd-card-head-icon orange">
                <i class="fas fa-print" style="color:#f59e0b;"></i>
            </div>
            <span class="wrd-card-title">MTQA — Print Approved Work Request</span>
            <span style="margin-left:auto; padding:4px 12px; background:rgba(52,211,153,0.15);
                        color:#34d399; border-radius:20px; font-size:11px; font-weight:700;
                        letter-spacing:0.5px; text-transform:uppercase;">
                ✓ Approved
            </span>
        </div>
        <div class="wrd-card-body">
            <p style="font-size:14px; color:var(--wr-text); margin-bottom:20px; line-height:1.6;">
                This work request has been <strong style="color:#34d399;">approved</strong> by the
                <strong>Provincial Engineer</strong>. You can now print or download the official document.
            </p>

            <div style="padding:14px 16px; border-radius:8px; background:rgba(52,211,153,0.08);
                        border:1px solid rgba(52,211,153,0.25); margin-bottom:20px;">
                <p style="font-size:12px; font-weight:700; color:#34d399; margin-bottom:8px;
                        text-transform:uppercase; letter-spacing:0.5px;">
                    <i class="fas fa-check-circle mr-2"></i> Approval Details
                </p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <p style="font-size:11px; color:var(--wr-muted); text-transform:uppercase;
                                letter-spacing:0.5px; margin-bottom:3px;">Approved By</p>
                        <p style="font-size:13px; font-weight:600; color:var(--wr-text);">
                            {{ $workRequest->approved_by ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p style="font-size:11px; color:var(--wr-muted); text-transform:uppercase;
                                letter-spacing:0.5px; margin-bottom:3px;">Project</p>
                        <p style="font-size:13px; font-weight:600; color:var(--wr-text);">
                            {{ $workRequest->name_of_project }}
                        </p>
                    </div>
                    @if($workRequest->approved_recommendation_action)
                        <div style="grid-column:span 2;">
                            <p style="font-size:11px; color:var(--wr-muted); text-transform:uppercase;
                                    letter-spacing:0.5px; margin-bottom:3px;">Remarks</p>
                            <p style="font-size:13px; color:var(--wr-text); white-space:pre-wrap;">
                                {{ $workRequest->approved_recommendation_action }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <a href="{{ route('reviewer.work-requests.print', $workRequest) }}"
                target="_blank"
                style="padding:10px 24px; background:#f59e0b; color:#fff; border:none;
                        border-radius:8px; font-size:13px; font-weight:700; text-decoration:none;
                        display:inline-flex; align-items:center; gap:8px; transition:opacity 0.2s;"
                onmouseover="this.style.opacity='0.85'"
                onmouseout="this.style.opacity='1'">
                    <i class="fas fa-print"></i> Print Work Request
                </a>

                <a href="{{ route('reviewer.work-requests.download', $workRequest) }}"
                style="padding:10px 24px; background:#059669; color:#fff; border:none;
                        border-radius:8px; font-size:13px; font-weight:700; text-decoration:none;
                        display:inline-flex; align-items:center; gap:8px; transition:opacity 0.2s;"
                onmouseover="this.style.opacity='0.85'"
                onmouseout="this.style.opacity='1'">
                    <i class="fas fa-download"></i> Download PDF
                </a>
            </div>
        </div>
    </div>

@else
    <div class="wrd-card">
        <div class="wrd-card-body" style="text-align:center; padding:32px;">
            @if($workRequest->status === 'approved')
                <p style="color:var(--wr-muted); font-size:14px;">
                    ✅ This work request has been <strong style="color:#34d399;">approved</strong>
                    by the Provincial Engineer.
                </p>
            @elseif($workRequest->status === 'rejected')
                <p style="color:var(--wr-muted); font-size:14px;">
                    ❌ This work request has been <strong style="color:#f87171;">rejected</strong>
                    by the Provincial Engineer.
                </p>
            @else
                <p style="color:var(--wr-muted); font-size:14px;">
                    👁 It is not your turn yet. Current step:
                    <strong style="color:var(--wr-text);">{{ $workRequest->current_step_label }}</strong>
                </p>
            @endif
        </div>
    </div>
@endif