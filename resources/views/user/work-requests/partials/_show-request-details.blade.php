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
                <span class="wrd-info-value {{ !$workRequest->requested_work_start_date ? 'empty' : '' }}">
                    {{ $workRequest->requested_work_start_date?->format('M d, Y') ?? 'Not set' }}
                </span>
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