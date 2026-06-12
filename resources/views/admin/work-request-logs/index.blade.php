@extends('layouts.app')

@section('title', 'Work Request Logs')

@push('styles')
<style>
    /* ═══════════════════════════════════════════════════════
       DESIGN TOKENS
    ═══════════════════════════════════════════════════════ */
    :root {
        --wr-surface:   #ffffff;
        --wr-surface2:  #f8fafc;
        --wr-border:    #e2e8f0;
        --wr-text:      #0f172a;
        --wr-text-sec:  #334155;
        --wr-muted:     #64748b;
        --wr-accent:    #4f46e5;
        --wr-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
    }
    .dark {
        --wr-surface:   #1a1f2e;
        --wr-surface2:  #1e2335;
        --wr-border:    #2a3050;
        --wr-text:      #e8eaf6;
        --wr-text-sec:  #c5cae9;
        --wr-muted:     #7c85a8;
        --wr-accent:    #818cf8;
    }

    /* ── Typography ── */
    .wr-page-title { font-size: 28px; font-weight: 800; color: var(--wr-text); line-height: 1.2; }
    .wr-page-sub   { font-size: 14px; color: var(--wr-muted); margin-top: 4px; }

    /* ── Breadcrumb ── */
    .wr-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--wr-muted); margin-bottom: 6px; }
    .wr-breadcrumb a { color: var(--wr-muted); text-decoration: none; transition: color .12s; }
    .wr-breadcrumb a:hover { color: var(--wr-text); }
    .wr-breadcrumb .sep { font-size: 10px; opacity: .6; }
    .wr-breadcrumb .current { color: var(--wr-text); font-weight: 600; }

    /* ── Alert ── */
    .wr-alert { display: flex; align-items: flex-start; justify-content: space-between; padding: 12px 16px; border-radius: 10px; border: 1px solid; margin-bottom: 16px; font-size: 14px; }
    .wr-alert.success { background: #f0fdf4; border-color: #86efac; color: #166534; }
    .dark .wr-alert.success { background: rgba(5,150,105,.12); border-color: rgba(52,211,153,.3); color: #6ee7b7; }
    .wr-alert-close { background: none; border: none; cursor: pointer; font-size: 14px; opacity: .6; color: inherit; padding: 0; margin-left: 12px; }
    .wr-alert-close:hover { opacity: 1; }

    /* ── Stat cards ── */
    .wr-stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
    @media(max-width:640px) { .wr-stat-grid { grid-template-columns: repeat(2, 1fr); } }
    .wr-stat-card  { background: var(--wr-surface); border: 1px solid var(--wr-border); border-radius: 12px; padding: 16px 20px; box-shadow: var(--wr-shadow); }
    .wr-stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--wr-muted); margin-bottom: 6px; }
    .wr-stat-val   { font-size: 26px; font-weight: 800; line-height: 1; }
    .wr-stat-val.clr-default { color: var(--wr-text); }
    .wr-stat-val.clr-indigo  { color: #6366f1; }
    .wr-stat-val.clr-green   { color: #059669; }
    .wr-stat-val.clr-red     { color: #dc2626; }

    /* ── Panel ── */
    .wr-panel { background: var(--wr-surface); border: 1px solid var(--wr-border); border-radius: 12px; overflow: hidden; box-shadow: var(--wr-shadow); }
    .wr-panel-body { padding: 20px 24px; }

    /* ── Form inputs ── */
    .wr-input { width: 100%; background: var(--wr-surface2); border: 1px solid var(--wr-border); border-radius: 8px; padding: 8px 14px; font-size: 14px; color: var(--wr-text); outline: none; transition: border-color .15s, box-shadow .15s; }
    .wr-input:focus { border-color: var(--wr-accent); box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
    .wr-input::placeholder { color: var(--wr-muted); }

    /* ── Buttons ── */
    .wr-btn            { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid; cursor: pointer; transition: all .15s; text-decoration: none; white-space: nowrap; }
    .wr-btn-dark       { background: #1e293b; border-color: #1e293b; color: #fff; }
    .wr-btn-dark:hover { background: #334155; }
    .dark .wr-btn-dark { background: #e2e8f0; border-color: #e2e8f0; color: #0f172a; }
    .wr-btn-secondary  { background: var(--wr-surface2); border-color: var(--wr-border); color: var(--wr-text-sec); }
    .wr-btn-secondary:hover { background: var(--wr-border); }

    /* ── Table ── */
    .wr-table { width: 100%; border-collapse: collapse; }
    .wr-table thead tr { background: var(--wr-surface2); border-bottom: 1px solid var(--wr-border); }
    .wr-table thead th { padding: 11px 20px; text-align: left; font-size: 11px; font-weight: 700; color: var(--wr-muted); text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
    .wr-table tbody tr { border-bottom: 1px solid var(--wr-border); transition: background .12s; cursor: pointer; }
    .wr-table tbody tr.detail-row { cursor: default; }
    .wr-table tbody tr:last-child { border-bottom: none; }
    .wr-table tbody tr:not(.detail-row):hover { background: var(--wr-surface2); }
    .wr-table td { padding: 13px 20px; font-size: 14px; color: var(--wr-text); }
    .wr-table td.muted { color: var(--wr-muted); }

    /* ── Event badges ── */
    .wr-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid; white-space: nowrap; }
    .wr-badge-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

    /* Light */
    .ev-created       { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
    .ev-updated       { color: #1d4ed8; border-color: #93c5fd; background: #eff6ff; }
    .ev-status_changed{ color: #6d28d9; border-color: #c4b5fd; background: #f5f3ff; }
    .ev-submitted     { color: #3730a3; border-color: #a5b4fc; background: #eef2ff; }
    .ev-inspected     { color: #92400e; border-color: #fcd34d; background: #fffbeb; }
    .ev-reviewed      { color: #c2410c; border-color: #fdba74; background: #fff7ed; }
    .ev-approved      { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
    .ev-rejected      { color: #b91c1c; border-color: #fca5a5; background: #fff1f2; }
    .ev-accepted      { color: #0f766e; border-color: #5eead4; background: #f0fdfa; }
    .ev-deleted       { color: #991b1b; border-color: #fca5a5; background: #fef2f2; }
    .ev-restored      { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
    .ev-default       { color: #475569; border-color: #cbd5e1; background: #f1f5f9; }

    /* Dark */
    .dark .ev-created       { color: #34d399; border-color: rgba(52,211,153,.3);   background: rgba(52,211,153,.08); }
    .dark .ev-updated       { color: #60a5fa; border-color: rgba(96,165,250,.3);   background: rgba(96,165,250,.08); }
    .dark .ev-status_changed{ color: #c084fc; border-color: rgba(192,132,252,.3);  background: rgba(192,132,252,.08); }
    .dark .ev-submitted     { color: #a5b4fc; border-color: rgba(165,180,252,.3);  background: rgba(165,180,252,.08); }
    .dark .ev-inspected     { color: #fbbf24; border-color: rgba(251,191,36,.3);   background: rgba(251,191,36,.08); }
    .dark .ev-reviewed      { color: #fb923c; border-color: rgba(251,146,60,.3);   background: rgba(251,146,60,.08); }
    .dark .ev-approved      { color: #34d399; border-color: rgba(52,211,153,.3);   background: rgba(52,211,153,.08); }
    .dark .ev-rejected      { color: #f87171; border-color: rgba(248,113,113,.3);  background: rgba(248,113,113,.08); }
    .dark .ev-accepted      { color: #2dd4bf; border-color: rgba(45,212,191,.3);   background: rgba(45,212,191,.08); }
    .dark .ev-deleted       { color: #f87171; border-color: rgba(248,113,113,.3);  background: rgba(248,113,113,.08); }
    .dark .ev-restored      { color: #34d399; border-color: rgba(52,211,153,.3);   background: rgba(52,211,153,.08); }
    .dark .ev-default       { color: #94a3b8; border-color: rgba(148,163,184,.3);  background: rgba(148,163,184,.08); }

    /* Badge dots */
    .ev-created .wr-badge-dot, .ev-approved .wr-badge-dot, .ev-restored .wr-badge-dot { background: #10b981; }
    .ev-updated .wr-badge-dot        { background: #3b82f6; }
    .ev-status_changed .wr-badge-dot { background: #a855f7; }
    .ev-submitted .wr-badge-dot      { background: #6366f1; }
    .ev-inspected .wr-badge-dot      { background: #f59e0b; }
    .ev-reviewed .wr-badge-dot       { background: #f97316; }
    .ev-rejected .wr-badge-dot, .ev-deleted .wr-badge-dot { background: #ef4444; }
    .ev-accepted .wr-badge-dot       { background: #14b8a6; }
    .ev-default .wr-badge-dot        { background: #94a3b8; }

    /* ── Work Request link chip ── */
    .wr-ref-link { font-weight: 600; color: var(--wr-accent); text-decoration: none; }
    .wr-ref-link:hover { text-decoration: underline; }

    /* ── Role chip ── */
    .wr-role { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; border: 1px solid; }
    .wr-role.admin               { background: #f1f5f9; color: #475569;  border-color: #cbd5e1; }
    .wr-role.contractor          { background: #fffbeb; color: #92400e;  border-color: #fde68a; }
    .wr-role.site_inspector      { background: #ecfeff; color: #155e75;  border-color: #a5f3fc; }
    .wr-role.surveyor            { background: #f0f9ff; color: #0c4a6e;  border-color: #7dd3fc; }
    .wr-role.resident_engineer   { background: #ecfeff; color: #155e75;  border-color: #a5f3fc; }
    .wr-role.mtqa                { background: #fff7ed; color: #9a3412;  border-color: #fed7aa; }
    .wr-role.engineeriv          { background: #faf5ff; color: #6b21a8;  border-color: #e9d5ff; }
    .wr-role.engineeriii         { background: #fdf4ff; color: #701a75;  border-color: #f0abfc; }
    .wr-role.provincial_engineer { background: #faf5ff; color: #6b21a8;  border-color: #e9d5ff; }
    .dark .wr-role.admin               { background: rgba(71,85,105,.15);    color: #94a3b8; border-color: rgba(148,163,184,.25); }
    .dark .wr-role.contractor          { background: rgba(251,191,36,.08);   color: #fbbf24; border-color: rgba(251,191,36,.25); }
    .dark .wr-role.site_inspector,
    .dark .wr-role.resident_engineer   { background: rgba(6,182,212,.08);    color: #67e8f9; border-color: rgba(6,182,212,.25); }
    .dark .wr-role.surveyor            { background: rgba(56,189,248,.08);   color: #7dd3fc; border-color: rgba(56,189,248,.25); }
    .dark .wr-role.mtqa                { background: rgba(249,115,22,.08);   color: #fdba74; border-color: rgba(249,115,22,.25); }
    .dark .wr-role.engineeriv,
    .dark .wr-role.provincial_engineer { background: rgba(168,85,247,.08);   color: #d8b4fe; border-color: rgba(168,85,247,.25); }
    .dark .wr-role.engineeriii         { background: rgba(217,70,239,.08);   color: #f0abfc; border-color: rgba(217,70,239,.25); }

    /* ── Avatar ── */
    .wr-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--wr-surface2); border: 1px solid var(--wr-border); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: var(--wr-text-sec); flex-shrink: 0; }

    /* ── Status flow arrow ── */
    .wr-status-flow { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; }
    .wr-status-pill { border-radius: 5px; padding: 2px 8px; font-size: 11px; font-weight: 600; border: 1px solid; text-transform: capitalize; }
    .wr-status-pill.from { background: var(--wr-surface2); border-color: var(--wr-border); color: var(--wr-muted); }
    .wr-status-pill.to-approved  { background: #f0fdf4; border-color: #6ee7b7; color: #047857; }
    .wr-status-pill.to-rejected  { background: #fff1f2; border-color: #fca5a5; color: #b91c1c; }
    .wr-status-pill.to-accepted  { background: #f0fdfa; border-color: #5eead4; color: #0f766e; }
    .wr-status-pill.to-default   { background: #eff6ff; border-color: #93c5fd; color: #1e40af; }
    .dark .wr-status-pill.to-approved { background: rgba(52,211,153,.08);   border-color: rgba(52,211,153,.3);   color: #34d399; }
    .dark .wr-status-pill.to-rejected { background: rgba(248,113,113,.08);  border-color: rgba(248,113,113,.3);  color: #f87171; }
    .dark .wr-status-pill.to-accepted { background: rgba(45,212,191,.08);   border-color: rgba(45,212,191,.3);   color: #2dd4bf; }
    .dark .wr-status-pill.to-default  { background: rgba(96,165,250,.08);   border-color: rgba(96,165,250,.3);   color: #60a5fa; }

    /* ── Action btn ── */
    .wr-action-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 7px; font-size: 13px; border: 1px solid; cursor: pointer; transition: all .15s; text-decoration: none; background: none; }
    .wr-action-btn.view        { color: var(--wr-accent); border-color: #c7d2fe; background: #eef2ff; }
    .wr-action-btn.view:hover  { background: #e0e7ff; border-color: #a5b4fc; }
    .dark .wr-action-btn.view  { color: #a5b4fc; border-color: rgba(165,180,252,.3); background: rgba(165,180,252,.1); }
    .wr-action-btn.expand        { color: #64748b; border-color: var(--wr-border); background: var(--wr-surface2); }
    .wr-action-btn.expand:hover  { border-color: var(--wr-muted); background: var(--wr-border); }

    /* ── Expandable detail row ── */
    .wr-detail-row { background: var(--wr-surface2); }
    .wr-detail-row td { padding: 0; }
    .wr-detail-inner { padding: 18px 24px; border-top: 1px solid var(--wr-border); }
    .wr-detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; }
    .wr-detail-block {}
    .wr-detail-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--wr-muted); margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .wr-detail-text  { font-size: 13px; color: var(--wr-text-sec); line-height: 1.7; }
    .wr-detail-code  { background: var(--wr-surface); border: 1px solid var(--wr-border); border-radius: 8px; padding: 10px 14px; font-family: monospace; font-size: 12px; color: var(--wr-text-sec); overflow-x: auto; white-space: pre; margin-top: 4px; }
    .wr-detail-ip { font-size: 11px; color: var(--wr-muted); margin-top: 12px; display: flex; align-items: center; gap: 6px; }

    /* Changes diff table */
    .wr-diff-table { width: 100%; border-collapse: collapse; margin-top: 6px; border-radius: 8px; overflow: hidden; border: 1px solid var(--wr-border); font-size: 12px; }
    .wr-diff-table thead tr { background: var(--wr-surface2); }
    .wr-diff-table thead th { padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 700; color: var(--wr-muted); text-transform: uppercase; letter-spacing: .5px; }
    .wr-diff-table tbody tr { border-top: 1px solid var(--wr-border); }
    .wr-diff-table tbody tr:hover { background: var(--wr-surface); }
    .wr-diff-table td { padding: 7px 12px; vertical-align: top; }
    .wr-diff-table td.field { font-family: monospace; color: var(--wr-text-sec); }
    .wr-diff-table td.old   { color: #dc2626; text-decoration: line-through; opacity: .85; }
    .wr-diff-table td.new   { color: #059669; font-weight: 600; }
    .dark .wr-diff-table td.old { color: #f87171; }
    .dark .wr-diff-table td.new { color: #34d399; }

    /* ── Pagination ── */
    .wr-pagination { padding: 16px 24px; border-top: 1px solid var(--wr-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }

    /* ── Empty ── */
    .wr-empty { padding: 60px 24px; text-align: center; }
    .wr-empty i { font-size: 36px; color: var(--wr-muted); opacity: .35; display: block; margin-bottom: 14px; }
    .wr-empty-title { font-size: 15px; font-weight: 600; color: var(--wr-text-sec); }
    .wr-empty-sub   { font-size: 13px; color: var(--wr-muted); margin-top: 4px; }

    /* ── Deleted chip ── */
    .wr-deleted-chip { font-size: 12px; color: var(--wr-muted); font-style: italic; }
</style>
@endpush

@section('content')

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="wr-breadcrumb">
            <a href="{{ route('admin.work-requests.index') }}">Work Requests</a>
            <i class="fas fa-chevron-right sep"></i>
            <span class="current">Activity Logs</span>
        </div>
        <h1 class="wr-page-title">Activity Logs</h1>
        <p class="wr-page-sub">Complete audit trail of all actions across every work request</p>
    </div>

    @if(session('success'))
        <div class="wr-alert success" role="alert">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button class="wr-alert-close" onclick="this.closest('.wr-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="wr-alert" style="background:#fff1f2;border-color:#fca5a5;color:#991b1b;" role="alert">
            <div>
                <strong>Please fix the following errors:</strong>
                <ul style="list-style:disc;padding-left:18px;margin-top:4px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button class="wr-alert-close" onclick="this.closest('.wr-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stat Cards --}}
    @php
        $totalCount    = $logs->total();
        $todayCount    = \App\Models\WorkRequestLog::whereDate('created_at', today())->count();
        $approvedCount = \App\Models\WorkRequestLog::where('event', \App\Models\WorkRequestLog::EVENT_APPROVED)->count();
        $rejectedCount = \App\Models\WorkRequestLog::where('event', \App\Models\WorkRequestLog::EVENT_REJECTED)->count();
    @endphp
    <div class="wr-stat-grid">
        <div class="wr-stat-card">
            <div class="wr-stat-label"><i class="fas fa-list-ul mr-1"></i> Total Events</div>
            <div class="wr-stat-val clr-default">{{ number_format($totalCount) }}</div>
        </div>
        <div class="wr-stat-card">
            <div class="wr-stat-label"><i class="fas fa-calendar-day mr-1"></i> Today</div>
            <div class="wr-stat-val clr-indigo">{{ number_format($todayCount) }}</div>
        </div>
        <div class="wr-stat-card">
            <div class="wr-stat-label"><i class="fas fa-check-circle mr-1"></i> Approvals</div>
            <div class="wr-stat-val clr-green">{{ number_format($approvedCount) }}</div>
        </div>
        <div class="wr-stat-card">
            <div class="wr-stat-label"><i class="fas fa-times-circle mr-1"></i> Rejections</div>
            <div class="wr-stat-val clr-red">{{ number_format($rejectedCount) }}</div>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="wr-panel mb-5">
        <div class="wr-panel-body">
            <form method="GET" action="{{ route('admin.work-request-logs.index') }}"
                  class="flex flex-wrap gap-3 items-end">

                {{-- Employee filter --}}
                <div class="min-w-[200px] flex-1">
                    <select name="employee_id" class="wr-input">
                        <option value="">All Employees</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}"
                                    @selected(request('employee_id') == $employee->id)>
                                {{ $employee->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Event filter --}}
                <div class="min-w-[180px]">
                    <select name="event" class="wr-input">
                        <option value="">All Events</option>
                        @foreach ($events as $event)
                            <option value="{{ $event }}"
                                    @selected(request('event') == $event)>
                                {{ $eventLabels[$event] ?? ucfirst(str_replace('_', ' ', $event)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Action buttons --}}
                <div class="flex gap-2 flex-wrap">
                    <button type="submit" class="wr-btn wr-btn-dark">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    @if(request()->hasAny(['employee_id', 'event']))
                        <a href="{{ route('admin.work-request-logs.index') }}" class="wr-btn wr-btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                    <a href="{{ route('admin.work-requests.index') }}" class="wr-btn wr-btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Logs Table --}}
    @php
        $knownEvents = ['created','updated','status_changed','submitted','inspected','reviewed','approved','rejected','accepted','deleted','restored'];

        $eventIcons = [
            'created'        => 'fa-plus-circle',
            'updated'        => 'fa-pen',
            'status_changed' => 'fa-arrows-rotate',
            'submitted'      => 'fa-paper-plane',
            'inspected'      => 'fa-hard-hat',
            'reviewed'       => 'fa-clipboard-check',
            'approved'       => 'fa-circle-check',
            'rejected'       => 'fa-circle-xmark',
            'accepted'       => 'fa-handshake',
            'deleted'        => 'fa-trash',
            'restored'       => 'fa-rotate-left',
        ];
    @endphp

    <div class="wr-panel">
        @if($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="wr-table">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Work Request</th>
                            <th>Event</th>
                            <th>Actor</th>
                            <th>Status Change</th>
                            <th>Description</th>
                            <th style="text-align:center;width:90px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            @php
                                $evKey    = in_array($log->event, $knownEvents) ? $log->event : 'default';
                                $badgeCls = 'ev-' . $evKey;
                                $icon     = $eventIcons[$evKey] ?? 'fa-clock';

                                // Resolve actor
                                $actorUser    = $log->user ?? $log->employee?->user ?? null;
                                $actorName    = $actorUser?->name ?? 'System';
                                $actorInitial = strtoupper(substr($actorName, 0, 1));
                                $actorRole    = $actorUser?->role ?? 'admin';

                                // Status pill classes
                                $toCls = match($log->status_to) {
                                    'approved'  => 'to-approved',
                                    'rejected'  => 'to-rejected',
                                    'accepted'  => 'to-accepted',
                                    default     => 'to-default',
                                };

                                $hasDetails = $log->description || $log->note || $log->changes;

                                // Work request detail URL
                                $wrUrl = $log->workRequest
                                    ? route('admin.work-request-logs.show', $log->workRequest)
                                    : null;
                            @endphp

                            {{-- Main row --}}
                            <tr @if($wrUrl) onclick="handleRowClick(event, '{{ $wrUrl }}', {{ $log->id }})" @endif
                                data-row="{{ $log->id }}">

                                {{-- Timestamp --}}
                                <td style="white-space:nowrap;">
                                    <div style="font-size:13px;font-weight:600;color:var(--wr-text-sec);">
                                        {{ $log->created_at->format('M d, Y') }}
                                    </div>
                                    <div style="font-size:11px;color:var(--wr-muted);margin-top:2px;">
                                        {{ $log->created_at->format('g:i A') }}
                                        &bull;
                                        {{ $log->created_at->diffForHumans() }}
                                    </div>
                                </td>

                                {{-- Work Request --}}
                                <td>
                                    @if($log->workRequest)
                                        <a href="{{ $wrUrl }}"
                                           class="wr-ref-link"
                                           onclick="event.stopPropagation()">
                                            #{{ $log->workRequest->id }}
                                        </a>
                                        <div style="font-size:12px;color:var(--wr-muted);margin-top:2px;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                            {{ $log->workRequest->name_of_project ?? $log->workRequest->project_name ?? '—' }}
                                        </div>
                                    @else
                                        <span class="wr-deleted-chip"><i class="fas fa-ban mr-1"></i>[Deleted]</span>
                                    @endif
                                </td>

                                {{-- Event --}}
                                <td>
                                    <span class="wr-badge {{ $badgeCls }}">
                                        <span class="wr-badge-dot"></span>
                                        {{ $log->event_label }}
                                    </span>
                                </td>

                                {{-- Actor --}}
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div class="wr-avatar">{{ $actorInitial }}</div>
                                        <div>
                                            <div style="font-size:13px;font-weight:600;color:var(--wr-text);white-space:nowrap;">
                                                {{ $actorName }}
                                            </div>
                                            <span class="wr-role {{ $actorRole }}">
                                                {{ ucwords(str_replace('_', ' ', $actorRole)) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Status Change --}}
                                <td>
                                    @if($log->status_from || $log->status_to)
                                        <div class="wr-status-flow">
                                            @if($log->status_from)
                                                <span class="wr-status-pill from">{{ $log->status_from }}</span>
                                                <i class="fas fa-arrow-right" style="font-size:9px;color:var(--wr-muted);"></i>
                                            @endif
                                            @if($log->status_to)
                                                <span class="wr-status-pill {{ $toCls }}">{{ $log->status_to }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color:var(--wr-muted);font-size:12px;">—</span>
                                    @endif
                                </td>

                                {{-- Description --}}
                                <td>
                                    <div style="font-size:13px;color:var(--wr-text);max-width:220px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                        {{ $log->description ?? '—' }}
                                    </div>
                                    @if($log->note)
                                        <div style="font-size:11px;color:var(--wr-muted);font-style:italic;margin-top:2px;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                            "{{ $log->note }}"
                                        </div>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td style="text-align:center;" onclick="event.stopPropagation()">
                                    <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                        @if($wrUrl)
                                            <a href="{{ $wrUrl }}" class="wr-action-btn view" title="View work request">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Expandable detail row --}}
                            @if($hasDetails)
                                <tr id="details-{{ $log->id }}" class="wr-detail-row hidden" style="display:none;">
                                    <td colspan="7">
                                        <div class="wr-detail-inner">
                                            <div class="wr-detail-grid">

                                                @if($log->description)
                                                    <div class="wr-detail-block">
                                                        <div class="wr-detail-label">
                                                            <i class="fas fa-align-left" style="font-size:10px;"></i>
                                                            Description
                                                        </div>
                                                        <div class="wr-detail-text">{{ $log->description }}</div>
                                                    </div>
                                                @endif

                                                @if($log->note)
                                                    <div class="wr-detail-block">
                                                        <div class="wr-detail-label">
                                                            <i class="fas fa-comment-dots" style="font-size:10px;"></i>
                                                            Note
                                                        </div>
                                                        <div class="wr-detail-text" style="font-style:italic;">"{{ $log->note }}"</div>
                                                    </div>
                                                @endif

                                            </div>

                                            @if($log->changes)
                                                <div style="margin-top:14px;">
                                                    <div class="wr-detail-label" style="margin-bottom:8px;">
                                                        <i class="fas fa-code-compare" style="font-size:10px;"></i>
                                                        {{ count($log->changes) }} Field(s) Changed
                                                    </div>
                                                    <table class="wr-diff-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Field</th>
                                                                <th>Before</th>
                                                                <th>After</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($log->changes as $field => $change)
                                                                @php
                                                                    [$old, $new] = is_array($change) ? $change : [null, $change];
                                                                @endphp
                                                                <tr>
                                                                    <td class="field">{{ str_replace('_', ' ', $field) }}</td>
                                                                    <td class="old">{{ is_bool($old) ? ($old ? 'Yes' : 'No') : ($old ?? '—') }}</td>
                                                                    <td class="new">{{ is_bool($new) ? ($new ? 'Yes' : 'No') : ($new ?? '—') }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif

                                            @if($log->ip_address)
                                                <div class="wr-detail-ip">
                                                    <i class="fas fa-network-wired"></i>
                                                    IP Address: <strong>{{ $log->ip_address }}</strong>
                                                    @if($log->user_agent)
                                                        &bull; <span style="font-family:monospace;font-size:10px;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:inline-block;vertical-align:bottom;">{{ $log->user_agent }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif

                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="wr-pagination">
                <span style="font-size:13px;color:var(--wr-muted);">
                    Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ number_format($logs->total()) }} events
                </span>
                {{ $logs->withQueryString()->links() }}
            </div>

        @else
            <div class="wr-empty">
                <i class="fas fa-history"></i>
                <div class="wr-empty-title">No activity logs found</div>
                <div class="wr-empty-sub">
                    @if(request()->hasAny(['employee_id', 'event']))
                        Try adjusting your filters.
                    @else
                        Work request activities will appear here as actions are taken.
                    @endif
                </div>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    /**
     * Toggle the expandable detail row.
     * The chevron rotates 180° when open.
     */
    function toggleDetails(event, logId) {
        event.preventDefault();
        event.stopPropagation();

        const row     = document.getElementById('details-' + logId);
        const chevron = document.getElementById('chevron-' + logId);
        if (!row) return;

        const isHidden = row.style.display === 'none' || row.style.display === '';
        row.style.display  = isHidden ? 'table-row' : 'none';
        if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    /**
     * Row click navigates to the work request,
     * unless the click was on an interactive child element.
     */
    function handleRowClick(event, url, logId) {
        if (event.target.closest('a, button')) return;
        window.location = url;
    }
</script>
@endpush