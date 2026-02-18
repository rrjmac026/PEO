<x-app-layout>
    <!-- ── Page Header ── -->
    <div class="mb-8">
        <h1 class="cp-page-title">Concrete Pouring Requests</h1>
        <p class="cp-page-sub">Manage and monitor all concrete pouring requests</p>
    </div>

    @push('styles')
    <style>
        /* ══════════════════════════════════════════
           LIGHT MODE TOKENS (primary / default)
        ══════════════════════════════════════════ */
        :root {
            --cp-surface:   #ffffff;
            --cp-surface2:  #f8fafc;
            --cp-border:    #e2e8f0;
            --cp-text:      #0f172a;
            --cp-text-sec:  #334155;
            --cp-muted:     #64748b;
            --cp-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            --cp-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
        }

        /* ══════════════════════════════════════════
           DARK MODE TOKENS (override on .dark)
        ══════════════════════════════════════════ */
        .dark {
            --cp-surface:   #1a1f2e;
            --cp-surface2:  #1e2335;
            --cp-border:    #2a3050;
            --cp-text:      #e8eaf6;
            --cp-text-sec:  #c5cae9;
            --cp-muted:     #7c85a8;
            --cp-shadow:    0 1px 4px rgba(0,0,0,0.35);
            --cp-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
        }

        /* ── Page heading ── */
        .cp-page-title { font-size: 28px; font-weight: 800; color: var(--cp-text); line-height: 1.2; }
        .cp-page-sub   { font-size: 14px; color: var(--cp-muted); margin-top: 4px; }

        /* ── Alert banner ── */
        .cp-alert {
            display: flex; align-items: flex-start; justify-content: space-between;
            padding: 12px 16px; border-radius: 10px; border: 1px solid;
            margin-bottom: 16px; font-size: 14px;
        }
        .cp-alert.success { background: #f0fdf4; border-color: #86efac; color: #166534; }
        .dark .cp-alert.success { background: rgba(5,150,105,.12); border-color: rgba(52,211,153,.3); color: #6ee7b7; }
        .cp-alert-close {
            background: none; border: none; cursor: pointer; font-size: 14px;
            opacity: .6; color: inherit; padding: 0; margin-left: 12px; flex-shrink: 0;
        }
        .cp-alert-close:hover { opacity: 1; }

        /* ── Panel ── */
        .cp-panel {
            background: var(--cp-surface);
            border: 1px solid var(--cp-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--cp-shadow);
        }
        .cp-panel-body { padding: 20px 24px; }

        /* ── Form inputs ── */
        .cp-input {
            width: 100%;
            background: var(--cp-surface2);
            border: 1px solid var(--cp-border);
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 14px;
            color: var(--cp-text);
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .cp-input::placeholder { color: var(--cp-muted); }
        .cp-input:focus {
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234,88,12,.12);
        }
        .dark .cp-input:focus {
            border-color: #fb923c;
            box-shadow: 0 0 0 3px rgba(251,146,60,.12);
        }

        /* ── Buttons ── */
        .cp-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px;
            font-size: 13px; font-weight: 600;
            border: 1px solid; cursor: pointer;
            transition: all .15s; text-decoration: none; white-space: nowrap;
        }
        .cp-btn-dark {
            background: #1e293b; border-color: #1e293b; color: #fff;
        }
        .cp-btn-dark:hover { background: #334155; border-color: #334155; }
        .dark .cp-btn-dark { background: #e2e8f0; border-color: #e2e8f0; color: #0f172a; }
        .dark .cp-btn-dark:hover { background: #fff; border-color: #fff; }

        .cp-btn-secondary {
            background: var(--cp-surface2); border-color: var(--cp-border); color: var(--cp-text-sec);
        }
        .cp-btn-secondary:hover { border-color: var(--cp-muted); }

        .cp-btn-blue   { background: #2563eb; border-color: #2563eb; color: #fff; }
        .cp-btn-blue:hover { background: #1d4ed8; border-color: #1d4ed8; }
        .dark .cp-btn-blue { background: #3b82f6; border-color: #3b82f6; }
        .dark .cp-btn-blue:hover { background: #60a5fa; border-color: #60a5fa; }

        .cp-btn-purple { background: #7c3aed; border-color: #7c3aed; color: #fff; }
        .cp-btn-purple:hover { background: #6d28d9; border-color: #6d28d9; }
        .dark .cp-btn-purple { background: #8b5cf6; border-color: #8b5cf6; }
        .dark .cp-btn-purple:hover { background: #a78bfa; border-color: #a78bfa; }

        /* ── Table ── */
        .cp-table { width: 100%; border-collapse: collapse; }
        .cp-table thead tr {
            background: var(--cp-surface2);
            border-bottom: 1px solid var(--cp-border);
        }
        .cp-table thead th {
            padding: 11px 20px; text-align: left;
            font-size: 11px; font-weight: 700;
            color: var(--cp-muted);
            text-transform: uppercase; letter-spacing: 0.5px;
            white-space: nowrap;
        }
        .cp-table tbody tr {
            border-bottom: 1px solid var(--cp-border);
            transition: background .12s;
        }
        .cp-table tbody tr:last-child { border-bottom: none; }
        .cp-table tbody tr:hover { background: var(--cp-surface2); }
        .cp-table td { padding: 14px 20px; font-size: 14px; color: var(--cp-text); white-space: nowrap; }
        .cp-table td.muted { color: var(--cp-muted); }

        /* ── Checkbox ── */
        .cp-checkbox {
            width: 15px; height: 15px; border-radius: 4px; cursor: pointer;
            accent-color: #ea580c;
        }

        /* ── Status badges ── */
        .cp-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 12px; font-weight: 600; border: 1px solid;
        }
        .cp-badge-dot { width: 6px; height: 6px; border-radius: 50%; }

        /* light */
        .cp-badge.approved     { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
        .cp-badge.disapproved  { color: #b91c1c; border-color: #fca5a5; background: #fff1f2; }
        .cp-badge.requested    { color: #92400e; border-color: #fcd34d; background: #fffbeb; }

        /* dark */
        .dark .cp-badge.approved    { color: #34d399; border-color: rgba(52,211,153,.3);  background: rgba(52,211,153,.08); }
        .dark .cp-badge.disapproved { color: #f87171; border-color: rgba(248,113,113,.3); background: rgba(248,113,113,.08); }
        .dark .cp-badge.requested   { color: #fbbf24; border-color: rgba(251,191,36,.3);  background: rgba(251,191,36,.08); }

        .cp-badge.approved .cp-badge-dot    { background: #10b981; }
        .cp-badge.disapproved .cp-badge-dot { background: #ef4444; }
        .cp-badge.requested .cp-badge-dot   { background: #f59e0b; }

        /* ── Progress bar ── */
        .cp-progress-wrap {
            display: flex; align-items: center; gap: 8px;
        }
        .cp-progress-track {
            width: 64px; height: 6px; border-radius: 99px;
            background: #e2e8f0; overflow: hidden; flex-shrink: 0;
        }
        .dark .cp-progress-track { background: rgba(255,255,255,.12); }
        .cp-progress-fill {
            height: 100%; border-radius: 99px;
            background: linear-gradient(90deg, #3b82f6, #6366f1);
            transition: width .3s ease;
        }
        .cp-progress-label { font-size: 12px; color: var(--cp-muted); }

        /* ── Action icon buttons ── */
        .cp-action-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 30px; height: 30px; border-radius: 7px;
            font-size: 13px; border: 1px solid; cursor: pointer;
            transition: all .15s; text-decoration: none; background: none;
        }
        .cp-action-btn.view  { color: #ea580c; border-color: #fed7aa; background: #fff7ed; }
        .cp-action-btn.print { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }

        .cp-action-btn.view:hover  { background: #ffedd5; border-color: #fdba74; }
        .cp-action-btn.print:hover { background: #dbeafe; border-color: #93c5fd; }

        .dark .cp-action-btn.view  { color: #fb923c; border-color: rgba(251,146,60,.3);  background: rgba(251,146,60,.1); }
        .dark .cp-action-btn.print { color: #60a5fa; border-color: rgba(96,165,250,.3);  background: rgba(96,165,250,.1); }

        .dark .cp-action-btn.view:hover  { background: rgba(251,146,60,.2);  border-color: rgba(251,146,60,.5); }
        .dark .cp-action-btn.print:hover { background: rgba(96,165,250,.2);  border-color: rgba(96,165,250,.5); }

        /* ── Empty state ── */
        .cp-empty { padding: 56px 24px; text-align: center; }
        .cp-empty i { font-size: 36px; color: var(--cp-muted); opacity: .4; display: block; margin-bottom: 14px; }
        .cp-empty-title { font-size: 15px; font-weight: 600; color: var(--cp-text-sec); }

        /* ── Pagination ── */
        .cp-pagination { padding: 16px 24px; border-top: 1px solid var(--cp-border); }
    </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ── Success Alert ── --}}
            @if(session('success'))
                <div class="cp-alert success" role="alert">
                    <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                    <button class="cp-alert-close" onclick="this.closest('.cp-alert').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- ── Filters ── --}}
            <div class="cp-panel mb-5">
                <div class="cp-panel-body">
                    <form method="GET" action="{{ route('admin.concrete-pouring.index') }}"
                          class="flex flex-wrap gap-3 items-end">

                        <div class="flex-1 min-w-[200px]">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search project, location, contractor…"
                                   class="cp-input">
                        </div>

                        <div class="min-w-[150px]">
                            <select name="status" class="cp-input">
                                <option value="">All Status</option>
                                <option value="requested"   {{ request('status') == 'requested'   ? 'selected' : '' }}>Requested</option>
                                <option value="approved"    {{ request('status') == 'approved'    ? 'selected' : '' }}>Approved</option>
                                <option value="disapproved" {{ request('status') == 'disapproved' ? 'selected' : '' }}>Disapproved</option>
                            </select>
                        </div>

                        <div class="min-w-[150px]">
                            <select name="contractor" class="cp-input">
                                <option value="">All Contractors</option>
                                @foreach($contractors as $contractor)
                                    <option value="{{ $contractor }}" {{ request('contractor') == $contractor ? 'selected' : '' }}>
                                        {{ $contractor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 flex-wrap">
                            <button type="submit" class="cp-btn cp-btn-dark">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            @if(request('search') || request('status') || request('contractor'))
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

            {{-- ── Table ── --}}
            <div class="cp-panel">
                <div class="overflow-x-auto">
                    <table class="cp-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">
                                    <input type="checkbox" id="selectAll" class="cp-checkbox"
                                           onchange="document.querySelectorAll('input.pouring-checkbox').forEach(el => el.checked = this.checked)">
                                </th>
                                <th>Project</th>
                                <th>Location</th>
                                <th>Contractor</th>
                                <th>Pouring Date</th>
                                <th>Status</th>
                                <th>Checklist</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($concretePourings as $pouring)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="pouring-checkbox cp-checkbox" value="{{ $pouring->id }}">
                                    </td>
                                    <td style="font-weight: 600;">{{ $pouring->project_name }}</td>
                                    <td class="muted">{{ $pouring->location }}</td>
                                    <td class="muted">{{ $pouring->contractor }}</td>
                                    <td class="muted">{{ $pouring->pouring_datetime?->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($pouring->status) {
                                                'approved'    => 'approved',
                                                'disapproved' => 'disapproved',
                                                default       => 'requested',
                                            };
                                            $statusLabel = match($pouring->status) {
                                                'approved'    => 'Approved',
                                                'disapproved' => 'Disapproved',
                                                default       => 'Pending',
                                            };
                                        @endphp
                                        <span class="cp-badge {{ $statusClass }}">
                                            <span class="cp-badge-dot"></span>
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="cp-progress-wrap">
                                            <div class="cp-progress-track">
                                                <div class="cp-progress-fill" style="width: {{ $pouring->checklist_progress }}%;"></div>
                                            </div>
                                            <span class="cp-progress-label">{{ $pouring->checklist_progress }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.concrete-pouring.show', $pouring) }}"
                                               class="cp-action-btn view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.concrete-pouring.print', $pouring) }}"
                                               class="cp-action-btn print" title="Print" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="padding: 0;">
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

                {{-- Pagination --}}
                <div class="cp-pagination">
                    {{ $concretePourings->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>