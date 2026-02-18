{{-- sidebar-admin.blade.php --}}
<style>
    /* ══════════════════════════════════════════
       SIDEBAR DESIGN TOKENS (mirrors dashboard)
    ══════════════════════════════════════════ */
    :root {
        --sb-surface:      #ffffff;
        --sb-surface2:     #f8fafc;
        --sb-border:       #e2e8f0;
        --sb-text:         #0f172a;
        --sb-text-sec:     #334155;
        --sb-muted:        #64748b;
        --sb-shadow:       0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --sb-accent:       #ea580c;
        --sb-accent-bg:    #fff7ed;
        --sb-accent-hover: #ffedd5;
        --sb-accent-dark:  #fb923c;
    }
    .dark {
        --sb-surface:      #1a1f2e;
        --sb-surface2:     #1e2335;
        --sb-border:       #2a3050;
        --sb-text:         #e8eaf6;
        --sb-text-sec:     #c5cae9;
        --sb-muted:        #7c85a8;
        --sb-accent-bg:    rgba(194,65,12,.18);
        --sb-accent-hover: rgba(194,65,12,.12);
    }

    /* ── Section label ── */
    .sb-section-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--sb-muted);
        padding: 0 16px;
        margin-bottom: 6px;
        margin-top: 16px;
        display: block;
    }

    /* ── Nav link base ── */
    .sb-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 500;
        color: var(--sb-muted);
        text-decoration: none;
        transition: background 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
        position: relative;
        overflow: hidden;
    }

    /* icon wrapper */
    .sb-link .sb-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
        background: transparent;
        transition: background 0.18s ease, color 0.18s ease;
    }

    /* hover */
    .sb-link:hover {
        background: var(--sb-surface2);
        color: var(--sb-text-sec);
        box-shadow: var(--sb-shadow);
    }
    .sb-link:hover .sb-icon {
        background: var(--sb-border);
        color: var(--sb-text);
    }

    /* active — orange accent, mirrors dashboard stat-foot/action-card style */
    .sb-link.active {
        background: var(--sb-accent-bg);
        color: var(--sb-accent);
        font-weight: 600;
        box-shadow: 0 1px 4px rgba(234,88,12,.12);
    }
    .dark .sb-link.active {
        color: var(--sb-accent-dark);
        background: var(--sb-accent-bg);
        box-shadow: 0 1px 6px rgba(194,65,12,.20);
    }
    .sb-link.active .sb-icon {
        background: rgba(234,88,12,.15);
        color: var(--sb-accent);
    }
    .dark .sb-link.active .sb-icon {
        background: rgba(194,65,12,.25);
        color: var(--sb-accent-dark);
    }

    /* active left bar */
    .sb-link.active::before {
        content: '';
        position: absolute;
        left: 0; top: 20%; bottom: 20%;
        width: 3px;
        border-radius: 0 3px 3px 0;
        background: var(--sb-accent);
    }
    .dark .sb-link.active::before {
        background: var(--sb-accent-dark);
    }

    /* ── Divider ── */
    .sb-divider {
        height: 1px;
        background: var(--sb-border);
        margin: 12px 16px;
    }
</style>

<nav class="space-y-1 p-3">

    <span class="sb-section-label">Main</span>

    <a href="{{ route('admin.dashboard') }}"
       class="sb-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-chart-line"></i></span>
        Dashboard
    </a>

    <div class="sb-divider"></div>
    <span class="sb-section-label">Management</span>

    <a href="{{ route('admin.users.index') }}"
       class="sb-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-user-shield"></i></span>
        Users
    </a>

    <a href="{{ route('admin.employees.index') }}"
       class="sb-link {{ request()->routeIs('admin.employees*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-id-badge"></i></span>
        Employees
    </a>

    <div class="sb-divider"></div>
    <span class="sb-section-label">Work</span>

    <a href="{{ route('admin.work-requests.index') }}"
       class="sb-link {{ request()->routeIs('admin.work-requests*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-file-contract"></i></span>
        Work Requests
    </a>

    <a href="{{ route('admin.concrete-pouring.index') }}"
       class="sb-link {{ request()->routeIs('admin.concrete-pouring*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-fill-drip"></i></span>
        Concrete Pouring
    </a>

    <a href="{{ route('admin.work-request-logs.index') }}"
       class="sb-link {{ request()->routeIs('admin.work-request-logs*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-history"></i></span>
        Work Request Logs
    </a>

    <div class="sb-divider"></div>
    <span class="sb-section-label">System</span>

    <a href="#"
       class="sb-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-sliders-h"></i></span>
        Settings
    </a>

</nav>