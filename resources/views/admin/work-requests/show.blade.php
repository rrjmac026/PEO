@extends('layouts.app')

@section('title', 'Work Request #' . $workRequest->id)

@push('styles')
<style>
    /* ══════════════════════════════════════════
       LIGHT MODE TOKENS (primary / default)
    ══════════════════════════════════════════ */
    :root {
        --wr-surface:   #ffffff;
        --wr-surface2:  #f8fafc;
        --wr-border:    #e2e8f0;
        --wr-text:      #0f172a;
        --wr-text-sec:  #334155;
        --wr-muted:     #64748b;
        --wr-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --wr-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
    }

    /* ══════════════════════════════════════════
       DARK MODE TOKENS (override on .dark)
    ══════════════════════════════════════════ */
    .dark {
        --wr-surface:   #1a1f2e;
        --wr-surface2:  #1e2335;
        --wr-border:    #2a3050;
        --wr-text:      #e8eaf6;
        --wr-text-sec:  #c5cae9;
        --wr-muted:     #7c85a8;
        --wr-shadow:    0 1px 4px rgba(0,0,0,0.35);
        --wr-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
    }

    /* ── Page heading ── */
    .wr-page-title { font-size: 28px; font-weight: 800; color: var(--wr-text); line-height: 1.2; }
    .wr-page-sub   { font-size: 14px; color: var(--wr-muted); margin-top: 4px; }

    /* ── Section title ── */
    .wr-section-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--wr-text);
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid #ea580c;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .dark .wr-section-title {
        border-bottom-color: #f97316;
    }

    /* ── Info group ── */
    .wr-info-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .wr-info-item { display: flex; flex-direction: column; }
    .wr-info-label { font-size: 11px; font-weight: 700; color: var(--wr-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .wr-info-value { font-size: 14px; color: var(--wr-text); font-weight: 500; }

    /* ── Panel ── */
    .wr-panel {
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--wr-shadow);
    }
    .wr-panel-body { padding: 24px; }

    /* ── Signature image ── */
    .wr-signature {
        border: 1px solid var(--wr-border);
        border-radius: 8px;
        padding: 8px;
        background: var(--wr-surface2);
        max-width: 120px;
        margin-top: 6px;
    }

    /* ── Status badges ── */
    .wr-badge {
        display: inline-flex; align-items: center;
        padding: 3px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 600; border: 1px solid;
    }
    .wr-badge-draft     { color: #475569; border-color: #cbd5e1; background: #f1f5f9; }
    .wr-badge-submitted { color: #1d4ed8; border-color: #93c5fd; background: #eff6ff; }
    .wr-badge-inspected { color: #6d28d9; border-color: #c4b5fd; background: #f5f3ff; }
    .wr-badge-reviewed  { color: #92400e; border-color: #fcd34d; background: #fffbeb; }
    .wr-badge-approved  { color: #166534; border-color: #86efac; background: #f0fdf4; }
    .wr-badge-accepted  { color: #0f766e; border-color: #5eead4; background: #f0fdfa; }
    .wr-badge-rejected  { color: #991b1b; border-color: #fca5a5; background: #fff1f2; }

    .dark .wr-badge-draft     { color: #94a3b8; border-color: rgba(148,163,184,.3); background: rgba(148,163,184,.1); }
    .dark .wr-badge-submitted { color: #60a5fa; border-color: rgba(96,165,250,.3);  background: rgba(96,165,250,.1); }
    .dark .wr-badge-inspected { color: #a78bfa; border-color: rgba(167,139,250,.3); background: rgba(167,139,250,.1); }
    .dark .wr-badge-reviewed  { color: #fbbf24; border-color: rgba(251,191,36,.3);  background: rgba(251,191,36,.1); }
    .dark .wr-badge-approved  { color: #4ade80; border-color: rgba(74,222,128,.3);  background: rgba(74,222,128,.1); }
    .dark .wr-badge-accepted  { color: #2dd4bf; border-color: rgba(45,212,191,.3);  background: rgba(45,212,191,.1); }
    .dark .wr-badge-rejected  { color: #f87171; border-color: rgba(248,113,113,.3); background: rgba(248,113,113,.1); }

    /* ── Buttons ── */
    .wr-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px;
        font-size: 13px; font-weight: 600;
        border: 1px solid; cursor: pointer;
        transition: all .15s; text-decoration: none; white-space: nowrap;
    }
    .wr-btn-orange {
        background: #ea580c; border-color: #ea580c; color: #fff;
    }
    .wr-btn-orange:hover { background: #c2410c; border-color: #c2410c; }
    .dark .wr-btn-orange { background: #f97316; border-color: #f97316; }
    .dark .wr-btn-orange:hover { background: #fb923c; border-color: #fb923c; }

    .wr-btn-secondary {
        background: var(--wr-surface2); border-color: var(--wr-border); color: var(--wr-text-sec);
    }
    .wr-btn-secondary:hover { border-color: var(--wr-muted); }

    .wr-btn-danger {
        background: #dc2626; border-color: #dc2626; color: #fff;
    }
    .wr-btn-danger:hover { background: #b91c1c; border-color: #b91c1c; }

    /* ── Meta section ── */
    .wr-meta {
        background: var(--wr-surface2);
        border: 1px solid var(--wr-border);
        border-radius: 12px;
        padding: 16px 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        box-shadow: var(--wr-shadow);
    }
    .wr-meta-item { display: flex; flex-direction: column; gap: 4px; }
    .wr-meta-label { font-size: 11px; font-weight: 700; color: var(--wr-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .wr-meta-value { font-size: 13px; color: var(--wr-text); font-family: monospace; font-weight: 500; }

    /* ── Whitespace for long text ── */
    .wr-whitespace-pre { white-space: pre-wrap; word-break: break-word; }
</style>
@endpush

@section('content')

    <!-- ── Page Header ── -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
            <div>
                <h1 class="wr-page-title">Work Request #{{ $workRequest->id }}</h1>
                <p class="wr-page-sub">Created {{ $workRequest->created_at?->diffForHumans() }}</p>
            </div>
            <div class="flex flex-wrap gap-2 justify-end">
                @if($workRequest->canEdit())
                    <a href="{{ route('admin.work-requests.edit', $workRequest) }}" class="wr-btn wr-btn-orange">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
                <a href="{{ route('admin.work-requests.print', $workRequest) }}" class="wr-btn wr-btn-secondary" target="_blank">
                    <i class="fas fa-print"></i> Print
                </a>
                <a href="{{ route('admin.work-requests.download', $workRequest) }}" class="wr-btn wr-btn-secondary">
                    <i class="fas fa-download"></i> PDF
                </a>
                <a href="{{ route('admin.work-requests.export-excel', $workRequest) }}" class="wr-btn wr-btn-secondary">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                @if($workRequest->canEdit())
                    <form action="{{ route('admin.work-requests.destroy', $workRequest) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="wr-btn wr-btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- ── Status Badge ── -->
    @php
        $badgeClass = match($workRequest->status) {
            'draft'     => 'wr-badge-draft',
            'submitted' => 'wr-badge-submitted',
            'inspected' => 'wr-badge-inspected',
            'reviewed'  => 'wr-badge-reviewed',
            'approved'  => 'wr-badge-approved',
            'accepted'  => 'wr-badge-accepted',
            'rejected'  => 'wr-badge-rejected',
            default     => 'wr-badge-draft',
        };
    @endphp

    <div class="mb-8">
        <span class="wr-badge {{ $badgeClass }}">
            {{ ucfirst($workRequest->status) }}
        </span>
    </div>

    <!-- ── Project Information ── -->
    <div class="wr-panel mb-6">
        <div class="wr-panel-body">
            <h2 class="wr-section-title">Project Information</h2>
            <div class="wr-info-group">
                <div class="wr-info-item">
                    <span class="wr-info-label">Project Name</span>
                    <span class="wr-info-value">{{ $workRequest->name_of_project ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Project Location</span>
                    <span class="wr-info-value">{{ $workRequest->project_location ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">For Office</span>
                    <span class="wr-info-value">{{ $workRequest->for_office ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">From Requester</span>
                    <span class="wr-info-value">{{ $workRequest->from_requester ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Request Details ── -->
    <div class="wr-panel mb-6">
        <div class="wr-panel-body">
            <h2 class="wr-section-title">Request Details</h2>
            <div class="wr-info-group">
                <div class="wr-info-item">
                    <span class="wr-info-label">From Requester</span>
                    <span class="wr-info-value">{{ $workRequest->from_requester ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Work Start Date</span>
                    <span class="wr-info-value">{{ $workRequest->requested_work_start_date?->format('M d, Y') ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Work Start Time</span>
                    <span class="wr-info-value">{{ $workRequest->requested_work_start_time ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Pay Item Details ── -->
    <div class="wr-panel mb-6">
        <div class="wr-panel-body">
            <h2 class="wr-section-title">Pay Item Details</h2>
            <div class="wr-info-group">
                <div class="wr-info-item">
                    <span class="wr-info-label">Item No.</span>
                    <span class="wr-info-value">{{ $workRequest->item_no ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Equipment to be Used</span>
                    <span class="wr-info-value">{{ $workRequest->equipment_to_be_used ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Estimated Quantity</span>
                    <span class="wr-info-value">{{ $workRequest->estimated_quantity ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Unit</span>
                    <span class="wr-info-value">{{ $workRequest->unit ?? '—' }}</span>
                </div>
            </div>
            <div class="mt-6 space-y-4">
                <div class="wr-info-item">
                    <span class="wr-info-label">Description</span>
                    <span class="wr-info-value">{{ $workRequest->description ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Description of Work Requested</span>
                    <span class="wr-info-value wr-whitespace-pre">{{ $workRequest->description_of_work_requested ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Submission ── -->
    <div class="wr-panel mb-6">
        <div class="wr-panel-body">
            <h2 class="wr-section-title">Submission</h2>
            <div class="wr-info-group">
                <div class="wr-info-item">
                    <span class="wr-info-label">Contractor Name</span>
                    <span class="wr-info-value">{{ $workRequest->contractor_name ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Submitted Date</span>
                    <span class="wr-info-value">{{ $workRequest->created_at?->format('M d, Y') ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Inspection ── -->
    <div class="wr-panel mb-6">
        <div class="wr-panel-body">
            <h2 class="wr-section-title">Inspection</h2>
            <div class="wr-info-group">
                <div class="wr-info-item">
                    <span class="wr-info-label">Inspected By (Site Inspector)</span>
                    <span class="wr-info-value">{{ $workRequest->inspected_by_site_inspector ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Site Inspector Signature</span>
                    @if($workRequest->site_inspector_signature)
                        <img src="{{ $workRequest->site_inspector_signature }}" alt="Site Inspector Signature" class="wr-signature">
                    @else
                        <span class="wr-info-value">—</span>
                    @endif
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Surveyor Name</span>
                    <span class="wr-info-value">{{ $workRequest->surveyor_name ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Surveyor Signature</span>
                    @if($workRequest->surveyor_signature)
                        <img src="{{ $workRequest->surveyor_signature }}" alt="Surveyor Signature" class="wr-signature">
                    @else
                        <span class="wr-info-value">—</span>
                    @endif
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Resident Engineer Name</span>
                    <span class="wr-info-value">{{ $workRequest->resident_engineer_name ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Resident Engineer Signature</span>
                    @if($workRequest->resident_engineer_signature)
                        <img src="{{ $workRequest->resident_engineer_signature }}" alt="Resident Engineer Signature" class="wr-signature">
                    @else
                        <span class="wr-info-value">—</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ── Findings and Recommendations ── -->
    <div class="wr-panel mb-6">
        <div class="wr-panel-body">
            <h2 class="wr-section-title">Findings and Recommendations</h2>
            <div class="space-y-6">
                <div class="wr-info-item">
                    <span class="wr-info-label">Findings/Comments</span>
                    <span class="wr-info-value wr-whitespace-pre">{{ $workRequest->findings_comments ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Recommendation</span>
                    <span class="wr-info-value wr-whitespace-pre">{{ $workRequest->recommendation ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Recommended Action</span>
                    <span class="wr-info-value wr-whitespace-pre">{{ $workRequest->recommended_action ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Review and Approval ── -->
    <div class="wr-panel mb-6">
        <div class="wr-panel-body">
            <h2 class="wr-section-title">Review and Approval</h2>
            <div class="wr-info-group">
                <div class="wr-info-item">
                    <span class="wr-info-label">Checked By MTQA</span>
                    <span class="wr-info-value">{{ $workRequest->checked_by_mtqa ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">MTQA Signature</span>
                    @if($workRequest->mtqa_signature)
                        <img src="{{ $workRequest->mtqa_signature }}" alt="MTQA Signature" class="wr-signature">
                    @else
                        <span class="wr-info-value">—</span>
                    @endif
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Reviewed By</span>
                    <span class="wr-info-value">{{ $workRequest->reviewed_by ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Reviewer Designation</span>
                    <span class="wr-info-value">{{ $workRequest->reviewer_designation ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Recommending Approval By</span>
                    <span class="wr-info-value">{{ $workRequest->recommending_approval_by ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Recommending Approval Designation</span>
                    <span class="wr-info-value">{{ $workRequest->recommending_approval_designation ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Recommending Approval Signature</span>
                    @if($workRequest->recommending_approval_signature)
                        <img src="{{ $workRequest->recommending_approval_signature }}" alt="Recommending Approval Signature" class="wr-signature">
                    @else
                        <span class="wr-info-value">—</span>
                    @endif
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Approved By</span>
                    <span class="wr-info-value">{{ $workRequest->approved_by ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Approved By Designation</span>
                    <span class="wr-info-value">{{ $workRequest->approved_by_designation ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Approval Signature</span>
                    @if($workRequest->approved_signature)
                        <img src="{{ $workRequest->approved_signature }}" alt="Approval Signature" class="wr-signature">
                    @else
                        <span class="wr-info-value">—</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ── Acceptance ── -->
    <div class="wr-panel mb-6">
        <div class="wr-panel-body">
            <h2 class="wr-section-title">Acceptance</h2>
            <div class="wr-info-group">
                <div class="wr-info-item">
                    <span class="wr-info-label">Accepted By Contractor</span>
                    <span class="wr-info-value">{{ $workRequest->accepted_by_contractor ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Accepted Date</span>
                    <span class="wr-info-value">{{ $workRequest->accepted_date?->format('M d, Y') ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Accepted Time</span>
                    <span class="wr-info-value">{{ $workRequest->accepted_time ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Received By</span>
                    <span class="wr-info-value">{{ $workRequest->received_by ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Received Date</span>
                    <span class="wr-info-value">{{ $workRequest->received_date?->format('M d, Y') ?? '—' }}</span>
                </div>
                <div class="wr-info-item">
                    <span class="wr-info-label">Received Time</span>
                    <span class="wr-info-value">{{ $workRequest->received_time ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Additional Information ── -->
    <div class="wr-panel mb-6">
        <div class="wr-panel-body">
            <h2 class="wr-section-title">Additional Information</h2>
            <div class="wr-info-item">
                <span class="wr-info-label">Notes</span>
                <span class="wr-info-value wr-whitespace-pre">{{ $workRequest->notes ?? '—' }}</span>
            </div>
        </div>
    </div>

    <!-- ── Metadata ── -->
    <div class="wr-meta mb-8">
        <div class="wr-meta-item">
            <span class="wr-meta-label">Created</span>
            <span class="wr-meta-value">{{ $workRequest->created_at?->format('M d, Y • h:i A') }}</span>
        </div>
        <div class="wr-meta-item">
            <span class="wr-meta-label">Last Updated</span>
            <span class="wr-meta-value">{{ $workRequest->updated_at?->format('M d, Y • h:i A') }}</span>
        </div>
    </div>

    <!-- ── Back Button ── -->
    <div class="mb-12">
        <a href="{{ route('admin.work-requests.index') }}" class="wr-btn wr-btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Work Requests
        </a>
    </div>

@endsection
