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
        .cp-page-title { font-size: 28px; font-weight: 800; color: var(--cp-text); }
        .cp-page-sub   { font-size: 14px; color: var(--cp-muted); margin-top: 4px; }
        .cp-panel      { background: var(--cp-surface); border: 1px solid var(--cp-border); border-radius: 12px; overflow: hidden; box-shadow: var(--cp-shadow); }
        .cp-panel-body { padding: 20px 24px; }
        .cp-input { width: 100%; background: var(--cp-surface2); border: 1px solid var(--cp-border); border-radius: 8px; padding: 8px 14px; font-size: 14px; color: var(--cp-text); outline: none; }
        .cp-input:focus { border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,.12); }
        .cp-btn            { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid; cursor: pointer; transition: all .15s; text-decoration: none; white-space: nowrap; }
        .cp-btn-dark       { background: #1e293b; border-color: #1e293b; color: #fff; }
        .cp-btn-dark:hover { background: #334155; }
        .dark .cp-btn-dark { background: #e2e8f0; border-color: #e2e8f0; color: #0f172a; }
        .cp-btn-secondary  { background: var(--cp-surface2); border-color: var(--cp-border); color: var(--cp-text-sec); }
        .cp-btn-secondary:hover { background: var(--cp-border); }

        /* Stat cards */
        .cp-stat-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 20px; }
        @media(max-width:640px){ .cp-stat-grid { grid-template-columns: repeat(2,1fr); } }
        .cp-stat-card  { background: var(--cp-surface); border: 1px solid var(--cp-border); border-radius: 12px; padding: 16px 20px; box-shadow: var(--cp-shadow); }
        .cp-stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--cp-muted); margin-bottom: 6px; }
        .cp-stat-val   { font-size: 26px; font-weight: 800; line-height: 1; }
        .cp-stat-val.clr-default { color: var(--cp-text); }
        .cp-stat-val.clr-indigo  { color: #6366f1; }
        .cp-stat-val.clr-green   { color: #059669; }
        .cp-stat-val.clr-red     { color: #dc2626; }

        /* Table */
        .cp-table { width: 100%; border-collapse: collapse; }
        .cp-table thead tr { background: var(--cp-surface2); border-bottom: 1px solid var(--cp-border); }
        .cp-table thead th { padding: 11px 20px; text-align: left; font-size: 11px; font-weight: 700; color: var(--cp-muted); text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
        .cp-table tbody tr { border-bottom: 1px solid var(--cp-border); transition: background .12s; cursor: pointer; }
        .cp-table tbody tr:last-child { border-bottom: none; }
        .cp-table tbody tr:hover { background: var(--cp-surface2); }
        .cp-table td { padding: 13px 20px; font-size: 14px; color: var(--cp-text); }
        .cp-table td.muted { color: var(--cp-muted); }

        /* Badges */
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

        /* Role chip */
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

        /* Avatar */
        .cp-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--cp-surface2); border: 1px solid var(--cp-border); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: var(--cp-text-sec); flex-shrink: 0; }

        /* Action btn */
        .cp-action-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 7px; font-size: 13px; border: 1px solid; cursor: pointer; transition: all .15s; text-decoration: none; background: none; }
        .cp-action-btn.view       { color: #ea580c; border-color: #fed7aa; background: #fff7ed; }
        .cp-action-btn.view:hover { background: #ffedd5; }
        .dark .cp-action-btn.view { color: #fb923c; border-color: rgba(251,146,60,.3); background: rgba(251,146,60,.1); }

        /* Empty */
        .cp-empty { padding: 60px 24px; text-align: center; }
        .cp-empty i { font-size: 36px; color: var(--cp-muted); opacity: .35; display: block; margin-bottom: 14px; }
        .cp-empty-title { font-size: 15px; font-weight: 600; color: var(--cp-text-sec); }
        .cp-empty-sub   { font-size: 13px; color: var(--cp-muted); margin-top: 4px; }

        /* Pagination row */
        .cp-pagination { padding: 16px 24px; border-top: 1px solid var(--cp-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }

        /* Reference link */
        .cp-ref-link { font-weight: 600; color: #ea580c; text-decoration: none; }
        .cp-ref-link:hover { text-decoration: underline; }
        .dark .cp-ref-link { color: #fb923c; }

        /* Alert */
        .cp-alert { display: flex; align-items: flex-start; justify-content: space-between; padding: 12px 16px; border-radius: 10px; border: 1px solid; margin-bottom: 16px; font-size: 14px; }
        .cp-alert.success { background: #f0fdf4; border-color: #86efac; color: #166534; }
        .dark .cp-alert.success { background: rgba(5,150,105,.12); border-color: rgba(52,211,153,.3); color: #6ee7b7; }
        .cp-alert-close { background: none; border: none; cursor: pointer; font-size: 14px; opacity: .6; color: inherit; padding: 0; margin-left: 12px; }
        .cp-alert-close:hover { opacity: 1; }

        /* Breadcrumb */
        .cp-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--cp-muted); margin-bottom: 6px; }
        .cp-breadcrumb a { color: var(--cp-muted); text-decoration: none; transition: color .12s; }
        .cp-breadcrumb a:hover { color: var(--cp-text); }
        .cp-breadcrumb .sep { font-size: 10px; }
        .cp-breadcrumb .current { color: var(--cp-text); font-weight: 600; }
    </style>
    @endpush

    {{-- Page header --}}
    <div class="mb-8">
        <div class="cp-breadcrumb">
            <a href="{{ route('admin.concrete-pouring.index') }}">Concrete Pouring</a>
            <i class="fas fa-chevron-right sep"></i>
            <span class="current">Activity Logs</span>
        </div>
        <h1 class="cp-page-title">Activity Logs</h1>
        <p class="cp-page-sub">Complete audit trail of all actions across every concrete pouring request</p>
    </div>

    <div class="py-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="cp-alert success" role="alert">
                    <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                    <button class="cp-alert-close" onclick="this.closest('.cp-alert').remove()"><i class="fas fa-times"></i></button>
                </div>
            @endif

            {{-- Stat cards --}}
            <div class="cp-stat-grid">
                <div class="cp-stat-card">
                    <div class="cp-stat-label"><i class="fas fa-list-ul mr-1"></i> Total Events</div>
                    <div class="cp-stat-val clr-default">{{ number_format($logs->total()) }}</div>
                </div>
                <div class="cp-stat-card">
                    <div class="cp-stat-label"><i class="fas fa-calendar-day mr-1"></i> Today</div>
                    <div class="cp-stat-val clr-indigo">{{ number_format($todayCount) }}</div>
                </div>
                <div class="cp-stat-card">
                    <div class="cp-stat-label"><i class="fas fa-check-circle mr-1"></i> Approvals</div>
                    <div class="cp-stat-val clr-green">{{ number_format($approvedCount) }}</div>
                </div>
                <div class="cp-stat-card">
                    <div class="cp-stat-label"><i class="fas fa-times-circle mr-1"></i> Disapprovals</div>
                    <div class="cp-stat-val clr-red">{{ number_format($disapprovedCount) }}</div>
                </div>
            </div>

            {{-- Filter panel --}}
            <div class="cp-panel mb-5">
                <div class="cp-panel-body">
                    <form method="GET" action="{{ route('admin.concrete-pouring.logs') }}"
                          class="flex flex-wrap gap-3 items-end">

                        <div class="flex-1 min-w-[200px]">
                            <div style="position:relative;">
                                <span style="position:absolute;inset-y:0;left:11px;display:flex;align-items:center;color:var(--cp-muted);pointer-events:none;">
                                    <i class="fas fa-search" style="font-size:12px;"></i>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Ref #, project, contractor, actor…"
                                       class="cp-input" style="padding-left:32px;">
                            </div>
                        </div>

                        <div class="min-w-[155px]">
                            <select name="event" class="cp-input">
                                <option value="">All Events</option>
                                <option value="submitted"    @selected(request('event')==='submitted')>Submitted</option>
                                <option value="updated"      @selected(request('event')==='updated')>Updated</option>
                                <option value="deleted"      @selected(request('event')==='deleted')>Deleted</option>
                                <option value="assigned"     @selected(request('event')==='assigned')>Assigned</option>
                                <option value="re_reviewed"  @selected(request('event')==='re_reviewed')>RE Reviewed</option>
                                <option value="pe_noted"     @selected(request('event')==='pe_noted')>PE Noted</option>
                                <option value="mtqa_decided" @selected(request('event')==='mtqa_decided')>MTQA Decided</option>
                                <option value="approved"     @selected(request('event')==='approved')>Approved</option>
                                <option value="disapproved"  @selected(request('event')==='disapproved')>Disapproved</option>
                            </select>
                        </div>

                        <div class="min-w-[155px]">
                            <select name="role" class="cp-input">
                                <option value="">All Roles</option>
                                <option value="admin"               @selected(request('role')==='admin')>Admin</option>
                                <option value="contractor"          @selected(request('role')==='contractor')>Contractor</option>
                                <option value="resident_engineer"   @selected(request('role')==='resident_engineer')>Resident Engineer</option>
                                <option value="provincial_engineer" @selected(request('role')==='provincial_engineer')>Provincial Engineer</option>
                                <option value="mtqa"                @selected(request('role')==='mtqa')>ME / MTQA</option>
                            </select>
                        </div>

                        <div class="min-w-[140px]">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="cp-input">
                        </div>

                        <div class="min-w-[140px]">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="cp-input">
                        </div>

                        <div class="flex gap-2 flex-wrap">
                            <button type="submit" class="cp-btn cp-btn-dark">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            @if(request()->hasAny(['search','event','role','date_from','date_to']))
                                <a href="{{ route('admin.concrete-pouring.logs') }}" class="cp-btn cp-btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            @endif
                            <a href="{{ route('admin.concrete-pouring.index') }}" class="cp-btn cp-btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            @php
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
            @endphp

            <div class="cp-panel">
                <div class="overflow-x-auto">
                    <table class="cp-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Request</th>
                                <th>Event</th>
                                <th>Description</th>
                                <th>Actor</th>
                                <th style="text-align:center;width:70px;">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                @php
                                    $badgeCls   = $evClass[$log->event] ?? 'ev-submitted';
                                    $userRole   = $log->user?->role ?? 'admin';
                                    $hasPouring = !is_null($log->concretePouring);
                                    $detailUrl  = $hasPouring
                                        ? route('admin.concrete-pouring.logs.show', $log->concretePouring)
                                        : null;
                                @endphp

                                <tr @if($detailUrl) onclick="window.location='{{ $detailUrl }}'" @endif>

                                    {{-- Timestamp --}}
                                    <td class="muted" style="white-space:nowrap;">
                                        <div style="font-size:13px;font-weight:600;color:var(--cp-text-sec);">
                                            {{ $log->created_at->format('M d, Y') }}
                                        </div>
                                        <div style="font-size:11px;margin-top:2px;">
                                            {{ $log->created_at->format('g:i A') }}
                                            &bull;
                                            {{ $log->created_at->diffForHumans() }}
                                        </div>
                                    </td>

                                    {{-- Request --}}
                                    <td>
                                        @if($hasPouring)
                                            <a href="{{ $detailUrl }}"
                                               class="cp-ref-link"
                                               onclick="event.stopPropagation()">
                                                {{ $log->concretePouring->reference_number ?? 'N/A' }}
                                            </a>
                                            <div style="font-size:12px;color:var(--cp-muted);margin-top:2px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                                {{ $log->concretePouring->project_name }}
                                            </div>
                                        @else
                                            <span style="font-size:12px;color:var(--cp-muted);font-style:italic;">[Deleted]</span>
                                        @endif
                                    </td>

                                    {{-- Event --}}
                                    <td>
                                        <span class="cp-badge {{ $badgeCls }}">
                                            <span class="cp-badge-dot"></span>
                                            {{ $log->event_label }}
                                        </span>
                                    </td>

                                    {{-- Description --}}
                                    <td>
                                        <div style="font-size:13px;color:var(--cp-text);max-width:260px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                            {{ $log->description ?? '—' }}
                                        </div>
                                        @if($log->note)
                                            <div style="font-size:11px;color:var(--cp-muted);font-style:italic;margin-top:2px;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                                "{{ $log->note }}"
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Actor --}}
                                    <td>
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <div class="cp-avatar">
                                                {{ strtoupper(substr($log->actor_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-size:13px;font-weight:600;color:var(--cp-text);white-space:nowrap;">
                                                    {{ $log->actor_name }}
                                                </div>
                                                <span class="cp-role {{ $userRole }}">{{ $log->actor_role }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- View --}}
                                    <td style="text-align:center;" onclick="event.stopPropagation()">
                                        @if($hasPouring)
                                            <a href="{{ $detailUrl }}" class="cp-action-btn view" title="View full timeline">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            <span style="color:var(--cp-muted);font-size:11px;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding:0;">
                                        <div class="cp-empty">
                                            <i class="fas fa-clipboard-list"></i>
                                            <div class="cp-empty-title">No activity logs found</div>
                                            <div class="cp-empty-sub">Try adjusting your search filters.</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="cp-pagination">
                    <span style="font-size:13px;color:var(--cp-muted);">
                        @if($logs->total() > 0)
                            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ number_format($logs->total()) }} events
                        @else
                            No events found
                        @endif
                    </span>
                    {{ $logs->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </div>

</x-app-layout>