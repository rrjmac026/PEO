@extends('layouts.app')

@section('title', 'Work Requests')

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

    /* ── Alert banners ── */
    .wr-alert {
        display: flex; align-items: flex-start; justify-content: space-between;
        padding: 12px 16px; border-radius: 10px; border: 1px solid;
        margin-bottom: 16px; font-size: 14px;
    }
    .wr-alert.success { background: #f0fdf4; border-color: #86efac; color: #166534; }
    .wr-alert.error   { background: #fff1f2; border-color: #fca5a5; color: #991b1b; }
    .dark .wr-alert.success { background: rgba(5,150,105,.12);  border-color: rgba(52,211,153,.3);  color: #6ee7b7; }
    .dark .wr-alert.error   { background: rgba(220,38,38,.10);  border-color: rgba(248,113,113,.3); color: #fca5a5; }
    .wr-alert-close {
        background: none; border: none; cursor: pointer; font-size: 14px;
        opacity: .6; color: inherit; padding: 0; margin-left: 12px; flex-shrink: 0;
    }
    .wr-alert-close:hover { opacity: 1; }

    /* ── Search / Filter bar ── */
    .wr-filter-panel {
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: var(--wr-shadow);
    }
    .wr-input {
        flex: 1;
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 14px;
        color: var(--wr-text);
        box-shadow: var(--wr-shadow);
        transition: border-color .15s, box-shadow .15s;
        outline: none;
        min-width: 200px;
    }
    .wr-input::placeholder { color: var(--wr-muted); }
    .wr-input:focus {
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234,88,12,.12);
    }
    .dark .wr-input:focus {
        border-color: #fb923c;
        box-shadow: 0 0 0 3px rgba(251,146,60,.12);
    }
    .wr-select {
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 14px;
        color: var(--wr-text);
        box-shadow: var(--wr-shadow);
        outline: none;
        min-width: 150px;
        cursor: pointer;
        transition: border-color .15s;
    }
    .wr-select:focus {
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234,88,12,.12);
    }
    .dark .wr-select { background: var(--wr-surface2); color: var(--wr-text); }

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

    .wr-btn-dark {
        background: #1e293b; border-color: #1e293b; color: #fff;
    }
    .wr-btn-dark:hover { background: #334155; border-color: #334155; }
    .dark .wr-btn-dark { background: #e2e8f0; border-color: #e2e8f0; color: #0f172a; }
    .dark .wr-btn-dark:hover { background: #fff; border-color: #fff; }

    .wr-btn-secondary {
        background: var(--wr-surface2); border-color: var(--wr-border); color: var(--wr-text-sec);
    }
    .wr-btn-secondary:hover { border-color: var(--wr-muted); }

    /* ── Panel ── */
    .wr-panel {
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--wr-shadow);
    }

    /* ── Table ── */
    .wr-table { width: 100%; border-collapse: collapse; }
    .wr-table thead tr {
        background: var(--wr-surface2);
        border-bottom: 1px solid var(--wr-border);
    }
    .wr-table thead th {
        padding: 11px 20px; text-align: left;
        font-size: 11px; font-weight: 700;
        color: var(--wr-muted);
        text-transform: uppercase; letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .wr-table thead th.right { text-align: right; }
    .wr-table tbody tr {
        border-bottom: 1px solid var(--wr-border);
        transition: background .12s;
    }
    .wr-table tbody tr:last-child { border-bottom: none; }
    .wr-table tbody tr:hover { background: var(--wr-surface2); }
    .wr-table td { padding: 14px 20px; font-size: 14px; color: var(--wr-text); white-space: nowrap; }
    .wr-table td.muted { color: var(--wr-muted); }
    .wr-table td.right { text-align: right; }

    /* ── ID chip ── */
    .wr-id-chip {
        font-family: monospace; font-size: 12px; font-weight: 700;
        color: var(--wr-muted);
        background: var(--wr-surface2);
        border: 1px solid var(--wr-border);
        padding: 2px 8px; border-radius: 6px;
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

    /* ── Action icon buttons ── */
    .wr-action-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 7px;
        font-size: 13px; border: 1px solid; cursor: pointer;
        transition: all .15s; text-decoration: none; background: none;
    }
    /* light */
    .wr-action-btn.view     { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }
    .wr-action-btn.edit     { color: #b45309; border-color: #fde68a; background: #fffbeb; }
    .wr-action-btn.print    { color: #6d28d9; border-color: #ddd6fe; background: #f5f3ff; }
    .wr-action-btn.download { color: #166534; border-color: #86efac; background: #f0fdf4; }
    .wr-action-btn.delete   { color: #dc2626; border-color: #fca5a5; background: #fff1f2; }
    /* light hover */
    .wr-action-btn.view:hover     { background: #dbeafe; border-color: #93c5fd; }
    .wr-action-btn.edit:hover     { background: #fef3c7; border-color: #fcd34d; }
    .wr-action-btn.print:hover    { background: #ede9fe; border-color: #c4b5fd; }
    .wr-action-btn.download:hover { background: #dcfce7; border-color: #4ade80; }
    .wr-action-btn.delete:hover   { background: #fee2e2; border-color: #f87171; }
    /* dark */
    .dark .wr-action-btn.view     { color: #60a5fa; border-color: rgba(96,165,250,.3);   background: rgba(96,165,250,.1); }
    .dark .wr-action-btn.edit     { color: #fbbf24; border-color: rgba(251,191,36,.3);   background: rgba(251,191,36,.1); }
    .dark .wr-action-btn.print    { color: #a78bfa; border-color: rgba(167,139,250,.3);  background: rgba(167,139,250,.1); }
    .dark .wr-action-btn.download { color: #4ade80; border-color: rgba(74,222,128,.3);   background: rgba(74,222,128,.1); }
    .dark .wr-action-btn.delete   { color: #f87171; border-color: rgba(248,113,113,.3);  background: rgba(248,113,113,.1); }
    /* dark hover */
    .dark .wr-action-btn.view:hover     { background: rgba(96,165,250,.2);  border-color: rgba(96,165,250,.5); }
    .dark .wr-action-btn.edit:hover     { background: rgba(251,191,36,.2);  border-color: rgba(251,191,36,.5); }
    .dark .wr-action-btn.print:hover    { background: rgba(167,139,250,.2); border-color: rgba(167,139,250,.5); }
    .dark .wr-action-btn.download:hover { background: rgba(74,222,128,.2);  border-color: rgba(74,222,128,.5); }
    .dark .wr-action-btn.delete:hover   { background: rgba(248,113,113,.2); border-color: rgba(248,113,113,.5); }

    /* ── Empty state ── */
    .wr-empty { padding: 56px 24px; text-align: center; }
    .wr-empty i { font-size: 36px; color: var(--wr-muted); opacity: .4; display: block; margin-bottom: 14px; }
    .wr-empty-title { font-size: 16px; font-weight: 600; color: var(--wr-text-sec); margin-bottom: 8px; }
    .wr-empty-sub   { font-size: 14px; color: var(--wr-muted); margin-bottom: 20px; }

    /* ── Pagination ── */
    .wr-pagination { padding: 16px 24px; border-top: 1px solid var(--wr-border); }

    /* ── Stats cards ── */
    .wr-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }
    .wr-stat-card {
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--wr-shadow);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .wr-stat-icon {
        width: 48px; height: 48px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .wr-stat-icon.gray    { background: #f1f5f9; color: #475569; }
    .wr-stat-icon.blue    { background: #eff6ff; color: #1d4ed8; }
    .wr-stat-icon.green   { background: #f0fdf4; color: #166534; }
    .wr-stat-icon.red     { background: #fff1f2; color: #dc2626; }
    .dark .wr-stat-icon.gray  { background: rgba(148,163,184,.1); color: #94a3b8; }
    .dark .wr-stat-icon.blue  { background: rgba(96,165,250,.1);  color: #60a5fa; }
    .dark .wr-stat-icon.green { background: rgba(74,222,128,.1);  color: #4ade80; }
    .dark .wr-stat-icon.red   { background: rgba(248,113,113,.1); color: #f87171; }

    .wr-stat-label { font-size: 12px; color: var(--wr-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; }
    .wr-stat-value { font-size: 28px; font-weight: 800; color: var(--wr-text); line-height: 1.1; margin-top: 2px; }
</style>
@endpush

@section('content')

    <!-- ── Page Header ── -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="wr-page-title">Work Requests</h1>
                <p class="wr-page-sub">Manage and track all work requests</p>
            </div>
            <a href="{{ route('admin.work-requests.create') }}" class="wr-btn wr-btn-orange">
                <i class="fas fa-plus-circle"></i> New Work Request
            </a>
        </div>
    </div>

    <!-- ── Success Alert ── -->
    @if (session('success'))
        <div class="wr-alert success">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button class="wr-alert-close" onclick="this.closest('.wr-alert').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- ── Error Alert ── -->
    @if (session('error'))
        <div class="wr-alert error">
            <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
            <button class="wr-alert-close" onclick="this.closest('.wr-alert').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- ── Search / Filter Bar ── -->
    <div class="wr-filter-panel">
        <form method="GET" action="{{ route('admin.work-requests.index') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search by project name, location, or requester…"
                   class="wr-input">

            <select name="status" class="wr-select">
                <option value="">All Statuses</option>
                @foreach(\App\Models\WorkRequest::getStatuses() as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="wr-btn wr-btn-dark">
                <i class="fas fa-search"></i> Filter
            </button>

            @if (request('search') || request('status'))
                <a href="{{ route('admin.work-requests.index') }}" class="wr-btn wr-btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- ── Work Requests Table ── -->
    <div class="wr-panel">
        @if ($workRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="wr-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Project Name</th>
                            <th>Location</th>
                            <th>Requested By</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th class="right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($workRequests as $request)
                            <tr>
                                <!-- ID -->
                                <td>
                                    <span class="wr-id-chip">#{{ $request->id }}</span>
                                </td>

                                <!-- Project Name -->
                                <td style="color: var(--wr-text-sec); font-weight: 600;">
                                    {{ Str::limit($request->name_of_project, 30) }}
                                </td>

                                <!-- Location -->
                                <td class="muted">
                                    {{ Str::limit($request->project_location, 25) }}
                                </td>

                                <!-- Requested By -->
                                <td class="muted">
                                    {{ $request->requested_by }}
                                </td>

                                <!-- Start Date -->
                                <td class="muted">
                                    {{ $request->requested_work_start_date ? $request->requested_work_start_date->format('M d, Y') : '—' }}
                                </td>

                                <!-- Status -->
                                <td>
                                    @php
                                        $badgeClass = match($request->status) {
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
                                    <span class="wr-badge {{ $badgeClass }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- View --}}
                                        <a href="{{ route('admin.work-requests.show', $request) }}"
                                           class="wr-action-btn view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Edit --}}
                                        @if ($request->canEdit())
                                            <a href="{{ route('admin.work-requests.edit', $request) }}"
                                               class="wr-action-btn edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        {{-- Print --}}
                                        <a href="{{ route('admin.work-requests.print', $request) }}"
                                           class="wr-action-btn print" title="Print PDF" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>

                                        {{-- Download --}}
                                        <a href="{{ route('admin.work-requests.download', $request) }}"
                                           class="wr-action-btn download" title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        {{-- Delete --}}
                                        @if ($request->status === 'draft')
                                            <form action="{{ route('admin.work-requests.destroy', $request) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this work request?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="wr-action-btn delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($workRequests->hasPages())
                <div class="wr-pagination">
                    {{ $workRequests->links() }}
                </div>
            @endif

        @else
            <div class="wr-empty">
                <i class="fas fa-inbox"></i>
                <div class="wr-empty-title">No work requests found</div>
                <div class="wr-empty-sub">Get started by creating your first work request</div>
                <a href="{{ route('admin.work-requests.create') }}" class="wr-btn wr-btn-orange">
                    <i class="fas fa-plus-circle"></i> Create Work Request
                </a>
            </div>
        @endif
    </div>

    <!-- ── Statistics Cards ── -->
    @php
        $stats = [
            ['status' => 'draft',     'label' => 'Draft',     'icon' => 'fa-file',         'color' => 'gray'],
            ['status' => 'submitted', 'label' => 'Submitted', 'icon' => 'fa-paper-plane',  'color' => 'blue'],
            ['status' => 'approved',  'label' => 'Approved',  'icon' => 'fa-check-circle', 'color' => 'green'],
            ['status' => 'rejected',  'label' => 'Rejected',  'icon' => 'fa-times-circle', 'color' => 'red'],
        ];
    @endphp

    <div class="wr-stats-grid">
        @foreach ($stats as $stat)
            <div class="wr-stat-card">
                <div class="wr-stat-icon {{ $stat['color'] }}">
                    <i class="fas {{ $stat['icon'] }}"></i>
                </div>
                <div>
                    <div class="wr-stat-label">{{ $stat['label'] }}</div>
                    <div class="wr-stat-value">
                        {{ \App\Models\WorkRequest::where('status', $stat['status'])->count() }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection