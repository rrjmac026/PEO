<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Approved Work Requests
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Ready to Print / Download
            </span>
        </div>
    </x-slot>

    @push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ap-bg:       #f1f5f9;
            --ap-surface:  #ffffff;
            --ap-surface2: #f8fafc;
            --ap-border:   #cbd5e1;
            --ap-text:     #0f172a;
            --ap-muted:    #64748b;
            --ap-radius:   12px;
            --ap-radius-sm:8px;
            --ap-shadow:   0 1px 4px rgba(0,0,0,0.06);
            --ap-green:    #059669;
            --ap-green-bg: rgba(5,150,105,0.08);
            --ap-green-bd: rgba(5,150,105,0.25);
        }
        .dark {
            --ap-bg:       #0f1117;
            --ap-surface:  #181c27;
            --ap-surface2: #1e2335;
            --ap-border:   #2a3050;
            --ap-text:     #e8eaf6;
            --ap-muted:    #7c85a8;
            --ap-green:    #34d399;
            --ap-green-bg: rgba(52,211,153,0.08);
            --ap-green-bd: rgba(52,211,153,0.25);
        }

        .ap-wrap { font-family: 'Inter', sans-serif; }

        /* ── Page Banner ── */
        .ap-banner {
            background: var(--ap-surface);
            border: 1px solid var(--ap-green-bd);
            border-left: 5px solid var(--ap-green);
            border-radius: var(--ap-radius);
            padding: 18px 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--ap-shadow);
        }
        .ap-banner-icon {
            width: 44px; height: 44px; border-radius: 10px;
            background: var(--ap-green-bg);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .ap-banner-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 15px; font-weight: 700; color: var(--ap-text);
            margin-bottom: 2px;
        }
        .ap-banner-sub { font-size: 13px; color: var(--ap-muted); }
        .ap-banner-count {
            margin-left: auto;
            background: var(--ap-green-bg);
            border: 1px solid var(--ap-green-bd);
            color: var(--ap-green);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 700;
            padding: 6px 16px; border-radius: 20px;
            white-space: nowrap;
        }

        /* ── Filter Card ── */
        .ap-filter {
            background: var(--ap-surface);
            border: 1px solid var(--ap-border);
            border-radius: var(--ap-radius);
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: var(--ap-shadow);
        }
        .ap-label {
            font-size: 12px; font-weight: 700; color: var(--ap-text);
            text-transform: uppercase; letter-spacing: 0.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: block; margin-bottom: 6px;
        }
        .ap-input {
            background: var(--ap-surface2); border: 1px solid var(--ap-border);
            color: var(--ap-text); border-radius: var(--ap-radius-sm);
            padding: 8px 12px; font-size: 13px; width: 100%;
            transition: border-color 0.2s;
        }
        .ap-input:focus { outline: none; border-color: var(--ap-green); }
        .ap-input::placeholder { color: var(--ap-muted); }
        .ap-btn {
            padding: 8px 18px; border-radius: var(--ap-radius-sm);
            font-size: 13px; font-weight: 600; border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;
        }
        .ap-btn-green { background: var(--ap-green); color: #fff; }
        .ap-btn-green:hover { opacity: 0.88; transform: translateY(-1px); }
        .ap-btn-ghost {
            background: var(--ap-surface2); color: var(--ap-text);
            border: 1px solid var(--ap-border);
        }
        .ap-btn-ghost:hover { background: var(--ap-border); }

        /* ── Alert ── */
        .ap-alert {
            padding: 12px 16px; border-radius: var(--ap-radius-sm);
            border-left: 4px solid var(--ap-green);
            background: var(--ap-green-bg); color: var(--ap-green);
            font-size: 13px; margin-bottom: 20px;
        }

        /* ── Table ── */
        .ap-table-wrap {
            background: var(--ap-surface);
            border: 1px solid var(--ap-border);
            border-radius: var(--ap-radius);
            overflow: hidden; box-shadow: var(--ap-shadow);
        }
        .ap-table { width: 100%; border-collapse: collapse; }
        .ap-thead { background: var(--ap-surface2); }
        .ap-th {
            padding: 14px 20px; text-align: left;
            font-size: 11px; font-weight: 700;
            color: var(--ap-muted); text-transform: uppercase; letter-spacing: 0.5px;
            border-bottom: 1px solid var(--ap-border);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .ap-td {
            padding: 16px 20px; border-bottom: 1px solid var(--ap-border);
            color: var(--ap-text); font-size: 13px;
            background: var(--ap-surface); transition: background 0.18s;
        }
        .ap-tr:hover .ap-td { background: var(--ap-surface2); }
        .ap-tr:last-child .ap-td { border-bottom: none; }

        .ap-project-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700; color: var(--ap-text); font-size: 14px; margin-bottom: 3px;
        }
        .ap-project-sub { font-size: 12px; color: var(--ap-muted); }

        .ap-badge-approved {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 20px;
            background: var(--ap-green-bg); color: var(--ap-green);
            border: 1px solid var(--ap-green-bd);
            font-size: 12px; font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .ap-badge-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--ap-green);
        }

        /* ── Action Buttons ── */
        .ap-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .ap-action-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: 7px;
            font-size: 12px; font-weight: 600; text-decoration: none;
            transition: all 0.18s; border: none; cursor: pointer;
        }
        .ap-action-view {
            background: rgba(37,99,235,0.1); color: #2563eb;
            border: 1px solid rgba(37,99,235,0.25);
        }
        .dark .ap-action-view { background: rgba(79,141,255,0.12); color: #60a5fa; border-color: rgba(96,165,250,0.25); }
        .ap-action-view:hover { background: rgba(37,99,235,0.18); }

        .ap-action-print {
            background: rgba(245,158,11,0.1); color: #d97706;
            border: 1px solid rgba(245,158,11,0.25);
        }
        .dark .ap-action-print { background: rgba(245,158,11,0.12); color: #fbbf24; border-color: rgba(245,158,11,0.25); }
        .ap-action-print:hover { background: rgba(245,158,11,0.18); }

        .ap-action-download {
            background: var(--ap-green-bg); color: var(--ap-green);
            border: 1px solid var(--ap-green-bd);
        }
        .ap-action-download:hover { opacity: 0.8; }

        /* ── Empty State ── */
        .ap-empty {
            text-align: center; padding: 56px 24px;
        }
        .ap-empty-icon {
            width: 64px; height: 64px; border-radius: 16px;
            background: var(--ap-green-bg);
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; margin: 0 auto 16px;
        }
        .ap-empty-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 16px; font-weight: 700; color: var(--ap-text); margin-bottom: 6px;
        }
        .ap-empty-sub { font-size: 13px; color: var(--ap-muted); }

        /* ── Pagination ── */
        .ap-pagination {
            padding: 14px 20px;
            border-top: 1px solid var(--ap-border);
            background: var(--ap-surface);
        }
    </style>
    @endpush

    <div class="py-8 ap-wrap">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash --}}
            @if(session('success'))
                <div class="ap-alert">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            {{-- Banner --}}
            <div class="ap-banner">
                <div class="ap-banner-icon">
                    <i class="fas fa-check-double" style="color: var(--ap-green);"></i>
                </div>
                <div>
                    <div class="ap-banner-title">Approved Work Requests</div>
                    <div class="ap-banner-sub">
                        All work requests approved by the Provincial Engineer — ready to print or download.
                    </div>
                </div>
                <div class="ap-banner-count">
                    {{ $workRequests->total() }} {{ Str::plural('request', $workRequests->total()) }}
                </div>
            </div>

            {{-- Filters --}}
            <div class="ap-filter">
                <form method="GET" action="{{ route('reviewer.work-requests.approved') }}"
                      class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[220px]">
                        <label class="ap-label">Search</label>
                        <input type="text" name="search"
                               value="{{ request('search') }}"
                               placeholder="Project name, location, contractor..."
                               class="ap-input" />
                    </div>
                    <div class="flex gap-2 items-end">
                        <button type="submit" class="ap-btn ap-btn-green">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('reviewer.work-requests.approved') }}"
                           class="ap-btn ap-btn-ghost">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="ap-table-wrap">
                <table class="ap-table">
                    <thead class="ap-thead">
                        <tr>
                            <th class="ap-th">Project</th>
                            <th class="ap-th">Location</th>
                            <th class="ap-th">Contractor</th>
                            <th class="ap-th">Approved By</th>
                            <th class="ap-th">Start Date</th>
                            <th class="ap-th">Status</th>
                            <th class="ap-th">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workRequests as $wr)
                            <tr class="ap-tr">
                                <td class="ap-td">
                                    <div class="ap-project-name">{{ $wr->name_of_project }}</div>
                                    <div class="ap-project-sub">#{{ str_pad($wr->id, 6, '0', STR_PAD_LEFT) }}</div>
                                    @if($wr->reference_number)
                                        <div class="ap-project-sub">Ref: {{ $wr->reference_number }}</div>
                                    @endif
                                </td>
                                <td class="ap-td">{{ $wr->project_location }}</td>
                                <td class="ap-td">{{ $wr->contractor_name ?? '—' }}</td>
                                <td class="ap-td">
                                    @if($wr->approved_by)
                                        <span style="font-weight:600; color:var(--ap-text);">
                                            {{ $wr->approved_by }}
                                        </span>
                                    @else
                                        <span style="color:var(--ap-muted); font-style:italic;">—</span>
                                    @endif
                                </td>
                                <td class="ap-td">
                                    {{ $wr->requested_work_start_date?->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="ap-td">
                                    <span class="ap-badge-approved">
                                        <span class="ap-badge-dot"></span>
                                        Approved
                                    </span>
                                </td>
                                <td class="ap-td">
                                    <div class="ap-actions">
                                        <a href="{{ route('reviewer.work-requests.show', $wr) }}"
                                           class="ap-action-btn ap-action-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('reviewer.work-requests.print', $wr) }}"
                                           target="_blank"
                                           class="ap-action-btn ap-action-print">
                                            <i class="fas fa-print"></i> Print
                                        </a>
                                        <a href="{{ route('reviewer.work-requests.download', $wr) }}"
                                           class="ap-action-btn ap-action-download">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="ap-td">
                                    <div class="ap-empty">
                                        <div class="ap-empty-icon">
                                            <i class="fas fa-clipboard-check" style="color:var(--ap-green);"></i>
                                        </div>
                                        <div class="ap-empty-title">No approved work requests found</div>
                                        <div class="ap-empty-sub">
                                            Approved requests from the Provincial Engineer will appear here.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($workRequests->hasPages())
                    <div class="ap-pagination">
                        {{ $workRequests->withQueryString()->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>