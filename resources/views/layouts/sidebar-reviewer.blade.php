{{-- resources/views/layouts/sidebar-reviewer.blade.php --}}
<style>
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

    .sb-section-label {
        font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
        color: var(--sb-muted); padding: 0 16px; margin-bottom: 6px; margin-top: 16px; display: block;
    }
    .sb-link {
        display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 10px;
        font-size: 13.5px; font-weight: 500; color: var(--sb-muted); text-decoration: none;
        transition: background 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
        position: relative; overflow: hidden;
    }
    .sb-link .sb-icon {
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; flex-shrink: 0; background: transparent;
        transition: background 0.18s ease, color 0.18s ease;
    }
    .sb-link:hover { background: var(--sb-surface2); color: var(--sb-text-sec); box-shadow: var(--sb-shadow); }
    .sb-link:hover .sb-icon { background: var(--sb-border); color: var(--sb-text); }
    .sb-link.active { background: var(--sb-accent-bg); color: var(--sb-accent); font-weight: 600; box-shadow: 0 1px 4px rgba(234,88,12,.12); }
    .dark .sb-link.active { color: var(--sb-accent-dark); background: var(--sb-accent-bg); box-shadow: 0 1px 6px rgba(194,65,12,.20); }
    .sb-link.active .sb-icon { background: rgba(234,88,12,.15); color: var(--sb-accent); }
    .dark .sb-link.active .sb-icon { background: rgba(194,65,12,.25); color: var(--sb-accent-dark); }
    .sb-link.active::before {
        content: ''; position: absolute; left: 0; top: 20%; bottom: 20%;
        width: 3px; border-radius: 0 3px 3px 0; background: var(--sb-accent);
    }
    .dark .sb-link.active::before { background: var(--sb-accent-dark); }

    /* Cyan variant for concrete pouring */
    .sb-link.cyan-active.active { background: rgba(8,145,178,.1); color: #0891b2; box-shadow: 0 1px 4px rgba(8,145,178,.12); }
    .dark .sb-link.cyan-active.active { color: #22d3ee; background: rgba(8,145,178,.15); }
    .sb-link.cyan-active.active .sb-icon { background: rgba(8,145,178,.15); color: #0891b2; }
    .dark .sb-link.cyan-active.active .sb-icon { color: #22d3ee; }
    .sb-link.cyan-active.active::before { background: #0891b2; }
    .dark .sb-link.cyan-active.active::before { background: #22d3ee; }

    .sb-divider { height: 1px; background: var(--sb-border); margin: 12px 16px; }
    .sb-role-badge {
        display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600;
        padding: 4px 10px; border-radius: 20px; background: var(--sb-accent-bg); color: var(--sb-accent);
        margin: 0 16px 4px;
    }
    .dark .sb-role-badge { color: var(--sb-accent-dark); }

    .sb-sub-link {
        display: flex; align-items: center; gap: 10px;
        padding: 7px 16px 7px 44px; border-radius: 8px;
        font-size: 12.5px; font-weight: 500; color: var(--sb-muted);
        text-decoration: none; transition: background 0.18s ease, color 0.18s ease; position: relative;
    }
    .sb-sub-link:hover { background: var(--sb-surface2); color: var(--sb-text-sec); }
    .sb-sub-link.active { color: var(--sb-accent); font-weight: 600; }
    .dark .sb-sub-link.active { color: var(--sb-accent-dark); }
    .sb-sub-link.cyan-sub.active { color: #0891b2; }
    .dark .sb-sub-link.cyan-sub.active { color: #22d3ee; }
    .sb-sub-link .sb-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; flex-shrink: 0; opacity: 0.5; }
    .sb-sub-link.active .sb-dot { opacity: 1; }

    /* Badge for queue count */
    .sb-count {
        margin-left: auto; font-size: 11px; font-weight: 700;
        background: #0891b2; color: white;
        padding: 2px 7px; border-radius: 10px; line-height: 1.4;
    }
    .dark .sb-count { background: #22d3ee; color: #0f172a; }
</style>

@php $role = Auth::user()->role; @endphp

<nav class="space-y-1 p-3">

    {{-- Role Badge --}}
    <div class="sb-role-badge">
        @if($role === 'site_inspector')       <i class="fas fa-hard-hat"></i> Site Inspector
        @elseif($role === 'surveyor')          <i class="fas fa-drafting-compass"></i> Surveyor
        @elseif($role === 'resident_engineer') <i class="fas fa-hard-hat"></i> Resident Engineer
        @elseif($role === 'provincial_engineer')<i class="fas fa-user-tie"></i> Provincial Engineer
        @elseif($role === 'mtqa')              <i class="fas fa-clipboard-check"></i> ME / MTQA
        @elseif($role === 'engineeriii')       <i class="fas fa-drafting-compass"></i> Engineer III
        @elseif($role === 'engineeriv')        <i class="fas fa-drafting-compass"></i> Engineer IV
        @else <i class="fas fa-user"></i> {{ ucfirst($role) }}
        @endif
    </div>

    <span class="sb-section-label">Main</span>

    <a href="{{ route('reviewer.dashboard') }}"
       class="sb-link {{ request()->routeIs('reviewer.dashboard') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-chart-line"></i></span>
        Dashboard
    </a>

    <div class="sb-divider"></div>
    <span class="sb-section-label">Work Requests</span>

    <a href="{{ route('reviewer.work-requests.index') }}"
       class="sb-link {{ request()->routeIs('reviewer.work-requests*') && !request('status') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-file-contract"></i></span>
        All Requests
    </a>

    <a href="{{ route('reviewer.work-requests.index', ['status' => 'submitted']) }}"
       class="sb-sub-link {{ request('status') === 'submitted' ? 'active' : '' }}">
        <span class="sb-dot"></span> Submitted
    </a>
    <a href="{{ route('reviewer.work-requests.index', ['status' => 'inspected']) }}"
       class="sb-sub-link {{ request('status') === 'inspected' ? 'active' : '' }}">
        <span class="sb-dot"></span> Inspected
    </a>
    <a href="{{ route('reviewer.work-requests.index', ['status' => 'reviewed']) }}"
       class="sb-sub-link {{ request('status') === 'reviewed' ? 'active' : '' }}">
        <span class="sb-dot"></span> Reviewed
    </a>
    <a href="{{ route('reviewer.work-requests.index', ['status' => 'approved']) }}"
       class="sb-sub-link {{ request('status') === 'approved' ? 'active' : '' }}">
        <span class="sb-dot"></span> Approved
    </a>
    <a href="{{ route('reviewer.work-requests.index', ['status' => 'rejected']) }}"
       class="sb-sub-link {{ request('status') === 'rejected' ? 'active' : '' }}">
        <span class="sb-dot"></span> Rejected
    </a>

    <div class="sb-divider"></div>
    <span class="sb-section-label">Concrete Pouring</span>

    {{-- Main queue link --}}
    <a href="{{ route('reviewer.concrete-pouring.index') }}"
       class="sb-link cyan-active {{ request()->routeIs('reviewer.concrete-pouring*') && !request('status') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-fill-drip"></i></span>
        My Review Queue
    </a>

    {{-- Concrete pouring status filters (passed as query param to index) --}}
    <a href="{{ route('reviewer.concrete-pouring.index', ['status' => 'requested']) }}"
       class="sb-sub-link cyan-sub {{ request()->routeIs('reviewer.concrete-pouring.index') && request('status') === 'requested' ? 'active' : '' }}">
        <span class="sb-dot"></span> Pending
    </a>
    <a href="{{ route('reviewer.concrete-pouring.index', ['status' => 'approved']) }}"
       class="sb-sub-link cyan-sub {{ request()->routeIs('reviewer.concrete-pouring.index') && request('status') === 'approved' ? 'active' : '' }}">
        <span class="sb-dot"></span> Approved
    </a>
    <a href="{{ route('reviewer.concrete-pouring.index', ['status' => 'disapproved']) }}"
       class="sb-sub-link cyan-sub {{ request()->routeIs('reviewer.concrete-pouring.index') && request('status') === 'disapproved' ? 'active' : '' }}">
        <span class="sb-dot"></span> Disapproved
    </a>

    {{-- ── Role-specific My Work section ── --}}
    @if($role === 'site_inspector')
        <div class="sb-divider"></div>
        <span class="sb-section-label">My Work</span>
        <a href="{{ route('reviewer.work-requests.index', ['inspected' => 'pending']) }}"
           class="sb-link {{ request('inspected') === 'pending' ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-clock"></i></span> Pending Inspection
        </a>
        <a href="{{ route('reviewer.work-requests.index', ['inspected' => 'done']) }}"
           class="sb-link {{ request('inspected') === 'done' ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-clipboard-check"></i></span> Inspected
        </a>
    @endif

    @if($role === 'surveyor')
        <div class="sb-divider"></div>
        <span class="sb-section-label">My Work</span>
        <a href="{{ route('reviewer.work-requests.index', ['surveyed' => 'pending']) }}"
           class="sb-link {{ request('surveyed') === 'pending' ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-clock"></i></span> Pending Survey
        </a>
        <a href="{{ route('reviewer.work-requests.index', ['surveyed' => 'done']) }}"
           class="sb-link {{ request('surveyed') === 'done' ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-map-marked-alt"></i></span> Surveyed
        </a>
    @endif

    @if($role === 'resident_engineer')
        <div class="sb-divider"></div>
        <span class="sb-section-label">My Work</span>
        <a href="{{ route('reviewer.work-requests.index', ['reviewed' => 'pending']) }}"
           class="sb-link {{ request('reviewed') === 'pending' ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-clock"></i></span> Pending Review
        </a>
        <a href="{{ route('reviewer.work-requests.index', ['reviewed' => 'done']) }}"
           class="sb-link {{ request('reviewed') === 'done' ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-tasks"></i></span> Reviewed
        </a>
    @endif

    @if($role === 'provincial_engineer')
        <div class="sb-divider"></div>
        <span class="sb-section-label">My Work</span>
        <a href="{{ route('reviewer.work-requests.index', ['noted' => 'pending']) }}"
           class="sb-link {{ request('noted') === 'pending' ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-clock"></i></span> Pending Notes
        </a>
        <a href="{{ route('reviewer.work-requests.index', ['noted' => 'done']) }}"
           class="sb-link {{ request('noted') === 'done' ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-pen-fancy"></i></span> Noted
        </a>
    @endif

    @if(in_array($role, ['mtqa', 'engineeriii', 'engineeriv']))
        <div class="sb-divider"></div>
        <span class="sb-section-label">My Work</span>
        <a href="{{ route('reviewer.concrete-pouring.index') }}"
           class="sb-link cyan-active {{ request()->routeIs('reviewer.concrete-pouring.index') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-clipboard-list"></i></span> Pouring Queue
        </a>
    @endif

    <div class="sb-divider"></div>
    <span class="sb-section-label">Account</span>

    <a href="{{ route('profile.edit') }}"
       class="sb-link {{ request()->routeIs('profile*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-user-circle"></i></span>
        My Profile
    </a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sb-link w-full text-left">
            <span class="sb-icon"><i class="fas fa-sign-out-alt"></i></span>
            Logout
        </button>
    </form>

</nav>