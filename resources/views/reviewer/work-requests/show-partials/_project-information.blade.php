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
                <span class="wrd-info-label">Contractor</span>
                <span class="wrd-info-value {{ !$workRequest->contractor_name ? 'empty' : '' }}">
                    {{ $workRequest->contractor_name ?? 'Not specified' }}
                </span>
            </div>

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
            @endif

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