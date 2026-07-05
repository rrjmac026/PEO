{{-- ── Pay Item Details ── --}}
<div class="wrd-card">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon orange">⚙️</div>
        <span class="wrd-card-title">Pay Item Details</span>
    </div>
    <div class="wrd-card-body">
        <div class="wrd-info-grid">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Item Number</span>
                @if($workRequest->item_no)
                    <span class="wrd-info-value mono">{{ $workRequest->item_no }}</span>
                @else
                    <span class="wrd-info-value empty">Not specified</span>
                @endif
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Equipment to be Used</span>
                <span class="wrd-info-value {{ !$workRequest->equipment_to_be_used ? 'empty' : '' }}">
                    {{ $workRequest->equipment_to_be_used ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Quantity</span>
                <span class="wrd-info-value {{ !$workRequest->quantity ? 'empty' : '' }}">
                    {{ $workRequest->quantity ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Estimated Quantity</span>
                <span class="wrd-info-value {{ !$workRequest->estimated_quantity ? 'empty' : '' }}">
                    {{ $workRequest->estimated_quantity ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Unit</span>
                <span class="wrd-info-value {{ !$workRequest->unit ? 'empty' : '' }}">
                    {{ $workRequest->unit ?? 'Not specified' }}
                </span>
            </div>
            @if($workRequest->description)
                <div class="wrd-info-item span2">
                    <span class="wrd-info-label">Item Description</span>
                    <span class="wrd-info-value pre">{{ $workRequest->description }}</span>
                </div>
            @endif
        </div>

        @if($workRequest->description_of_work_requested)
            <div class="wrd-divider"></div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Description of Work Requested</span>
                <span class="wrd-info-value pre">{{ $workRequest->description_of_work_requested }}</span>
            </div>
        @endif
    </div>
</div>