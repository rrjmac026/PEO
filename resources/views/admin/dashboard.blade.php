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

        /* ── Footer divider & text ── */
        .db-footer-divider { border-top: 1px solid var(--db-border); }
        .db-footer-label   { font-size: 13px; font-weight: 500; color: var(--db-muted); margin-bottom: 4px; }
        .db-footer-value   { font-size: 14px; font-weight: 600; color: var(--db-text); }

        /* ── Section headings ── */
        .db-section-heading {
            font-size: 22px; font-weight: 700;
            color: var(--db-text);
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 24px;
        }
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
                        Here's what's happening with your system today
                    </p>
                </div>
            </div>

            <!-- ── Stats Grid ── -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <div class="db-stat-card">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="db-stat-label">Total Work Requests</p>
                            <p class="db-stat-value">{{ $totalRequests }}</p>
                            <p class="db-stat-sub">Active in system</p>
                        </div>
                        <div class="db-icon-tray blue"><i class="fas fa-file-contract"></i></div>
                    </div>
                    <div class="db-stat-foot blue">
                        <a href="{{ route('admin.work-requests.index') }}">View All <i class="fas fa-arrow-right text-xs"></i></a>
                    </div>
                </div>

                <div class="db-stat-card">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="db-stat-label">Total Employees</p>
                            <p class="db-stat-value">{{ $totalEmployees }}</p>
                            <p class="db-stat-sub">Registered</p>
                        </div>
                        <div class="db-icon-tray green"><i class="fas fa-users"></i></div>
                    </div>
                    <div class="db-stat-foot green">
                        <a href="#">Manage Employees <i class="fas fa-arrow-right text-xs"></i></a>
                    </div>
                </div>

                <div class="db-stat-card">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="db-stat-label">System Users</p>
                            <p class="db-stat-value">{{ $totalUsers }}</p>
                            <p class="db-stat-sub">Active accounts</p>
                        </div>
                        <div class="db-icon-tray purple"><i class="fas fa-user-shield"></i></div>
                    </div>
                    <div class="db-stat-foot purple">
                        <a href="{{ route('admin.users.index') }}">Manage Users <i class="fas fa-arrow-right text-xs"></i></a>
                    </div>
                </div>

            </div>

            <!-- ── Quick Actions ── -->
            <div>
                <h2 class="db-section-heading">
                    <i class="fas fa-bolt text-orange-500 dark:text-orange-400"></i>
                    Quick Actions
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    <a href="{{ route('admin.work-requests.create') }}" class="db-action-card orange">
                        <div class="db-action-overlay"></div>
                        <div class="db-action-icon orange"><i class="fas fa-file-medical"></i></div>
                        <div class="db-action-title">New Work Request</div>
                        <div class="db-action-desc">Create a new work request</div>
                    </a>

                    <a href="#" class="db-action-card green">
                        <div class="db-action-overlay"></div>
                        <div class="db-action-icon green"><i class="fas fa-upload"></i></div>
                        <div class="db-action-title">Import Employees</div>
                        <div class="db-action-desc">Upload employee data</div>
                    </a>

                    <a href="#" class="db-action-card blue">
                        <div class="db-action-overlay"></div>
                        <div class="db-action-icon blue"><i class="fas fa-chart-bar"></i></div>
                        <div class="db-action-title">View Reports</div>
                        <div class="db-action-desc">System analytics & reports</div>
                    </a>

                    <a href="#" class="db-action-card purple">
                        <div class="db-action-overlay"></div>
                        <div class="db-action-icon purple"><i class="fas fa-sliders-h"></i></div>
                        <div class="db-action-title">Settings</div>
                        <div class="db-action-desc">Configure system settings</div>
                    </a>

                </div>
            </div>

            <!-- ── Recent Activity ── -->
            <div>
                <h2 class="db-section-heading">
                    <i class="fas fa-history text-orange-500 dark:text-orange-400"></i>
                    Recent Activity
                </h2>

                <div class="db-panel">
                    <div class="db-activity-item">
                        <div class="flex items-start gap-4">
                            <div class="db-act-icon orange"><i class="fas fa-file-circle-plus"></i></div>
                            <div class="flex-1 min-w-0">
                                <p class="db-act-title">New system initialized</p>
                                <p class="db-act-desc">Admin dashboard activated for Provincial Engineering Office</p>
                                <p class="db-act-time">Today at {{ now()->format('g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="db-activity-item">
                        <div class="flex items-start gap-4">
                            <div class="db-act-icon green"><i class="fas fa-users"></i></div>
                            <div class="flex-1 min-w-0">
                                <p class="db-act-title">Employee module activated</p>
                                <p class="db-act-desc">Ready to manage employee records and information</p>
                                <p class="db-act-time">Today at {{ now()->format('g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="db-activity-item">
                        <div class="flex items-start gap-4">
                            <div class="db-act-icon blue"><i class="fas fa-check-circle"></i></div>
                            <div class="flex-1 min-w-0">
                                <p class="db-act-title">System configuration completed</p>
                                <p class="db-act-desc">All required modules have been configured and are ready for use</p>
                                <p class="db-act-time">Today at {{ now()->format('g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="db-panel-foot">
                        <button>View All Activity <i class="fas fa-arrow-right text-xs"></i></button>
                    </div>
                </div>
            </div>

            <!-- ── Footer Stats ── -->
            <div class="db-footer-divider pt-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <p class="db-footer-label">Last Updated</p>
                        <p class="db-footer-value">{{ now()->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="db-footer-label">System Status</p>
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-2 h-2 bg-green-500 dark:bg-green-400 rounded-full animate-pulse"></div>
                            <p class="db-footer-value">Operational</p>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="db-footer-label">Current Version</p>
                        <p class="db-footer-value">v{{ config('app.version', '1.0.0') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>