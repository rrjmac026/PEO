{{-- resources/views/admin/work-requests/show-partials/_review-pipeline.blade.php --}}

@push('styles')
<style>
    /* ── Review Pipeline ── */
    .rp-progress-bar-wrap {
        height: 3px; background: var(--wr-border); border-radius: 2px;
        margin: 14px 0 4px; overflow: hidden;
    }
    .rp-progress-bar {
        height: 100%; border-radius: 2px;
        background: linear-gradient(90deg, var(--wr-accent2), var(--wr-accent));
        transition: width 0.4s;
    }
    .rp-progress-label { font-size: 11px; color: var(--wr-muted); margin-bottom: 2px; }

    .rp-steps { display: flex; flex-direction: column; margin-top: 8px; }
    .rp-step  { display: flex; gap: 0; position: relative; }

    .rp-connector { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; width: 36px; }
    .rp-line { width: 1.5px; flex: 1; min-height: 14px; background: var(--wr-border); }
    .rp-line.done   { background: rgba(0,212,170,0.3); }
    .rp-line.active { background: rgba(79,141,255,0.25); }

    .rp-dot {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 500; flex-shrink: 0;
        border: 1.5px solid; transition: all 0.2s; z-index: 1;
    }
    .rp-dot.done    { background: rgba(0,212,170,0.12); color: var(--wr-accent2); border-color: rgba(0,212,170,0.4); }
    .rp-dot.active  {
        background: rgba(79,141,255,0.12); color: var(--wr-accent); border-color: rgba(79,141,255,0.4);
        animation: rp-pulse 2s ease-in-out infinite;
    }
    .rp-dot.pending { background: var(--wr-surface2); color: var(--wr-muted); border-color: var(--wr-border); }
    .rp-dot.skipped { background: var(--wr-surface2); color: var(--wr-muted); border-color: var(--wr-border); opacity: 0.5; }
    @keyframes rp-pulse {
        0%,100% { box-shadow: 0 0 0 4px rgba(79,141,255,0.08); }
        50%      { box-shadow: 0 0 0 7px rgba(79,141,255,0.04); }
    }

    .rp-content { flex: 1; padding: 4px 0 20px 14px; }
    .rp-step:last-child .rp-content { padding-bottom: 4px; }

    .rp-step-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
    .rp-step-label { font-size: 13.5px; font-weight: 500; line-height: 1.35; }
    .rp-step-label.done    { color: var(--wr-accent2); }
    .rp-step-label.active  { color: var(--wr-accent); }
    .rp-step-label.pending,.rp-step-label.skipped { color: var(--wr-muted); }

    .rp-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 500;
        letter-spacing: 0.3px; white-space: nowrap; margin-left: 8px;
    }
    .rp-badge.waiting { background: rgba(79,141,255,0.12); color: var(--wr-accent); }
    .rp-badge.final   { background: rgba(20,184,166,0.12); color: #14b8a6; }
    .rp-badge.skipped { background: var(--wr-surface2);    color: var(--wr-muted); }

    .rp-assignee {
        font-size: 11.5px; color: var(--wr-muted); font-weight: 400;
        display: flex; align-items: center; gap: 5px; white-space: nowrap; flex-shrink: 0;
    }
    .rp-avatar {
        width: 20px; height: 20px; border-radius: 50%;
        background: rgba(79,141,255,0.15); color: var(--wr-accent);
        font-size: 9px; font-weight: 600;
        display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .rp-avatar.done { background: rgba(0,212,170,0.15); color: var(--wr-accent2); }

    .rp-completed-by {
        margin-top: 5px; font-size: 12px; color: var(--wr-muted);
        display: flex; align-items: center; gap: 5px;
    }
    .rp-completed-by i { font-size: 11px; color: var(--wr-accent2); }

    .rp-decision {
        margin-top: 20px; border-radius: var(--wr-radius-sm);
        padding: 16px; border: 1px solid; position: relative; overflow: hidden;
    }
    .rp-decision.approved { background: rgba(0,212,170,0.06); border-color: rgba(0,212,170,0.3); border-left: 3px solid var(--wr-accent2); }
    .rp-decision.rejected { background: rgba(248,113,113,0.06); border-color: rgba(248,113,113,0.3); border-left: 3px solid var(--wr-accent3); }
    .rp-decision-title {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; margin-bottom: 8px; display: flex; align-items: center; gap: 7px;
    }
    .rp-decision-title.approved { color: var(--wr-accent2); }
    .rp-decision-title.rejected { color: var(--wr-accent3); }
    .rp-decision-body { font-size: 13px; color: var(--wr-text); line-height: 1.55; white-space: pre-wrap; margin-bottom: 8px; }
    .rp-decision-by   { font-size: 11.5px; color: var(--wr-muted); }
    .rp-print-notice  {
        margin-top: 12px; padding: 10px 14px; border-radius: 6px;
        background: rgba(79,141,255,0.08); border: 1px solid rgba(79,141,255,0.2);
        font-size: 12px; color: var(--wr-accent); display: flex; align-items: center; gap: 7px;
    }
</style>
@endpush

@php
    $steps = [
        ['step' => 'site_inspector',     'label' => 'Site Inspector',             'assigned' => $workRequest->assignedSiteInspector,     'done_field' => $workRequest->inspected_by_site_inspector],
        ['step' => 'surveyor',           'label' => 'Surveyor',                   'assigned' => $workRequest->assignedSurveyor,           'done_field' => $workRequest->surveyor_name],
        ['step' => 'resident_engineer',  'label' => 'Resident Engineer',          'assigned' => $workRequest->assignedResidentEngineer,   'done_field' => $workRequest->resident_engineer_name],
        ['step' => 'mtqa',               'label' => 'Material Engineer Assigned', 'assigned' => $workRequest->assignedMtqa,               'done_field' => $workRequest->checked_by_mtqa],
        ['step' => 'engineer_iv',        'label' => 'MTQA Division Chief',        'assigned' => $workRequest->assignedEngineerIv,         'done_field' => $workRequest->reviewed_by],
        ['step' => 'engineer_iii',       'label' => 'Project Division Chief',     'assigned' => $workRequest->assignedEngineerIii,        'done_field' => $workRequest->recommending_approval_by],
        ['step' => 'provincial_engineer','label' => 'Provincial Engineer',        'assigned' => $workRequest->assignedProvincialEngineer, 'done_field' => $workRequest->approved_by, 'is_final' => true],
    ];

    $completedCount = collect($steps)->filter(fn($s) => !empty($s['done_field']))->count();
    $totalCount     = count($steps);
    $progressPct    = round(($completedCount / $totalCount) * 100);
@endphp

<div class="wrd-card">
    {{-- Header --}}
    <div class="wrd-card-head" style="justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="wrd-card-head-icon purple">📋</div>
            <span class="wrd-card-title">Review Pipeline</span>
        </div>

        @if(Auth::user()->role === 'admin')
            @if(in_array($workRequest->status, ['submitted', 'assigned']))
                <a href="{{ route('admin.work-requests.assign-form', $workRequest) }}"
                   style="padding: 6px 14px; background: var(--wr-accent); color: white; border: none;
                          border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none;
                          display: inline-flex; align-items: center; gap: 6px; transition: opacity 0.2s;"
                   onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-user-plus"></i>
                    {{ $workRequest->isAssigned() ? '↺ Re-assign' : '+ Assign Reviewers' }}
                </a>
            @endif
        @endif
    </div>

    <div class="wrd-card-body">

        {{-- Progress bar --}}
        <div class="rp-progress-label">{{ $completedCount }} of {{ $totalCount }} steps completed</div>
        <div class="rp-progress-bar-wrap">
            <div class="rp-progress-bar" style="width: {{ $progressPct }}%"></div>
        </div>

        {{-- Steps --}}
        <div class="rp-steps">
            @foreach ($steps as $index => $s)
                @php
                    $isCurrent = $workRequest->current_review_step === $s['step'];
                    $isDone    = !empty($s['done_field']);
                    $isSkipped = !$isCurrent && !$isDone && is_null($s['assigned']) && ($s['step'] !== 'provincial_engineer');
                    $isFinal   = $s['is_final'] ?? false;

                    $dotClass  = $isDone ? 'done' : ($isCurrent ? 'active' : ($isSkipped ? 'skipped' : 'pending'));
                    $labelClass = $dotClass;

                    // Line after this step (all except last)
                    $lineClass = $isDone ? 'done' : ($isCurrent ? 'active' : '');
                    $isLast    = $index === count($steps) - 1;

                    // Avatar initials
                    $initials = '';
                    if ($s['assigned']) {
                        $parts    = explode(' ', $s['assigned']->name);
                        $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                    }
                @endphp

                <div class="rp-step">
                    {{-- Connector --}}
                    <div class="rp-connector">
                        <div class="rp-dot {{ $dotClass }}">
                            @if($isDone)
                                <i class="fas fa-check" style="font-size:12px;"></i>
                            @elseif($isCurrent)
                                <i class="fas fa-spinner fa-spin" style="font-size:12px;"></i>
                            @else
                                <span style="font-size:10px; opacity:0.4;">—</span>
                            @endif
                        </div>
                        @unless($isLast)
                            <div class="rp-line {{ $lineClass }}"></div>
                        @endunless
                    </div>

                    {{-- Content --}}
                    <div class="rp-content">
                        <div class="rp-step-top">
                            <div>
                                <span class="rp-step-label {{ $labelClass }}">
                                    {{ $s['label'] }}
                                    @if($isFinal)
                                        <span class="rp-badge final">Final Decision</span>
                                    @endif
                                    @if($isCurrent)
                                        <span class="rp-badge waiting">Waiting</span>
                                    @endif
                                    @if($isSkipped)
                                        <span class="rp-badge skipped">Skipped</span>
                                    @endif
                                </span>
                            </div>

                            @if($s['assigned'])
                                <div class="rp-assignee">
                                    <div class="rp-avatar {{ $isDone ? 'done' : '' }}">{{ $initials }}</div>
                                    {{ $s['assigned']->name }}
                                </div>
                            @endif
                        </div>

                        @if($isDone && !empty($s['done_field']))
                            <div class="rp-completed-by">
                                <i class="fas fa-check-circle"></i>
                                Completed by <strong>{{ $s['done_field'] }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Final decision block --}}
        @if($workRequest->approved_by && in_array($workRequest->status, ['approved', 'rejected']))
            <div class="rp-decision {{ $workRequest->status }}">
                <div class="rp-decision-title {{ $workRequest->status }}">
                    <i class="fas {{ $workRequest->status === 'approved' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                    Provincial Engineer Decision: {{ ucfirst($workRequest->status) }}
                </div>
                @if($workRequest->approved_recommendation_action)
                    <div class="rp-decision-body">{{ $workRequest->approved_recommendation_action }}</div>
                @endif
                <div class="rp-decision-by">by <strong>{{ $workRequest->approved_by }}</strong></div>

                @if($workRequest->status === 'approved')
                    <div class="rp-print-notice">
                        <i class="fas fa-print"></i>
                        <strong>Material Engineer Assigned</strong> has been notified and can now print this work request.
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>