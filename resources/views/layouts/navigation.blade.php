@php
    $navNotifications = [];
    if (isset($notifications) && $notifications instanceof \Illuminate\Support\Collection) {
        $navNotifications = $notifications->map(function($notif) {
            return [
                'id' => $notif->id,
                'title' => $notif->title,
                'message' => $notif->message,
                'created_at' => $notif->created_at->diffForHumans(),
                'is_read' => (bool) $notif->is_read,
                'link' => $notif->link ?? null,
            ];
        })->toArray();
    }

    $userRole = Auth::user()->role ?? null;

    $reviewerRoles = [
        'site_inspector',
        'surveyor',
        'resident_engineer',
        'provincial_engineer',
        'mtqa',
        'engineeriii',
        'engineeriv',
    ];

    $dashboardRoute = match(true) {
        $userRole === 'admin'              => route('admin.dashboard'),
        $userRole === 'contractor'         => route('user.dashboard'),
        in_array($userRole, $reviewerRoles) => route('reviewer.dashboard'),
        default                            => '/',
    };
@endphp

<style>
    /* ══════════════════════════════════════════
       NAV DROPDOWN TOKENS — Light
    ══════════════════════════════════════════ */
    :root {
        --nd-surface:    #ffffff;
        --nd-surface2:   #f8fafc;
        --nd-surface3:   #f1f5f9;
        --nd-border:     #e2e8f0;
        --nd-text:       #0f172a;
        --nd-text-sec:   #334155;
        --nd-muted:      #64748b;
        --nd-shadow:     0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
        --nd-shadow-lg:  0 20px 48px rgba(0,0,0,0.14), 0 4px 12px rgba(0,0,0,0.08);
        --nd-accent:     #ea580c;
        --nd-accent-bg:  #fff7ed;
        --nd-unread-bg:  #eff6ff;
        --nd-unread-dot: #3b82f6;
        --nd-read-bg:    #f0fdf4;
        --nd-read-dot:   #22c55e;
    }

    /* ══════════════════════════════════════════
       NAV DROPDOWN TOKENS — Dark
    ══════════════════════════════════════════ */
    .dark {
        --nd-surface:    #1a1f2e;
        --nd-surface2:   #1e2335;
        --nd-surface3:   #242840;
        --nd-border:     #2a3050;
        --nd-text:       #e8eaf6;
        --nd-text-sec:   #c5cae9;
        --nd-muted:      #7c85a8;
        --nd-shadow:     0 4px 16px rgba(0,0,0,0.45);
        --nd-shadow-lg:  0 20px 48px rgba(0,0,0,0.55), 0 4px 12px rgba(0,0,0,0.35);
        --nd-accent:     #f97316;
        --nd-accent-bg:  rgba(249,115,22,0.10);
        --nd-unread-bg:  rgba(59,130,246,0.10);
        --nd-unread-dot: #60a5fa;
        --nd-read-bg:    rgba(34,197,94,0.08);
        --nd-read-dot:   #4ade80;
    }

    /* ── Shared dropdown shell ── */
    .nd-dropdown {
        position: absolute;
        right: 0;
        top: calc(100% + 12px);
        border-radius: 14px;
        border: 1px solid var(--nd-border);
        background: var(--nd-surface);
        box-shadow: var(--nd-shadow-lg);
        overflow: hidden;
        z-index: 50;
    }

    /* ── Dropdown top accent bar ── */
    .nd-accent-bar {
        height: 3px;
        background: linear-gradient(90deg, #ea580c 0%, #f97316 50%, #ea580c 100%);
    }

    /* ── Section headers inside dropdowns ── */
    .nd-dropdown-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px 10px;
        border-bottom: 1px solid var(--nd-border);
        background: var(--nd-surface2);
    }
    .nd-dropdown-header-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--nd-text);
        letter-spacing: 0.2px;
    }
    .nd-dropdown-header-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--nd-muted);
    }

    /* ══════════════════════════════════════════
       NOTIFICATION DROPDOWN
    ══════════════════════════════════════════ */
    .nd-notif-dropdown { width: 340px; }

    .nd-notif-list { max-height: 380px; overflow-y: auto; }
    .nd-notif-list::-webkit-scrollbar { width: 4px; }
    .nd-notif-list::-webkit-scrollbar-thumb { background: var(--nd-border); border-radius: 4px; }

    .nd-notif-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 13px 16px;
        border-bottom: 1px solid var(--nd-border);
        text-decoration: none;
        transition: background .14s;
        cursor: pointer;
    }
    .nd-notif-item:last-child { border-bottom: none; }
    .nd-notif-item:hover { background: var(--nd-surface2); }
    .nd-notif-item.unread { background: var(--nd-unread-bg); }
    .nd-notif-item.unread:hover { filter: brightness(0.97); }

    .nd-notif-icon {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 14px;
        transition: background .14s;
    }
    .nd-notif-icon.unread { background: rgba(59,130,246,.15); color: var(--nd-unread-dot); }
    .nd-notif-icon.read   { background: var(--nd-surface3);   color: var(--nd-muted); }

    .nd-notif-body { flex: 1; min-width: 0; }
    .nd-notif-title {
        font-size: 13px; font-weight: 700;
        color: var(--nd-text); line-height: 1.35;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .nd-notif-msg {
        font-size: 12px; color: var(--nd-text-sec);
        margin-top: 2px; line-height: 1.4;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .nd-notif-time {
        font-size: 11px; color: var(--nd-muted); margin-top: 4px;
    }

    .nd-notif-dot {
        width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 5px;
    }
    .nd-notif-dot.unread { background: var(--nd-unread-dot); }
    .nd-notif-dot.read   { background: transparent; border: 1.5px solid var(--nd-border); }

    /* Empty state */
    .nd-notif-empty {
        padding: 40px 16px; text-align: center; color: var(--nd-muted);
    }
    .nd-notif-empty i { font-size: 28px; opacity: .35; display: block; margin-bottom: 10px; }
    .nd-notif-empty p { font-size: 13px; }

    /* ══════════════════════════════════════════
       PROFILE DROPDOWN
    ══════════════════════════════════════════ */
    .nd-profile-dropdown { width: 300px; }

    /* Profile hero section */
    .nd-profile-hero {
        padding: 16px;
        background: var(--nd-surface2);
        border-bottom: 1px solid var(--nd-border);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .nd-profile-avatar {
        width: 46px; height: 46px; border-radius: 12px;
        background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 800; color: #fff;
        flex-shrink: 0; position: relative;
    }
    .nd-profile-avatar-dot {
        position: absolute; bottom: -2px; right: -2px;
        width: 12px; height: 12px;
        background: #22c55e;
        border: 2px solid var(--nd-surface2);
        border-radius: 50%;
    }
    .nd-profile-name {
        font-size: 14px; font-weight: 700; color: var(--nd-text); line-height: 1.3;
    }
    .nd-profile-role {
        display: inline-flex; align-items: center; gap: 4px;
        margin-top: 4px;
        padding: 2px 8px; border-radius: 20px;
        font-size: 11px; font-weight: 600;
        background: var(--nd-accent-bg);
        color: var(--nd-accent);
        border: 1px solid rgba(234,88,12,.2);
    }
    .dark .nd-profile-role { border-color: rgba(249,115,22,.2); }

    /* Menu items */
    .nd-menu-body { padding: 8px; }

    .nd-menu-item {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 12px; border-radius: 9px;
        font-size: 13px; font-weight: 600;
        color: var(--nd-text-sec);
        text-decoration: none;
        cursor: pointer; border: none; width: 100%;
        background: none; text-align: left;
        transition: background .14s, color .14s;
    }
    .nd-menu-item:hover { background: var(--nd-surface2); color: var(--nd-text); }

    .nd-menu-icon {
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; flex-shrink: 0;
        transition: transform .15s;
    }
    .nd-menu-item:hover .nd-menu-icon { transform: scale(1.1); }

    .nd-menu-icon.blue   { background: #eff6ff; color: #2563eb; }
    .nd-menu-icon.green  { background: #f0fdf4; color: #16a34a; }
    .nd-menu-icon.red    { background: #fff1f2; color: #dc2626; }
    .dark .nd-menu-icon.blue  { background: rgba(96,165,250,.15);  color: #60a5fa; }
    .dark .nd-menu-icon.green { background: rgba(74,222,128,.12);  color: #4ade80; }
    .dark .nd-menu-icon.red   { background: rgba(248,113,113,.12); color: #f87171; }

    .nd-menu-item-label { flex: 1; }
    .nd-menu-item-label span { display: block; }
    .nd-menu-item-label .sub { font-size: 11px; font-weight: 400; color: var(--nd-muted); margin-top: 1px; }

    /* Dark mode toggle switch */
    .nd-toggle-track {
        width: 36px; height: 20px; border-radius: 10px;
        background: var(--nd-surface3); border: 1px solid var(--nd-border);
        position: relative; flex-shrink: 0;
        transition: background .2s;
    }
    .nd-toggle-track.on { background: #22c55e; border-color: #22c55e; }
    .nd-toggle-thumb {
        position: absolute; top: 2px; left: 2px;
        width: 14px; height: 14px; border-radius: 50%;
        background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.2);
        transition: transform .2s;
    }
    .nd-toggle-track.on .nd-toggle-thumb { transform: translateX(16px); }

    /* Divider */
    .nd-divider { height: 1px; background: var(--nd-border); margin: 4px 8px; }

    /* Footer */
    .nd-dropdown-footer {
        padding: 8px 16px;
        border-top: 1px solid var(--nd-border);
        background: var(--nd-surface2);
        text-align: center;
        font-size: 11px;
        color: var(--nd-muted);
    }
</style>
<style>
    /* ═══════════════════════════════════════════════════
    NOTIFICATION DROPDOWN — Refined Glass-Dark Theme
    ═══════════════════════════════════════════════════ */
    :root {
        --notif-bg:          #ffffff;
        --notif-bg2:         #f8f9fc;
        --notif-bg3:         #f0f2f7;
        --notif-border:      rgba(0,0,0,0.07);
        --notif-text:        #111827;
        --notif-text-sec:    #374151;
        --notif-muted:       #9ca3af;
        --notif-accent:      #ea580c;
        --notif-accent2:     #f97316;
        --notif-shadow:      0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06);
        --notif-unread-bg:   #fff7ed;
        --notif-unread-dot:  #ea580c;
        --notif-green:       #16a34a;
        --notif-blue:        #2563eb;
        --notif-radius:      16px;
    }
    .dark {
        --notif-bg:          #161b2e;
        --notif-bg2:         #1c2340;
        --notif-bg3:         #222847;
        --notif-border:      rgba(255,255,255,0.06);
        --notif-text:        #f1f5f9;
        --notif-text-sec:    #cbd5e1;
        --notif-muted:       #64748b;
        --notif-unread-bg:   rgba(234,88,12,0.10);
        --notif-shadow:      0 8px 40px rgba(0,0,0,0.5), 0 2px 10px rgba(0,0,0,0.3);
    }

    /* ── Shell ── */
    .ndd-shell {
        position: absolute;
        right: 0;
        top: calc(100% + 14px);
        width: 380px;
        background: var(--notif-bg);
        border: 1px solid var(--notif-border);
        border-radius: var(--notif-radius);
        box-shadow: var(--notif-shadow);
        overflow: hidden;
        z-index: 9999;
        transform-origin: top right;
    }

    /* ── Top gradient bar ── */
    .ndd-glow-bar {
        height: 3px;
        background: linear-gradient(90deg, #ea580c, #f97316, #fbbf24, #f97316, #ea580c);
        background-size: 200% 100%;
        animation: shimmer 3s linear infinite;
    }
    @keyframes shimmer {
        0%   { background-position: 200% center; }
        100% { background-position: -200% center; }
    }

    /* ── Header ── */
    .ndd-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px 12px;
        background: var(--notif-bg2);
        border-bottom: 1px solid var(--notif-border);
    }
    .ndd-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ndd-header-icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #ea580c, #f97316);
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(234,88,12,0.35);
    }
    .ndd-header-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--notif-text);
        letter-spacing: -0.2px;
        font-family: 'Segoe UI', sans-serif;
    }
    .ndd-header-sub {
        font-size: 11px;
        color: var(--notif-muted);
        margin-top: 1px;
    }
    .ndd-badge-unread {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        background: linear-gradient(135deg, #ea580c, #f97316);
        color: white;
        box-shadow: 0 2px 8px rgba(234,88,12,0.4);
        cursor: pointer;
        border: none;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .ndd-badge-unread:hover {
        transform: scale(1.05);
        box-shadow: 0 3px 12px rgba(234,88,12,0.5);
    }
    .ndd-badge-unread .pulse-dot {
        width: 6px;
        height: 6px;
        background: white;
        border-radius: 50%;
        animation: pulse-dot 1.5s ease-in-out infinite;
        flex-shrink: 0;
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.5; transform: scale(0.7); }
    }
    .ndd-badge-all-read {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        font-weight: 600;
        color: var(--notif-green);
        padding: 3px 10px;
        background: rgba(22,163,74,0.1);
        border-radius: 20px;
    }
    .ndd-loader {
        width: 16px;
        height: 16px;
        border: 2px solid var(--notif-border);
        border-top-color: var(--notif-accent);
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Tab row ── */
    .ndd-tabs {
        display: flex;
        gap: 0;
        background: var(--notif-bg2);
        border-bottom: 1px solid var(--notif-border);
        padding: 0 18px;
    }
    .ndd-tab {
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 600;
        color: var(--notif-muted);
        cursor: pointer;
        border: none;
        background: none;
        border-bottom: 2px solid transparent;
        transition: color 0.15s, border-color 0.15s;
        position: relative;
        top: 1px;
    }
    .ndd-tab.active {
        color: var(--notif-accent);
        border-bottom-color: var(--notif-accent);
    }
    .ndd-tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        font-size: 10px;
        font-weight: 800;
        margin-left: 5px;
        background: var(--notif-accent);
        color: white;
    }

    /* ── Notification list ── */
    .ndd-list {
        max-height: 360px;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: var(--notif-bg3) transparent;
    }
    .ndd-list::-webkit-scrollbar { width: 4px; }
    .ndd-list::-webkit-scrollbar-track { background: transparent; }
    .ndd-list::-webkit-scrollbar-thumb { background: var(--notif-bg3); border-radius: 4px; }

    /* ── Notification item ── */
    .ndd-item {
        display: flex;
        align-items: flex-start;
        gap: 13px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--notif-border);
        text-decoration: none;
        cursor: pointer;
        transition: background 0.12s;
        position: relative;
        overflow: hidden;
    }
    .ndd-item:last-child { border-bottom: none; }
    .ndd-item::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        background: transparent;
        transition: background 0.15s;
    }
    .ndd-item.unread {
        background: var(--notif-unread-bg);
    }
    .ndd-item.unread::before {
        background: linear-gradient(180deg, #ea580c, #f97316);
    }
    .ndd-item:hover {
        background: var(--notif-bg3);
    }
    .ndd-item.unread:hover {
        filter: brightness(0.97);
    }

    /* ── Item icon ── */
    .ndd-item-icon {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
        position: relative;
    }
    .ndd-item-icon.type-work_request   { background: #eff6ff; color: #2563eb; }
    .ndd-item-icon.type-concrete_pouring { background: #fef3c7; color: #d97706; }
    .ndd-item-icon.type-default        { background: var(--notif-bg3); color: var(--notif-muted); }
    .dark .ndd-item-icon.type-work_request    { background: rgba(37,99,235,0.15); color: #60a5fa; }
    .dark .ndd-item-icon.type-concrete_pouring{ background: rgba(217,119,6,0.15); color: #fbbf24; }
    .ndd-item-icon .unread-ring {
        position: absolute;
        inset: -2px;
        border-radius: 13px;
        border: 2px solid var(--notif-accent2);
        opacity: 0.5;
        animation: ring-pulse 2s ease-in-out infinite;
    }
    @keyframes ring-pulse {
        0%,100% { transform: scale(1); opacity: 0.5; }
        50%      { transform: scale(1.08); opacity: 0.2; }
    }

    /* ── Item body ── */
    .ndd-item-body { flex: 1; min-width: 0; }
    .ndd-item-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--notif-text);
        line-height: 1.35;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ndd-item-msg {
        font-size: 12px;
        color: var(--notif-text-sec);
        line-height: 1.45;
        margin-top: 3px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .ndd-item-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
    }
    .ndd-item-time {
        font-size: 11px;
        color: var(--notif-muted);
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .ndd-item-type-tag {
        font-size: 10px;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .ndd-item-type-tag.work_request    { background: #dbeafe; color: #1d4ed8; }
    .ndd-item-type-tag.concrete_pouring{ background: #fef9c3; color: #92400e; }
    .dark .ndd-item-type-tag.work_request    { background: rgba(37,99,235,0.2); color: #93c5fd; }
    .dark .ndd-item-type-tag.concrete_pouring{ background: rgba(217,119,6,0.2); color: #fcd34d; }

    /* ── Unread dot ── */
    .ndd-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 4px;
        transition: background 0.2s;
    }
    .ndd-dot.unread { background: var(--notif-accent); box-shadow: 0 0 6px rgba(234,88,12,0.5); }
    .ndd-dot.read   { background: transparent; border: 1.5px solid var(--notif-border); }

    /* ── Empty state ── */
    .ndd-empty {
        padding: 48px 24px 40px;
        text-align: center;
    }
    .ndd-empty-icon-wrap {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        background: var(--notif-bg3);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        font-size: 26px;
        color: var(--notif-muted);
    }
    .ndd-empty-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--notif-text);
        margin-bottom: 5px;
    }
    .ndd-empty-sub {
        font-size: 12px;
        color: var(--notif-muted);
        line-height: 1.5;
    }

    /* ── Footer ── */
    .ndd-footer {
        padding: 10px 18px;
        border-top: 1px solid var(--notif-border);
        background: var(--notif-bg2);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ndd-footer-link {
        font-size: 12px;
        font-weight: 600;
        color: var(--notif-accent);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: opacity 0.15s;
    }
    .ndd-footer-link:hover { opacity: 0.75; }
    .ndd-footer-count {
        font-size: 11px;
        color: var(--notif-muted);
    }
</style>

<div x-data="navigationComponent()" x-init="init()">
    <nav class="fixed top-0 left-0 right-0 z-50 border-b border-orange-200/50 dark:border-orange-800/50 shadow-lg"
         style="background: linear-gradient(90deg, #EA580C 0%, #F97316 50%, #EA580C 100%);"
         @scroll.window="isScrolled = (window.pageYOffset > 10)"
         :class="{ 'backdrop-blur-md bg-orange-400/90 dark:bg-orange-900/90': isScrolled }">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                <!-- Left Side -->
                <div class="flex items-center gap-4">
                    <button @click="$store.sidebar.toggle()"
                        class="p-3 rounded-xl text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 group"
                        style="background: linear-gradient(135deg, #EA580C 0%, #DC2626 100%);">
                        <i class="fas fa-bars-staggered transition-transform duration-200 group-hover:rotate-12"></i>
                    </button>

                    <a href="{{ $dashboardRoute }}" class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-white rounded-2xl flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                            <img src="{{ asset('assets/app_logo.PNG') }}" alt="App Logo" class="h-8 w-8 object-contain" />
                        </div>
                        <span class="text-2xl font-black tracking-tight text-white">
                            {{ config('app.name', 'Laravel') }}
                        </span>
                    </a>
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-3">

                    <!-- ══════════════════════════════════
                         NOTIFICATION BELL + DROPDOWN
                    ══════════════════════════════════ -->
                    <div class="relative">
                        <button @click="toggleNotifications()"
                                class="relative p-2 rounded-xl hover:bg-white/15 transition-colors duration-200">
                            <i class="fas fa-bell text-white text-xl"></i>
                            <div x-show="unreadCount > 0"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-0"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center animate-pulse">
                                <span class="text-white text-xs font-bold" x-text="unreadCount"></span>
                            </div>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="notificationOpen"
                            @click.away="notificationOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-3"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-3"
                            class="ndd-shell"
                            x-data="{ activeTab: 'all' }">

                            <!-- Animated gradient top bar -->
                            <div class="ndd-glow-bar"></div>

                            <!-- Header -->
                            <div class="ndd-header">
                                <div class="ndd-header-left">
                                    <div class="ndd-header-icon">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                    <div>
                                        <div class="ndd-header-title">Notifications</div>
                                        <div class="ndd-header-sub" x-text="notifications.length + ' total'"></div>
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <!-- Spinner -->
                                    <div x-show="loading" class="ndd-loader"></div>

                                    <!-- Mark all read pill -->
                                    <button x-show="unreadCount > 0"
                                            @click.stop="markAllRead()"
                                            class="ndd-badge-unread">
                                        <span class="pulse-dot"></span>
                                        <span x-text="unreadCount + ' new'"></span>
                                    </button>

                                    <!-- All read badge -->
                                    <span x-show="!loading && allRead && notifications.length > 0"
                                        class="ndd-badge-all-read">
                                        <i class="fas fa-check-circle"></i> All read
                                    </span>
                                </div>
                            </div>

                            <!-- Tabs -->
                            <div class="ndd-tabs">
                                <button class="ndd-tab"
                                        :class="{ active: activeTab === 'all' }"
                                        @click="activeTab = 'all'">
                                    All
                                    <span x-show="notifications.length > 0" class="ndd-tab-count"
                                        x-text="notifications.length"
                                        style="background: var(--notif-bg3); color: var(--notif-muted);"></span>
                                </button>
                                <button class="ndd-tab"
                                        :class="{ active: activeTab === 'unread' }"
                                        @click="activeTab = 'unread'">
                                    Unread
                                    <span x-show="unreadCount > 0" class="ndd-tab-count" x-text="unreadCount"></span>
                                </button>
                            </div>

                            <!-- List -->
                            <div class="ndd-list">
                                <template x-for="notif in notifications.filter(n => activeTab === 'all' || !n.is_read)"
                                        :key="notif.id">
                                    <a :href="notif.link || '#'"
                                    @click="notif.link ? handleNotificationClick($event, notif) : $event.preventDefault()"
                                    class="ndd-item"
                                    :class="notif.is_read ? 'read' : 'unread'">

                                        <!-- Icon -->
                                        <div class="ndd-item-icon" :class="'type-' + (notif.type || 'default')">
                                            <template x-if="notif.type === 'work_request'">
                                                <i class="fas fa-file-alt"></i>
                                            </template>
                                            <template x-if="notif.type === 'concrete_pouring'">
                                                <i class="fas fa-hard-hat"></i>
                                            </template>
                                            <template x-if="!notif.type || (notif.type !== 'work_request' && notif.type !== 'concrete_pouring')">
                                                <i class="fas fa-bell"></i>
                                            </template>
                                            <!-- Pulse ring for unread -->
                                            <div x-show="!notif.is_read" class="unread-ring"></div>
                                        </div>

                                        <!-- Body -->
                                        <div class="ndd-item-body">
                                            <div class="ndd-item-title" x-text="notif.title"></div>
                                            <div class="ndd-item-msg"   x-text="notif.message"></div>
                                            <div class="ndd-item-meta">
                                                <span class="ndd-item-time">
                                                    <i class="fas fa-clock" style="font-size:9px;"></i>
                                                    <span x-text="notif.created_at"></span>
                                                </span>
                                                <span x-show="notif.type"
                                                    class="ndd-item-type-tag"
                                                    :class="notif.type"
                                                    x-text="notif.type === 'work_request' ? 'Work Request' : notif.type === 'concrete_pouring' ? 'Concrete' : notif.type">
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Unread indicator -->
                                        <div class="ndd-dot" :class="notif.is_read ? 'read' : 'unread'"></div>
                                    </a>
                                </template>

                                <!-- Empty state -->
                                <div x-show="notifications.filter(n => activeTab === 'all' || !n.is_read).length === 0"
                                    class="ndd-empty">
                                    <div class="ndd-empty-icon-wrap">
                                        <i class="fas fa-bell-slash"></i>
                                    </div>
                                    <div class="ndd-empty-title"
                                        x-text="activeTab === 'unread' ? 'All caught up!' : 'No notifications yet'">
                                    </div>
                                    <div class="ndd-empty-sub"
                                        x-text="activeTab === 'unread' ? 'You have no unread notifications.' : 'Notifications about work requests and approvals will appear here.'">
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="ndd-footer">
                                <a href="{{ route('notifications.page') }}" class="ndd-footer-link">
                                    View all <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                                </a>
                                <span class="ndd-footer-count"
                                    x-text="notifications.filter(n => n.is_read).length + ' read'">
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════
                         PROFILE BUTTON + DROPDOWN
                    ══════════════════════════════════ -->
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen"
                                class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-200 hover:bg-white/15 group">
                            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shadow">
                                <span class="text-sm font-bold text-white" x-text="userInitial">
                                    {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'G' }}
                                </span>
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-bold text-white leading-tight" x-text="userName">
                                    {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                                </div>
                                <div class="text-xs text-orange-100 leading-tight">System User</div>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-white/75 transition-transform duration-200"
                               :class="{ 'rotate-180': profileOpen }"></i>
                        </button>

                        <!-- Profile Dropdown -->
                        <div x-show="profileOpen"
                             @click.away="profileOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                             class="nd-dropdown nd-profile-dropdown">

                            <!-- Accent bar -->
                            <div class="nd-accent-bar"></div>

                            <!-- Profile hero -->
                            <div class="nd-profile-hero">
                                <div class="nd-profile-avatar">
                                    <span x-text="userInitial">{{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'G' }}</span>
                                    <div class="nd-profile-avatar-dot"></div>
                                </div>
                                <div>
                                    <div class="nd-profile-name" x-text="userName">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
                                    <div class="nd-profile-role">
                                        <i class="fas fa-star" style="font-size: 9px;"></i> System User
                                    </div>
                                </div>
                            </div>

                            <!-- Menu -->
                            <div class="nd-menu-body">

                                <!-- My Profile -->
                                <a :href="profileEditRoute" class="nd-menu-item">
                                    <div class="nd-menu-icon blue">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="nd-menu-item-label">
                                        <span>My Profile</span>
                                        <span class="sub">Manage account settings</span>
                                    </div>
                                    <i class="fas fa-chevron-right" style="font-size: 11px; color: var(--nd-muted);"></i>
                                </a>

                                <!-- Dark Mode Toggle -->
                                <button @click="$store.darkMode.toggle()" class="nd-menu-item">
                                    <div class="nd-menu-icon green">
                                        <template x-if="$store.darkMode.on">
                                            <i class="fas fa-sun" style="color: #f59e0b;"></i>
                                        </template>
                                        <template x-if="!$store.darkMode.on">
                                            <i class="fas fa-moon"></i>
                                        </template>
                                    </div>
                                    <div class="nd-menu-item-label">
                                        <span x-text="$store.darkMode.on ? 'Light Mode' : 'Dark Mode'">Dark Mode</span>
                                        <span class="sub">Switch theme appearance</span>
                                    </div>
                                    <!-- Toggle switch -->
                                    <div class="nd-toggle-track" :class="{ 'on': $store.darkMode.on }">
                                        <div class="nd-toggle-thumb"></div>
                                    </div>
                                </button>

                                <div class="nd-divider"></div>

                                <!-- Logout -->
                                <button @click="logout()" class="nd-menu-item">
                                    <div class="nd-menu-icon red">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </div>
                                    <div class="nd-menu-item-label">
                                        <span>Sign Out</span>
                                        <span class="sub">End your current session</span>
                                    </div>
                                    <i class="fas fa-chevron-right" style="font-size: 11px; color: var(--nd-muted);"></i>
                                </button>
                            </div>

                            <!-- Footer -->
                            <div class="nd-dropdown-footer">
                                {{ config('app.name') }} &nbsp;·&nbsp; v1.0
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </nav>
</div>

<script>
    function navigationComponent() {
        return {
            isScrolled: false,
            notificationOpen: false,
            profileOpen: false,
            loading: false,
            allRead: false,

            userName: @json(Auth::check() ? Auth::user()->name : 'Guest'),
            userInitial: @json(Auth::check() ? substr(Auth::user()->name, 0, 1) : 'G'),
            unreadCount: 0,
            notifications: [],

            // API routes (server-rendered)
            apiIndex:       '{{ route("notifications.index") }}',
            apiMarkRead:    '{{ url("notifications") }}',   // /{id}/read
            apiMarkAllRead: '{{ route("notifications.mark-all-read") }}',
            apiUnreadCount: '{{ route("notifications.unread-count") }}',

            profileEditRoute: '{{ route("profile.edit") }}',
            logoutRoute: '{{ route("logout") }}',
            csrfToken: '{{ csrf_token() }}',

            pollInterval: null,

            init() {
                this.fetchNotifications();
                // Poll every 30 seconds for new notifications
                this.pollInterval = setInterval(() => this.fetchUnreadCount(), 30000);
            },

            async fetchNotifications() {
                this.loading = true;
                try {
                    const res  = await fetch(this.apiIndex, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();
                    this.notifications = data;
                    this.updateUnreadCount();
                    this.updateAllReadStatus();
                } catch (e) {
                    console.error('Failed to fetch notifications', e);
                } finally {
                    this.loading = false;
                }
            },

            async fetchUnreadCount() {
                try {
                    const res  = await fetch(this.apiUnreadCount, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();
                    // If count changed, reload full list
                    if (data.count !== this.unreadCount) {
                        this.fetchNotifications();
                    }
                } catch (e) { /* silently fail */ }
            },

            toggleNotifications() {
                this.notificationOpen = !this.notificationOpen;
                if (this.notificationOpen) {
                    this.fetchNotifications();
                }
            },

            async handleNotificationClick(event, notif) {
                if (!notif.is_read) {
                    notif.is_read = true;
                    this.updateUnreadCount();
                    this.updateAllReadStatus();

                    // Mark as read on server (non-blocking)
                    fetch(`${this.apiMarkRead}/${notif.id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    }).catch(() => {});
                }
                this.notificationOpen = false;
            },

            async markAllRead() {
                this.notifications.forEach(n => n.is_read = true);
                this.updateUnreadCount();
                this.updateAllReadStatus();

                await fetch(this.apiMarkAllRead, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                }).catch(() => {});
            },

            updateUnreadCount() {
                this.unreadCount = this.notifications.filter(n => !n.is_read).length;
            },

            updateAllReadStatus() {
                this.allRead = this.notifications.length > 0 && this.unreadCount === 0;
            },

            logout() {
                if (confirm('Are you sure you want to log out?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = this.logoutRoute;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = this.csrfToken;
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
    }
</script>