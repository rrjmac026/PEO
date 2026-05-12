@extends('layouts.app')

@section('title', 'Memos')

@push('styles')
<style>
    /* ══════════════════════════════════════════
       LIGHT MODE TOKENS
    ══════════════════════════════════════════ */
    :root {
        --mo-surface:   #ffffff;
        --mo-surface2:  #f8fafc;
        --mo-border:    #e2e8f0;
        --mo-text:      #0f172a;
        --mo-text-sec:  #334155;
        --mo-muted:     #64748b;
        --mo-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --mo-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
    }

    /* ══════════════════════════════════════════
       DARK MODE TOKENS
    ══════════════════════════════════════════ */
    .dark {
        --mo-surface:   #1a1f2e;
        --mo-surface2:  #1e2335;
        --mo-border:    #2a3050;
        --mo-text:      #e8eaf6;
        --mo-text-sec:  #c5cae9;
        --mo-muted:     #7c85a8;
        --mo-shadow:    0 1px 4px rgba(0,0,0,0.35);
        --mo-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
    }

    /* ── Page heading ── */
    .mo-page-title { font-size: 28px; font-weight: 800; color: var(--mo-text); line-height: 1.2; }
    .mo-page-sub   { font-size: 14px; color: var(--mo-muted); margin-top: 4px; }

    /* ── Alerts ── */
    .mo-alert {
        display: flex; align-items: flex-start; justify-content: space-between;
        padding: 12px 16px; border-radius: 10px; border: 1px solid;
        margin-bottom: 16px; font-size: 14px;
    }
    .mo-alert.success { background: #f0fdf4; border-color: #86efac; color: #166534; }
    .mo-alert.error   { background: #fff1f2; border-color: #fca5a5; color: #991b1b; }
    .dark .mo-alert.success { background: rgba(5,150,105,.12); border-color: rgba(52,211,153,.3); color: #6ee7b7; }
    .dark .mo-alert.error   { background: rgba(220,38,38,.10); border-color: rgba(248,113,113,.3); color: #fca5a5; }
    .mo-alert-close {
        background: none; border: none; cursor: pointer; font-size: 14px;
        opacity: .6; color: inherit; padding: 0; margin-left: 12px; flex-shrink: 0;
    }
    .mo-alert-close:hover { opacity: 1; }

    /* ── Stats grid ── */
    .mo-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }
    .mo-stat-card {
        background: var(--mo-surface);
        border: 1px solid var(--mo-border);
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: var(--mo-shadow);
        display: flex; align-items: center; gap: 14px;
    }
    .mo-stat-icon {
        width: 44px; height: 44px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }
    .mo-stat-icon.gray   { background: #f1f5f9; color: #475569; }
    .mo-stat-icon.green  { background: #f0fdf4; color: #166534; }
    .mo-stat-icon.blue   { background: #eff6ff; color: #1d4ed8; }
    .mo-stat-icon.yellow { background: #fefce8; color: #a16207; }
    .dark .mo-stat-icon.gray   { background: rgba(148,163,184,.1); color: #94a3b8; }
    .dark .mo-stat-icon.green  { background: rgba(74,222,128,.1);  color: #4ade80; }
    .dark .mo-stat-icon.blue   { background: rgba(96,165,250,.1);  color: #60a5fa; }
    .dark .mo-stat-icon.yellow { background: rgba(250,204,21,.1);  color: #facc15; }
    .mo-stat-label { font-size: 11px; color: var(--mo-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }
    .mo-stat-value { font-size: 26px; font-weight: 800; color: var(--mo-text); line-height: 1.1; margin-top: 2px; }

    /* ── Filter panel ── */
    .mo-filter-panel {
        background: var(--mo-surface);
        border: 1px solid var(--mo-border);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: var(--mo-shadow);
    }
    .mo-input {
        flex: 1; min-width: 200px;
        background: var(--mo-surface);
        border: 1px solid var(--mo-border);
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 14px; color: var(--mo-text);
        box-shadow: var(--mo-shadow);
        transition: border-color .15s, box-shadow .15s; outline: none;
    }
    .mo-input::placeholder { color: var(--mo-muted); }
    .mo-input:focus { border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,.12); }
    .dark .mo-input:focus { border-color: #fb923c; box-shadow: 0 0 0 3px rgba(251,146,60,.12); }
    .mo-select {
        background: var(--mo-surface);
        border: 1px solid var(--mo-border);
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 14px; color: var(--mo-text);
        box-shadow: var(--mo-shadow);
        outline: none; min-width: 140px; cursor: pointer;
        transition: border-color .15s;
    }
    .mo-select:focus { border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,.12); }
    .dark .mo-select { background: var(--mo-surface2); color: var(--mo-text); }

    /* ── Buttons ── */
    .mo-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px;
        font-size: 13px; font-weight: 600;
        border: 1px solid; cursor: pointer;
        transition: all .15s; text-decoration: none; white-space: nowrap;
    }
    .mo-btn-orange { background: #ea580c; border-color: #ea580c; color: #fff; }
    .mo-btn-orange:hover { background: #c2410c; border-color: #c2410c; color: #fff; }
    .dark .mo-btn-orange { background: #f97316; border-color: #f97316; }
    .dark .mo-btn-orange:hover { background: #fb923c; border-color: #fb923c; }
    .mo-btn-dark { background: #1e293b; border-color: #1e293b; color: #fff; }
    .mo-btn-dark:hover { background: #334155; border-color: #334155; }
    .dark .mo-btn-dark { background: #e2e8f0; border-color: #e2e8f0; color: #0f172a; }
    .mo-btn-secondary { background: var(--mo-surface2); border-color: var(--mo-border); color: var(--mo-text-sec); }
    .mo-btn-secondary:hover { border-color: var(--mo-muted); }

    /* ── Panel / Table ── */
    .mo-panel {
        background: var(--mo-surface);
        border: 1px solid var(--mo-border);
        border-radius: 12px; overflow: hidden;
        box-shadow: var(--mo-shadow);
    }
    .mo-table { width: 100%; border-collapse: collapse; }
    .mo-table thead tr { background: var(--mo-surface2); border-bottom: 1px solid var(--mo-border); }
    .mo-table thead th {
        padding: 11px 20px; text-align: left;
        font-size: 11px; font-weight: 700; color: var(--mo-muted);
        text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;
    }
    .mo-table thead th.right { text-align: right; }
    .mo-table tbody tr { border-bottom: 1px solid var(--mo-border); transition: background .12s; }
    .mo-table tbody tr:last-child { border-bottom: none; }
    .mo-table tbody tr:hover { background: var(--mo-surface2); }
    .mo-table td { padding: 13px 20px; font-size: 14px; color: var(--mo-text); white-space: nowrap; }
    .mo-table td.muted { color: var(--mo-muted); }
    .mo-table td.right { text-align: right; }

    /* ── Ref chip ── */
    .mo-ref-chip {
        font-family: monospace; font-size: 11px; font-weight: 700;
        color: var(--mo-muted); background: var(--mo-surface2);
        border: 1px solid var(--mo-border);
        padding: 2px 8px; border-radius: 6px;
    }

    /* ── Type badge ── */
    .mo-type-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 9px; border-radius: 20px;
        font-size: 11px; font-weight: 600; border: 1px solid;
    }
    .mo-type-blue   { color: #1d4ed8; border-color: #93c5fd; background: #eff6ff; }
    .mo-type-pink   { color: #9d174d; border-color: #f9a8d4; background: #fdf2f8; }
    .mo-type-green  { color: #166534; border-color: #86efac; background: #f0fdf4; }
    .mo-type-orange { color: #9a3412; border-color: #fdba74; background: #fff7ed; }
    .mo-type-purple { color: #6d28d9; border-color: #c4b5fd; background: #f5f3ff; }
    .mo-type-yellow { color: #92400e; border-color: #fcd34d; background: #fffbeb; }
    .mo-type-gray   { color: #475569; border-color: #cbd5e1; background: #f1f5f9; }
    .dark .mo-type-blue   { color: #60a5fa; border-color: rgba(96,165,250,.3);   background: rgba(96,165,250,.1); }
    .dark .mo-type-pink   { color: #f472b6; border-color: rgba(244,114,182,.3);  background: rgba(244,114,182,.1); }
    .dark .mo-type-green  { color: #4ade80; border-color: rgba(74,222,128,.3);   background: rgba(74,222,128,.1); }
    .dark .mo-type-orange { color: #fb923c; border-color: rgba(251,146,60,.3);   background: rgba(251,146,60,.1); }
    .dark .mo-type-purple { color: #a78bfa; border-color: rgba(167,139,250,.3);  background: rgba(167,139,250,.1); }
    .dark .mo-type-yellow { color: #fbbf24; border-color: rgba(251,191,36,.3);   background: rgba(251,191,36,.1); }
    .dark .mo-type-gray   { color: #94a3b8; border-color: rgba(148,163,184,.3);  background: rgba(148,163,184,.1); }

    /* ── Status badge ── */
    .mo-status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 600; border: 1px solid;
    }
    .mo-status-sent      { color: #166534; border-color: #86efac; background: #f0fdf4; }
    .mo-status-draft     { color: #475569; border-color: #cbd5e1; background: #f1f5f9; }
    .mo-status-scheduled { color: #1d4ed8; border-color: #93c5fd; background: #eff6ff; }
    .mo-status-cancelled { color: #991b1b; border-color: #fca5a5; background: #fff1f2; }
    .dark .mo-status-sent      { color: #4ade80; border-color: rgba(74,222,128,.3);   background: rgba(74,222,128,.1); }
    .dark .mo-status-draft     { color: #94a3b8; border-color: rgba(148,163,184,.3);  background: rgba(148,163,184,.1); }
    .dark .mo-status-scheduled { color: #60a5fa; border-color: rgba(96,165,250,.3);   background: rgba(96,165,250,.1); }
    .dark .mo-status-cancelled { color: #f87171; border-color: rgba(248,113,113,.3);  background: rgba(248,113,113,.1); }

    /* ── Action icon buttons ── */
    .mo-action-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 7px;
        font-size: 13px; border: 1px solid; cursor: pointer;
        transition: all .15s; text-decoration: none; background: none;
    }
    .mo-action-btn.view   { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }
    .mo-action-btn.edit   { color: #b45309; border-color: #fde68a; background: #fffbeb; }
    .mo-action-btn.send   { color: #166534; border-color: #86efac; background: #f0fdf4; }
    .mo-action-btn.delete { color: #dc2626; border-color: #fca5a5; background: #fff1f2; }
    .mo-action-btn.view:hover   { background: #dbeafe; border-color: #93c5fd; }
    .mo-action-btn.edit:hover   { background: #fef3c7; border-color: #fcd34d; }
    .mo-action-btn.send:hover   { background: #dcfce7; border-color: #4ade80; }
    .mo-action-btn.delete:hover { background: #fee2e2; border-color: #f87171; }
    .dark .mo-action-btn.view   { color: #60a5fa; border-color: rgba(96,165,250,.3);   background: rgba(96,165,250,.1); }
    .dark .mo-action-btn.edit   { color: #fbbf24; border-color: rgba(251,191,36,.3);   background: rgba(251,191,36,.1); }
    .dark .mo-action-btn.send   { color: #4ade80; border-color: rgba(74,222,128,.3);   background: rgba(74,222,128,.1); }
    .dark .mo-action-btn.delete { color: #f87171; border-color: rgba(248,113,113,.3);  background: rgba(248,113,113,.1); }
    .dark .mo-action-btn.view:hover   { background: rgba(96,165,250,.2);  border-color: rgba(96,165,250,.5); }
    .dark .mo-action-btn.edit:hover   { background: rgba(251,191,36,.2);  border-color: rgba(251,191,36,.5); }
    .dark .mo-action-btn.send:hover   { background: rgba(74,222,128,.2);  border-color: rgba(74,222,128,.5); }
    .dark .mo-action-btn.delete:hover { background: rgba(248,113,113,.2); border-color: rgba(248,113,113,.5); }

    /* ── Empty ── */
    .mo-empty { padding: 56px 24px; text-align: center; }
    .mo-empty i { font-size: 36px; color: var(--mo-muted); opacity: .4; display: block; margin-bottom: 14px; }
    .mo-empty-title { font-size: 16px; font-weight: 600; color: var(--mo-text-sec); margin-bottom: 8px; }
    .mo-empty-sub   { font-size: 14px; color: var(--mo-muted); margin-bottom: 20px; }

    /* ── Pagination ── */
    .mo-pagination { padding: 16px 24px; border-top: 1px solid var(--mo-border); }

    /* ── Read rate bar ── */
    .mo-read-bar {
        display: flex; align-items: center; gap: 6px; min-width: 90px;
    }
    .mo-read-bar-track {
        flex: 1; height: 5px; border-radius: 3px; background: var(--mo-border); overflow: hidden;
    }
    .mo-read-bar-fill { height: 100%; border-radius: 3px; background: #22c55e; transition: width .3s; }
    .mo-read-bar span { font-size: 11px; color: var(--mo-muted); white-space: nowrap; }
</style>
@endpush

@section('content')

    <!-- ── Page Header ── -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="mo-page-title">Memos</h1>
                <p class="mo-page-sub">Compose and manage internal communications</p>
            </div>
            <a href="{{ route('admin.memos.create') }}" class="mo-btn mo-btn-orange">
                <i class="fas fa-plus-circle"></i> New Memo
            </a>
        </div>
    </div>

    <!-- ── Alerts ── -->
    @if (session('success'))
        <div class="mo-alert success">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button class="mo-alert-close" onclick="this.closest('.mo-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if (session('error'))
        <div class="mo-alert error">
            <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
            <button class="mo-alert-close" onclick="this.closest('.mo-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- ── Stats ── -->
    <div class="mo-stats-grid">
        <div class="mo-stat-card">
            <div class="mo-stat-icon gray"><i class="fas fa-envelope"></i></div>
            <div>
                <div class="mo-stat-label">Total</div>
                <div class="mo-stat-value">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="mo-stat-card">
            <div class="mo-stat-icon green"><i class="fas fa-paper-plane"></i></div>
            <div>
                <div class="mo-stat-label">Sent</div>
                <div class="mo-stat-value">{{ $stats['sent'] }}</div>
            </div>
        </div>
        <div class="mo-stat-card">
            <div class="mo-stat-icon gray"><i class="fas fa-file-alt"></i></div>
            <div>
                <div class="mo-stat-label">Draft</div>
                <div class="mo-stat-value">{{ $stats['draft'] }}</div>
            </div>
        </div>
        <div class="mo-stat-card">
            <div class="mo-stat-icon blue"><i class="fas fa-clock"></i></div>
            <div>
                <div class="mo-stat-label">Scheduled</div>
                <div class="mo-stat-value">{{ $stats['scheduled'] }}</div>
            </div>
        </div>
    </div>

    <!-- ── Filter Bar ── -->
    <div class="mo-filter-panel">
        <form method="GET" action="{{ route('admin.memos.index') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by subject, body, reference, or sender…"
                   class="mo-input">

            <select name="type" class="mo-select">
                <option value="">All Types</option>
                @foreach ($types as $key => $label)
                    <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="mo-select">
                <option value="">All Statuses</option>
                <option value="draft"     {{ request('status') === 'draft'     ? 'selected' : '' }}>Draft</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="sent"      {{ request('status') === 'sent'      ? 'selected' : '' }}>Sent</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <button type="submit" class="mo-btn mo-btn-dark">
                <i class="fas fa-search"></i> Filter
            </button>

            @if (request('search') || request('type') || request('status'))
                <a href="{{ route('admin.memos.index') }}" class="mo-btn mo-btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- ── Table ── -->
    <div class="mo-panel">
        @if ($memos->count() > 0)
            <div class="overflow-x-auto">
                <table class="mo-table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Sent By</th>
                            <th>Recipients</th>
                            <th>Read Rate</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($memos as $memo)
                            @php
                                $typeColorMap = [
                                    'announcement'       => 'blue',
                                    'birthday'           => 'pink',
                                    'holiday_greeting'   => 'green',
                                    'policy_update'      => 'orange',
                                    'event_invitation'   => 'purple',
                                    'performance_notice' => 'yellow',
                                    'general'            => 'gray',
                                ];
                                $typeColor = $typeColorMap[$memo->type] ?? 'gray';
                            @endphp
                            <tr>
                                <!-- Reference -->
                                <td>
                                    <span class="mo-ref-chip">{{ $memo->reference_number }}</span>
                                </td>

                                <!-- Type -->
                                <td>
                                    <span class="mo-type-badge mo-type-{{ $typeColor }}">
                                        <i class="fas {{ $memo->type_icon }} text-xs"></i>
                                        {{ $memo->type_label }}
                                    </span>
                                </td>

                                <!-- Subject -->
                                <td style="color: var(--mo-text-sec); font-weight: 600; max-width: 220px; overflow: hidden; text-overflow: ellipsis;">
                                    {{ Str::limit($memo->subject, 45) }}
                                </td>

                                <!-- Sent By -->
                                <td class="muted">{{ $memo->sender->name ?? '—' }}</td>

                                <!-- Recipients -->
                                <td class="muted" style="text-align: center;">
                                    {{ $memo->memo_recipients_count ?? 0 }}
                                </td>

                                <!-- Read Rate -->
                                <td>
                                    @if ($memo->status === 'sent' && ($memo->memo_recipients_count ?? 0) > 0)
                                        @php $rate = $memo->read_rate; @endphp
                                        <div class="mo-read-bar">
                                            <div class="mo-read-bar-track">
                                                <div class="mo-read-bar-fill" style="width: {{ $rate }}%"></div>
                                            </div>
                                            <span>{{ $rate }}%</span>
                                        </div>
                                    @else
                                        <span class="muted" style="font-size:12px; color: var(--mo-muted);">—</span>
                                    @endif
                                </td>

                                <!-- Date -->
                                <td class="muted">
                                    @if ($memo->status === 'sent' && $memo->sent_at)
                                        {{ $memo->sent_at->format('M d, Y') }}
                                    @elseif ($memo->status === 'scheduled' && $memo->scheduled_at)
                                        <span title="Scheduled">
                                            <i class="fas fa-clock mr-1"></i>{{ $memo->scheduled_at->format('M d, Y') }}
                                        </span>
                                    @else
                                        {{ $memo->created_at->format('M d, Y') }}
                                    @endif
                                </td>

                                <!-- Status -->
                                <td>
                                    <span class="mo-status-badge mo-status-{{ $memo->status }}">
                                        {{ ucfirst($memo->status) }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- View --}}
                                        <a href="{{ route('admin.memos.show', $memo) }}"
                                           class="mo-action-btn view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Edit (draft/scheduled only) --}}
                                        @if ($memo->status !== 'sent' && $memo->status !== 'cancelled')
                                            <a href="{{ route('admin.memos.edit', $memo) }}"
                                               class="mo-action-btn edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        {{-- Send Now (draft/scheduled only) --}}
                                        @if (in_array($memo->status, ['draft', 'scheduled']))
                                            <form action="{{ route('admin.memos.send-now', $memo) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Send this memo to all recipients now?')">
                                                @csrf
                                                <button type="submit" class="mo-action-btn send" title="Send Now">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Delete (non-sent) --}}
                                        @if ($memo->status !== 'sent')
                                            <form action="{{ route('admin.memos.destroy', $memo) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Delete this memo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="mo-action-btn delete" title="Delete">
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

            @if ($memos->hasPages())
                <div class="mo-pagination">
                    {{ $memos->links() }}
                </div>
            @endif

        @else
            <div class="mo-empty">
                <i class="fas fa-envelope-open"></i>
                <div class="mo-empty-title">No memos found</div>
                <div class="mo-empty-sub">Compose your first memo to get started</div>
                <a href="{{ route('admin.memos.create') }}" class="mo-btn mo-btn-orange">
                    <i class="fas fa-plus-circle"></i> New Memo
                </a>
            </div>
        @endif
    </div>

@endsection