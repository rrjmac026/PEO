<x-app-layout>
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* ══════════════════════════════════════════
           TOKENS — Light
        ══════════════════════════════════════════ */
        :root {
            --np-bg:           #f1f5f9;
            --np-surface:      #ffffff;
            --np-surface2:     #f8fafc;
            --np-surface3:     #f1f5f9;
            --np-border:       #e2e8f0;
            --np-text:         #0f172a;
            --np-text-sec:     #334155;
            --np-muted:        #64748b;
            --np-accent:       #ea580c;
            --np-accent2:      #f97316;
            --np-accent-bg:    #fff7ed;
            --np-unread-bg:    #fff7ed;
            --np-unread-dot:   #ea580c;
            --np-shadow:       0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            --np-shadow-lg:    0 4px 24px rgba(0,0,0,0.10);
            --np-radius:       14px;
            --np-green:        #16a34a;
            --np-blue:         #2563eb;
            --np-amber:        #d97706;
        }

        /* ══════════════════════════════════════════
           TOKENS — Dark
        ══════════════════════════════════════════ */
        .dark {
            --np-bg:           #0f1117;
            --np-surface:      #181c27;
            --np-surface2:     #1e2335;
            --np-surface3:     #242840;
            --np-border:       #2a3050;
            --np-text:         #e8eaf6;
            --np-text-sec:     #c5cae9;
            --np-muted:        #7c85a8;
            --np-accent-bg:    rgba(234,88,12,0.10);
            --np-unread-bg:    rgba(234,88,12,0.08);
            --np-shadow:       0 1px 4px rgba(0,0,0,0.35);
            --np-shadow-lg:    0 4px 24px rgba(0,0,0,0.45);
        }

        /* ── Layout ── */
        .np-wrap {
            font-family: 'Inter', sans-serif;
            background: var(--np-bg);
            min-height: 100vh;
        }

        /* ── Page hero ── */
        .np-hero {
            background: linear-gradient(90deg, #EA580C 0%, #F97316 50%, #EA580C 100%);
            padding: 32px 0 80px;
            position: relative;
            overflow: hidden;
        }
        .np-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        .np-hero-inner {
            max-width: 960px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 1;
        }
        .np-hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 12px;
            font-weight: 700;
            color: rgba(255,255,255,0.9);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .np-hero-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 30px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .np-hero-sub {
            font-size: 14px;
            color: rgba(255,255,255,0.75);
        }
        .np-hero-stats {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            flex-wrap: wrap;
        }
        .np-stat {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .np-stat-num {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }
        .np-stat-label {
            font-size: 12px;
            color: rgba(255,255,255,0.75);
            font-weight: 500;
        }

        /* ── Main content (pulls up over hero) ── */
        .np-content {
            max-width: 960px;
            margin: -50px auto 40px;
            padding: 0 24px;
            position: relative;
            z-index: 10;
        }

        /* ── Filter / toolbar card ── */
        .np-toolbar {
            background: var(--np-surface);
            border: 1px solid var(--np-border);
            border-radius: var(--np-radius);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            box-shadow: var(--np-shadow-lg);
        }
        .np-filter-tabs {
            display: flex;
            gap: 4px;
            flex: 1;
            flex-wrap: wrap;
        }
        .np-filter-tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--np-muted);
            cursor: pointer;
            border: 1.5px solid transparent;
            background: none;
            transition: all 0.18s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: none;
        }
        .np-filter-tab:hover {
            background: var(--np-surface2);
            color: var(--np-text);
        }
        .np-filter-tab.active {
            background: var(--np-accent-bg);
            color: var(--np-accent);
            border-color: rgba(234,88,12,0.25);
        }
        .np-filter-tab .tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 9px;
            font-size: 10px;
            font-weight: 800;
            background: var(--np-accent);
            color: #fff;
        }
        .np-filter-tab:not(.active) .tab-count {
            background: var(--np-surface3);
            color: var(--np-muted);
        }
        .np-toolbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .np-btn-mark-all {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            background: linear-gradient(135deg, #EA580C, #F97316);
            color: #fff;
            border: none;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-shadow: 0 2px 8px rgba(234,88,12,0.35);
            transition: all 0.18s;
            text-decoration: none;
        }
        .np-btn-mark-all:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(234,88,12,0.45);
        }

        /* ── Notification list container ── */
        .np-list-wrap {
            background: var(--np-surface);
            border: 1px solid var(--np-border);
            border-radius: var(--np-radius);
            overflow: hidden;
            box-shadow: var(--np-shadow-lg);
        }

        /* ── Date group header ── */
        .np-date-group {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            background: var(--np-surface2);
            border-bottom: 1px solid var(--np-border);
        }
        .np-date-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--np-muted);
            text-transform: uppercase;
            letter-spacing: 0.7px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            white-space: nowrap;
        }
        .np-date-line {
            flex: 1;
            height: 1px;
            background: var(--np-border);
        }

        /* ── Notification item ── */
        .np-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 18px 20px;
            border-bottom: 1px solid var(--np-border);
            text-decoration: none;
            transition: background 0.12s;
            position: relative;
            overflow: hidden;
        }
        .np-item:last-child { border-bottom: none; }
        .np-item::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: transparent;
            transition: background 0.15s;
        }
        .np-item.unread {
            background: var(--np-unread-bg);
        }
        .np-item.unread::before {
            background: linear-gradient(180deg, #EA580C, #F97316);
        }
        .np-item:hover {
            background: var(--np-surface2);
        }
        .np-item.unread:hover {
            filter: brightness(0.97);
        }

        /* ── Item icon ── */
        .np-item-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            position: relative;
        }
        .np-item-icon.type-work_request {
            background: #eff6ff;
            color: #2563eb;
        }
        .np-item-icon.type-concrete_pouring {
            background: #fef3c7;
            color: #d97706;
        }
        .np-item-icon.type-default {
            background: var(--np-surface3);
            color: var(--np-muted);
        }
        .dark .np-item-icon.type-work_request    { background: rgba(37,99,235,0.15);  color: #60a5fa; }
        .dark .np-item-icon.type-concrete_pouring{ background: rgba(217,119,6,0.15);  color: #fbbf24; }

        .np-item-icon .np-unread-ring {
            position: absolute;
            inset: -3px;
            border-radius: 15px;
            border: 2px solid var(--np-accent2);
            opacity: 0.45;
            animation: ring-pulse 2s ease-in-out infinite;
        }
        @keyframes ring-pulse {
            0%,100% { transform: scale(1); opacity: 0.45; }
            50%      { transform: scale(1.1); opacity: 0.15; }
        }

        /* ── Item body ── */
        .np-item-body { flex: 1; min-width: 0; }
        .np-item-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 4px;
        }
        .np-item-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--np-text);
            line-height: 1.35;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .np-item-time {
            font-size: 11px;
            color: var(--np-muted);
            white-space: nowrap;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 2px;
        }
        .np-item-msg {
            font-size: 13px;
            color: var(--np-text-sec);
            line-height: 1.55;
            margin-bottom: 8px;
        }
        .np-item-footer {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .np-type-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .np-type-tag.work_request {
            background: #dbeafe;
            color: #1d4ed8;
        }
        .np-type-tag.concrete_pouring {
            background: #fef9c3;
            color: #92400e;
        }
        .dark .np-type-tag.work_request     { background: rgba(37,99,235,0.2); color: #93c5fd; }
        .dark .np-type-tag.concrete_pouring { background: rgba(217,119,6,0.2); color: #fcd34d; }

        .np-action-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 600;
            color: var(--np-accent);
            text-decoration: none;
            padding: 3px 10px;
            border-radius: 6px;
            background: var(--np-accent-bg);
            border: 1px solid rgba(234,88,12,0.2);
            transition: all 0.15s;
        }
        .np-action-link:hover {
            background: var(--np-accent);
            color: #fff;
        }

        /* ── Unread dot ── */
        .np-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 6px;
        }
        .np-dot.unread {
            background: var(--np-accent);
            box-shadow: 0 0 8px rgba(234,88,12,0.5);
        }
        .np-dot.read {
            background: transparent;
            border: 1.5px solid var(--np-border);
        }

        /* ── Empty state ── */
        .np-empty {
            padding: 72px 24px 60px;
            text-align: center;
        }
        .np-empty-icon {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            background: var(--np-surface2);
            border: 1px solid var(--np-border);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            font-size: 28px;
            color: var(--np-muted);
        }
        .np-empty-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--np-text);
            margin-bottom: 8px;
        }
        .np-empty-sub {
            font-size: 14px;
            color: var(--np-muted);
            max-width: 320px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* ── Pagination ── */
        .np-pagination {
            padding: 16px 20px;
            border-top: 1px solid var(--np-border);
            background: var(--np-surface2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .np-pagination-info {
            font-size: 13px;
            color: var(--np-muted);
        }

        /* ── Stagger animation ── */
        .np-item {
            animation: npFadeUp 0.3s ease both;
        }
        @keyframes npFadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .np-item:nth-child(1)  { animation-delay: 0.00s; }
        .np-item:nth-child(2)  { animation-delay: 0.03s; }
        .np-item:nth-child(3)  { animation-delay: 0.06s; }
        .np-item:nth-child(4)  { animation-delay: 0.09s; }
        .np-item:nth-child(5)  { animation-delay: 0.12s; }
        .np-item:nth-child(6)  { animation-delay: 0.15s; }
        .np-item:nth-child(7)  { animation-delay: 0.18s; }
        .np-item:nth-child(8)  { animation-delay: 0.21s; }
        .np-item:nth-child(9)  { animation-delay: 0.24s; }
        .np-item:nth-child(10) { animation-delay: 0.27s; }
        .np-item:nth-child(11) { animation-delay: 0.30s; }
        .np-item:nth-child(12) { animation-delay: 0.33s; }
        .np-item:nth-child(13) { animation-delay: 0.36s; }
        .np-item:nth-child(14) { animation-delay: 0.39s; }
        .np-item:nth-child(15) { animation-delay: 0.42s; }
        .np-item:nth-child(16) { animation-delay: 0.45s; }
        .np-item:nth-child(17) { animation-delay: 0.48s; }
        .np-item:nth-child(18) { animation-delay: 0.51s; }
        .np-item:nth-child(19) { animation-delay: 0.54s; }
        .np-item:nth-child(20) { animation-delay: 0.57s; }
    </style>
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Notifications') }}
            </h2>
        </div>
    </x-slot>
    <div class="np-wrap">

        {{-- ── Hero ── --}}
        <div class="np-hero">
            <div class="np-hero-inner">
                <div class="np-hero-eyebrow">
                    <i class="fas fa-bell"></i>
                    Notification Center
                </div>
                <div class="np-hero-title">Your Notifications</div>
                <div class="np-hero-sub">Stay up to date with work requests, approvals, and system updates.</div>

                <div class="np-hero-stats">
                    <div class="np-stat">
                        <div>
                            <div class="np-stat-num">{{ $totalCount }}</div>
                            <div class="np-stat-label">Total</div>
                        </div>
                    </div>
                    <div class="np-stat">
                        <div>
                            <div class="np-stat-num">{{ $unreadCount }}</div>
                            <div class="np-stat-label">Unread</div>
                        </div>
                    </div>
                    <div class="np-stat">
                        <div>
                            <div class="np-stat-num">{{ $readCount }}</div>
                            <div class="np-stat-label">Read</div>
                        </div>
                    </div>
                    @if($wrCount > 0)
                    <div class="np-stat">
                        <div>
                            <div class="np-stat-num">{{ $wrCount }}</div>
                            <div class="np-stat-label">Work Requests</div>
                        </div>
                    </div>
                    @endif
                    @if($cpCount > 0)
                    <div class="np-stat">
                        <div>
                            <div class="np-stat-num">{{ $cpCount }}</div>
                            <div class="np-stat-label">Concrete Pouring</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Content ── --}}
        <div class="np-content">

            {{-- Flash --}}
            @if(session('success'))
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#15803d; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            {{-- ── Toolbar ── --}}
            <div class="np-toolbar">
                <div class="np-filter-tabs">
                    {{-- Status filters --}}
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'all', 'page' => 1]) }}"
                       class="np-filter-tab {{ $filter === 'all' && !$typeFilter ? 'active' : '' }}">
                        All
                        @if($totalCount > 0)
                            <span class="tab-count">{{ $totalCount }}</span>
                        @endif
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'unread', 'page' => 1]) }}"
                       class="np-filter-tab {{ $filter === 'unread' ? 'active' : '' }}">
                        Unread
                        @if($unreadCount > 0)
                            <span class="tab-count">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'read', 'page' => 1]) }}"
                       class="np-filter-tab {{ $filter === 'read' ? 'active' : '' }}">
                        Read
                    </a>

                    {{-- Type filters --}}
                    @if($wrCount > 0)
                    <a href="{{ request()->fullUrlWithQuery(['type' => 'work_request', 'filter' => 'all', 'page' => 1]) }}"
                       class="np-filter-tab {{ $typeFilter === 'work_request' ? 'active' : '' }}">
                        <i class="fas fa-file-alt" style="font-size:11px;"></i>
                        Work Requests
                        <span class="tab-count">{{ $wrCount }}</span>
                    </a>
                    @endif
                    @if($cpCount > 0)
                    <a href="{{ request()->fullUrlWithQuery(['type' => 'concrete_pouring', 'filter' => 'all', 'page' => 1]) }}"
                       class="np-filter-tab {{ $typeFilter === 'concrete_pouring' ? 'active' : '' }}">
                        <i class="fas fa-hard-hat" style="font-size:11px;"></i>
                        Concrete Pouring
                        <span class="tab-count">{{ $cpCount }}</span>
                    </a>
                    @endif
                </div>

                <div class="np-toolbar-actions">
                    @if($unreadCount > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="np-btn-mark-all">
                            <i class="fas fa-check-double" style="font-size:11px;"></i>
                            Mark all read
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- ── Notification list ── --}}
            <div class="np-list-wrap">
                @if($notifications->isEmpty())
                    <div class="np-empty">
                        <div class="np-empty-icon">
                            <i class="fas fa-bell-slash"></i>
                        </div>
                        <div class="np-empty-title">
                            {{ $filter === 'unread' ? 'All caught up!' : 'No notifications yet' }}
                        </div>
                        <div class="np-empty-sub">
                            {{ $filter === 'unread'
                                ? 'You have no unread notifications. Check back later.'
                                : 'Notifications about work requests, approvals, and system updates will appear here.' }}
                        </div>
                    </div>
                @else
                    @foreach($grouped as $dateLabel => $group)
                        {{-- Date group header --}}
                        <div class="np-date-group">
                            <span class="np-date-label">{{ $dateLabel }}</span>
                            <div class="np-date-line"></div>
                            <span class="np-date-label">{{ $group->count() }} {{ Str::plural('item', $group->count()) }}</span>
                        </div>

                        @foreach($group as $notif)
                            @php
                                $isUnread = !$notif->is_read;
                                $typeClass = $notif->type ?? 'default';
                                $icon = match($notif->type) {
                                    'work_request'    => 'fas fa-file-alt',
                                    'concrete_pouring'=> 'fas fa-hard-hat',
                                    default           => 'fas fa-bell',
                                };
                                $typeLabel = match($notif->type) {
                                    'work_request'    => 'Work Request',
                                    'concrete_pouring'=> 'Concrete Pouring',
                                    default           => 'Notification',
                                };
                            @endphp

                            <div class="np-item {{ $isUnread ? 'unread' : 'read' }}"
                                 style="animation-delay: {{ $loop->index * 0.03 }}s">

                                {{-- Icon --}}
                                <div class="np-item-icon type-{{ $typeClass }}">
                                    <i class="{{ $icon }}"></i>
                                    @if($isUnread)
                                        <div class="np-unread-ring"></div>
                                    @endif
                                </div>

                                {{-- Body --}}
                                <div class="np-item-body">
                                    <div class="np-item-header">
                                        <div class="np-item-title">{{ $notif->title }}</div>
                                        <div class="np-item-time">
                                            <i class="fas fa-clock" style="font-size:9px;"></i>
                                            {{ $notif->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    <div class="np-item-msg">{{ $notif->message }}</div>

                                    <div class="np-item-footer">
                                        <span class="np-type-tag {{ $typeClass }}">
                                            <i class="{{ $icon }}" style="font-size:9px;"></i>
                                            {{ $typeLabel }}
                                        </span>

                                        @if($notif->link)
                                            <a href="{{ $notif->link }}"
                                               class="np-action-link"
                                               onclick="markRead({{ $notif->id }})">
                                                View details
                                                <i class="fas fa-arrow-right" style="font-size:10px;"></i>
                                            </a>
                                        @endif

                                        @if($isUnread)
                                            <form action="{{ url('notifications/' . $notif->id . '/read') }}"
                                                  method="POST"
                                                  style="display:inline;">
                                                @csrf
                                                <button type="submit"
                                                        style="background:none; border:none; font-size:12px; color:var(--np-muted); cursor:pointer; padding:3px 8px; border-radius:5px; transition:color 0.15s;"
                                                        onmouseover="this.style.color='var(--np-accent)'"
                                                        onmouseout="this.style.color='var(--np-muted)'">
                                                    Mark read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                {{-- Dot --}}
                                <div class="np-dot {{ $isUnread ? 'unread' : 'read' }}"></div>
                            </div>
                        @endforeach
                    @endforeach

                    {{-- Pagination --}}
                    @if($notifications->hasPages())
                        <div class="np-pagination">
                            <div class="np-pagination-info">
                                Showing {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }}
                                of {{ $notifications->total() }} notifications
                            </div>
                            <div>
                                {{ $notifications->links() }}
                            </div>
                        </div>
                    @endif
                @endif
            </div>

        </div>{{-- /.np-content --}}
    </div>{{-- /.np-wrap --}}
</x-app-layout>