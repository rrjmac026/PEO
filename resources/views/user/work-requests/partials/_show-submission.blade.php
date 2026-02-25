<div class="wrd-card">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon purple">🏛</div>
        <span class="wrd-card-title">Submission Details</span>
    </div>
    <div class="wrd-card-body">
        <div class="wrd-info-grid">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Contractor Name</span>
                <span class="wrd-info-value {{ !$workRequest->contractor_name ? 'empty' : '' }}">
                    {{ $workRequest->contractor_name ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Submitted Date</span>
                <span class="wrd-info-value {{ !$workRequest->submitted_date ? 'empty' : '' }}">
                    {{ $workRequest->submitted_date?->format('M d, Y') ?? 'Not submitted yet' }}
                </span>
            </div>
            @if($workRequest->notes)
                <div class="wrd-info-item span2">
                    <span class="wrd-info-label">Additional Notes</span>
                    <span class="wrd-info-value pre">{{ $workRequest->notes }}</span>
                </div>
            @endif
        </div>
    </div>
</div>