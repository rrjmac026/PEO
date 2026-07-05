{{-- ── Reception ── --}}
<div class="wrd-card">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon green">📥</div>
        <span class="wrd-card-title">Reception</span>
    </div>
    <div class="wrd-card-body">
        <div class="wrd-info-grid three">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Received By</span>
                <span class="wrd-info-value {{ !$workRequest->received_by ? 'empty' : '' }}">
                    {{ $workRequest->received_by ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Received Date</span>
                <span class="wrd-info-value {{ !$workRequest->received_date ? 'empty' : '' }}">
                    {{ $workRequest->received_date?->format('M d, Y') ?? 'Not set' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Received Time</span>
                <span class="wrd-info-value {{ !$workRequest->received_time ? 'empty' : '' }}">
                    {{ $workRequest->received_time ?? 'Not set' }}
                </span>
            </div>
        </div>
    </div>
</div>