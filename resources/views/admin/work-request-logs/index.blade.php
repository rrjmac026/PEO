@extends('layouts.app')

@section('title', 'Work Request Logs')

@push('styles')
<style>
    /* ══════════════════════════════════════════
       LIGHT MODE TOKENS (primary / default)
    ══════════════════════════════════════════ */
    :root {
        --lg-surface:   #ffffff;
        --lg-surface2:  #f8fafc;
        --lg-border:    #e2e8f0;
        --lg-text:      #0f172a;
        --lg-text-sec:  #334155;
        --lg-muted:     #64748b;
        --lg-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --lg-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
    }

    /* ══════════════════════════════════════════
       DARK MODE TOKENS (override on .dark)
    ══════════════════════════════════════════ */
    .dark {
        --lg-surface:   #1a1f2e;
        --lg-surface2:  #1e2335;
        --lg-border:    #2a3050;
        --lg-text:      #e8eaf6;
        --lg-text-sec:  #c5cae9;
        --lg-muted:     #7c85a8;
        --lg-shadow:    0 1px 4px rgba(0,0,0,0.35);
        --lg-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
    }

    /* ── Page heading ── */
    .lg-page-title { font-size: 28px; font-weight: 800; color: var(--lg-text); line-height: 1.2; }
    .lg-page-sub   { font-size: 14px; color: var(--lg-muted); margin-top: 4px; }

    /* ── Alert banners ── */
    .lg-alert {
        display: flex; align-items: flex-start; justify-content: space-between;
        padding: 12px 16px; border-radius: 10px; border: 1px solid;
        margin-bottom: 16px; font-size: 14px;
    }
    .lg-alert.success { background: #f0fdf4; border-color: #86efac; color: #166534; }
    .lg-alert.error   { background: #fff1f2; border-color: #fca5a5; color: #991b1b; }
    .dark .lg-alert.success { background: rgba(5,150,105,.12); border-color: rgba(52,211,153,.3); color: #6ee7b7; }
    .dark .lg-alert.error   { background: rgba(220,38,38,.10); border-color: rgba(248,113,113,.3); color: #fca5a5; }

    .lg-alert ul { list-style: disc; padding-left: 20px; margin-top: 4px; }
    .lg-alert-close {
        background: none; border: none; cursor: pointer; font-size: 14px;
        opacity: .6; line-height: 1; color: inherit; padding: 0; margin-left: 12px; flex-shrink: 0;
    }
    .lg-alert-close:hover { opacity: 1; }

    /* ── Panel ── */
    .lg-panel {
        background: var(--lg-surface);
        border: 1px solid var(--lg-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--lg-shadow);
    }
    .lg-panel-body { padding: 20px 24px; }

    /* ── Form controls ── */
    .lg-label {
        display: block; font-size: 12px; font-weight: 600;
        color: var(--lg-muted); text-transform: uppercase; letter-spacing: 0.4px;
        margin-bottom: 6px;
    }
    .lg-input {
        width: 100%; background: var(--lg-surface2);
        border: 1px solid var(--lg-border); border-radius: 8px;
        padding: 8px 14px; font-size: 14px; color: var(--lg-text);
        transition: border-color .15s, box-shadow .15s; outline: none;
    }
    .lg-input::placeholder { color: var(--lg-muted); }
    .lg-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    }
    .dark .lg-input:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129,140,248,.12);
    }

    /* ── Buttons ── */
    .lg-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 600;
        border: 1px solid; cursor: pointer; transition: all .15s; text-decoration: none;
        white-space: nowrap;
    }
    .lg-btn-primary {
        background: #4f46e5; border-color: #4f46e5; color: #fff;
    }
    .lg-btn-primary:hover { background: #4338ca; border-color: #4338ca; }
    .dark .lg-btn-primary { background: #6366f1; border-color: #6366f1; }
    .dark .lg-btn-primary:hover { background: #818cf8; border-color: #818cf8; }

    .lg-btn-secondary {
        background: var(--lg-surface2); border-color: var(--lg-border); color: var(--lg-text-sec);
    }
    .lg-btn-secondary:hover { border-color: var(--lg-muted); }

    /* ── Table ── */
    .lg-table { width: 100%; border-collapse: collapse; }
    .lg-table thead tr {
        background: var(--lg-surface2);
        border-bottom: 1px solid var(--lg-border);
    }
    .lg-table thead th {
        padding: 11px 20px; text-align: left;
        font-size: 11px; font-weight: 700;
        color: var(--lg-muted);
        text-transform: uppercase; letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .lg-table tbody tr {
        border-bottom: 1px solid var(--lg-border);
        transition: background .12s;
    }
    .lg-table tbody tr:last-child { border-bottom: none; }
    .lg-table tbody tr:hover { background: var(--lg-surface2); }
    .lg-table td { padding: 14px 20px; font-size: 14px; color: var(--lg-text); }
    .lg-table td.muted { color: var(--lg-muted); }

    /* expandable detail row */
    .lg-detail-row { background: var(--lg-surface2); }
    .lg-detail-row td { padding: 16px 24px; }
    .lg-detail-block { margin-bottom: 12px; }
    .lg-detail-block:last-child { margin-bottom: 0; }
    .lg-detail-label { font-size: 12px; font-weight: 700; color: var(--lg-muted); text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 4px; }
    .lg-detail-text  { font-size: 13px; color: var(--lg-text-sec); line-height: 1.6; }
    .lg-detail-code  {
        background: var(--lg-surface); border: 1px solid var(--lg-border);
        border-radius: 8px; padding: 10px 14px;
        font-family: monospace; font-size: 12px; color: var(--lg-text-sec);
        overflow-x: auto; white-space: pre;
    }
    .lg-detail-ip { font-size: 12px; color: var(--lg-muted); }

    /* ── Work request link chip ── */
    .lg-wr-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 3px 10px; border-radius: 7px;
        font-size: 12px; font-weight: 700; font-family: monospace;
        text-decoration: none; transition: all .15s;
        background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8;
    }
    .lg-wr-chip:hover { background: #dbeafe; border-color: #93c5fd; }
    .dark .lg-wr-chip { background: rgba(96,165,250,.12); border-color: rgba(96,165,250,.3); color: #60a5fa; }
    .dark .lg-wr-chip:hover { background: rgba(96,165,250,.2); }

    .lg-deleted-chip { font-size: 13px; color: var(--lg-muted); font-style: italic; }

    /* ── Avatar + name ── */
    .lg-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; flex-shrink: 0;
        background: #e0e7ff; color: #4338ca;
    }
    .dark .lg-avatar { background: rgba(129,140,248,.2); color: #a5b4fc; }
    .lg-emp-link {
        font-size: 14px; font-weight: 500; color: var(--lg-text);
        text-decoration: none; transition: color .15s;
    }
    .lg-emp-link:hover { color: #4f46e5; }
    .dark .lg-emp-link:hover { color: #818cf8; }
    .lg-system-user { font-size: 13px; color: var(--lg-muted); font-style: italic; }

    /* ── Event badges ── */
    .lg-event-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 600; border: 1px solid;
    }
    .lg-event-badge .dot { width: 6px; height: 6px; border-radius: 50%; }

    /* light */
    .lg-ev-created       { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
    .lg-ev-updated       { color: #1d4ed8; border-color: #93c5fd; background: #eff6ff; }
    .lg-ev-status_changed{ color: #6d28d9; border-color: #c4b5fd; background: #f5f3ff; }
    .lg-ev-submitted     { color: #4338ca; border-color: #a5b4fc; background: #eef2ff; }
    .lg-ev-inspected     { color: #92400e; border-color: #fcd34d; background: #fffbeb; }
    .lg-ev-reviewed      { color: #c2410c; border-color: #fdba74; background: #fff7ed; }
    .lg-ev-approved      { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
    .lg-ev-rejected      { color: #b91c1c; border-color: #fca5a5; background: #fff1f2; }
    .lg-ev-accepted      { color: #0f766e; border-color: #5eead4; background: #f0fdfa; }
    .lg-ev-deleted       { color: #b91c1c; border-color: #fca5a5; background: #fff1f2; }
    .lg-ev-restored      { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
    .lg-ev-default       { color: #475569; border-color: #cbd5e1; background: #f1f5f9; }

    /* dark */
    .dark .lg-ev-created       { color: #34d399; border-color: rgba(52,211,153,.3);  background: rgba(52,211,153,.08);  }
    .dark .lg-ev-updated       { color: #60a5fa; border-color: rgba(96,165,250,.3);  background: rgba(96,165,250,.08);  }
    .dark .lg-ev-status_changed{ color: #c084fc; border-color: rgba(192,132,252,.3); background: rgba(192,132,252,.08); }
    .dark .lg-ev-submitted     { color: #818cf8; border-color: rgba(129,140,248,.3); background: rgba(129,140,248,.08); }
    .dark .lg-ev-inspected     { color: #fbbf24; border-color: rgba(251,191,36,.3);  background: rgba(251,191,36,.08);  }
    .dark .lg-ev-reviewed      { color: #fb923c; border-color: rgba(251,146,60,.3);  background: rgba(251,146,60,.08);  }
    .dark .lg-ev-approved      { color: #34d399; border-color: rgba(52,211,153,.3);  background: rgba(52,211,153,.08);  }
    .dark .lg-ev-rejected      { color: #f87171; border-color: rgba(248,113,113,.3); background: rgba(248,113,113,.08); }
    .dark .lg-ev-accepted      { color: #2dd4bf; border-color: rgba(45,212,191,.3);  background: rgba(45,212,191,.08);  }
    .dark .lg-ev-deleted       { color: #f87171; border-color: rgba(248,113,113,.3); background: rgba(248,113,113,.08); }
    .dark .lg-ev-restored      { color: #34d399; border-color: rgba(52,211,153,.3);  background: rgba(52,211,153,.08);  }
    .dark .lg-ev-default       { color: #94a3b8; border-color: rgba(148,163,184,.3); background: rgba(148,163,184,.08); }

    /* dot colors match event color */
    .lg-ev-created .dot, .lg-ev-approved .dot, .lg-ev-restored .dot { background: #10b981; }
    .lg-ev-updated .dot        { background: #3b82f6; }
    .lg-ev-status_changed .dot { background: #a855f7; }
    .lg-ev-submitted .dot      { background: #6366f1; }
    .lg-ev-inspected .dot      { background: #f59e0b; }
    .lg-ev-reviewed .dot       { background: #f97316; }
    .lg-ev-rejected .dot, .lg-ev-deleted .dot { background: #ef4444; }
    .lg-ev-accepted .dot       { background: #14b8a6; }
    .lg-ev-default .dot        { background: #94a3b8; }

    /* ── Status change arrow ── */
    .lg-status-change {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 12px; font-family: monospace;
        color: var(--lg-text-sec);
    }
    .lg-status-change .arrow { color: var(--lg-muted); font-size: 10px; }

    /* ── Expand button ── */
    .lg-expand-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 7px;
        font-size: 13px; border: 1px solid; cursor: pointer;
        transition: all .15s; background: none;
        color: #4f46e5; border-color: #c7d2fe; background: #eef2ff;
    }
    .lg-expand-btn:hover { background: #e0e7ff; border-color: #a5b4fc; }
    .dark .lg-expand-btn { color: #818cf8; border-color: rgba(129,140,248,.3); background: rgba(129,140,248,.1); }
    .dark .lg-expand-btn:hover { background: rgba(129,140,248,.2); border-color: rgba(129,140,248,.5); }

    /* ── Empty state ── */
    .lg-empty { padding: 56px 24px; text-align: center; }
    .lg-empty i { font-size: 36px; color: var(--lg-muted); opacity: .4; display: block; margin-bottom: 14px; }
    .lg-empty-title { font-size: 16px; font-weight: 600; color: var(--lg-text-sec); }
    .lg-empty-sub   { font-size: 13px; color: var(--lg-muted); margin-top: 4px; }

    /* ── Pagination wrapper ── */
    .lg-pagination { padding: 16px 24px; border-top: 1px solid var(--lg-border); }
</style>
@endpush

@section('content')

    <!-- ── Page Header ── -->
    <div class="mb-8">
        <h1 class="lg-page-title">Work Request Logs</h1>
        <p class="lg-page-sub">Track and monitor all work request activities and changes</p>
    </div>

    <!-- ── Error Alert ── -->
    @if ($errors->any())
        <div class="lg-alert error">
            <div>
                <div style="font-weight:700; margin-bottom:6px;">Please fix the following errors:</div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button class="lg-alert-close" onclick="this.closest('.lg-alert').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- ── Success Alert ── -->
    @if (session('success'))
        <div class="lg-alert success">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button class="lg-alert-close" onclick="this.closest('.lg-alert').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- ── Filters ── -->
    <div class="lg-panel mb-5">
        <div class="lg-panel-body">
            <form method="GET" action="{{ route('admin.work-request-logs.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">

                    <div>
                        <label for="employee_id" class="lg-label">Filter by Employee</label>
                        <select name="employee_id" id="employee_id" class="lg-input">
                            <option value="">All Employees</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                        {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="event" class="lg-label">Filter by Event</label>
                        <select name="event" id="event" class="lg-input">
                            <option value="">All Events</option>
                            @foreach ($events as $event)
                                <option value="{{ $event }}"
                                        {{ request('event') == $event ? 'selected' : '' }}>
                                    {{ $eventLabels[$event] ?? ucfirst(str_replace('_', ' ', $event)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="lg-btn lg-btn-primary flex-1 justify-center">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        @if (request('employee_id') || request('event'))
                            <a href="{{ route('admin.work-request-logs.index') }}" class="lg-btn lg-btn-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- ── Logs Table ── -->
    <div class="lg-panel">
        @if ($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="lg-table">
                    <thead>
                        <tr>
                            <th>Work Request</th>
                            <th>Employee</th>
                            <th>Event</th>
                            <th>Status Change</th>
                            <th>Date &amp; Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            @php
                                $evClass = 'lg-ev-' . ($log->event ?? 'default');
                                $knownEvents = ['created','updated','status_changed','submitted','inspected','reviewed','approved','rejected','accepted','deleted','restored'];
                                if (!in_array($log->event, $knownEvents)) $evClass = 'lg-ev-default';
                            @endphp

                            <tr>
                                <!-- Work Request -->
                                <td>
                                    @if ($log->workRequest)
                                        <a href="{{ route('admin.work-requests.show', $log->workRequest) }}" class="lg-wr-chip">
                                            #{{ $log->workRequest->id }}
                                            <i class="fas fa-external-link-alt" style="font-size:10px;"></i>
                                        </a>
                                    @else
                                        <span class="lg-deleted-chip">Deleted Request</span>
                                    @endif
                                </td>

                                <!-- Employee -->
                                <td>
                                    @if ($log->employee)
                                        <div class="flex items-center gap-2">
                                            <div class="lg-avatar">
                                                {{ strtoupper(substr($log->employee->user->name, 0, 1)) }}
                                            </div>
                                            <a href="{{ route('admin.employees.show', $log->employee) }}" class="lg-emp-link">
                                                {{ $log->employee->user->name }}
                                            </a>
                                        </div>
                                    @else
                                        <span class="lg-system-user">System User</span>
                                    @endif
                                </td>

                                <!-- Event -->
                                <td>
                                    <span class="lg-event-badge {{ $evClass }}">
                                        <span class="dot"></span>
                                        {{ $log->getEventLabelAttribute() }}
                                    </span>
                                </td>

                                <!-- Status Change -->
                                <td>
                                    @if ($log->status_from || $log->status_to)
                                        <div class="lg-status-change">
                                            <span>{{ $log->status_from ? ucfirst($log->status_from) : '—' }}</span>
                                            <span class="arrow"><i class="fas fa-arrow-right"></i></span>
                                            <span>{{ $log->status_to ? ucfirst($log->status_to) : '—' }}</span>
                                        </div>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>

                                <!-- Date & Time -->
                                <td>
                                    <div style="font-size:13px; font-weight:600; color:var(--lg-text);">
                                        {{ $log->created_at->format('M d, Y') }}
                                    </div>
                                    <div style="font-size:11px; color:var(--lg-muted); margin-top:2px;">
                                        {{ $log->created_at->format('h:i A') }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td>
                                    @if ($log->description || $log->note || $log->changes)
                                        <button onclick="toggleDetails(event, {{ $log->id }})"
                                                class="lg-expand-btn" title="View Details">
                                            <i class="fas fa-expand-alt"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            <!-- Expandable Detail Row -->
                            @if ($log->description || $log->note || $log->changes)
                                <tr id="details-{{ $log->id }}" class="hidden lg-detail-row">
                                    <td colspan="6">
                                        @if ($log->description)
                                            <div class="lg-detail-block">
                                                <div class="lg-detail-label">Description</div>
                                                <div class="lg-detail-text">{{ $log->description }}</div>
                                            </div>
                                        @endif
                                        @if ($log->note)
                                            <div class="lg-detail-block">
                                                <div class="lg-detail-label">Note</div>
                                                <div class="lg-detail-text">{{ $log->note }}</div>
                                            </div>
                                        @endif
                                        @if ($log->changes)
                                            <div class="lg-detail-block">
                                                <div class="lg-detail-label">Changes</div>
                                                <div class="lg-detail-code">{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
                                            </div>
                                        @endif
                                        @if ($log->ip_address)
                                            <div class="lg-detail-ip">
                                                <i class="fas fa-network-wired mr-1"></i>
                                                IP Address: <strong>{{ $log->ip_address }}</strong>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="lg-pagination">
                {{ $logs->links() }}
            </div>

        @else
            <div class="lg-empty">
                <i class="fas fa-history"></i>
                <div class="lg-empty-title">No work request logs found</div>
                <div class="lg-empty-sub">
                    @if (request('employee_id') || request('event'))
                        Try adjusting your filters
                    @else
                        Work request activities will appear here
                    @endif
                </div>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    function toggleDetails(event, logId) {
        event.preventDefault();
        const row = document.getElementById('details-' + logId);
        if (!row) return;
        row.classList.toggle('hidden');
        const icon = event.target.closest('button').querySelector('i');
        icon.classList.toggle('fa-expand-alt');
        icon.classList.toggle('fa-compress-alt');
    }
</script>
@endpush