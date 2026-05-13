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

    /* ── Breadcrumb ── */
    .mo-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--mo-muted); margin-bottom: 20px; }
    .mo-breadcrumb a { color: var(--mo-muted); text-decoration: none; }
    .mo-breadcrumb a:hover { color: #ea580c; }
    .mo-breadcrumb .sep { opacity: .4; }

    /* ── Card ── */
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

    /* ── Memo body ── */
    .mo-memo-body {
        font-size: 15px;
        line-height: 1.8;
        color: var(--mo-text-sec);
        white-space: pre-wrap;
        word-break: break-word;
    }

    /* ── Meta grid ── */
    .mo-meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 16px;
    }
    .mo-meta-item { display: flex; flex-direction: column; gap: 4px; }
    .mo-meta-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--mo-muted); }
    .mo-meta-value { font-size: 14px; font-weight: 600; color: var(--mo-text-sec); }

    /* ── Ref chip ── */
    .mo-ref-chip {
        font-family: monospace; font-size: 12px; font-weight: 700;
        color: var(--mo-muted); background: var(--mo-surface2);
        border: 1px solid var(--mo-border);
        padding: 3px 10px; border-radius: 6px;
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

    /* ── Read status badge ── */
    .mo-read-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px; border-radius: 20px;
        font-size: 12px; font-weight: 600; border: 1px solid;
    }
    .mo-read-badge.read   { color: #166534; border-color: #86efac; background: #f0fdf4; }
    .mo-read-badge.unread { color: #9a3412; border-color: #fdba74; background: #fff7ed; }
    .dark .mo-read-badge.read   { color: #4ade80; border-color: rgba(74,222,128,.3); background: rgba(74,222,128,.1); }
    .dark .mo-read-badge.unread { color: #fb923c; border-color: rgba(251,146,60,.3); background: rgba(251,146,60,.1); }

    /* ── Buttons ── */
    .mo-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 9px;
        font-size: 13px; font-weight: 600; border: 1px solid; cursor: pointer;
        transition: all .15s; text-decoration: none; white-space: nowrap;
        font-family: inherit;
    }
    .mo-btn-secondary { background: var(--mo-surface2); border-color: var(--mo-border); color: var(--mo-text-sec); }
    .mo-btn-secondary:hover { border-color: var(--mo-muted); }

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
    .mo-alert-close { background: none; border: none; cursor: pointer; font-size: 14px; opacity: .6; color: inherit; padding: 0; margin-left: 12px; }
    .mo-alert-close:hover { opacity: 1; }

    /* ── Attachment chip ── */
    .mo-attach-chip {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 14px; border-radius: 8px;
        background: var(--mo-surface2); border: 1px solid var(--mo-border);
        font-size: 13px; color: var(--mo-text-sec); text-decoration: none;
        transition: border-color .12s;
    }
    .mo-attach-chip:hover { border-color: #ea580c; color: #ea580c; }

    /* ── Info sidebar rows ── */
    .mo-info-row {
        display: flex; align-items: flex-start; justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--mo-border);
        font-size: 13px;
    }
    .mo-info-row:last-child { border-bottom: none; padding-bottom: 0; }
    .mo-info-row:first-child { padding-top: 0; }
    .mo-info-key   { color: var(--mo-muted); font-weight: 600; font-size: 12px; flex-shrink: 0; margin-right: 12px; }
    .mo-info-value { color: var(--mo-text-sec); font-weight: 500; text-align: right; }

    /* ── Divider ── */
    .mo-divider { height: 1px; background: var(--mo-border); margin: 20px 0; }

    /* ── Action bar ── */
    .mo-action-bar {
        padding: 18px 24px;
        border-top: 1px solid var(--mo-border);
        background: var(--mo-surface2);
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        flex-wrap: wrap;
    }

    /* ── "New" flash strip ── */
    .mo-new-strip {
        display: flex; align-items: center; gap: 10px;
        padding: 11px 18px; border-radius: 10px;
        background: #fff7ed; border: 1px solid #fdba74; color: #9a3412;
        font-size: 13px; font-weight: 600; margin-bottom: 20px;
    }
    .dark .mo-new-strip {
        background: rgba(251,146,60,.1); border-color: rgba(251,146,60,.3); color: #fb923c;
    }
</style>
@endpush

@section('content')

    <!-- ── Breadcrumb ── -->
    <div class="mo-breadcrumb">
        <a href="{{ route('user.memos.index') }}"><i class="fas fa-envelope mr-1"></i>My Memos</a>
        <span class="sep">/</span>
        <span>{{ Str::limit($memo->subject, 50) }}</span>
    </div>

    <!-- ── Alerts ── -->
    @if (session('success'))
        <div class="mo-alert success">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button class="mo-alert-close" onclick="this.closest('.mo-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Show "just marked read" strip only if they were previously unread --}}
    @if ($recipient && $recipient->read_at && $recipient->read_at->gt(now()->subSeconds(5)))
        <div class="mo-new-strip">
            <i class="fas fa-check-circle"></i>
            This memo has been marked as <strong>read</strong>.
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- ── LEFT: Memo Content ── -->
        <div class="xl:col-span-2 space-y-6">

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
                            <div style="display:flex; align-items:center; gap:8px; margin-top:5px; flex-wrap:wrap;">
                                <span class="mo-ref-chip">{{ $memo->reference_number }}</span>
                                <span class="mo-type-badge mo-type-{{ $typeColor }}">
                                    <i class="fas {{ $memo->type_icon }} text-xs"></i>
                                    {{ $memo->type_label }}
                                </span>
                                @if ($recipient)
                                    @if ($recipient->read_at)
                                        <span class="mo-read-badge read">
                                            <i class="fas fa-check text-xs"></i> Read
                                        </span>
                                    @else
                                        <span class="mo-read-badge unread">
                                            <i class="fas fa-circle text-xs" style="font-size:7px;"></i> Unread
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mo-card-body">

                    <!-- Meta -->
                    <div class="mo-meta-grid" style="margin-bottom: 24px;">
                        <div class="mo-meta-item">
                            <span class="mo-meta-label">From</span>
                            <span class="mo-meta-value">
                                <i class="fas fa-user mr-1" style="color:var(--mo-muted);font-size:11px;"></i>
                                {{ $memo->sender->name ?? '—' }}
                            </span>
                        </div>
                        <div class="mo-meta-item">
                            <span class="mo-meta-label">Sent At</span>
                            <span class="mo-meta-value">
                                {{ $memo->sent_at ? $memo->sent_at->format('M d, Y g:i A') : '—' }}
                            </span>
                        </div>
                        @if ($recipient && $recipient->read_at)
                            <div class="mo-meta-item">
                                <span class="mo-meta-label">Read At</span>
                                <span class="mo-meta-value">
                                    <i class="fas fa-envelope-open mr-1" style="color:var(--mo-muted);font-size:11px;"></i>
                                    {{ $recipient->read_at->format('M d, Y g:i A') }}
                                </span>
                            </div>
                        @endif
                        @if ($recipient && $recipient->email_sent_at)
                            <div class="mo-meta-item">
                                <span class="mo-meta-label">Email Delivered</span>
                                <span class="mo-meta-value" style="color:#166534;">
                                    <i class="fas fa-check-circle mr-1" style="font-size:11px;"></i>
                                    {{ $recipient->email_sent_at->format('M d, Y g:i A') }}
                                </span>
                            </div>
                        @elseif ($recipient && $recipient->email_failed)
                            <div class="mo-meta-item">
                                <span class="mo-meta-label">Email Delivery</span>
                                <span class="mo-meta-value" style="color:#dc2626;">
                                    <i class="fas fa-exclamation-circle mr-1" style="font-size:11px;"></i>
                                    Failed
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="mo-divider"></div>

                    <!-- Body -->
                    <div>
                        <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--mo-muted); margin-bottom:12px;">
                            Message
                        </p>
                        <div class="mo-memo-body">{{ $memo->body }}</div>
                    </div>

                    <!-- Attachments -->
                    @if ($memo->attachments && count($memo->attachments))
                        <div class="mo-divider"></div>
                        <div>
                            <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--mo-muted); margin-bottom:12px;">
                                <i class="fas fa-paperclip mr-1"></i> Attachments ({{ count($memo->attachments) }})
                            </p>
                            <div style="display:flex; flex-wrap:wrap; gap:8px;">
                                @foreach ($memo->attachments as $attachment)
                                    <a href="{{ Storage::url($attachment) }}"
                                       target="_blank"
                                       class="mo-attach-chip">
                                        <i class="fas fa-file"></i>
                                        {{ basename($attachment) }}
                                        <i class="fas fa-external-link-alt" style="font-size:10px; opacity:.5;"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Action bar -->
                <div class="mo-action-bar">
                    <a href="{{ route('user.memos.index') }}" class="mo-btn mo-btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Memos
                    </a>
                </div>
            </div>

        </div>

        <!-- ── RIGHT: Sidebar ── -->
        <div class="space-y-6">

            <!-- Memo Info -->
            <div class="mo-card">
                <div class="mo-card-header">
                    <div class="mo-card-header-icon"><i class="fas fa-info-circle"></i></div>
                    <div>
                        <div class="mo-card-title">Memo Info</div>
                        <div class="mo-card-subtitle">Details about this communication</div>
                    </div>
                </div>
                <div class="mo-card-body" style="padding: 16px 20px;">
                    <div class="mo-info-row">
                        <span class="mo-info-key">Reference</span>
                        <span class="mo-info-value">
                            <span class="mo-ref-chip">{{ $memo->reference_number }}</span>
                        </span>
                    </div>
                    <div class="mo-info-row">
                        <span class="mo-info-key">Type</span>
                        <span class="mo-info-value">
                            <span class="mo-type-badge mo-type-{{ $typeColor }}">
                                <i class="fas {{ $memo->type_icon }} text-xs"></i>
                                {{ $memo->type_label }}
                            </span>
                        </span>
                    </div>
                    <div class="mo-info-row">
                        <span class="mo-info-key">Sent By</span>
                        <span class="mo-info-value">{{ $memo->sender->name ?? '—' }}</span>
                    </div>
                    <div class="mo-info-row">
                        <span class="mo-info-key">Date Sent</span>
                        <span class="mo-info-value">
                            {{ $memo->sent_at ? $memo->sent_at->format('M d, Y') : '—' }}
                        </span>
                    </div>
                    <div class="mo-info-row">
                        <span class="mo-info-key">Total Recipients</span>
                        <span class="mo-info-value">{{ $memo->memoRecipients->count() }}</span>
                    </div>
                    @if ($memo->attachments && count($memo->attachments))
                        <div class="mo-info-row">
                            <span class="mo-info-key">Attachments</span>
                            <span class="mo-info-value">{{ count($memo->attachments) }} file(s)</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- My Receipt -->
            <div class="mo-card">
                <div class="mo-card-header">
                    <div class="mo-card-header-icon"><i class="fas fa-receipt"></i></div>
                    <div>
                        <div class="mo-card-title">My Receipt</div>
                        <div class="mo-card-subtitle">Your delivery record</div>
                    </div>
                </div>
                <div class="mo-card-body" style="padding: 16px 20px;">
                    @if ($recipient)
                        <div class="mo-info-row">
                            <span class="mo-info-key">Read Status</span>
                            <span class="mo-info-value">
                                @if ($recipient->read_at)
                                    <span class="mo-read-badge read">
                                        <i class="fas fa-check text-xs"></i> Read
                                    </span>
                                @else
                                    <span class="mo-read-badge unread">
                                        <i class="fas fa-circle text-xs" style="font-size:7px;"></i> Unread
                                    </span>
                                @endif
                            </span>
                        </div>
                        @if ($recipient->read_at)
                            <div class="mo-info-row">
                                <span class="mo-info-key">Read At</span>
                                <span class="mo-info-value">{{ $recipient->read_at->format('M d, Y g:i A') }}</span>
                            </div>
                        @endif
                        <div class="mo-info-row">
                            <span class="mo-info-key">Email</span>
                            <span class="mo-info-value">
                                @if ($recipient->email_sent_at)
                                    <span style="color:#166534; font-size:12px; font-weight:600;">
                                        <i class="fas fa-check-circle mr-1"></i>Delivered
                                    </span>
                                @elseif ($recipient->email_failed)
                                    <span style="color:#dc2626; font-size:12px; font-weight:600;">
                                        <i class="fas fa-times-circle mr-1"></i>Failed
                                    </span>
                                @else
                                    <span style="color:var(--mo-muted); font-size:12px;">Pending</span>
                                @endif
                            </span>
                        </div>
                    @else
                        <p style="font-size:13px; color:var(--mo-muted); text-align:center; padding: 8px 0;">
                            No receipt record found.
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Auto mark-read via AJAX (fires once on page load if not already read)
    @if ($recipient && !$recipient->read_at)
        fetch('{{ route('user.memos.mark-read', $memo) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }).then(() => {
            // Quietly update the badge in the UI without a reload
            document.querySelectorAll('.mo-read-badge.unread').forEach(el => {
                el.classList.replace('unread', 'read');
                el.innerHTML = '<i class="fas fa-check text-xs"></i> Read';
            });
        });
    @endif
</script>
@endpush