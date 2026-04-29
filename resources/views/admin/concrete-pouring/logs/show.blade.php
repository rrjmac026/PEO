{{--
    resources/views/admin/concrete-pouring/logs/show.blade.php
    Admin — Full Activity Timeline for a single Concrete Pouring request
    Styled to match the index blade design system.
--}}

<x-app-layout>

    @push('styles')
    <style>
        :root {
            --cp-surface:   #ffffff;
            --cp-surface2:  #f8fafc;
            --cp-border:    #e2e8f0;
            --cp-text:      #0f172a;
            --cp-text-sec:  #334155;
            --cp-muted:     #64748b;
            --cp-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        }
        .dark {
            --cp-surface:   #1a1f2e;
            --cp-surface2:  #1e2335;
            --cp-border:    #2a3050;
            --cp-text:      #e8eaf6;
            --cp-text-sec:  #c5cae9;
            --cp-muted:     #7c85a8;
        }

        /* ── Base ── */
        .cp-page-title { font-size: 28px; font-weight: 800; color: var(--cp-text); }
        .cp-page-sub   { font-size: 14px; color: var(--cp-muted); margin-top: 4px; }

        /* ── Panel ── */
        .cp-panel      { background: var(--cp-surface); border: 1px solid var(--cp-border); border-radius: 12px; overflow: hidden; box-shadow: var(--cp-shadow); }
        .cp-panel-header { padding: 14px 20px; border-bottom: 1px solid var(--cp-border); display: flex; align-items: center; justify-content: space-between; }
        .cp-panel-header-title { font-size: 13px; font-weight: 700; color: var(--cp-text); display: flex; align-items: center; gap: 8px; }
        .cp-panel-body { padding: 20px 24px; }

        /* ── Buttons ── */
        .cp-btn            { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid; cursor: pointer; transition: all .15s; text-decoration: none; white-space: nowrap; }
        .cp-btn-dark       { background: #1e293b; border-color: #1e293b; color: #fff; }
        .cp-btn-dark:hover { background: #334155; }
        .dark .cp-btn-dark { background: #e2e8f0; border-color: #e2e8f0; color: #0f172a; }
        .cp-btn-secondary  { background: var(--cp-surface2); border-color: var(--cp-border); color: var(--cp-text-sec); }
        .cp-btn-secondary:hover { background: var(--cp-border); }

        /* ── Badges / Events ── */
        .cp-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid; white-space: nowrap; }
        .cp-badge-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .cp-badge.ev-submitted   { color: #1e40af; border-color: #93c5fd; background: #eff6ff; }
        .cp-badge.ev-updated     { color: #854d0e; border-color: #fde047; background: #fefce8; }
        .cp-badge.ev-deleted     { color: #991b1b; border-color: #fca5a5; background: #fef2f2; }
        .cp-badge.ev-assigned    { color: #3730a3; border-color: #a5b4fc; background: #eef2ff; }
        .cp-badge.ev-re-reviewed { color: #155e75; border-color: #67e8f9; background: #ecfeff; }
        .cp-badge.ev-pe-noted    { color: #6b21a8; border-color: #d8b4fe; background: #faf5ff; }
        .cp-badge.ev-mtqa        { color: #9a3412; border-color: #fdba74; background: #fff7ed; }
        .cp-badge.ev-approved    { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
        .cp-badge.ev-disapproved { color: #b91c1c; border-color: #fca5a5; background: #fff1f2; }
        .dark .cp-badge.ev-submitted   { color: #60a5fa; border-color: rgba(96,165,250,.3);   background: rgba(96,165,250,.08); }
        .dark .cp-badge.ev-updated     { color: #fde047; border-color: rgba(253,224,71,.3);   background: rgba(253,224,71,.08); }
        .dark .cp-badge.ev-deleted     { color: #f87171; border-color: rgba(248,113,113,.3);  background: rgba(248,113,113,.08); }
        .dark .cp-badge.ev-assigned    { color: #a5b4fc; border-color: rgba(165,180,252,.3);  background: rgba(165,180,252,.08); }
        .dark .cp-badge.ev-re-reviewed { color: #67e8f9; border-color: rgba(103,232,249,.3);  background: rgba(103,232,249,.08); }
        .dark .cp-badge.ev-pe-noted    { color: #d8b4fe; border-color: rgba(216,180,254,.3);  background: rgba(216,180,254,.08); }
        .dark .cp-badge.ev-mtqa        { color: #fdba74; border-color: rgba(253,186,116,.3);  background: rgba(253,186,116,.08); }
        .dark .cp-badge.ev-approved    { color: #34d399; border-color: rgba(52,211,153,.3);   background: rgba(52,211,153,.08); }
        .dark .cp-badge.ev-disapproved { color: #f87171; border-color: rgba(248,113,113,.3);  background: rgba(248,113,113,.08); }
        .cp-badge.ev-submitted   .cp-badge-dot { background: #3b82f6; }
        .cp-badge.ev-updated     .cp-badge-dot { background: #eab308; }
        .cp-badge.ev-deleted     .cp-badge-dot { background: #ef4444; }
        .cp-badge.ev-assigned    .cp-badge-dot { background: #6366f1; }
        .cp-badge.ev-re-reviewed .cp-badge-dot { background: #06b6d4; }
        .cp-badge.ev-pe-noted    .cp-badge-dot { background: #a855f7; }
        .cp-badge.ev-mtqa        .cp-badge-dot { background: #f97316; }
        .cp-badge.ev-approved    .cp-badge-dot { background: #10b981; }
        .cp-badge.ev-disapproved .cp-badge-dot { background: #ef4444; }

        /* ── Status badge (for current status pill) ── */
        .cp-status { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid; }
        .cp-status.approved    { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
        .cp-status.disapproved { color: #b91c1c; border-color: #fca5a5; background: #fff1f2; }
        .cp-status.pending, .cp-status.default { color: #1e40af; border-color: #93c5fd; background: #eff6ff; }
        .dark .cp-status.approved    { color: #34d399; border-color: rgba(52,211,153,.3);   background: rgba(52,211,153,.08); }
        .dark .cp-status.disapproved { color: #f87171; border-color: rgba(248,113,113,.3);  background: rgba(248,113,113,.08); }
        .dark .cp-status.pending, .dark .cp-status.default { color: #60a5fa; border-color: rgba(96,165,250,.3); background: rgba(96,165,250,.08); }

        /* ── Role chip ── */
        .cp-role { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; border: 1px solid; }
        .cp-role.admin               { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }
        .cp-role.contractor          { background: #fffbeb; color: #92400e; border-color: #fde68a; }
        .cp-role.resident_engineer   { background: #ecfeff; color: #155e75; border-color: #a5f3fc; }
        .cp-role.provincial_engineer { background: #faf5ff; color: #6b21a8; border-color: #e9d5ff; }
        .cp-role.mtqa                { background: #fff7ed; color: #9a3412; border-color: #fed7aa; }
        .dark .cp-role.admin               { background: rgba(71,85,105,.15);  color: #94a3b8; border-color: rgba(148,163,184,.25); }
        .dark .cp-role.contractor          { background: rgba(251,191,36,.08); color: #fbbf24; border-color: rgba(251,191,36,.25); }
        .dark .cp-role.resident_engineer   { background: rgba(6,182,212,.08);  color: #67e8f9; border-color: rgba(6,182,212,.25); }
        .dark .cp-role.provincial_engineer { background: rgba(168,85,247,.08); color: #d8b4fe; border-color: rgba(168,85,247,.25); }
        .dark .cp-role.mtqa                { background: rgba(249,115,22,.08); color: #fdba74; border-color: rgba(249,115,22,.25); }

        /* ── Avatar ── */
        .cp-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--cp-surface2); border: 1px solid var(--cp-border); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: var(--cp-text-sec); flex-shrink: 0; }
        .cp-avatar.sm { width: 26px; height: 26px; font-size: 10px; }

        /* ── Info table ── */
        .cp-info-table { width: 100%; font-size: 13px; }
        .cp-info-table tr { border-bottom: 1px solid var(--cp-border); }
        .cp-info-table tr:last-child { border-bottom: none; }
        .cp-info-table td { padding: 10px 20px; vertical-align: top; }
        .cp-info-table td:first-child { color: var(--cp-muted); white-space: nowrap; width: 40%; }
        .cp-info-table td:last-child  { color: var(--cp-text); font-weight: 600; text-align: right; }

        /* ── Reviewer rows ── */
        .cp-reviewer-row { padding: 14px 20px; border-bottom: 1px solid var(--cp-border); }
        .cp-reviewer-row:last-child { border-bottom: none; }
        .cp-reviewer-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--cp-muted); margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between; }
        .cp-reviewer-name  { font-size: 13px; font-weight: 600; color: var(--cp-text); }
        .cp-reviewer-date  { font-size: 11px; color: var(--cp-muted); margin-top: 2px; }
        .cp-reviewer-empty { font-size: 13px; color: var(--cp-muted); font-style: italic; }
        .cp-done-badge    { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f0fdf4; color: #047857; border: 1px solid #6ee7b7; }
        .cp-pending-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #fefce8; color: #854d0e; border: 1px solid #fde047; }
        .dark .cp-done-badge    { background: rgba(52,211,153,.08); color: #34d399; border-color: rgba(52,211,153,.3); }
        .dark .cp-pending-badge { background: rgba(253,224,71,.08); color: #fde047; border-color: rgba(253,224,71,.3); }

        /* ── Outcome card ── */
        .cp-outcome { border-radius: 12px; padding: 16px 20px; border: 1px solid; box-shadow: var(--cp-shadow); }
        .cp-outcome.approved    { background: #f0fdf4; border-color: #6ee7b7; color: #047857; }
        .cp-outcome.disapproved { background: #fff1f2; border-color: #fca5a5; color: #b91c1c; }
        .dark .cp-outcome.approved    { background: rgba(52,211,153,.08); border-color: rgba(52,211,153,.3); color: #34d399; }
        .dark .cp-outcome.disapproved { background: rgba(248,113,113,.08); border-color: rgba(248,113,113,.3); color: #f87171; }
        .cp-outcome-title { font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
        .cp-outcome-by    { font-size: 12px; opacity: .85; }
        .cp-outcome-note  { margin-top: 10px; font-size: 12px; font-style: italic; border-radius: 8px; padding: 8px 12px; background: rgba(255,255,255,.55); border: 1px solid rgba(0,0,0,.06); }
        .dark .cp-outcome-note { background: rgba(0,0,0,.2); border-color: rgba(255,255,255,.06); }

        /* ── Breadcrumb ── */
        .cp-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--cp-muted); margin-bottom: 6px; }
        .cp-breadcrumb a { color: var(--cp-muted); text-decoration: none; transition: color .12s; }
        .cp-breadcrumb a:hover { color: var(--cp-text); }
        .cp-breadcrumb .sep { font-size: 10px; }
        .cp-breadcrumb .current { color: var(--cp-text); font-weight: 600; }

        /* ── Timeline ── */
        .cp-timeline { position: relative; padding-left: 28px; }
        .cp-timeline::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background: var(--cp-border); border-radius: 2px; }
        .cp-tl-item { position: relative; margin-bottom: 4px; }
        .cp-tl-item:last-child { margin-bottom: 0; }
        .cp-tl-dot { position: absolute; left: -34px; top: 14px; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid var(--cp-surface); box-shadow: 0 0 0 1px var(--cp-border); font-size: 14px; color: #fff; }
        .cp-tl-dot.approved    { background: #10b981; }
        .cp-tl-dot.disapproved { background: #ef4444; }
        .cp-tl-dot.assigned    { background: #6366f1; }
        .cp-tl-dot.submitted   { background: #3b82f6; }
        .cp-tl-dot.default     { background: var(--cp-muted); }

        /* ── Event card ── */
        .cp-event-card { background: var(--cp-surface); border: 1px solid var(--cp-border); border-radius: 12px; padding: 16px 18px; margin-bottom: 16px; box-shadow: var(--cp-shadow); transition: box-shadow .15s, border-color .15s; }
        .cp-event-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-color: #ea580c; }
        .cp-event-top { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 10px; }
        .cp-event-actors { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
        .cp-event-time { font-size: 11px; color: var(--cp-muted); white-space: nowrap; }
        .cp-event-desc { font-size: 13px; color: var(--cp-text); line-height: 1.6; }
        .cp-event-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 10px; }
        .cp-event-step { display: inline-flex; align-items: center; gap: 5px; background: var(--cp-surface2); border: 1px solid var(--cp-border); border-radius: 6px; padding: 3px 10px; font-size: 11px; color: var(--cp-text-sec); }
        .cp-status-flow { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; }
        .cp-status-from { background: var(--cp-surface2); border: 1px solid var(--cp-border); border-radius: 4px; padding: 2px 8px; color: var(--cp-muted); text-transform: capitalize; }
        .cp-status-to   { border-radius: 4px; padding: 2px 8px; font-weight: 600; text-transform: capitalize; }
        .cp-status-to.approved    { background: #f0fdf4; color: #047857; border: 1px solid #6ee7b7; }
        .cp-status-to.disapproved { background: #fff1f2; color: #b91c1c; border: 1px solid #fca5a5; }
        .cp-status-to.default     { background: #eff6ff; color: #1e40af; border: 1px solid #93c5fd; }
        .dark .cp-status-to.approved    { background: rgba(52,211,153,.08); color: #34d399; border-color: rgba(52,211,153,.3); }
        .dark .cp-status-to.disapproved { background: rgba(248,113,113,.08); color: #f87171; border-color: rgba(248,113,113,.3); }
        .dark .cp-status-to.default     { background: rgba(96,165,250,.08); color: #60a5fa; border-color: rgba(96,165,250,.3); }

        /* ── Note block ── */
        .cp-note { margin-top: 12px; background: var(--cp-surface2); border: 1px solid var(--cp-border); border-radius: 8px; padding: 10px 14px; display: flex; gap: 10px; }
        .cp-note-icon { color: var(--cp-muted); flex-shrink: 0; margin-top: 1px; }
        .cp-note-text { font-size: 12px; color: var(--cp-text-sec); font-style: italic; line-height: 1.6; }

        /* ── Changes diff ── */
        .cp-diff-toggle { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #ea580c; cursor: pointer; margin-top: 10px; background: none; border: none; padding: 0; }
        .dark .cp-diff-toggle { color: #fb923c; }
        .cp-diff-toggle:hover { text-decoration: underline; }
        .cp-diff-table { width: 100%; border-collapse: collapse; margin-top: 8px; border-radius: 8px; overflow: hidden; border: 1px solid var(--cp-border); font-size: 12px; }
        .cp-diff-table thead tr { background: var(--cp-surface2); }
        .cp-diff-table thead th { padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 700; color: var(--cp-muted); text-transform: uppercase; letter-spacing: .5px; }
        .cp-diff-table tbody tr { border-top: 1px solid var(--cp-border); }
        .cp-diff-table tbody tr:hover { background: var(--cp-surface2); }
        .cp-diff-table td { padding: 7px 12px; vertical-align: top; }
        .cp-diff-table td.field { font-family: monospace; color: var(--cp-text-sec); text-transform: capitalize; }
        .cp-diff-table td.old   { color: #dc2626; text-decoration: line-through; }
        .cp-diff-table td.new   { color: #059669; font-weight: 600; }

        /* ── Tech details ── */
        .cp-tech-toggle { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; color: var(--cp-muted); cursor: pointer; margin-top: 6px; background: none; border: none; padding: 0; }
        .cp-tech-toggle:hover { color: var(--cp-text-sec); }
        .cp-tech-val { font-size: 11px; font-family: monospace; color: var(--cp-muted); margin-top: 4px; }

        /* ── Timeline end marker ── */
        .cp-tl-end { display: flex; align-items: center; gap: 10px; padding: 6px 0 2px; }
        .cp-tl-end-dot { width: 28px; height: 28px; border-radius: 50%; background: var(--cp-surface2); border: 2px solid var(--cp-border); display: flex; align-items: center; justify-content: center; position: absolute; left: -32px; top: 4px; }
        .cp-tl-end-label { font-size: 12px; color: var(--cp-muted); font-style: italic; }

        /* ── Empty state ── */
        .cp-empty { padding: 60px 24px; text-align: center; }
        .cp-empty i { font-size: 36px; color: var(--cp-muted); opacity: .35; display: block; margin-bottom: 14px; }
        .cp-empty-title { font-size: 15px; font-weight: 600; color: var(--cp-text-sec); }
        .cp-empty-sub   { font-size: 13px; color: var(--cp-muted); margin-top: 4px; }

        /* ── Layout grid ── */
        .cp-show-grid { display: grid; grid-template-columns: 320px 1fr; gap: 20px; align-items: start; }
        @media(max-width: 1024px) { .cp-show-grid { grid-template-columns: 1fr; } }
        .cp-sidebar { display: flex; flex-direction: column; gap: 16px; }
    </style>
    @endpush

    @php
        $logs = $concretePouring->logs ?? collect();

        $evClass = [
            'submitted'      => 'ev-submitted',
            'updated'        => 'ev-updated',
            'deleted'        => 'ev-deleted',
            'assigned'       => 'ev-assigned',
            're_reviewed'    => 'ev-re-reviewed',
            'pe_noted'       => 'ev-pe-noted',
            'mtqa_decided'   => 'ev-mtqa',
            'approved'       => 'ev-approved',
            'disapproved'    => 'ev-disapproved',
            'status_changed' => 'ev-submitted',
        ];

        $dotClass = [
            'approved'    => 'approved',
            'disapproved' => 'disapproved',
            'assigned'    => 'assigned',
            'submitted'   => 'submitted',
        ];

        $statusCls = match($concretePouring->status) {
            'approved'    => 'approved',
            'disapproved' => 'disapproved',
            default       => 'pending',
        };
    @endphp

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="mb-8">
        <div class="cp-breadcrumb">
            <a href="{{ route('admin.concrete-pouring.index') }}">Concrete Pouring</a>
            <i class="fas fa-chevron-right sep"></i>
            <a href="{{ route('admin.concrete-pouring.logs') }}">Activity Logs</a>
            <i class="fas fa-chevron-right sep"></i>
            <span class="current">{{ $concretePouring->reference_number }}</span>
        </div>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="cp-page-title flex flex-wrap items-center gap-3">
                    Activity Timeline
                    <span class="cp-status {{ $statusCls }}">
                        <span style="width:7px;height:7px;border-radius:50%;background:currentColor;display:inline-block;opacity:.8;"></span>
                        {{ ucfirst($concretePouring->status) }}
                    </span>
                </h1>
                <p class="cp-page-sub">
                    {{ $concretePouring->project_name }}
                    @if($concretePouring->location)
                        &bull; {{ $concretePouring->location }}
                    @endif
                </p>
            </div>

            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('admin.concrete-pouring.show', $concretePouring) }}" class="cp-btn cp-btn-dark">
                    <i class="fas fa-eye"></i> View Request
                </a>
                <a href="{{ route('admin.concrete-pouring.logs') }}" class="cp-btn cp-btn-secondary">
                    <i class="fas fa-arrow-left"></i> All Logs
                </a>
            </div>
        </div>
    </div>

    <div class="py-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="cp-show-grid">

                {{-- ── SIDEBAR ───────────────────────────────────────────── --}}
                <div class="cp-sidebar">

                    {{-- Request Info --}}
                    <div class="cp-panel">
                        <div class="cp-panel-header">
                            <span class="cp-panel-header-title">
                                <i class="fas fa-file-alt" style="color:var(--cp-muted);font-size:13px;"></i>
                                Request Info
                            </span>
                            <span style="font-size:12px;color:var(--cp-muted);">{{ $logs->count() }} event(s)</span>
                        </div>
                        @php
                            $infoRows = [
                                ['label' => 'Reference #',   'value' => $concretePouring->reference_number ?? '—'],
                                ['label' => 'Project',        'value' => $concretePouring->project_name],
                                ['label' => 'Location',       'value' => $concretePouring->location],
                                ['label' => 'Contractor',     'value' => $concretePouring->contractor],
                                ['label' => 'Structure Part', 'value' => $concretePouring->part_of_structure],
                                ['label' => 'Volume (m³)',    'value' => $concretePouring->estimated_volume],
                                ['label' => 'Pouring Date',   'value' => optional($concretePouring->pouring_datetime)->format('M d, Y g:i A')],
                                ['label' => 'Submitted',      'value' => $concretePouring->created_at->format('M d, Y g:i A')],
                            ];
                        @endphp
                        <table class="cp-info-table">
                            @foreach($infoRows as $row)
                                <tr>
                                    <td>{{ $row['label'] }}</td>
                                    <td>{{ $row['value'] ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    {{-- Assigned Reviewers --}}
                    <div class="cp-panel">
                        <div class="cp-panel-header">
                            <span class="cp-panel-header-title">
                                <i class="fas fa-users" style="color:var(--cp-muted);font-size:13px;"></i>
                                Assigned Reviewers
                            </span>
                        </div>
                        @php
                            $reviewers = [
                                ['label' => 'Resident Engineer',   'user' => $concretePouring->residentEngineer,  'date' => $concretePouring->re_date,     'done' => !is_null($concretePouring->re_date)],
                                ['label' => 'Provincial Engineer', 'user' => $concretePouring->notedByEngineer,   'date' => $concretePouring->noted_date,  'done' => !is_null($concretePouring->noted_date)],
                                ['label' => 'ME / MTQA',           'user' => $concretePouring->meMtqaChecker,     'date' => $concretePouring->me_mtqa_date,'done' => !is_null($concretePouring->me_mtqa_date)],
                            ];
                        @endphp
                        @foreach($reviewers as $reviewer)
                            <div class="cp-reviewer-row">
                                <div class="cp-reviewer-label">
                                    <span>{{ $reviewer['label'] }}</span>
                                    @if($reviewer['done'])
                                        <span class="cp-done-badge"><i class="fas fa-check" style="font-size:9px;"></i> Done</span>
                                    @elseif($reviewer['user'])
                                        <span class="cp-pending-badge">Pending</span>
                                    @else
                                        <span style="font-size:11px;color:var(--cp-muted);">Unassigned</span>
                                    @endif
                                </div>
                                @if($reviewer['user'])
                                    <div class="cp-reviewer-name">{{ $reviewer['user']->name }}</div>
                                    @if($reviewer['date'])
                                        <div class="cp-reviewer-date">
                                            <i class="fas fa-calendar-check" style="font-size:10px;margin-right:4px;"></i>
                                            Reviewed {{ \Carbon\Carbon::parse($reviewer['date'])->format('M d, Y') }}
                                        </div>
                                    @endif
                                @else
                                    <div class="cp-reviewer-empty">Not assigned</div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Outcome Card --}}
                    @if(in_array($concretePouring->status, ['approved', 'disapproved']))
                        @php
                            $outcomeCls  = $concretePouring->status;
                            $outcomeIcon = $concretePouring->status === 'approved' ? 'fa-check-circle' : 'fa-times-circle';
                            $finalUser   = $concretePouring->status === 'approved' ? $concretePouring->approver : $concretePouring->disapprover;
                        @endphp
                        <div class="cp-outcome {{ $outcomeCls }}">
                            <div class="cp-outcome-title">
                                <i class="fas {{ $outcomeIcon }}"></i>
                                {{ ucfirst($concretePouring->status) }}
                            </div>
                            @if($finalUser)
                                <div class="cp-outcome-by">By <strong>{{ $finalUser->name }}</strong></div>
                            @endif
                            @if($concretePouring->approval_remarks)
                                <div class="cp-outcome-note">"{{ $concretePouring->approval_remarks }}"</div>
                            @endif
                        </div>
                    @endif

                </div>

                {{-- ── TIMELINE ──────────────────────────────────────────── --}}
                <div class="cp-panel">
                    <div class="cp-panel-header">
                        <span class="cp-panel-header-title">
                            <i class="fas fa-history" style="color:var(--cp-muted);font-size:13px;"></i>
                            Full Activity Timeline
                        </span>
                        <span style="font-size:12px;color:var(--cp-muted);">
                            {{ $logs->count() }} event(s) &bull; Newest first
                        </span>
                    </div>

                    <div class="cp-panel-body">
                        @if($logs->isEmpty())
                            <div class="cp-empty">
                                <i class="fas fa-clipboard-list"></i>
                                <div class="cp-empty-title">No activity recorded yet</div>
                                <div class="cp-empty-sub">Events will appear here as actions are taken on this request.</div>
                            </div>
                        @else
                            <div class="cp-timeline">
                                @foreach($logs as $log)
                                    @php
                                        $badgeCls = $evClass[$log->event] ?? 'ev-submitted';
                                        $dot      = $dotClass[$log->event] ?? 'default';
                                        $userRole = $log->user?->role ?? 'admin';
                                    @endphp

                                    <div class="cp-tl-item">
                                        <div class="cp-tl-dot {{ $dot }}">
                                            @if($log->event === 'approved')
                                                <i class="fas fa-check" style="font-size:12px;"></i>
                                            @elseif($log->event === 'disapproved')
                                                <i class="fas fa-times" style="font-size:12px;"></i>
                                            @elseif($log->event === 'assigned')
                                                <i class="fas fa-user-check" style="font-size:11px;"></i>
                                            @elseif($log->event === 'submitted')
                                                <i class="fas fa-paper-plane" style="font-size:11px;"></i>
                                            @else
                                                <i class="fas fa-circle" style="font-size:8px;"></i>
                                            @endif
                                        </div>

                                        <div class="cp-event-card">

                                            {{-- Top row --}}
                                            <div class="cp-event-top">
                                                <div class="cp-event-actors">
                                                    <span class="cp-badge {{ $badgeCls }}">
                                                        <span class="cp-badge-dot"></span>
                                                        {{ $log->event_label }}
                                                    </span>
                                                    <div style="display:flex;align-items:center;gap:6px;">
                                                        <div class="cp-avatar sm">
                                                            {{ strtoupper(substr($log->actor_name, 0, 1)) }}
                                                        </div>
                                                        <span style="font-size:13px;font-weight:600;color:var(--cp-text);">
                                                            {{ $log->actor_name }}
                                                        </span>
                                                        <span class="cp-role {{ $userRole }}">{{ $log->actor_role }}</span>
                                                    </div>
                                                </div>
                                                <div class="cp-event-time">
                                                    <span style="font-weight:600;color:var(--cp-text-sec);">{{ $log->created_at->format('M d, Y') }}</span>
                                                    &bull;
                                                    {{ $log->created_at->format('g:i A') }}
                                                    <span style="display:none;" class="sm:inline">&bull; {{ $log->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>

                                            {{-- Description --}}
                                            @if($log->description)
                                                <p class="cp-event-desc">{{ $log->description }}</p>
                                            @endif

                                            {{-- Review step + status transition --}}
                                            @if($log->review_step || $log->status_from || $log->status_to)
                                                <div class="cp-event-meta">
                                                    @if($log->review_step)
                                                        <span class="cp-event-step">
                                                            <i class="fas fa-layer-group" style="font-size:10px;color:var(--cp-muted);"></i>
                                                            Step: {{ ucwords(str_replace('_', ' ', $log->review_step)) }}
                                                        </span>
                                                    @endif

                                                    @if($log->status_from || $log->status_to)
                                                        <div class="cp-status-flow">
                                                            @if($log->status_from)
                                                                <span class="cp-status-from">{{ $log->status_from }}</span>
                                                                <i class="fas fa-arrow-right" style="font-size:9px;color:var(--cp-muted);"></i>
                                                            @endif
                                                            @if($log->status_to)
                                                                @php
                                                                    $toCls = match($log->status_to) {
                                                                        'approved'    => 'approved',
                                                                        'disapproved' => 'disapproved',
                                                                        default       => 'default',
                                                                    };
                                                                @endphp
                                                                <span class="cp-status-to {{ $toCls }}">{{ $log->status_to }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- Freeform note --}}
                                            @if($log->note)
                                                <div class="cp-note">
                                                    <i class="fas fa-comment-dots cp-note-icon" style="font-size:13px;"></i>
                                                    <p class="cp-note-text">{{ $log->note }}</p>
                                                </div>
                                            @endif

                                            {{-- Changes diff --}}
                                            @if(!empty($log->changes))
                                                <details>
                                                    <summary class="cp-diff-toggle">
                                                        <i class="fas fa-chevron-down" style="font-size:10px;"></i>
                                                        {{ count($log->changes) }} field(s) changed — click to expand
                                                    </summary>
                                                    <table class="cp-diff-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Field</th>
                                                                <th>Before</th>
                                                                <th>After</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($log->changes as $field => [$old, $new])
                                                                <tr>
                                                                    <td class="field">{{ str_replace('_', ' ', $field) }}</td>
                                                                    <td class="old">{{ is_bool($old) ? ($old ? 'Yes' : 'No') : ($old ?? '—') }}</td>
                                                                    <td class="new">{{ is_bool($new) ? ($new ? 'Yes' : 'No') : ($new ?? '—') }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </details>
                                            @endif

                                            {{-- Technical details --}}
                                            @if($log->ip_address)
                                                <details>
                                                    <summary class="cp-tech-toggle">
                                                        <i class="fas fa-info-circle" style="font-size:10px;"></i>
                                                        Technical details
                                                    </summary>
                                                    <p class="cp-tech-val">IP: {{ $log->ip_address }}</p>
                                                </details>
                                            @endif

                                        </div>
                                    </div>
                                @endforeach

                                {{-- End marker --}}
                                <div class="cp-tl-item" style="position:relative;">
                                    <div class="cp-tl-end-dot">
                                        <i class="fas fa-clock" style="font-size:11px;color:var(--cp-muted);"></i>
                                    </div>
                                    <div style="padding-left:12px;padding-top:6px;padding-bottom:4px;">
                                        <span class="cp-tl-end-label">
                                            Request created {{ $concretePouring->created_at->format('M d, Y \a\t g:i A') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

</x-app-layout>