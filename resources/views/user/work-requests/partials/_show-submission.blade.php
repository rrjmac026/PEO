<div class="wrd-card">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon purple">🏛</div>
        <span class="wrd-card-title">Submission Details</span>
    </div>
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

            {{-- Contractor Signature --}}
            @if($workRequest->contractor_signature)
                <div class="wrd-info-item span2">
                    <span class="wrd-info-label">Contractor Signature</span>
                    <div style="
                        margin-top: 6px;
                        display: inline-block;
                        background: var(--wr-surface);
                        border: 1px solid var(--wr-border);
                        border-radius: 8px;
                        padding: 10px 16px;
                    ">
                        <img src="{{ $workRequest->contractor_signature }}"
                             alt="Contractor Signature"
                             style="display: block; max-width: 280px; max-height: 90px; object-fit: contain;">
                        <div style="
                            margin-top: 8px;
                            padding-top: 6px;
                            border-top: 1px dashed var(--wr-border);
                            font-size: 11px;
                            color: var(--wr-muted);
                            font-family: 'Inter', sans-serif;
                        ">
                            {{ $workRequest->contractor_name }}
                            &nbsp;·&nbsp;
                            {{ $workRequest->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="wrd-info-item span2">
                    <span class="wrd-info-label">Contractor Signature</span>
                    <span class="wrd-info-value empty">No signature on file</span>
                </div>
            @endif

        </div>
    </div>
</div>
</div>