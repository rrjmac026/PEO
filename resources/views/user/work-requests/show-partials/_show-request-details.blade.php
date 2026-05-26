<div class="wrd-card">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon green">📋</div>
        <span class="wrd-card-title">Request Details</span>
    </div>
    <div class="wrd-card-body">
        <div class="wrd-info-grid">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Requested By</span>
                <span class="wrd-info-value {{ !$workRequest->contractor_name ? 'empty' : '' }}">
                    {{ $workRequest->contractor_name ?? '—' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Work Start Date</span>
                @if($workRequest->requested_work_start_date)
                    <span class="wrd-info-value">
                        {{ $workRequest->requested_work_start_date->format('M d, Y') }}
                    </span>
                    <span style="
                        display: inline-flex; align-items: center; gap: 5px;
                        margin-top: 5px;
                        font-size: 11px;
                        color: var(--wr-muted);
                        background: var(--wr-surface2);
                        border: 1px solid var(--wr-border);
                        border-radius: 6px;
                        padding: 4px 10px;
                        font-family: 'Inter', sans-serif;
                    ">
                        📋 Filed {{ $workRequest->created_at->format('M d, Y') }}
                        &nbsp;·&nbsp;
                        🚫 Blocked {{ $workRequest->created_at->copy()->addDay()->format('M d') }}–{{ $workRequest->created_at->copy()->addDays(3)->format('M d') }}
                        &nbsp;·&nbsp;
                        ✅ Starts {{ $workRequest->requested_work_start_date->format('M d, Y') }}
                    </span>
                @else
                    <span class="wrd-info-value empty">Not set</span>
                @endif
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Start Time</span>
                <span class="wrd-info-value {{ !$workRequest->requested_work_start_time ? 'empty' : '' }}">
                    {{ $workRequest->requested_work_start_time ?? 'Not set' }}
                </span>
            </div>
        </div>

        <div class="wrd-divider"></div>

        <div class="wrd-info-item">
            <span class="wrd-info-label">Description of Work Requested</span>
            <span class="wrd-info-value pre">{{ $workRequest->description_of_work_requested }}</span>
        </div>
    </div>
</div>