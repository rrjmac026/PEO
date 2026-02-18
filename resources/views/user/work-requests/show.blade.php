<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Work Request Details') }}
            </h2>
            <div class="flex gap-2 flex-wrap">
                @if($workRequest->canEdit())
                    <a href="{{ route('user.work-requests.edit', $workRequest) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i>{{ __('Edit') }}
                    </a>
                @endif
                <a href="{{ route('user.work-requests.print', $workRequest) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-print mr-2"></i>{{ __('Print') }}
                </a>
                <a href="{{ route('user.work-requests.download', $workRequest) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-download mr-2"></i>{{ __('PDF') }}
                </a>
                <a href="{{ route('user.work-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
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

        /* ── Activity log ── */
        .wrd-log-item {
            display: flex; align-items: flex-start; gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid var(--wr-border);
            animation: wrdFadeIn 0.3s ease both;
        }
        .wrd-log-item:last-child { border-bottom: none; padding-bottom: 0; }
        @keyframes wrdFadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }

        .wrd-log-dot-wrap {
            display: flex; flex-direction: column; align-items: center;
            padding-top: 3px; flex-shrink: 0;
        }
        .wrd-log-dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: var(--wr-accent);
            border: 2px solid var(--wr-surface);
            box-shadow: 0 0 0 2px var(--wr-border);
        }
        .wrd-log-event {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 600;
            color: var(--wr-text);
            margin-bottom: 2px;
        }
        .wrd-log-desc { font-size: 12px; color: var(--wr-muted); line-height: 1.5; }
        .wrd-log-time {
            margin-left: auto; flex-shrink: 0;
            font-size: 11px; color: var(--wr-muted);
            font-family: 'Inter', sans-serif;
            white-space: nowrap;
            padding-top: 2px;
        }

        /* ── Delete zone ── */
        .wrd-danger-zone {
            background: var(--wr-surface);
            border: 1px solid rgba(248,113,113,0.25);
            border-radius: var(--wr-radius);
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        html:not(.dark) .wrd-danger-zone { border-color: rgba(220,38,38,0.2); }
        .wrd-danger-text h4 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px; font-weight: 700;
            color: var(--wr-text); margin-bottom: 2px;
        }
        .wrd-danger-text p { font-size: 12px; color: var(--wr-muted); }

        .wrd-btn-danger {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px;
            background: rgba(248,113,113,0.1);
            border: 1.5px solid rgba(248,113,113,0.35);
            border-radius: var(--wr-radius-sm);
            color: #f87171;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all 0.2s;
            text-decoration: none;
        }
        .wrd-btn-danger:hover {
            background: rgba(248,113,113,0.18);
            border-color: rgba(248,113,113,0.6);
            transform: translateY(-1px);
        }
        html:not(.dark) .wrd-btn-danger { color: #dc2626; border-color: rgba(220,38,38,0.3); background: rgba(220,38,38,0.06); }
        html:not(.dark) .wrd-btn-danger:hover { background: rgba(220,38,38,0.12); }
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
                    📨 Submitted <strong>{{ $workRequest->submitted_date->format('M d, Y') }}</strong>
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
                    </div>
                </div>
            </div>

            {{-- ── Request Details ── --}}
            <div class="wrd-card">
                <div class="wrd-card-head">
                    <div class="wrd-card-head-icon green">📋</div>
                    <span class="wrd-card-title">Request Details</span>
                </div>
                <div class="wrd-card-body">
                    <div class="wrd-info-grid">
                        <div class="wrd-info-item">
                            <span class="wrd-info-label">Requested By</span>
                            <span class="wrd-info-value">{{ $workRequest->requested_by }}</span>
                        </div>
                        <div class="wrd-info-item">
                            <span class="wrd-info-label">Work Start Date</span>
                            <span class="wrd-info-value {{ !$workRequest->requested_work_start_date ? 'empty' : '' }}">
                                {{ $workRequest->requested_work_start_date?->format('M d, Y') ?? 'Not set' }}
                            </span>
                        </div>
                        <div class="wrd-info-item">
                            <span class="wrd-info-label">Start Time</span>
                            <span class="wrd-info-value {{ !$workRequest->requested_work_start_time ? 'empty' : '' }}">
                                {{ $workRequest->requested_work_start_time ?? 'Not set' }}
                            </span>
                        </div>
                    </div>

                    <div class="wrd-divider"></div>

                    <div class="wrd-info-item">
                        <span class="wrd-info-label">Description of Work Requested</span>
                        <span class="wrd-info-value pre">{{ $workRequest->description_of_work_requested }}</span>
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
                </div>
            </div>

            {{-- ── Submission Details ── --}}
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

            {{-- ── Activity Log ── --}}
            @if($workRequest->logs->count() > 0)
                <div class="wrd-card">
                    <div class="wrd-card-head">
                        <div class="wrd-card-head-icon slate">🕐</div>
                        <span class="wrd-card-title">Activity Log</span>
                        <span style="margin-left:auto;font-size:12px;color:var(--wr-muted);font-family:'Inter',sans-serif;">
                            {{ $workRequest->logs->count() }} {{ Str::plural('event', $workRequest->logs->count()) }}
                        </span>
                    </div>
                    <div class="wrd-card-body" style="padding-top:8px;padding-bottom:8px;">
                        @foreach($workRequest->logs as $log)
                            <div class="wrd-log-item" style="animation-delay: {{ $loop->index * 0.05 }}s">
                                <div class="wrd-log-dot-wrap">
                                    <div class="wrd-log-dot"></div>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div class="wrd-log-event">{{ ucfirst(str_replace('_', ' ', $log->event)) }}</div>
                                    @if($log->description)
                                        <div class="wrd-log-desc">{{ $log->description }}</div>
                                    @endif
                                </div>
                                <div class="wrd-log-time">{{ $log->created_at->diffForHumans() }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Danger Zone ── --}}
            @if($workRequest->canEdit())
                <div class="wrd-danger-zone">
                    <div class="wrd-danger-text">
                        <h4>Delete this work request</h4>
                        <p>This action is permanent and cannot be undone.</p>
                    </div>
                    <form action="{{ route('user.work-requests.destroy', $workRequest) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this work request? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="wrd-btn-danger">
                            <i class="fas fa-trash"></i> Delete Request
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>