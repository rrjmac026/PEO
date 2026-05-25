{{-- resources/views/admin/work-requests/partials/_review-pipeline.blade.php --}}
<div class="wrd-card">
    <div class="wrd-card-head" style="justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="wrd-card-head-icon purple">📋</div>
            <span class="wrd-card-title">Review Pipeline</span>
        </div>

        {{-- Admin can only assign/re-assign --}}
        @if(Auth::user()->role === 'admin')
            <div style="display: flex; gap: 8px;">
                @if(in_array($workRequest->status, ['submitted', 'assigned']))
                    <a href="{{ route('admin.work-requests.assign-form', $workRequest) }}"
                       style="padding: 6px 14px; background: var(--wr-accent); color: white; border: none;
                              border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none;
                              display: inline-flex; align-items: center; gap: 6px; transition: opacity 0.2s;"
                       onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                        {{ $workRequest->isAssigned() ? '↺ Re-assign' : '+ Assign Reviewers' }}
                    </a>
                @endif
            </div>
        @endif
    </div>

    <div class="wrd-card-body">
        @php
            $steps = [
                ['step' => 'site_inspector',     'label' => 'Site Inspector',      'icon' => '👷', 'assigned' => $workRequest->assignedSiteInspector,      'done_field' => $workRequest->inspected_by_site_inspector],
                ['step' => 'surveyor',           'label' => 'Surveyor',            'icon' => '📐', 'assigned' => $workRequest->assignedSurveyor,            'done_field' => $workRequest->surveyor_name],
                ['step' => 'resident_engineer',  'label' => 'Resident Engineer',   'icon' => '🛠️', 'assigned' => $workRequest->assignedResidentEngineer,    'done_field' => $workRequest->resident_engineer_name],
                ['step' => 'mtqa',               'label' => 'MTQA',                'icon' => '✓',  'assigned' => $workRequest->assignedMtqa,                'done_field' => $workRequest->checked_by_mtqa],
                ['step' => 'engineer_iv',        'label' => 'Engineer IV',         'icon' => '👨‍💼', 'assigned' => $workRequest->assignedEngineerIv,          'done_field' => $workRequest->reviewed_by],
                ['step' => 'engineer_iii',       'label' => 'Engineer III',        'icon' => '👨‍💼', 'assigned' => $workRequest->assignedEngineerIii,         'done_field' => $workRequest->recommending_approval_by],
                ['step' => 'provincial_engineer','label' => 'Provincial Engineer', 'icon' => '👔', 'assigned' => $workRequest->assignedProvincialEngineer,  'done_field' => $workRequest->approved_by, 'is_final' => true],
            ];
        @endphp

        <div style="margin-top: 12px;">
            @foreach ($steps as $index => $s)
                @php
                    $isCurrent = $workRequest->current_review_step === $s['step'];
                    $isDone    = !empty($s['done_field']);
                    $isSkipped = !$isCurrent && !$isDone && is_null($s['assigned']) && ($s['step'] !== 'provincial_engineer');
                    $isFinal   = $s['is_final'] ?? false;
                @endphp

                <div style="display: flex; gap: 12px; padding: 14px 0;
                    {{ $index < count($steps) - 1 ? 'border-bottom: 1px solid var(--wr-border);' : '' }}">

                    {{-- Status indicator circle --}}
                    <div style="
                        width: 36px; height: 36px; border-radius: 50%;
                        display: flex; align-items: center; justify-content: center;
                        flex-shrink: 0; font-weight: 700; font-size: 14px;
                        {{ $isDone   ? 'background: rgba(0, 212, 170, 0.2); color: var(--wr-accent2); border: 2px solid var(--wr-accent2);' : '' }}
                        {{ $isCurrent ? 'background: rgba(79, 141, 255, 0.2); color: var(--wr-accent); border: 2px solid var(--wr-accent); animation: pulse 2s infinite;' : '' }}
                        {{ $isSkipped ? 'background: var(--wr-surface2); color: var(--wr-muted); border: 1px solid var(--wr-border);' : '' }}
                        {{ !$isDone && !$isCurrent && !$isSkipped ? 'background: var(--wr-surface2); color: var(--wr-muted); border: 1px solid var(--wr-border);' : '' }}
                    ">
                        @if($isDone) ✓
                        @elseif($isCurrent) →
                        @elseif($isSkipped) —
                        @else ○
                        @endif
                    </div>

                    {{-- Step content --}}
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                            <div>
                                <p style="
                                    font-weight: 600; font-size: 14px; line-height: 1.4;
                                    {{ $isDone ? 'color: var(--wr-accent2);' : ($isCurrent ? 'color: var(--wr-accent);' : 'color: var(--wr-text);') }}
                                ">
                                    {{ $s['label'] }}
                                    @if($isFinal)
                                        <span style="display:inline-block; margin-left:8px; padding:2px 8px;
                                                    font-size:10px; font-weight:700;
                                                    background:rgba(20,184,166,0.15); color:#14b8a6;
                                                    border-radius:4px; text-transform:uppercase; letter-spacing:0.4px;">
                                            Final Decision
                                        </span>
                                    @endif
                                    @if($isCurrent)
                                        <span style="display:inline-block; margin-left:8px; padding:3px 10px;
                                                    font-size:10px; font-weight:700;
                                                    background:rgba(79,141,255,0.2); color:var(--wr-accent);
                                                    border-radius:4px; text-transform:uppercase; letter-spacing:0.4px;">
                                            Waiting
                                        </span>
                                    @endif
                                    @if($isSkipped)
                                        <span style="display:inline-block; margin-left:8px; font-size:12px;
                                                    color:var(--wr-muted); font-style:italic; font-weight:400;">
                                            (skipped)
                                        </span>
                                    @endif
                                </p>
                            </div>
                            @if($s['assigned'])
                                <span style="font-size:12px; color:var(--wr-muted); font-weight:500;">
                                    {{ $s['assigned']->name }}
                                </span>
                            @endif
                        </div>

                        @if($isDone && !empty($s['done_field']))
                            <p style="font-size:12px; color:var(--wr-muted); margin-top:4px;">
                                <strong>Completed by:</strong> {{ $s['done_field'] }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Provincial Engineer final decision result --}}
        @if($workRequest->approved_by && in_array($workRequest->status, ['approved', 'rejected']))
            <div style="
                margin-top: 20px; padding: 16px;
                border-left: 4px solid {{ $workRequest->status === 'approved' ? 'var(--wr-accent2)' : 'var(--wr-accent3)' }};
                border-radius: 4px;
                background: {{ $workRequest->status === 'approved' ? 'rgba(0,212,170,0.08)' : 'rgba(255,107,107,0.08)' }};
            ">
                <p style="
                    font-weight: 700; font-size: 13px;
                    color: {{ $workRequest->status === 'approved' ? 'var(--wr-accent2)' : 'var(--wr-accent3)' }};
                    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;
                ">
                    <i class="fas {{ $workRequest->status === 'approved' ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"></i>
                    Provincial Engineer Decision: {{ ucfirst($workRequest->status) }}
                </p>
                @if($workRequest->approved_recommendation_action)
                    <p style="font-size:13px; color:var(--wr-text); margin-bottom:8px;
                              white-space:pre-wrap; line-height:1.5;">{{ $workRequest->approved_recommendation_action }}</p>
                @endif
                <p style="font-size:11px; color:var(--wr-muted);">
                    <strong>by {{ $workRequest->approved_by }}</strong>
                </p>

                {{-- MTQA print notice when approved --}}
                @if($workRequest->status === 'approved')
                    <div style="margin-top:12px; padding:10px 14px; border-radius:6px;
                                background:rgba(79,141,255,0.08); border:1px solid rgba(79,141,255,0.2);
                                font-size:12px; color:var(--wr-accent);">
                        <i class="fas fa-print mr-2"></i>
                        <strong>MTQA</strong> has been notified and can now print this work request.
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>