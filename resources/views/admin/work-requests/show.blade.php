<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Work Request Review') }}
            </h2>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('admin.work-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    @push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* ── DARK tokens (default) ── */
        :root {
            --wr-bg:       #0f1117;
            --wr-surface:  #181c27;
            --wr-surface2: #1e2335;
            --wr-border:   #2a3050;
            --wr-accent:   #4f8dff;
            --wr-accent2:  #00d4aa;
            --wr-accent3:  #ff6b6b;
            --wr-text:     #e8eaf6;
            --wr-muted:    #7c85a8;
            --wr-label:    #a8b3d8;
            --wr-glow-1:   rgba(79,141,255,0.05);
            --wr-glow-2:   rgba(0,212,170,0.04);
            --wr-radius:   12px;
            --wr-radius-sm:8px;
        }
        /* ── LIGHT tokens ── */
        html:not(.dark) {
            --wr-bg:       #f1f5f9;
            --wr-surface:  #ffffff;
            --wr-surface2: #f8fafc;
            --wr-border:   #cbd5e1;
            --wr-accent:   #2563eb;
            --wr-accent2:  #059669;
            --wr-accent3:  #dc2626;
            --wr-text:     #0f172a;
            --wr-muted:    #64748b;
            --wr-label:    #475569;
            --wr-glow-1:   rgba(37,99,235,0.04);
            --wr-glow-2:   rgba(5,150,105,0.03);
        }

        .wrd-wrap { font-family: 'Inter', sans-serif; }

        /* ── Hero status bar ── */
        .wrd-hero {
            background: var(--wr-surface);
            border: 1px solid var(--wr-border);
            border-radius: var(--wr-radius);
            padding: 20px 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .wrd-hero::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background:
                radial-gradient(ellipse 60% 100% at 0% 50%, var(--wr-glow-1) 0%, transparent 70%),
                radial-gradient(ellipse 40% 100% at 100% 50%, var(--wr-glow-2) 0%, transparent 70%);
            pointer-events: none;
        }
        .wrd-hero-left {
            display: flex; align-items: center; gap: 16px;
            position: relative; z-index: 1;
        }
        .wrd-req-id {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 700;
            color: var(--wr-muted);
            background: var(--wr-surface2);
            border: 1px solid var(--wr-border);
            padding: 5px 12px;
            border-radius: 6px;
            letter-spacing: 0.5px;
        }
        .wrd-project-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18px; font-weight: 700;
            color: var(--wr-text);
            letter-spacing: -0.2px;
        }
        .wrd-project-loc {
            font-size: 13px; color: var(--wr-muted); margin-top: 2px;
            display: flex; align-items: center; gap: 5px;
        }
        .wrd-hero-right { position: relative; z-index: 1; }

        /* ── Status badge ── */
        .wrd-status-badge {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 7px 16px; border-radius: 20px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 600;
            border: 1.5px solid;
        }
        .wrd-status-dot { width: 7px; height: 7px; border-radius: 50%; }

        .wrd-status--draft     { color: #94a3b8; border-color: rgba(148,163,184,0.3); background: rgba(148,163,184,0.08); }
        .wrd-status--submitted { color: #60a5fa; border-color: rgba(96,165,250,0.3);  background: rgba(96,165,250,0.08); }
        .wrd-status--inspected { color: #c084fc; border-color: rgba(192,132,252,0.3); background: rgba(192,132,252,0.08); }
        .wrd-status--reviewed  { color: #818cf8; border-color: rgba(129,140,248,0.3); background: rgba(129,140,248,0.08); }
        .wrd-status--approved  { color: #34d399; border-color: rgba(52,211,153,0.3);  background: rgba(52,211,153,0.08); }
        .wrd-status--accepted  { color: #34d399; border-color: rgba(52,211,153,0.3);  background: rgba(52,211,153,0.08); }
        .wrd-status--rejected  { color: #f87171; border-color: rgba(248,113,113,0.3); background: rgba(248,113,113,0.08); }

        html:not(.dark) .wrd-status--draft     { color: #64748b;  border-color: rgba(100,116,139,0.25); background: rgba(100,116,139,0.06); }
        html:not(.dark) .wrd-status--submitted { color: #2563eb;  border-color: rgba(37,99,235,0.25);  background: rgba(37,99,235,0.06); }
        html:not(.dark) .wrd-status--inspected { color: #7c3aed;  border-color: rgba(124,58,237,0.25); background: rgba(124,58,237,0.06); }
        html:not(.dark) .wrd-status--reviewed  { color: #4338ca;  border-color: rgba(67,56,202,0.25);  background: rgba(67,56,202,0.06); }
        html:not(.dark) .wrd-status--approved  { color: #059669;  border-color: rgba(5,150,105,0.25);  background: rgba(5,150,105,0.06); }
        html:not(.dark) .wrd-status--accepted  { color: #059669;  border-color: rgba(5,150,105,0.25);  background: rgba(5,150,105,0.06); }
        html:not(.dark) .wrd-status--rejected  { color: #dc2626;  border-color: rgba(220,38,38,0.25);  background: rgba(220,38,38,0.06); }

        /* ── Card ── */
        .wrd-card {
            background: var(--wr-surface);
            border: 1px solid var(--wr-border);
            border-radius: var(--wr-radius);
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .wrd-card-head {
            display: flex; align-items: center; gap: 10px;
            padding: 16px 24px;
            border-bottom: 1px solid var(--wr-border);
            background: var(--wr-surface2);
        }
        .wrd-card-head-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }
        .wrd-card-head-icon.blue   { background: rgba(79,141,255,0.12); }
        .wrd-card-head-icon.green  { background: rgba(0,212,170,0.12); }
        .wrd-card-head-icon.orange { background: rgba(245,158,11,0.12); }
        .wrd-card-head-icon.purple { background: rgba(167,139,250,0.12); }
        .wrd-card-head-icon.slate  { background: rgba(148,163,184,0.12); }
        html:not(.dark) .wrd-card-head-icon.blue   { background: rgba(37,99,235,0.08); }
        html:not(.dark) .wrd-card-head-icon.green  { background: rgba(5,150,105,0.08); }
        html:not(.dark) .wrd-card-head-icon.orange { background: rgba(217,119,6,0.08); }
        html:not(.dark) .wrd-card-head-icon.purple { background: rgba(124,58,237,0.08); }

        .wrd-card-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px; font-weight: 700;
            color: var(--wr-text);
        }
        .wrd-card-body { padding: 24px; }

        /* ── Info grid ── */
        .wrd-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        @media (max-width: 600px) { .wrd-info-grid { grid-template-columns: 1fr; } }
        .wrd-info-grid.three { grid-template-columns: repeat(3, 1fr); }
        @media (max-width: 700px) { .wrd-info-grid.three { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .wrd-info-grid.three { grid-template-columns: 1fr; } }
        .wrd-info-grid.full { grid-template-columns: 1fr; }

        .wrd-info-item { display: flex; flex-direction: column; gap: 4px; }
        .wrd-info-item.span2 { grid-column: span 2; }
        @media (max-width: 600px) { .wrd-info-item.span2 { grid-column: span 1; } }

        .wrd-info-label {
            font-size: 11px; font-weight: 600;
            color: var(--wr-muted);
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .wrd-info-value {
            font-size: 14px; font-weight: 500;
            color: var(--wr-text);
            line-height: 1.55;
        }
        .wrd-info-value.empty { color: var(--wr-muted); font-style: italic; font-weight: 400; }
        .wrd-info-value.pre { white-space: pre-wrap; font-weight: 400; font-size: 13.5px; line-height: 1.65; }
        .wrd-info-value.mono {
            font-family: 'Inter', monospace;
            background: var(--wr-surface2);
            border: 1px solid var(--wr-border);
            border-radius: 6px;
            padding: 3px 10px;
            font-size: 13px;
            display: inline-block;
        }

        /* ── Divider ── */
        .wrd-divider {
            height: 1px;
            background: var(--wr-border);
            margin: 22px 0;
        }

        /* ── Meta row (timestamps) ── */
        .wrd-meta-row {
            display: flex; align-items: center; gap: 6px;
            flex-wrap: wrap;
        }
        .wrd-meta-chip {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--wr-surface2);
            border: 1px solid var(--wr-border);
            border-radius: 6px;
            padding: 5px 12px;
            font-size: 12px; color: var(--wr-muted);
            font-family: 'Inter', sans-serif;
        }
        .wrd-meta-chip strong { color: var(--wr-text); font-weight: 500; }

        /* ── Section divider ── */
        .wrd-section-divider {
            height: 2px;
            background: var(--wr-border);
            margin: 24px 0;
        }
    </style>
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
                    📨 Submitted <strong>{{ $workRequest->submitted_date ?? 'N/A' }}</strong>
                </div>
                @endif
            </div>

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
                        <div style="padding: 16px; border-left: 4px solid #60a5fa; border-radius: 4px; background: rgba(96, 165, 250, 0.06); margin-bottom: 16px;">
                            <p style="font-size: 12px; font-weight: 700; color: #60a5fa; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-hard-hat mr-2"></i> Site Inspector
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Inspector Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->inspected_by_site_inspector ? 'empty' : '' }}">
                                        {{ $workRequest->inspected_by_site_inspector ?? 'Not specified' }}
                                    </span>
                                </div>
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
                    @endif

                    {{-- Surveyor --}}
                    @if($workRequest->surveyor_name || $workRequest->recommendation_surveyor || $workRequest->findings_surveyor)
                        <div style="padding: 16px; border-left: 4px solid #c084fc; border-radius: 4px; background: rgba(192, 132, 252, 0.06); margin-bottom: 16px;">
                            <p style="font-size: 12px; font-weight: 700; color: #c084fc; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-drafting-compass mr-2"></i> Surveyor
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Surveyor Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->surveyor_name ? 'empty' : '' }}">
                                        {{ $workRequest->surveyor_name ?? 'Not specified' }}
                                    </span>
                                </div>
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
                    @endif

                    {{-- Resident Engineer --}}
                    @if($workRequest->resident_engineer_name || $workRequest->recommendation_engineer || $workRequest->findings_engineer)
                        <div style="padding: 16px; border-left: 4px solid #34d399; border-radius: 4px; background: rgba(52, 211, 153, 0.06); margin-bottom: 16px;">
                            <p style="font-size: 12px; font-weight: 700; color: #34d399; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-hard-hat mr-2"></i> Resident Engineer
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Engineer Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->resident_engineer_name ? 'empty' : '' }}">
                                        {{ $workRequest->resident_engineer_name ?? 'Not specified' }}
                                    </span>
                                </div>
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
                    @endif

                    {{-- MTQA --}}
                    @if($workRequest->checked_by_mtqa || $workRequest->recommended_action)
                        <div style="padding: 16px; border-left: 4px solid #f59e0b; border-radius: 4px; background: rgba(245, 158, 11, 0.06); margin-bottom: 16px;">
                            <p style="font-size: 12px; font-weight: 700; color: #f59e0b; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-clipboard-check mr-2"></i> MTQA
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Checked By</span>
                                    <span class="wrd-info-value {{ !$workRequest->checked_by_mtqa ? 'empty' : '' }}">
                                        {{ $workRequest->checked_by_mtqa ?? 'Not specified' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Recommended Action</span>
                                    <span class="wrd-info-value {{ !$workRequest->recommended_action ? 'empty' : '' }}">
                                        {{ $workRequest->recommended_action ?? 'Not specified' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Reviewed By --}}
                    @if($workRequest->reviewed_by || $workRequest->reviewer_designation || $workRequest->reviewed_by_notes)
                        <div style="padding: 16px; border-left: 4px solid #818cf8; border-radius: 4px; background: rgba(129, 140, 248, 0.06); margin-bottom: 16px;">
                            <p style="font-size: 12px; font-weight: 700; color: #818cf8; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-user-check mr-2"></i> Reviewed By
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->reviewed_by ? 'empty' : '' }}">
                                        {{ $workRequest->reviewed_by ?? 'Not specified' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Designation</span>
                                    <span class="wrd-info-value {{ !$workRequest->reviewer_designation ? 'empty' : '' }}">
                                        {{ $workRequest->reviewer_designation ?? 'Not specified' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item span2">
                                    <span class="wrd-info-label">Notes</span>
                                    <span class="wrd-info-value pre {{ !$workRequest->reviewed_by_notes ? 'empty' : '' }}">
                                        {{ $workRequest->reviewed_by_notes ?? 'No notes' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Recommending Approval --}}
                    @if($workRequest->recommending_approval_by || $workRequest->recommending_approval_designation || $workRequest->recommending_approval_notes)
                        <div style="padding: 16px; border-left: 4px solid #f97316; border-radius: 4px; background: rgba(249, 115, 22, 0.06); margin-bottom: 16px;">
                            <p style="font-size: 12px; font-weight: 700; color: #f97316; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-thumbs-up mr-2"></i> Recommending Approval
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->recommending_approval_by ? 'empty' : '' }}">
                                        {{ $workRequest->recommending_approval_by ?? 'Not specified' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Designation</span>
                                    <span class="wrd-info-value {{ !$workRequest->recommending_approval_designation ? 'empty' : '' }}">
                                        {{ $workRequest->recommending_approval_designation ?? 'Not specified' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item span2">
                                    <span class="wrd-info-label">Notes</span>
                                    <span class="wrd-info-value pre {{ !$workRequest->recommending_approval_notes ? 'empty' : '' }}">
                                        {{ $workRequest->recommending_approval_notes ?? 'No notes' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Approved By --}}
                    @if($workRequest->approved_by || $workRequest->approved_by_designation || $workRequest->approved_notes)
                        <div style="padding: 16px; border-left: 4px solid #14b8a6; border-radius: 4px; background: rgba(20, 184, 166, 0.06); margin-bottom: 16px;">
                            <p style="font-size: 12px; font-weight: 700; color: #14b8a6; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-check-circle mr-2"></i> Approved By
                            </p>
                            <div class="wrd-info-grid">
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Name</span>
                                    <span class="wrd-info-value {{ !$workRequest->approved_by ? 'empty' : '' }}">
                                        {{ $workRequest->approved_by ?? 'Not specified' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item">
                                    <span class="wrd-info-label">Designation</span>
                                    <span class="wrd-info-value {{ !$workRequest->approved_by_designation ? 'empty' : '' }}">
                                        {{ $workRequest->approved_by_designation ?? 'Not specified' }}
                                    </span>
                                </div>
                                <div class="wrd-info-item span2">
                                    <span class="wrd-info-label">Notes</span>
                                    <span class="wrd-info-value pre {{ !$workRequest->approved_notes ? 'empty' : '' }}">
                                        {{ $workRequest->approved_notes ?? 'No notes' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Acceptance --}}
                    @if($workRequest->accepted_by_contractor || $workRequest->accepted_date || $workRequest->accepted_time)
                        <div style="padding: 16px; border-left: 4px solid #6b7280; border-radius: 4px; background: rgba(107, 114, 128, 0.06);">
                            <p style="font-size: 12px; font-weight: 700; color: #6b7280; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
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

        </div>
    </div>
</x-app-layout>
