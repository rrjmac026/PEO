<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Work Request Review') }}
            </h2>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('reviewer.work-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    @push('styles')
        @include('reviewer.work-requests.partials._styles')
    @endpush

    <div class="py-8 wrd-wrap">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @php
                $statusSlug = $workRequest->status ?? 'draft';
                $statusDots = [
                    'draft'     => '#94a3b8',
                    'submitted' => '#60a5fa',
                    'inspected' => '#c084fc',
                    'reviewed'  => '#818cf8',
                    'approved'  => '#34d399',
                    'accepted'  => '#34d399',
                    'rejected'  => '#f87171',
                ];
                $dotColor = $statusDots[$statusSlug] ?? '#94a3b8';
            @endphp

            {{-- ── Hero Bar ── --}}
            <div class="wrd-hero">
                <div class="wrd-hero-left">
                    <div class="wrd-req-id"># {{ str_pad($workRequest->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div>
                        <div class="wrd-project-name">{{ $workRequest->name_of_project }}</div>
                        <div class="wrd-project-loc">
                            <span>📍</span> {{ $workRequest->project_location }}
                        </div>
                    </div>
                </div>
                <div class="wrd-hero-right">
                    <div class="wrd-status-badge wrd-status--{{ $statusSlug }}">
                        <span class="wrd-status-dot" style="background: {{ $dotColor }};"></span>
                        {{ ucfirst($workRequest->status) }}
                    </div>
                </div>
            </div>

            {{-- ── Meta chips ── --}}
            <div class="wrd-meta-row mb-5">
                <div class="wrd-meta-chip">
                    🕐 Created <strong>{{ $workRequest->created_at->format('M d, Y · H:i') }}</strong>
                </div>
                <div class="wrd-meta-chip">
                    ✏️ Updated <strong>{{ $workRequest->updated_at->format('M d, Y · H:i') }}</strong>
                </div>
                @if($workRequest->submitted_date)
                    <div class="wrd-meta-chip">
                        📨 Submitted <strong>{{ $workRequest->submitted_date->format('M d, Y') }}</strong>
                    </div>
                @endif
            </div>

            {{-- ── Flash Messages ── --}}
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700 text-sm font-medium">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-700 text-sm font-medium">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

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

            <div class="wrd-section-divider"></div>

            {{-- ── Inspection & Review Results ── --}}
            <div class="wrd-card">
                <div class="wrd-card-head">
                    <div class="wrd-card-head-icon blue">🔍</div>
                    <span class="wrd-card-title">Inspection & Review Results</span>
                </div>
                <div class="wrd-card-body">

                    {{-- Site Inspector --}}
                    @if($workRequest->inspected_by_site_inspector || $workRequest->recommendation || $workRequest->findings_comments)
                        <div style="padding:16px; border-left:4px solid #60a5fa; border-radius:4px; background:rgba(96,165,250,0.06); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#60a5fa; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-hard-hat mr-2"></i> Site Inspector
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Inspector Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->inspected_by_site_inspector ? 'empty' : '' }}">
                                        {{ $workRequest->inspected_by_site_inspector ?? 'Not specified' }}
                                    </span>
                                </div>
                                @if($workRequest->site_inspector_signature)
                                    <div class="wrd-info-item">
                                        <span class="wrd-info-label">Signature</span>
                                        <img src="{{ $workRequest->site_inspector_signature }}" alt="Site Inspector Signature"
                                             style="max-width:180px; border:1px solid var(--wr-border); border-radius:6px; padding:4px; background:var(--wr-surface);">
                                    </div>
                                @endif
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Recommendation</span>
                                    <span class="wrd-info-value {{ !$workRequest->recommendation ? 'empty' : '' }}">
                                        {{ $workRequest->recommendation ?? 'Not specified' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item span2">
                                    <span class="wrd-info-label">Findings & Comments</span>
                                    <span class="wrd-info-value pre {{ !$workRequest->findings_comments ? 'empty' : '' }}">
                                        {{ $workRequest->findings_comments ?? 'No findings recorded' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="padding:16px; border-left:4px solid #60a5fa; border-radius:4px; background:rgba(96,165,250,0.04); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#60a5fa; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-hard-hat mr-2"></i> Site Inspector
                            </p>
                            <p style="font-size:13px; color:var(--wr-muted); font-style:italic;">No inspection submitted yet.</p>
                        </div>
                    @endif

                    {{-- Surveyor --}}
                    @if($workRequest->surveyor_name || $workRequest->recommendation_surveyor || $workRequest->findings_surveyor)
                        <div style="padding:16px; border-left:4px solid #c084fc; border-radius:4px; background:rgba(192,132,252,0.06); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#c084fc; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-drafting-compass mr-2"></i> Surveyor
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Surveyor Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->surveyor_name ? 'empty' : '' }}">
                                        {{ $workRequest->surveyor_name ?? 'Not specified' }}
                                    </span>
                                </div>
                                @if($workRequest->surveyor_signature)
                                    <div class="wrd-info-item">
                                        <span class="wrd-info-label">Signature</span>
                                        <img src="{{ $workRequest->surveyor_signature }}" alt="Surveyor Signature"
                                             style="max-width:180px; border:1px solid var(--wr-border); border-radius:6px; padding:4px; background:var(--wr-surface);">
                                    </div>
                                @endif
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Recommendation</span>
                                    <span class="wrd-info-value {{ !$workRequest->recommendation_surveyor ? 'empty' : '' }}">
                                        {{ $workRequest->recommendation_surveyor ?? 'Not specified' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item span2">
                                    <span class="wrd-info-label">Findings</span>
                                    <span class="wrd-info-value pre {{ !$workRequest->findings_surveyor ? 'empty' : '' }}">
                                        {{ $workRequest->findings_surveyor ?? 'No findings recorded' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="padding:16px; border-left:4px solid #c084fc; border-radius:4px; background:rgba(192,132,252,0.04); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#c084fc; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-drafting-compass mr-2"></i> Surveyor
                            </p>
                            <p style="font-size:13px; color:var(--wr-muted); font-style:italic;">No survey submitted yet.</p>
                        </div>
                    @endif

                    {{-- Resident Engineer --}}
                    @if($workRequest->resident_engineer_name || $workRequest->recommendation_engineer || $workRequest->findings_engineer)
                        <div style="padding:16px; border-left:4px solid #34d399; border-radius:4px; background:rgba(52,211,153,0.06); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#34d399; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-hard-hat mr-2"></i> Resident Engineer
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Engineer Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->resident_engineer_name ? 'empty' : '' }}">
                                        {{ $workRequest->resident_engineer_name ?? 'Not specified' }}
                                    </span>
                                </div>
                                @if($workRequest->resident_engineer_signature)
                                    <div class="wrd-info-item">
                                        <span class="wrd-info-label">Signature</span>
                                        <img src="{{ $workRequest->resident_engineer_signature }}" alt="Resident Engineer Signature"
                                             style="max-width:180px; border:1px solid var(--wr-border); border-radius:6px; padding:4px; background:var(--wr-surface);">
                                    </div>
                                @endif
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Recommendation</span>
                                    <span class="wrd-info-value {{ !$workRequest->recommendation_engineer ? 'empty' : '' }}">
                                        {{ $workRequest->recommendation_engineer ?? 'Not specified' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item span2">
                                    <span class="wrd-info-label">Findings</span>
                                    <span class="wrd-info-value pre {{ !$workRequest->findings_engineer ? 'empty' : '' }}">
                                        {{ $workRequest->findings_engineer ?? 'No findings recorded' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="padding:16px; border-left:4px solid #34d399; border-radius:4px; background:rgba(52,211,153,0.04); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#34d399; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-hard-hat mr-2"></i> Resident Engineer
                            </p>
                            <p style="font-size:13px; color:var(--wr-muted); font-style:italic;">No engineer review submitted yet.</p>
                        </div>
                    @endif

                    {{-- MTQA --}}
                    @if($workRequest->checked_by_mtqa || $workRequest->recommended_action)
                        <div style="padding:16px; border-left:4px solid #f59e0b; border-radius:4px; background:rgba(245,158,11,0.06); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#f59e0b; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-clipboard-check mr-2"></i> MTQA
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Checked By</span>
                                    <span class="wrd-info-value {{ !$workRequest->checked_by_mtqa ? 'empty' : '' }}">
                                        {{ $workRequest->checked_by_mtqa ?? 'Not specified' }}
                                    </span>
                                </div>
                                @if($workRequest->mtqa_signature)
                                    <div class="wrd-info-item">
                                        <span class="wrd-info-label">Signature</span>
                                        <img src="{{ $workRequest->mtqa_signature }}" alt="MTQA Signature"
                                            style="max-width:180px; border:1px solid var(--wr-border); border-radius:6px; padding:4px; background:var(--wr-surface);">
                                    </div>
                                @endif
                                <div class="wrd-info-item span2">
                                    <span class="wrd-info-label">Recommended Action</span>
                                    <span class="wrd-info-value pre {{ !$workRequest->recommended_action ? 'empty' : '' }}">
                                        {{ $workRequest->recommended_action ?? 'Not specified' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="padding:16px; border-left:4px solid #f59e0b; border-radius:4px; background:rgba(245,158,11,0.04); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#f59e0b; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-clipboard-check mr-2"></i> MTQA
                            </p>
                            <p style="font-size:13px; color:var(--wr-muted); font-style:italic;">No MTQA check submitted yet.</p>
                        </div>
                    @endif

                    {{-- Engineer IV --}}
                    @if($workRequest->engineer_iv_name || $workRequest->recommendation_engineer_iv || $workRequest->findings_engineer_iv)
                        <div style="padding:16px; border-left:4px solid #0891b2; border-radius:4px; background:rgba(8,145,178,0.06); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#0891b2; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-briefcase mr-2"></i> Engineer IV
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Engineer Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->engineer_iv_name ? 'empty' : '' }}">
                                        {{ $workRequest->engineer_iv_name ?? 'Not specified' }}
                                    </span>
                                </div>
                                @if($workRequest->engineer_iv_signature)
                                    <div class="wrd-info-item">
                                        <span class="wrd-info-label">Signature</span>
                                        <img src="{{ $workRequest->engineer_iv_signature }}" alt="Engineer IV Signature"
                                             style="max-width:180px; border:1px solid var(--wr-border); border-radius:6px; padding:4px; background:var(--wr-surface);">
                                    </div>
                                @endif
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Recommendation</span>
                                    <span class="wrd-info-value {{ !$workRequest->recommendation_engineer_iv ? 'empty' : '' }}">
                                        {{ $workRequest->recommendation_engineer_iv ?? 'Not specified' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item span2">
                                    <span class="wrd-info-label">Findings</span>
                                    <span class="wrd-info-value pre {{ !$workRequest->findings_engineer_iv ? 'empty' : '' }}">
                                        {{ $workRequest->findings_engineer_iv ?? 'No findings recorded' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="padding:16px; border-left:4px solid #0891b2; border-radius:4px; background:rgba(8,145,178,0.04); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#0891b2; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-briefcase mr-2"></i> Engineer IV
                            </p>
                            <p style="font-size:13px; color:var(--wr-muted); font-style:italic;">No engineer IV review submitted yet.</p>
                        </div>
                    @endif

                    {{-- Reviewed By --}}
                    @if($workRequest->reviewed_by || $workRequest->reviewed_by_recommendation_action)
                        <div style="padding:16px; border-left:4px solid #818cf8; border-radius:4px; background:rgba(129,140,248,0.06); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#818cf8; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-user-check mr-2"></i> Reviewed By (Engineer IV)
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->reviewed_by ? 'empty' : '' }}">
                                        {{ $workRequest->reviewed_by ?? 'Not specified' }}
                                    </span>
                                </div>
                                @if($workRequest->reviewer_signature)
                                    <div class="wrd-info-item">
                                        <span class="wrd-info-label">Signature</span>
                                        <img src="{{ $workRequest->reviewer_signature }}" alt="Engineer IV Signature"
                                             style="max-width:180px; border:1px solid var(--wr-border); border-radius:6px; padding:4px; background:var(--wr-surface);">
                                    </div>
                                @endif
                                <div class="wrd-info-item span2">
                                    <span class="wrd-info-label">Recommendation Action</span>
                                    <span class="wrd-info-value pre {{ !$workRequest->reviewed_by_recommendation_action ? 'empty' : '' }}">
                                        {{ $workRequest->reviewed_by_recommendation_action ?? 'No action specified' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Recommending Approval --}}
                    @if($workRequest->recommending_approval_by || $workRequest->recommending_approval_recommendation_action)
                        <div style="padding:16px; border-left:4px solid #f97316; border-radius:4px; background:rgba(249,115,22,0.06); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#f97316; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-thumbs-up mr-2"></i> Recommending Approval (Engineer III)
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->recommending_approval_by ? 'empty' : '' }}">
                                        {{ $workRequest->recommending_approval_by ?? 'Not specified' }}
                                    </span>
                                </div>
                                @if($workRequest->recommending_approval_signature)
                                    <div class="wrd-info-item">
                                        <span class="wrd-info-label">Signature</span>
                                        <img src="{{ $workRequest->recommending_approval_signature }}" alt="Recommending Approval Signature"
                                             style="max-width:180px; border:1px solid var(--wr-border); border-radius:6px; padding:4px; background:var(--wr-surface);">
                                    </div>
                                @endif
                                <div class="wrd-info-item span2">
                                    <span class="wrd-info-label">Recommendation Action</span>
                                    <span class="wrd-info-value pre {{ !$workRequest->recommending_approval_recommendation_action ? 'empty' : '' }}">
                                        {{ $workRequest->recommending_approval_recommendation_action ?? 'No action specified' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Approved By --}}
                    @if($workRequest->approved_by || $workRequest->approved_notes)
                        <div style="padding:16px; border-left:4px solid #14b8a6; border-radius:4px; background:rgba(20,184,166,0.06); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#14b8a6; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-check-circle mr-2"></i> Approved By (Provincial Engineer)
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">recommendation_action)
                        <div style="padding:16px; border-left:4px solid #14b8a6; border-radius:4px; background:rgba(20,184,166,0.06); margin-bottom:16px;">
                            <p style="font-size:12px; font-weight:700; color:#14b8a6; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-check-circle mr-2"></i> Approved By (Provincial Engineer)
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->approved_by ? 'empty' : '' }}">
                                        {{ $workRequest->approved_by ?? 'Not specified' }}
                                    </span>
                                </div>
                                @if($workRequest->approved_signature)
                                    <div class="wrd-info-item">
                                        <span class="wrd-info-label">Signature</span>
                                        <img src="{{ $workRequest->approved_signature }}" alt="Approval Signature"
                                             style="max-width:180px; border:1px solid var(--wr-border); border-radius:6px; padding:4px; background:var(--wr-surface);">
                                    </div>
                                @endif
                                <div class="wrd-info-item span2">
                                    <span class="wrd-info-label">Recommendation Action</span>
                                    <span class="wrd-info-value pre {{ !$workRequest->approved_recommendation_action ? 'empty' : '' }}">
                                        {{ $workRequest->approved_recommendation_action ?? 'No action specified
                    {{-- Acceptance --}}
                    @if($workRequest->accepted_by_contractor || $workRequest->accepted_date)
                        <div style="padding:16px; border-left:4px solid #6b7280; border-radius:4px; background:rgba(107,114,128,0.06);">
                            <p style="font-size:12px; font-weight:700; color:#6b7280; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-handshake mr-2"></i> Acceptance
                            </p>
                            <div class="wrd-info-grid three">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Accepted By</span>
                                    <span class="wrd-info-value {{ !$workRequest->accepted_by_contractor ? 'empty' : '' }}">
                                        {{ $workRequest->accepted_by_contractor ?? 'Not specified' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Date</span>
                                    <span class="wrd-info-value {{ !$workRequest->accepted_date ? 'empty' : '' }}">
                                        {{ $workRequest->accepted_date?->format('M d, Y') ?? 'Not set' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Time</span>
                                    <span class="wrd-info-value {{ !$workRequest->accepted_time ? 'empty' : '' }}">
                                        {{ $workRequest->accepted_time ?? 'Not set' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <div class="wrd-section-divider"></div>

            {{-- ── Role-specific Action Form ── --}}
            @if($role === 'site_inspector')
                @include('reviewer.work-requests.partials._site-inspector-form')
            @elseif($role === 'surveyor')
                @include('reviewer.work-requests.partials._surveyor-form')
            @elseif($role === 'resident_engineer')
                @include('reviewer.work-requests.partials._resident-engineer-form')
            @elseif($role === 'mtqa')
                @include('reviewer.work-requests.partials._mtqa-form')
            @elseif($role === 'engineeriv')
                @include('reviewer.work-requests.partials._engineer-iv-form')
            @elseif($role === 'engineeriii')
                @include('reviewer.work-requests.partials._recommending-approval-form')
            @elseif($role === 'provincial_engineer')
                @include('reviewer.work-requests.partials._provincial-engineer-form')
            @endif

        </div>
    </div>

    @push('scripts')
        @include('reviewer.work-requests.partials._scripts')
    @endpush

</x-app-layout>