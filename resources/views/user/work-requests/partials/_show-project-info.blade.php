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
                <span class="wrd-info-label">Reference Number</span>
                @if($workRequest->reference_number)
                    <span class="wrd-info-value mono">{{ $workRequest->reference_number }}</span>
                @else
                    <span class="wrd-info-value empty">Not assigned</span>
                @endif
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
        </div>
    </div>
</div>