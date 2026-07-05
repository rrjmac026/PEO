{{-- resources/views/admin/work-requests/partials/_inspection-results.blade.php --}}
{{-- Each reviewer block is a self-contained section.
     Uses a shared helper macro via @include for DRY rendering. --}}

{{-- Site Inspector --}}
@if($workRequest->inspected_by_site_inspector || $workRequest->recommendation || $workRequest->findings_comments)
    <div style="padding: 16px; border-left: 4px solid #60a5fa; border-radius: 4px; background: rgba(96, 165, 250, 0.06); margin-bottom: 16px;">
        <p style="font-size: 12px; font-weight: 700; color: #60a5fa; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
            <i class="fas fa-hard-hat mr-2"></i> Site Inspector
        </p>
        <div class="wrd-info-grid">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Inspector Name</span>
                <span class="wrd-info-value {{ !$workRequest->inspected_by_site_inspector ? 'empty' : '' }}">
                    {{ $workRequest->inspected_by_site_inspector ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Recommendation</span>
                <span class="wrd-info-value {{ !$workRequest->recommendation ? 'empty' : '' }}">
                    {{ $workRequest->recommendation ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item span2">
                <span class="wrd-info-label">Findings & Comments</span>
                <span class="wrd-info-value pre {{ !$workRequest->findings_comments ? 'empty' : '' }}">
                    {{ $workRequest->findings_comments ?? 'No findings recorded' }}
                </span>
            </div>
        </div>
    </div>
@endif

{{-- Surveyor --}}
@if($workRequest->surveyor_name || $workRequest->recommendation_surveyor || $workRequest->findings_surveyor)
    <div style="padding: 16px; border-left: 4px solid #c084fc; border-radius: 4px; background: rgba(192, 132, 252, 0.06); margin-bottom: 16px;">
        <p style="font-size: 12px; font-weight: 700; color: #c084fc; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
            <i class="fas fa-drafting-compass mr-2"></i> Surveyor
        </p>
        <div class="wrd-info-grid">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Surveyor Name</span>
                <span class="wrd-info-value {{ !$workRequest->surveyor_name ? 'empty' : '' }}">
                    {{ $workRequest->surveyor_name ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Recommendation</span>
                <span class="wrd-info-value {{ !$workRequest->recommendation_surveyor ? 'empty' : '' }}">
                    {{ $workRequest->recommendation_surveyor ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item span2">
                <span class="wrd-info-label">Findings</span>
                <span class="wrd-info-value pre {{ !$workRequest->findings_surveyor ? 'empty' : '' }}">
                    {{ $workRequest->findings_surveyor ?? 'No findings recorded' }}
                </span>
            </div>
        </div>
    </div>
@endif

{{-- Resident Engineer --}}
@if($workRequest->resident_engineer_name || $workRequest->recommendation_engineer || $workRequest->findings_engineer)
    <div style="padding: 16px; border-left: 4px solid #34d399; border-radius: 4px; background: rgba(52, 211, 153, 0.06); margin-bottom: 16px;">
        <p style="font-size: 12px; font-weight: 700; color: #34d399; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
            <i class="fas fa-hard-hat mr-2"></i> Resident Engineer
        </p>
        <div class="wrd-info-grid">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Engineer Name</span>
                <span class="wrd-info-value {{ !$workRequest->resident_engineer_name ? 'empty' : '' }}">
                    {{ $workRequest->resident_engineer_name ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Recommendation</span>
                <span class="wrd-info-value {{ !$workRequest->recommendation_engineer ? 'empty' : '' }}">
                    {{ $workRequest->recommendation_engineer ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item span2">
                <span class="wrd-info-label">Findings</span>
                <span class="wrd-info-value pre {{ !$workRequest->findings_engineer ? 'empty' : '' }}">
                    {{ $workRequest->findings_engineer ?? 'No findings recorded' }}
                </span>
            </div>
        </div>

        @include('admin.work-requests.show-partials._recommendation-history', ['step' => 'resident_engineer'])
    </div>
@endif

{{-- MTQA --}}
@if($workRequest->checked_by_mtqa || $workRequest->recommended_action)
    <div style="padding: 16px; border-left: 4px solid #f59e0b; border-radius: 4px; background: rgba(245, 158, 11, 0.06); margin-bottom: 16px;">
        <p style="font-size: 12px; font-weight: 700; color: #f59e0b; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
            <i class="fas fa-clipboard-check mr-2"></i> Material Engineer Assigned
        </p>
        <div class="wrd-info-grid">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Checked By</span>
                <span class="wrd-info-value {{ !$workRequest->checked_by_mtqa ? 'empty' : '' }}">
                    {{ $workRequest->checked_by_mtqa ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Recommended Action</span>
                <span class="wrd-info-value {{ !$workRequest->recommended_action ? 'empty' : '' }}">
                    {{ $workRequest->recommended_action ?? 'Not specified' }}
                </span>
            </div>
        </div>

        @include('admin.work-requests.show-partials._recommendation-history', ['step' => 'mtqa'])
    </div>
@endif

{{-- Reviewed By (Engineer IV) --}}
@if($workRequest->reviewed_by || $workRequest->reviewed_by_recommendation_action)
    <div style="padding: 16px; border-left: 4px solid #818cf8; border-radius: 4px; background: rgba(129, 140, 248, 0.06); margin-bottom: 16px;">
        <p style="font-size: 12px; font-weight: 700; color: #818cf8; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
            <i class="fas fa-user-check mr-2"></i> Reviewed By (MTQA Division Chief)
        </p>
        <div class="wrd-info-grid">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Name</span>
                <span class="wrd-info-value {{ !$workRequest->reviewed_by ? 'empty' : '' }}">
                    {{ $workRequest->reviewed_by ?? 'Not specified' }}
                </span>
            </div>
            @if($workRequest->reviewer_signature)
                <div class="wrd-info-item">
                    <span class="wrd-info-label">Signature</span>
                    <img src="{{ $workRequest->reviewer_signature }}" alt="Engineer IV Signature"
                         style="max-width:180px; border:1px solid var(--wr-border); border-radius:6px; padding:4px; background:var(--wr-surface);">
                </div>
            @endif
            <div class="wrd-info-item span2">
                <span class="wrd-info-label">Recommendation Action</span>
                <span class="wrd-info-value pre {{ !$workRequest->reviewed_by_recommendation_action ? 'empty' : '' }}">
                    {{ $workRequest->reviewed_by_recommendation_action ?? 'No action specified' }}
                </span>
            </div>
        </div>

        @include('admin.work-requests.show-partials._recommendation-history', ['step' => 'engineer_iv'])
    </div>
@endif

{{-- Recommending Approval (Engineer III) --}}
@if($workRequest->recommending_approval_by || $workRequest->recommending_approval_recommendation_action)
    <div style="padding: 16px; border-left: 4px solid #f97316; border-radius: 4px; background: rgba(249, 115, 22, 0.06); margin-bottom: 16px;">
        <p style="font-size: 12px; font-weight: 700; color: #f97316; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
            <i class="fas fa-thumbs-up mr-2"></i> Recommending Approval (Project Division Chief)
        </p>
        <div class="wrd-info-grid">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Name</span>
                <span class="wrd-info-value {{ !$workRequest->recommending_approval_by ? 'empty' : '' }}">
                    {{ $workRequest->recommending_approval_by ?? 'Not specified' }}
                </span>
            </div>
            @if($workRequest->recommending_approval_signature)
                <div class="wrd-info-item">
                    <span class="wrd-info-label">Signature</span>
                    <img src="{{ $workRequest->recommending_approval_signature }}" alt="Engineer III Signature"
                         style="max-width:180px; border:1px solid var(--wr-border); border-radius:6px; padding:4px; background:var(--wr-surface);">
                </div>
            @endif
            <div class="wrd-info-item span2">
                <span class="wrd-info-label">Recommendation Action</span>
                <span class="wrd-info-value pre {{ !$workRequest->recommending_approval_recommendation_action ? 'empty' : '' }}">
                    {{ $workRequest->recommending_approval_recommendation_action ?? 'No action specified' }}
                </span>
            </div>
        </div>

        @include('admin.work-requests.show-partials._recommendation-history', ['step' => 'engineer_iii'])
    </div>
@endif

{{-- Approved By (Provincial Engineer) --}}
@if($workRequest->approved_by || $workRequest->approved_recommendation_action)
    <div style="padding: 16px; border-left: 4px solid #14b8a6; border-radius: 4px; background: rgba(20, 184, 166, 0.06); margin-bottom: 16px;">
        <p style="font-size: 12px; font-weight: 700; color: #14b8a6; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
            <i class="fas fa-check-circle mr-2"></i> Approved By (Provincial Engineer)
        </p>
        <div class="wrd-info-grid">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Name</span>
                <span class="wrd-info-value {{ !$workRequest->approved_by ? 'empty' : '' }}">
                    {{ $workRequest->approved_by ?? 'Not specified' }}
                </span>
            </div>
            @if($workRequest->approved_signature)
                <div class="wrd-info-item">
                    <span class="wrd-info-label">Signature</span>
                    <img src="{{ $workRequest->approved_signature }}" alt="Provincial Engineer Signature"
                         style="max-width:180px; border:1px solid var(--wr-border); border-radius:6px; padding:4px; background:var(--wr-surface);">
                </div>
            @endif
            <div class="wrd-info-item span2">
                <span class="wrd-info-label">Recommendation Action</span>
                <span class="wrd-info-value pre {{ !$workRequest->approved_recommendation_action ? 'empty' : '' }}">
                    {{ $workRequest->approved_recommendation_action ?? 'No action specified' }}
                </span>
            </div>
        </div>

        @include('admin.work-requests.show-partials._recommendation-history', ['step' => 'provincial_engineer'])
    </div>
@endif

{{-- Acceptance --}}
@if($workRequest->accepted_by_contractor || $workRequest->accepted_date || $workRequest->accepted_time)
    <div style="padding: 16px; border-left: 4px solid #6b7280; border-radius: 4px; background: rgba(107, 114, 128, 0.06);">
        <p style="font-size: 12px; font-weight: 700; color: #6b7280; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
            <i class="fas fa-handshake mr-2"></i> Acceptance
        </p>
        <div class="wrd-info-grid three">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Accepted By</span>
                <span class="wrd-info-value {{ !$workRequest->accepted_by_contractor ? 'empty' : '' }}">
                    {{ $workRequest->accepted_by_contractor ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Date</span>
                <span class="wrd-info-value {{ !$workRequest->accepted_date ? 'empty' : '' }}">
                    {{ $workRequest->accepted_date?->format('M d, Y') ?? 'Not set' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Time</span>
                <span class="wrd-info-value {{ !$workRequest->accepted_time ? 'empty' : '' }}">
                    {{ $workRequest->accepted_time ?? 'Not set' }}
                </span>
            </div>
        </div>
    </div>
@endif