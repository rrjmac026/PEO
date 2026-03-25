<x-app-layout>
    <!-- ── Page Header ── -->
    <div class="mb-8">
        <h1 class="cp-page-title">Concrete Pouring Requests</h1>
        <p class="cp-page-sub">Manage and monitor all concrete pouring requests</p>
    </div>

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
        .cp-panel { background: var(--cp-surface); border: 1px solid var(--cp-border); border-radius: 12px; overflow: hidden; box-shadow: var(--cp-shadow); }
        .cp-panel-body { padding: 20px 24px; }
        .cp-input { width: 100%; background: var(--cp-surface2); border: 1px solid var(--cp-border); border-radius: 8px; padding: 8px 14px; font-size: 14px; color: var(--cp-text); outline: none; }
        .cp-input:focus { border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,.12); }
        .cp-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid; cursor: pointer; transition: all .15s; text-decoration: none; white-space: nowrap; }
        .cp-btn-dark { background: #1e293b; border-color: #1e293b; color: #fff; }
        .cp-btn-dark:hover { background: #334155; }
        .dark .cp-btn-dark { background: #e2e8f0; border-color: #e2e8f0; color: #0f172a; }
        .cp-btn-secondary { background: var(--cp-surface2); border-color: var(--cp-border); color: var(--cp-text-sec); }
        .cp-btn-blue   { background: #2563eb; border-color: #2563eb; color: #fff; }
        .cp-btn-blue:hover { background: #1d4ed8; }
        .cp-btn-purple { background: #7c3aed; border-color: #7c3aed; color: #fff; }
        .cp-btn-purple:hover { background: #6d28d9; }
        .cp-table { width: 100%; border-collapse: collapse; }
        .cp-table thead tr { background: var(--cp-surface2); border-bottom: 1px solid var(--cp-border); }
        .cp-table thead th { padding: 11px 20px; text-align: left; font-size: 11px; font-weight: 700; color: var(--cp-muted); text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .cp-table tbody tr { border-bottom: 1px solid var(--cp-border); transition: background .12s; }
        .cp-table tbody tr:last-child { border-bottom: none; }
        .cp-table tbody tr:hover { background: var(--cp-surface2); }
        .cp-table td { padding: 14px 20px; font-size: 14px; color: var(--cp-text); white-space: nowrap; }
        .cp-table td.muted { color: var(--cp-muted); }
        .cp-checkbox { width: 15px; height: 15px; border-radius: 4px; cursor: pointer; accent-color: #ea580c; }
        .cp-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid; }
        .cp-badge-dot { width: 6px; height: 6px; border-radius: 50%; }
        .cp-badge.approved    { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
        .cp-badge.disapproved { color: #b91c1c; border-color: #fca5a5; background: #fff1f2; }
        .cp-badge.requested   { color: #92400e; border-color: #fcd34d; background: #fffbeb; }
        .cp-badge.in-review   { color: #1e40af; border-color: #93c5fd; background: #eff6ff; }
        .dark .cp-badge.approved    { color: #34d399; border-color: rgba(52,211,153,.3);  background: rgba(52,211,153,.08); }
        .dark .cp-badge.disapproved { color: #f87171; border-color: rgba(248,113,113,.3); background: rgba(248,113,113,.08); }
        .dark .cp-badge.requested   { color: #fbbf24; border-color: rgba(251,191,36,.3);  background: rgba(251,191,36,.08); }
        .dark .cp-badge.in-review   { color: #60a5fa; border-color: rgba(96,165,250,.3);  background: rgba(96,165,250,.08); }
        .cp-badge.approved .cp-badge-dot    { background: #10b981; }
        .cp-badge.disapproved .cp-badge-dot { background: #ef4444; }
        .cp-badge.requested .cp-badge-dot   { background: #f59e0b; }
        .cp-badge.in-review .cp-badge-dot   { background: #3b82f6; }
        .cp-progress-wrap { display: flex; align-items: center; gap: 8px; }
        .cp-progress-track { width: 64px; height: 6px; border-radius: 99px; background: #e2e8f0; overflow: hidden; flex-shrink: 0; }
        .dark .cp-progress-track { background: rgba(255,255,255,.12); }
        .cp-progress-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #3b82f6, #6366f1); }
        .cp-progress-label { font-size: 12px; color: var(--cp-muted); }
        .cp-action-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 7px; font-size: 13px; border: 1px solid; cursor: pointer; transition: all .15s; text-decoration: none; background: none; }
        .cp-action-btn.view  { color: #ea580c; border-color: #fed7aa; background: #fff7ed; }
        .cp-action-btn.print { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }
        .cp-action-btn.view:hover  { background: #ffedd5; }
        .cp-action-btn.print:hover { background: #dbeafe; }
        .dark .cp-action-btn.view  { color: #fb923c; border-color: rgba(251,146,60,.3); background: rgba(251,146,60,.1); }
        .dark .cp-action-btn.print { color: #60a5fa; border-color: rgba(96,165,250,.3); background: rgba(96,165,250,.1); }
        .cp-empty { padding: 56px 24px; text-align: center; }
        .cp-empty i { font-size: 36px; color: var(--cp-muted); opacity: .4; display: block; margin-bottom: 14px; }
        .cp-empty-title { font-size: 15px; font-weight: 600; color: var(--cp-text-sec); }
        .cp-pagination { padding: 16px 24px; border-top: 1px solid var(--cp-border); }
        .cp-alert { display: flex; align-items: flex-start; justify-content: space-between; padding: 12px 16px; border-radius: 10px; border: 1px solid; margin-bottom: 16px; font-size: 14px; }
        .cp-alert.success { background: #f0fdf4; border-color: #86efac; color: #166534; }
        .dark .cp-alert.success { background: rgba(5,150,105,.12); border-color: rgba(52,211,153,.3); color: #6ee7b7; }
        .cp-alert-close { background: none; border: none; cursor: pointer; font-size: 14px; opacity: .6; color: inherit; padding: 0; margin-left: 12px; }
        .cp-alert-close:hover { opacity: 1; }
    </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="cp-alert success" role="alert">
                    <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                    <button class="cp-alert-close" onclick="this.closest('.cp-alert').remove()"><i class="fas fa-times"></i></button>
                </div>
            @endif

            {{-- Quick stats --}}
            <div class="grid grid-cols-3 gap-4 mb-5">
                <div class="cp-panel cp-panel-body text-center">
                    <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Pending Assignment</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $pendingAssignment }}</p>
                </div>
                <div class="cp-panel cp-panel-body text-center">
                    <p class="text-xs text-gray-400 uppercase font-semibold mb-1">In Review</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $inReview }}</p>
                </div>
                <div class="cp-panel cp-panel-body text-center">
                    <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Awaiting Decision</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $awaitingDecision }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="cp-panel mb-5">
                <div class="cp-panel-body">
                    <form method="GET" action="{{ route('admin.concrete-pouring.index') }}"
                          class="flex flex-wrap gap-3 items-end">

                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Search project, location, contractor, ref…"
                                   class="cp-input">
                        </div>

                        <div class="min-w-[150px]">
                            <select name="status" class="cp-input">
                                <option value="">All Status</option>
                                <option value="requested"   {{ request('status') === 'requested'   ? 'selected' : '' }}>Pending</option>
                                <option value="approved"    {{ request('status') === 'approved'    ? 'selected' : '' }}>Approved</option>
                                <option value="disapproved" {{ request('status') === 'disapproved' ? 'selected' : '' }}>Disapproved</option>
                            </select>
                        </div>

                        <div class="min-w-[150px]">
                            <select name="review_step" class="cp-input">
                                <option value="">All Steps</option>
                                <option value="mtqa"                {{ request('review_step') === 'mtqa'                ? 'selected' : '' }}>ME/MTQA</option>
                                <option value="resident_engineer"   {{ request('review_step') === 'resident_engineer'   ? 'selected' : '' }}>Resident Engineer</option>
                                <option value="provincial_engineer" {{ request('review_step') === 'provincial_engineer' ? 'selected' : '' }}>Provincial Engineer</option>
                                <option value="admin_final"         {{ request('review_step') === 'admin_final'         ? 'selected' : '' }}>Awaiting Decision</option>
                            </select>
                        </div>

                        <div class="min-w-[150px]">
                            <select name="contractor" class="cp-input">
                                <option value="">All Contractors</option>
                                @foreach($contractors as $c)
                                    <option value="{{ $c }}" {{ request('contractor') === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 flex-wrap">
                            <button type="submit" class="cp-btn cp-btn-dark"><i class="fas fa-search"></i> Filter</button>
                            @if(request()->hasAny(['search','status','contractor','review_step']))
                                <a href="{{ route('admin.concrete-pouring.index') }}" class="cp-btn cp-btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            @endif
                            <a href="{{ route('admin.concrete-pouring.calendar') }}" class="cp-btn cp-btn-blue">
                                <i class="fas fa-calendar"></i> Calendar
                            </a>
                            <a href="{{ route('admin.concrete-pouring.reports') }}" class="cp-btn cp-btn-purple">
                                <i class="fas fa-chart-bar"></i> Reports
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="cp-panel">
                <div class="overflow-x-auto">
                    <table class="cp-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">
                                    <input type="checkbox" id="selectAll" class="cp-checkbox"
                                           onchange="document.querySelectorAll('input.pouring-checkbox').forEach(el=>el.checked=this.checked)">
                                </th>
                                <th>Ref / Project</th>
                                <th>Contractor</th>
                                <th>Pouring Date</th>
                                <th>Step</th>
                                <th>Status</th>
                                <th>Checklist</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($concretePourings as $p)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="pouring-checkbox cp-checkbox" value="{{ $p->id }}">
                                    </td>
                                    <td>
                                        <div style="font-weight:600;">{{ $p->project_name }}</div>
                                        @if($p->reference_number)
                                            <div style="font-size:11px;color:var(--cp-muted);">{{ $p->reference_number }}</div>
                                        @endif
                                        <div style="font-size:11px;color:var(--cp-muted);">{{ $p->location }}</div>
                                    </td>
                                    <td class="muted">{{ $p->contractor }}</td>
                                    <td class="muted">{{ $p->pouring_datetime?->format('M d, Y') }}</td>
                                    <td class="muted" style="font-size:12px;">{{ $p->current_step_label }}</td>
                                    <td>
                                        @php
                                            $cls = match($p->status) {
                                                'approved'    => 'approved',
                                                'disapproved' => 'disapproved',
                                                default       => $p->current_review_step ? 'in-review' : 'requested',
                                            };
                                            $lbl = match($p->status) {
                                                'approved'    => 'Approved',
                                                'disapproved' => 'Disapproved',
                                                default       => $p->current_review_step ? 'In Review' : 'Pending',
                                            };
                                        @endphp
                                        <span class="cp-badge {{ $cls }}">
                                            <span class="cp-badge-dot"></span>{{ $lbl }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="cp-progress-wrap">
                                            <div class="cp-progress-track">
                                                <div class="cp-progress-fill" style="width:{{ $p->checklist_progress }}%;"></div>
                                            </div>
                                            <span class="cp-progress-label">{{ $p->checklist_progress }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.concrete-pouring.show', $p) }}"
                                               class="cp-action-btn view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.concrete-pouring.print', $p) }}"
                                               class="cp-action-btn print" title="Print" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="padding:0;">
                                        <div class="cp-empty">
                                            <i class="fas fa-layer-group"></i>
                                            <div class="cp-empty-title">No concrete pouring requests found</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="cp-pagination">{{ $concretePourings->links() }}</div>
            </div>

        </div>
    </div>
</x-app-layout>