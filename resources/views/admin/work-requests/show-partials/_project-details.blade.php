{{-- resources/views/admin/work-requests/partials/_project-details.blade.php --}}

{{-- ── Project Information ── --}}
<div class="wrd-card">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon blue">📁</div>
        <span class="wrd-card-title">Project Information</span>
    </div>
    <div class="wrd-card-body">
        <div class="wrd-info-grid">
            <div class="wrd-info-item">
                <span class="wrd-info-label">Project Name</span>
                <span class="wrd-info-value">{{ $workRequest->name_of_project }}</span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Project Location</span>
                <span class="wrd-info-value">{{ $workRequest->project_location }}</span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">For Office</span>
                <span class="wrd-info-value {{ !$workRequest->for_office ? 'empty' : '' }}">
                    {{ $workRequest->for_office ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">From Requester</span>
                <span class="wrd-info-value {{ !$workRequest->from_requester ? 'empty' : '' }}">
                    {{ $workRequest->from_requester ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Contract Number</span>
                <span class="wrd-info-value {{ !$workRequest->contract_number ? 'empty' : '' }}">
                    {{ $workRequest->contract_number ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Contractor</span>
                <span class="wrd-info-value {{ !$workRequest->contractor_name ? 'empty' : '' }}">
                    {{ $workRequest->contractor_name ?? 'Not specified' }}
                </span>
            </div>
            <div class="wrd-info-item">
                <span class="wrd-info-label">Work Start Date & Time</span>
                <span class="wrd-info-value {{ !$workRequest->requested_work_start_date ? 'empty' : '' }}">
                    {{ $workRequest->requested_work_start_date?->format('M d, Y') ?? 'Not set' }}
                    @if($workRequest->requested_work_start_time)
                        at {{ $workRequest->requested_work_start_time }}
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

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