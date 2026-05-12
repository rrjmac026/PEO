@extends('layouts.app')

@section('title', 'Memo: ' . $memo->subject)

@push('styles')
<style>
    :root {
        --mo-surface:   #ffffff;
        --mo-surface2:  #f8fafc;
        --mo-border:    #e2e8f0;
        --mo-text:      #0f172a;
        --mo-text-sec:  #334155;
        --mo-muted:     #64748b;
        --mo-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
    }
    .dark {
        --mo-surface:   #1a1f2e;
        --mo-surface2:  #1e2335;
        --mo-border:    #2a3050;
        --mo-text:      #e8eaf6;
        --mo-text-sec:  #c5cae9;
        --mo-muted:     #7c85a8;
        --mo-shadow:    0 1px 4px rgba(0,0,0,0.35);
    }

    .mo-page-title { font-size: 24px; font-weight: 800; color: var(--mo-text); line-height: 1.2; }
    .mo-page-sub   { font-size: 14px; color: var(--mo-muted); margin-top: 4px; }

    /* Breadcrumb */
    .mo-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--mo-muted); margin-bottom: 20px; }
    .mo-breadcrumb a { color: var(--mo-muted); text-decoration: none; }
    .mo-breadcrumb a:hover { color: #ea580c; }
    .mo-breadcrumb .sep { opacity: .4; }

    /* Card */
    .mo-card {
        background: var(--mo-surface);
        border: 1px solid var(--mo-border);
        border-radius: 14px;
        box-shadow: var(--mo-shadow);
        overflow: hidden;
    }
    .mo-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--mo-border);
        background: var(--mo-surface2);
        display: flex; align-items: center; gap: 12px;
    }
    .mo-card-header-icon {
        width: 38px; height: 38px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; background: rgba(234,88,12,.12); color: #ea580c;
        flex-shrink: 0;
    }
    .dark .mo-card-header-icon { background: rgba(251,146,60,.15); color: #fb923c; }
    .mo-card-title    { font-size: 15px; font-weight: 700; color: var(--mo-text); }
    .mo-card-subtitle { font-size: 12px; color: var(--mo-muted); margin-top: 1px; }
    .mo-card-body { padding: 24px; }

    /* Memo body display */
    .mo-memo-body {
        font-size: 15px;
        line-height: 1.8;
        color: var(--mo-text-sec);
        white-space: pre-wrap;
        word-break: break-word;
    }

    /* Meta grid */
    .mo-meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 16px;
    }
    .mo-meta-item { display: flex; flex-direction: column; gap: 4px; }
    .mo-meta-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--mo-muted); }
    .mo-meta-value { font-size: 14px; font-weight: 600; color: var(--mo-text-sec); }

    /* Status badge */
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

    /* Type badge */
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

    /* Ref chip */
    .mo-ref-chip {
        font-family: monospace; font-size: 12px; font-weight: 700;
        color: var(--mo-muted); background: var(--mo-surface2);
        border: 1px solid var(--mo-border);
        padding: 3px 10px; border-radius: 6px;
    }

    /* Buttons */
    .mo-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 9px;
        font-size: 13px; font-weight: 600; border: 1px solid; cursor: pointer;
        transition: all .15s; text-decoration: none; white-space: nowrap;
        font-family: inherit;
    }
    .mo-btn-orange   { background: #ea580c; border-color: #ea580c; color: #fff; }
    .mo-btn-orange:hover { background: #c2410c; border-color: #c2410c; color: #fff; }
    .dark .mo-btn-orange { background: #f97316; border-color: #f97316; }
    .mo-btn-blue     { background: #2563eb; border-color: #2563eb; color: #fff; }
    .mo-btn-blue:hover { background: #1d4ed8; border-color: #1d4ed8; }
    .mo-btn-green    { background: #16a34a; border-color: #16a34a; color: #fff; }
    .mo-btn-green:hover { background: #15803d; border-color: #15803d; }
    .mo-btn-red      { background: #dc2626; border-color: #dc2626; color: #fff; }
    .mo-btn-red:hover { background: #b91c1c; border-color: #b91c1c; }
    .mo-btn-secondary { background: var(--mo-surface2); border-color: var(--mo-border); color: var(--mo-text-sec); }
    .mo-btn-secondary:hover { border-color: var(--mo-muted); }
    .mo-btn-yellow   { background: #d97706; border-color: #d97706; color: #fff; }
    .mo-btn-yellow:hover { background: #b45309; }

    /* Alerts */
    .mo-alert {
        display: flex; align-items: flex-start; justify-content: space-between;
        padding: 12px 16px; border-radius: 10px; border: 1px solid;
        margin-bottom: 16px; font-size: 14px;
    }
    .mo-alert.success { background: #f0fdf4; border-color: #86efac; color: #166534; }
    .mo-alert.error   { background: #fff1f2; border-color: #fca5a5; color: #991b1b; }
    .dark .mo-alert.success { background: rgba(5,150,105,.12); border-color: rgba(52,211,153,.3); color: #6ee7b7; }
    .dark .mo-alert.error   { background: rgba(220,38,38,.10); border-color: rgba(248,113,113,.3); color: #fca5a5; }
    .mo-alert-close { background: none; border: none; cursor: pointer; font-size: 14px; opacity: .6; color: inherit; padding: 0; margin-left: 12px; }
    .mo-alert-close:hover { opacity: 1; }

    /* Recipient table */
    .mo-table { width: 100%; border-collapse: collapse; }
    .mo-table thead tr { background: var(--mo-surface2); border-bottom: 1px solid var(--mo-border); }
    .mo-table thead th {
        padding: 10px 16px; text-align: left;
        font-size: 11px; font-weight: 700; color: var(--mo-muted);
        text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;
    }
    .mo-table tbody tr { border-bottom: 1px solid var(--mo-border); transition: background .12s; }
    .mo-table tbody tr:last-child { border-bottom: none; }
    .mo-table tbody tr:hover { background: var(--mo-surface2); }
    .mo-table td { padding: 11px 16px; font-size: 13px; color: var(--mo-text); }
    .mo-table td.muted { color: var(--mo-muted); }

    /* Read dot */
    .mo-dot {
        display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 6px;
    }
    .mo-dot.read   { background: #22c55e; }
    .mo-dot.unread { background: #e2e8f0; border: 1px solid #cbd5e1; }
    .dark .mo-dot.unread { background: #2a3050; border-color: #3a4060; }

    /* Attachment chip */
    .mo-attach-chip {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 14px; border-radius: 8px;
        background: var(--mo-surface2); border: 1px solid var(--mo-border);
        font-size: 13px; color: var(--mo-text-sec); text-decoration: none;
        transition: border-color .12s;
    }
    .mo-attach-chip:hover { border-color: #ea580c; color: #ea580c; }

    /* Read rate bar */
    .mo-read-bar { display: flex; align-items: center; gap: 8px; }
    .mo-read-bar-track { flex: 1; height: 6px; border-radius: 3px; background: var(--mo-border); overflow: hidden; }
    .mo-read-bar-fill  { height: 100%; border-radius: 3px; background: #22c55e; }
    .mo-read-bar span  { font-size: 12px; font-weight: 600; color: var(--mo-text-sec); min-width: 36px; }

    /* Scope badge */
    .mo-scope-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 600;
        background: var(--mo-surface2); border: 1px solid var(--mo-border);
        color: var(--mo-text-sec);
    }

    /* Action bar */
    .mo-action-bar {
        padding: 18px 24px;
        border-top: 1px solid var(--mo-border);
        background: var(--mo-surface2);
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        flex-wrap: wrap;
    }

    /* Divider */
    .mo-divider { height: 1px; background: var(--mo-border); margin: 20px 0; }
</style>
@endpush

@section('content')

    <!-- Breadcrumb -->
    <div class="mo-breadcrumb">
        <a href="{{ route('admin.memos.index') }}"><i class="fas fa-envelope mr-1"></i>Memos</a>
        <span class="sep">/</span>
        <span>{{ Str::limit($memo->subject, 50) }}</span>
    </div>

    <!-- Alerts -->
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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- ── LEFT: Memo Content ── -->
        <div class="xl:col-span-2 space-y-6">

            <!-- Main memo card -->
            <div class="mo-card">
                <div class="mo-card-header" style="justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <div style="display:flex; align-items:center; gap:12px;">
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
                        <div class="mo-card-header-icon"><i class="fas {{ $memo->type_icon }}"></i></div>
                        <div>
                            <div class="mo-card-title">{{ $memo->subject }}</div>
                            <div class="mo-card-subtitle" style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                                <span class="mo-ref-chip">{{ $memo->reference_number }}</span>
                                <span class="mo-type-badge mo-type-{{ $typeColor }}">
                                    <i class="fas {{ $memo->type_icon }} text-xs"></i>
                                    {{ $memo->type_label }}
                                </span>
                                <span class="mo-status-badge mo-status-{{ $memo->status }}">
                                    {{ ucfirst($memo->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mo-card-body">
                    <!-- Meta info -->
                    <div class="mo-meta-grid" style="margin-bottom: 24px;">
                        <div class="mo-meta-item">
                            <span class="mo-meta-label">Sent By</span>
                            <span class="mo-meta-value">
                                <i class="fas fa-user mr-1" style="color:var(--mo-muted);font-size:11px;"></i>
                                {{ $memo->sender->name ?? '—' }}
                            </span>
                        </div>
                        <div class="mo-meta-item">
                            <span class="mo-meta-label">Recipient Scope</span>
                            <span class="mo-meta-value">
                                @php
                                    $scopeIcons = ['all' => 'fa-globe', 'by_role' => 'fa-user-tag', 'by_department' => 'fa-building', 'specific' => 'fa-user-check'];
                                    $scopeLabels = ['all' => 'All Users', 'by_role' => 'By Role', 'by_department' => 'By Department', 'specific' => 'Specific Users'];
                                @endphp
                                <i class="fas {{ $scopeIcons[$memo->recipient_scope] ?? 'fa-users' }} mr-1" style="color:var(--mo-muted);font-size:11px;"></i>
                                {{ $scopeLabels[$memo->recipient_scope] ?? ucfirst($memo->recipient_scope) }}
                            </span>
                        </div>
                        <div class="mo-meta-item">
                            <span class="mo-meta-label">
                                @if ($memo->status === 'sent') Sent At
                                @elseif ($memo->status === 'scheduled') Scheduled For
                                @else Created At
                                @endif
                            </span>
                            <span class="mo-meta-value">
                                @if ($memo->status === 'sent' && $memo->sent_at)
                                    {{ $memo->sent_at->format('M d, Y g:i A') }}
                                @elseif ($memo->status === 'scheduled' && $memo->scheduled_at)
                                    {{ $memo->scheduled_at->format('M d, Y g:i A') }}
                                @else
                                    {{ $memo->created_at->format('M d, Y g:i A') }}
                                @endif
                            </span>
                        </div>
                        <div class="mo-meta-item">
                            <span class="mo-meta-label">Recipients</span>
                            <span class="mo-meta-value">
                                <i class="fas fa-users mr-1" style="color:var(--mo-muted);font-size:11px;"></i>
                                {{ $memo->memoRecipients->count() }} user(s)
                            </span>
                        </div>
                        @if ($memo->target_roles)
                            <div class="mo-meta-item">
                                <span class="mo-meta-label">Target Roles</span>
                                <span class="mo-meta-value" style="font-size:13px;">
                                    {{ implode(', ', array_map(fn($r) => ucwords(str_replace('_',' ',$r)), $memo->target_roles)) }}
                                </span>
                            </div>
                        @endif
                        @if ($memo->target_departments)
                            <div class="mo-meta-item">
                                <span class="mo-meta-label">Target Departments</span>
                                <span class="mo-meta-value" style="font-size:13px;">
                                    {{ implode(', ', $memo->target_departments) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="mo-divider"></div>

                    <!-- Body -->
                    <div>
                        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--mo-muted);margin-bottom:12px;">
                            Message
                        </p>
                        <div class="mo-memo-body">{{ $memo->body }}</div>
                    </div>

                    <!-- Attachments -->
                    @if ($memo->attachments && count($memo->attachments))
                        <div class="mo-divider"></div>
                        <div>
                            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--mo-muted);margin-bottom:12px;">
                                <i class="fas fa-paperclip mr-1"></i> Attachments ({{ count($memo->attachments) }})
                            </p>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                @foreach ($memo->attachments as $attachment)
                                    <a href="{{ Storage::url($attachment) }}"
                                       target="_blank"
                                       class="mo-attach-chip">
                                        <i class="fas fa-file"></i>
                                        {{ basename($attachment) }}
                                        <i class="fas fa-external-link-alt" style="font-size:10px;opacity:.5;"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Action bar -->
                <div class="mo-action-bar">
                    <a href="{{ route('admin.memos.index') }}" class="mo-btn mo-btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <div class="flex gap-3 flex-wrap">
                        @if ($memo->status !== 'sent' && $memo->status !== 'cancelled')
                            <a href="{{ route('admin.memos.edit', $memo) }}" class="mo-btn mo-btn-yellow">
                                <i class="fas fa-edit"></i> Edit Memo
                            </a>
                        @endif

                        @if (in_array($memo->status, ['draft', 'scheduled']))
                            <form action="{{ route('admin.memos.send-now', $memo) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Send this memo to all {{ $memo->memoRecipients->count() }} recipient(s) now?')">
                                @csrf
                                <button type="submit" class="mo-btn mo-btn-green">
                                    <i class="fas fa-paper-plane"></i> Send Now
                                </button>
                            </form>
                        @endif

                        @if ($memo->status === 'scheduled')
                            <form action="{{ route('admin.memos.cancel', $memo) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Cancel this scheduled memo?')">
                                @csrf
                                <button type="submit" class="mo-btn mo-btn-secondary">
                                    <i class="fas fa-ban"></i> Cancel Schedule
                                </button>
                            </form>
                        @endif

                        @if ($memo->status !== 'sent')
                            <form action="{{ route('admin.memos.destroy', $memo) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Permanently delete this memo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="mo-btn mo-btn-red">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- ── RIGHT: Stats & Recipients ── -->
        <div class="space-y-6">

            <!-- Read Rate -->
            @if ($memo->status === 'sent')
                <div class="mo-card">
                    <div class="mo-card-header">
                        <div class="mo-card-header-icon"><i class="fas fa-chart-pie"></i></div>
                        <div>
                            <div class="mo-card-title">Read Statistics</div>
                            <div class="mo-card-subtitle">Recipient engagement</div>
                        </div>
                    </div>
                    <div class="mo-card-body">
                        @php
                            $total    = $memo->memoRecipients->count();
                            $read     = $memo->memoRecipients->whereNotNull('read_at')->count();
                            $unread   = $total - $read;
                            $rate     = $total > 0 ? round(($read / $total) * 100) : 0;
                            $emailOk  = $memo->memoRecipients->whereNotNull('email_sent_at')->count();
                            $emailFail= $memo->memoRecipients->where('email_failed', true)->count();
                        @endphp
                        <div style="margin-bottom:16px;">
                            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                                <span style="font-size:13px;color:var(--mo-text-sec);font-weight:600;">Read Rate</span>
                                <span style="font-size:20px;font-weight:800;color:var(--mo-text);">{{ $rate }}%</span>
                            </div>
                            <div class="mo-read-bar">
                                <div class="mo-read-bar-track" style="height:8px;">
                                    <div class="mo-read-bar-fill" style="width:{{ $rate }}%;"></div>
                                </div>
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                            <div style="text-align:center;padding:12px;background:var(--mo-surface2);border-radius:10px;border:1px solid var(--mo-border);">
                                <div style="font-size:22px;font-weight:800;color:#22c55e;">{{ $read }}</div>
                                <div style="font-size:11px;color:var(--mo-muted);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Read</div>
                            </div>
                            <div style="text-align:center;padding:12px;background:var(--mo-surface2);border-radius:10px;border:1px solid var(--mo-border);">
                                <div style="font-size:22px;font-weight:800;color:var(--mo-muted);">{{ $unread }}</div>
                                <div style="font-size:11px;color:var(--mo-muted);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Unread</div>
                            </div>
                            <div style="text-align:center;padding:12px;background:var(--mo-surface2);border-radius:10px;border:1px solid var(--mo-border);">
                                <div style="font-size:22px;font-weight:800;color:#2563eb;">{{ $emailOk }}</div>
                                <div style="font-size:11px;color:var(--mo-muted);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Email Sent</div>
                            </div>
                            <div style="text-align:center;padding:12px;background:var(--mo-surface2);border-radius:10px;border:1px solid var(--mo-border);">
                                <div style="font-size:22px;font-weight:800;color:#ef4444;">{{ $emailFail }}</div>
                                <div style="font-size:11px;color:var(--mo-muted);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Email Failed</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recipients list -->
            <div class="mo-card">
                <div class="mo-card-header">
                    <div class="mo-card-header-icon"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="mo-card-title">Recipients</div>
                        <div class="mo-card-subtitle">{{ $memo->memoRecipients->count() }} user(s) targeted</div>
                    </div>
                </div>
                <div style="max-height: 420px; overflow-y: auto;">
                    @if ($memo->memoRecipients->count() > 0)
                        <table class="mo-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Status</th>
                                    @if ($memo->status === 'sent')
                                        <th>Read At</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($memo->memoRecipients as $recipient)
                                    <tr>
                                        <td>
                                            <div style="font-weight:600;font-size:13px;">{{ $recipient->user->name ?? '—' }}</div>
                                            @if ($recipient->user?->employee?->department)
                                                <div style="font-size:11px;color:var(--mo-muted);">{{ $recipient->user->employee->department }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($memo->status === 'sent')
                                                @if ($recipient->read_at)
                                                    <span style="font-size:12px;color:#22c55e;font-weight:600;">
                                                        <span class="mo-dot read"></span>Read
                                                    </span>
                                                @else
                                                    <span style="font-size:12px;color:var(--mo-muted);">
                                                        <span class="mo-dot unread"></span>Unread
                                                    </span>
                                                @endif
                                            @else
                                                <span style="font-size:12px;color:var(--mo-muted);">Pending</span>
                                            @endif
                                        </td>
                                        @if ($memo->status === 'sent')
                                            <td class="muted" style="font-size:12px;">
                                                {{ $recipient->read_at ? $recipient->read_at->format('M d, g:i A') : '—' }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="padding:32px;text-align:center;color:var(--mo-muted);">
                            <i class="fas fa-user-slash" style="font-size:28px;opacity:.3;display:block;margin-bottom:10px;"></i>
                            No recipients assigned
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

@endsection