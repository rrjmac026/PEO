{{-- ============================================================
     Quick Actions
     ============================================================ --}}
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