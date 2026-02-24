<x-app-layout>

    @push('styles')
    <style>
        /* ══════════════════════════════════════════
           LIGHT MODE TOKENS (primary / default)
        ══════════════════════════════════════════ */
        :root {
            --db-surface:   #ffffff;
            --db-surface2:  #f8fafc;
            --db-border:    #e2e8f0;
            --db-text:      #0f172a;
            --db-text-sec:  #334155;
            --db-muted:     #64748b;
            --db-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            --db-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
        }

        /* ══════════════════════════════════════════
           DARK MODE TOKENS (override on .dark)
        ══════════════════════════════════════════ */
        .dark {
            --db-surface:   #1a1f2e;
            --db-surface2:  #1e2335;
            --db-border:    #2a3050;
            --db-text:      #e8eaf6;
            --db-text-sec:  #c5cae9;
            --db-muted:     #7c85a8;
            --db-shadow:    0 1px 4px rgba(0,0,0,0.35);
            --db-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
        }

        /* ── Stat cards ── */
        .db-stat-card {
            background: var(--db-surface);
            border: 1px solid var(--db-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--db-shadow);
            transition: box-shadow 0.25s ease;
        }
        .db-stat-card:hover { box-shadow: var(--db-shadow-lg); }

        .db-stat-label { color: var(--db-muted);  font-size: 13px; font-weight: 500; margin-bottom: 6px; }
        .db-stat-value { color: var(--db-text);   font-size: 30px; font-weight: 900; line-height: 1; }
        .db-stat-sub   { color: var(--db-muted);  font-size: 11px; margin-top: 6px; }

        /* icon trays — light solid fills, dark translucent */
        .db-icon-tray        { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .db-icon-tray.blue   { background: #dbeafe; }
        .db-icon-tray.green  { background: #d1fae5; }
        .db-icon-tray.purple { background: #ede9fe; }
        .dark .db-icon-tray.blue   { background: rgba(79,141,255,.15); }
        .dark .db-icon-tray.green  { background: rgba(0,212,170,.13);  }
        .dark .db-icon-tray.purple { background: rgba(167,139,250,.14); }

        .db-icon-tray.blue   i { color: #2563eb; }
        .db-icon-tray.green  i { color: #059669; }
        .db-icon-tray.purple i { color: #7c3aed; }
        .dark .db-icon-tray.blue   i { color: #60a5fa; }
        .dark .db-icon-tray.green  i { color: #34d399; }
        .dark .db-icon-tray.purple i { color: #c084fc; }

        /* stat card footer strips */
        .db-stat-foot         { padding: 10px 24px; border-top: 1px solid var(--db-border); }
        .db-stat-foot.blue    { background: #eff6ff; }
        .db-stat-foot.green   { background: #f0fdf4; }
        .db-stat-foot.purple  { background: #f5f3ff; }
        .dark .db-stat-foot.blue   { background: rgba(37,99,235,.08);  border-top-color: var(--db-border); }
        .dark .db-stat-foot.green  { background: rgba(5,150,105,.08);  border-top-color: var(--db-border); }
        .dark .db-stat-foot.purple { background: rgba(124,58,237,.08); border-top-color: var(--db-border); }

        .db-stat-foot a      { font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: opacity .15s; text-decoration: none; }
        .db-stat-foot.blue   a { color: #2563eb; }
        .db-stat-foot.green  a { color: #059669; }
        .db-stat-foot.purple a { color: #7c3aed; }
        .dark .db-stat-foot.blue   a { color: #60a5fa; }
        .dark .db-stat-foot.green  a { color: #34d399; }
        .dark .db-stat-foot.purple a { color: #c084fc; }
        .db-stat-foot a:hover { opacity: .7; }

        /* ── Quick action cards ── */
        .db-action-card {
            position: relative;
            background: var(--db-surface);
            border: 1px solid var(--db-border);
            border-radius: 12px;
            padding: 24px;
            overflow: hidden;
            box-shadow: var(--db-shadow);
            transition: box-shadow 0.25s ease, transform 0.25s ease;
            display: block; text-decoration: none;
        }
        .db-action-card:hover { box-shadow: var(--db-shadow-lg); transform: translateY(-3px); }

        .db-action-overlay {
            position: absolute; inset: 0; opacity: 0; border-radius: 12px;
            transition: opacity 0.25s ease; pointer-events: none;
        }
        .db-action-card:hover .db-action-overlay { opacity: 1; }

        /* overlay tints — light */
        .db-action-card.orange .db-action-overlay { background: linear-gradient(135deg, #fff7ed 0%, transparent 70%); }
        .db-action-card.green  .db-action-overlay { background: linear-gradient(135deg, #f0fdf4 0%, transparent 70%); }
        .db-action-card.blue   .db-action-overlay { background: linear-gradient(135deg, #eff6ff 0%, transparent 70%); }
        .db-action-card.purple .db-action-overlay { background: linear-gradient(135deg, #f5f3ff 0%, transparent 70%); }
        /* overlay tints — dark */
        .dark .db-action-card.orange .db-action-overlay { background: linear-gradient(135deg, rgba(194,65,12,.15) 0%, transparent 70%); }
        .dark .db-action-card.green  .db-action-overlay { background: linear-gradient(135deg, rgba(5,150,105,.12) 0%, transparent 70%); }
        .dark .db-action-card.blue   .db-action-overlay { background: linear-gradient(135deg, rgba(37,99,235,.12) 0%, transparent 70%); }
        .dark .db-action-card.purple .db-action-overlay { background: linear-gradient(135deg, rgba(124,58,237,.12) 0%, transparent 70%); }

        /* action icon trays */
        .db-action-icon        { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 16px; position: relative; z-index: 1; }
        .db-action-icon.orange { background: #fff7ed; }
        .db-action-icon.green  { background: #f0fdf4; }
        .db-action-icon.blue   { background: #eff6ff; }
        .db-action-icon.purple { background: #f5f3ff; }
        .dark .db-action-icon.orange { background: rgba(194,65,12,.18);  }
        .dark .db-action-icon.green  { background: rgba(5,150,105,.16);  }
        .dark .db-action-icon.blue   { background: rgba(37,99,235,.16);  }
        .dark .db-action-icon.purple { background: rgba(124,58,237,.16); }

        .db-action-icon.orange i { color: #ea580c; }
        .db-action-icon.green  i { color: #059669; }
        .db-action-icon.blue   i { color: #2563eb; }
        .db-action-icon.purple i { color: #7c3aed; }
        .dark .db-action-icon.orange i { color: #fb923c; }
        .dark .db-action-icon.green  i { color: #34d399; }
        .dark .db-action-icon.blue   i { color: #60a5fa; }
        .dark .db-action-icon.purple i { color: #c084fc; }

        .db-action-title { font-size: 15px; font-weight: 600; color: var(--db-text);  margin-bottom: 4px; position: relative; z-index: 1; }
        .db-action-desc  { font-size: 13px; color: var(--db-muted); position: relative; z-index: 1; }

        /* ── Activity panel ── */
        .db-panel {
            background: var(--db-surface);
            border: 1px solid var(--db-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--db-shadow);
        }
        .db-activity-item {
            padding: 20px 24px;
            border-bottom: 1px solid var(--db-border);
            transition: background 0.15s;
        }
        .db-activity-item:last-child { border-bottom: none; }
        .db-activity-item:hover { background: var(--db-surface2); }

        .db-act-icon        { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
        .db-act-icon.orange { background: #fff7ed; }
        .db-act-icon.green  { background: #f0fdf4; }
        .db-act-icon.blue   { background: #eff6ff; }
        .dark .db-act-icon.orange { background: rgba(194,65,12,.18);  }
        .dark .db-act-icon.green  { background: rgba(5,150,105,.16);  }
        .dark .db-act-icon.blue   { background: rgba(37,99,235,.16);  }

        .db-act-icon.orange i { color: #ea580c; }
        .db-act-icon.green  i { color: #059669; }
        .db-act-icon.blue   i { color: #2563eb; }
        .dark .db-act-icon.orange i { color: #fb923c; }
        .dark .db-act-icon.green  i { color: #34d399; }
        .dark .db-act-icon.blue   i { color: #60a5fa; }

        .db-act-title { font-size: 13px; font-weight: 600; color: var(--db-text); }
        .db-act-desc  { font-size: 13px; color: var(--db-muted); margin-top: 3px; }
        .db-act-time  { font-size: 11px; color: var(--db-muted); margin-top: 6px; }

        .db-panel-foot {
            padding: 14px 24px;
            background: var(--db-surface2);
            border-top: 1px solid var(--db-border);
        }
        .db-panel-foot button {
            font-size: 13px; font-weight: 600; color: #ea580c;
            display: inline-flex; align-items: center; gap: 6px;
            background: none; border: none; cursor: pointer; transition: opacity .15s;
        }
        .dark .db-panel-foot button { color: #fb923c; }
        .db-panel-foot button:hover { opacity: .7; }

        /* ── Section headings ── */
        .db-section-heading {
            font-size: 22px; font-weight: 700;
            color: var(--db-text);
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 24px;
        }

        /* ── Table styling ── */
        .db-table-container {
            background: var(--db-surface);
            border: 1px solid var(--db-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--db-shadow);
        }

        .db-table-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--db-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--db-surface2);
        }

        .db-table-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--db-text);
        }

        .db-table-link {
            font-size: 13px;
            font-weight: 600;
            color: #ea580c;
            text-decoration: none;
            transition: opacity 0.15s;
        }
        .dark .db-table-link { color: #fb923c; }
        .db-table-link:hover { opacity: 0.7; }

        .db-table {
            width: 100%;
            border-collapse: collapse;
        }

        .db-table thead {
            background: var(--db-surface2);
        }

        .db-table th {
            padding: 12px 24px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--db-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--db-border);
        }

        .db-table td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--db-border);
            color: var(--db-text);
            font-size: 14px;
        }

        .db-table tr:last-child td {
            border-bottom: none;
        }

        .db-table tbody tr {
            transition: background 0.15s;
        }

        .db-table tbody tr:hover {
            background: var(--db-surface2);
        }

        .db-table-secondary {
            color: var(--db-text-sec);
            font-size: 13px;
        }

        .db-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .db-badge.approved {
            background: #dcfce7;
            color: #166534;
        }
        .dark .db-badge.approved {
            background: rgba(5,150,105,.15);
            color: #34d399;
        }

        .db-badge.pending {
            background: #dbeafe;
            color: #1e40af;
        }
        .dark .db-badge.pending {
            background: rgba(37,99,235,.15);
            color: #60a5fa;
        }

        .db-badge.rejected {
            background: #fee2e2;
            color: #991b1b;
        }
        .dark .db-badge.rejected {
            background: rgba(239,68,68,.15);
            color: #fca5a5;
        }

        .db-badge.draft {
            background: #f1f5f9;
            color: #475569;
        }
        .dark .db-badge.draft {
            background: rgba(125,132,168,.15);
            color: #cbd5e1;
        }

        .db-empty-state {
            padding: 40px 24px;
            text-align: center;
        }

        .db-empty-message {
            color: var(--db-muted);
            font-size: 14px;
            margin-bottom: 12px;
        }

        .db-empty-action {
            color: #ea580c;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.15s;
        }
        .dark .db-empty-action { color: #fb923c; }
        .db-empty-action:hover { opacity: 0.7; }
    </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- ── Welcome Hero ── -->
            <div class="relative overflow-hidden shadow-xl sm:rounded-2xl
                        bg-gradient-to-r from-orange-500 via-orange-600 to-orange-500
                        dark:from-orange-950 dark:via-orange-900 dark:to-orange-950
                        dark:border dark:border-orange-800/40">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 dark:bg-white/5 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16 pointer-events-none"></div>
                <div class="absolute top-1/2 right-1/4 w-24 h-24 bg-orange-300/20 dark:bg-orange-600/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative px-6 py-12 sm:px-12 z-10">
                    <h1 class="text-3xl sm:text-4xl font-black text-white dark:text-orange-50 mb-2 tracking-tight">
                        Welcome back, {{ Auth::user()->name }}! 👋
                    </h1>
                    <p class="text-orange-100 dark:text-orange-300/80 text-lg font-medium">
                        Here's what's happening with your work requests today
                    </p>
                </div>
            </div>

            {{-- ── Flash Messages ── --}}
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- ── Work Request Stats Grid ── -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="db-stat-card">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="db-stat-label">Total Work Requests</p>
                            <p class="db-stat-value">{{ $workRequestStats['total'] }}</p>
                            <p class="db-stat-sub">All requests</p>
                        </div>
                        <div class="db-icon-tray blue"><i class="fas fa-file-contract"></i></div>
                    </div>
                    <div class="db-stat-foot blue">
                        <a href="{{ route('user.work-requests.index') }}">View All <i class="fas fa-arrow-right text-xs"></i></a>
                    </div>
                </div>

                <div class="db-stat-card">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="db-stat-label">Pending</p>
                            <p class="db-stat-value">{{ $workRequestStats['submitted'] }}</p>
                            <p class="db-stat-sub">Awaiting review</p>
                        </div>
                        <div class="db-icon-tray blue"><i class="fas fa-hourglass-half"></i></div>
                    </div>
                    <div class="db-stat-foot blue">
                        <a href="#">Browse <i class="fas fa-arrow-right text-xs"></i></a>
                    </div>
                </div>

                <div class="db-stat-card">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="db-stat-label">Approved</p>
                            <p class="db-stat-value">{{ $workRequestStats['approved'] }}</p>
                            <p class="db-stat-sub">Completed</p>
                        </div>
                        <div class="db-icon-tray green"><i class="fas fa-check-circle"></i></div>
                    </div>
                    <div class="db-stat-foot green">
                        <a href="#">View <i class="fas fa-arrow-right text-xs"></i></a>
                    </div>
                </div>

                <div class="db-stat-card">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="db-stat-label">Rejected</p>
                            <p class="db-stat-value">{{ $workRequestStats['rejected'] }}</p>
                            <p class="db-stat-sub">Need revision</p>
                        </div>
                        <div class="db-icon-tray purple"><i class="fas fa-exclamation-circle"></i></div>
                    </div>
                    <div class="db-stat-foot purple">
                        <a href="#">Review <i class="fas fa-arrow-right text-xs"></i></a>
                    </div>
                </div>

            </div>

            <!-- ── Concrete Pouring Stats Grid (if Employee) ── -->
            @if(Auth::user()->employee)
                <div>
                    <h2 class="db-section-heading">
                        <i class="fas fa-cement text-orange-500 dark:text-orange-400"></i>
                        Concrete Pouring Requests
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                        <div class="db-stat-card">
                            <div class="p-6 flex items-center justify-between">
                                <div>
                                    <p class="db-stat-label">Total Requests</p>
                                    <p class="db-stat-value">{{ $concretePouringStats['total'] }}</p>
                                    <p class="db-stat-sub">All requests</p>
                                </div>
                                <div class="db-icon-tray blue"><i class="fas fa-layer-group"></i></div>
                            </div>
                            <div class="db-stat-foot blue">
                                <a href="{{ route('user.concrete-pouring.index') }}">View All <i class="fas fa-arrow-right text-xs"></i></a>
                            </div>
                        </div>

                        <div class="db-stat-card">
                            <div class="p-6 flex items-center justify-between">
                                <div>
                                    <p class="db-stat-label">Pending</p>
                                    <p class="db-stat-value">{{ $concretePouringStats['pending'] }}</p>
                                    <p class="db-stat-sub">Awaiting approval</p>
                                </div>
                                <div class="db-icon-tray blue"><i class="fas fa-hourglass-half"></i></div>
                            </div>
                            <div class="db-stat-foot blue">
                                <a href="#">Browse <i class="fas fa-arrow-right text-xs"></i></a>
                            </div>
                        </div>

                        <div class="db-stat-card">
                            <div class="p-6 flex items-center justify-between">
                                <div>
                                    <p class="db-stat-label">Approved</p>
                                    <p class="db-stat-value">{{ $concretePouringStats['approved'] }}</p>
                                    <p class="db-stat-sub">Ready to proceed</p>
                                </div>
                                <div class="db-icon-tray green"><i class="fas fa-check-circle"></i></div>
                            </div>
                            <div class="db-stat-foot green">
                                <a href="#">View <i class="fas fa-arrow-right text-xs"></i></a>
                            </div>
                        </div>

                        <div class="db-stat-card">
                            <div class="p-6 flex items-center justify-between">
                                <div>
                                    <p class="db-stat-label">Disapproved</p>
                                    <p class="db-stat-value">{{ $concretePouringStats['disapproved'] }}</p>
                                    <p class="db-stat-sub">Need revision</p>
                                </div>
                                <div class="db-icon-tray purple"><i class="fas fa-exclamation-circle"></i></div>
                            </div>
                            <div class="db-stat-foot purple">
                                <a href="#">Review <i class="fas fa-arrow-right text-xs"></i></a>
                            </div>
                        </div>

                    </div>
                </div>
            @endif

            <!-- ── Quick Actions ── -->
            <div>
                <h2 class="db-section-heading">
                    <i class="fas fa-bolt text-orange-500 dark:text-orange-400"></i>
                    Quick Actions
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ Auth::user()->employee ? '4' : '3' }} gap-4">

                    <a href="{{ route('user.work-requests.create') }}" class="db-action-card orange">
                        <div class="db-action-overlay"></div>
                        <div class="db-action-icon orange"><i class="fas fa-file-circle-plus"></i></div>
                        <div class="db-action-title">New Work Request</div>
                        <div class="db-action-desc">Submit a new work request</div>
                    </a>

                    <a href="{{ route('user.work-requests.index') }}" class="db-action-card blue">
                        <div class="db-action-overlay"></div>
                        <div class="db-action-icon blue"><i class="fas fa-list-check"></i></div>
                        <div class="db-action-title">My Work Requests</div>
                        <div class="db-action-desc">View all your requests</div>
                    </a>

                    @if(Auth::user()->employee)
                        <a href="{{ route('user.concrete-pouring.create') }}" class="db-action-card green">
                            <div class="db-action-overlay"></div>
                            <div class="db-action-icon green"><i class="fas fa-plus-circle"></i></div>
                            <div class="db-action-title">New Pouring Request</div>
                            <div class="db-action-desc">Request concrete pouring</div>
                        </a>

                        <a href="{{ route('user.concrete-pouring.index') }}" class="db-action-card purple">
                            <div class="db-action-overlay"></div>
                            <div class="db-action-icon purple"><i class="fas fa-layer-group"></i></div>
                            <div class="db-action-title">My Pouring Requests</div>
                            <div class="db-action-desc">View pouring records</div>
                        </a>
                    @endif

                </div>
            </div>

            <!-- ── Recent Work Requests ── -->
            <div>
                <h2 class="db-section-heading">
                    <i class="fas fa-history text-orange-500 dark:text-orange-400"></i>
                    Recent Work Requests
                </h2>
                <div class="db-table-container">
                    <div class="db-table-header">
                        <div class="db-table-title">Latest Submissions</div>
                        <a href="{{ route('user.work-requests.index') }}" class="db-table-link">
                            View All <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>

                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Location</th>
                                <th>Start Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentWorkRequests as $workRequest)
                                <tr>
                                    <td>
                                        <p class="font-medium">{{ $workRequest->name_of_project }}</p>
                                        <p class="db-table-secondary">#{{ str_pad($workRequest->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    </td>
                                    <td>{{ $workRequest->project_location }}</td>
                                    <td>{{ $workRequest->requested_work_start_date?->format('M d, Y') ?? '—' }}</td>
                                    <td>
                                        <span class="db-badge {{ strtolower($workRequest->status) }}">
                                            {{ ucfirst($workRequest->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('user.work-requests.show', $workRequest) }}" class="db-table-link">
                                            View <i class="fas fa-arrow-right text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="db-empty-state">
                                            <p class="db-empty-message">No work requests yet.</p>
                                            <a href="{{ route('user.work-requests.create') }}" class="db-empty-action">
                                                Create your first one →
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── Recent Concrete Pourings ── -->
            @if(Auth::user()->employee)
                <div>
                    <h2 class="db-section-heading">
                        <i class="fas fa-cement text-orange-500 dark:text-orange-400"></i>
                        Recent Pouring Requests
                    </h2>

                    <div class="db-table-container">
                        <div class="db-table-header">
                            <div class="db-table-title">Latest Pouring Submissions</div>
                            <a href="{{ route('user.concrete-pouring.index') }}" class="db-table-link">
                                View All <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>

                        <table class="db-table">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Contractor</th>
                                    <th>Pouring Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentConcretePourings as $pouring)
                                    <tr>
                                        <td>
                                            <p class="font-medium">{{ $pouring->project_name }}</p>
                                            <p class="db-table-secondary">#{{ str_pad($pouring->id, 6, '0', STR_PAD_LEFT) }}</p>
                                        </td>
                                        <td>{{ $pouring->contractor }}</td>
                                        <td>{{ $pouring->pouring_datetime?->format('M d, Y') ?? '—' }}</td>
                                        <td>
                                            <span class="db-badge {{ strtolower($pouring->status) }}">
                                                {{ ucfirst($pouring->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('user.concrete-pouring.show', $pouring) }}" class="db-table-link">
                                                View <i class="fas fa-arrow-right text-xs"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="db-empty-state">
                                                <p class="db-empty-message">No concrete pouring requests yet.</p>
                                                <a href="{{ route('user.concrete-pouring.create') }}" class="db-empty-action">
                                                    Create your first one →
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>