{{-- resources/views/reviewer/dashboard-partials/quick-actions.blade.php --}}
<div>
    <h2 class="db-section-heading">
        <i class="fas fa-bolt text-blue-500 dark:text-blue-400"></i>
        Quick Actions
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        <a href="{{ route('reviewer.work-requests.index') }}" class="db-action-card blue">
            <div class="db-action-overlay"></div>
            <div class="db-action-icon blue"><i class="fas fa-list-check"></i></div>
            <div class="db-action-title">View All Requests</div>
            <div class="db-action-desc">See all work requests to review</div>
        </a>

        <a href="#" class="db-action-card green">
            <div class="db-action-overlay"></div>
            <div class="db-action-icon green"><i class="fas fa-filter"></i></div>
            <div class="db-action-title">Filter Requests</div>
            <div class="db-action-desc">Find requests by status</div>
        </a>

        <a href="#" class="db-action-card purple">
            <div class="db-action-overlay"></div>
            <div class="db-action-icon purple"><i class="fas fa-chart-pie"></i></div>
            <div class="db-action-title">View Analytics</div>
            <div class="db-action-desc">Review statistics & trends</div>
        </a>

        <a href="#" class="db-action-card orange">
            <div class="db-action-overlay"></div>
            <div class="db-action-icon orange"><i class="fas fa-cog"></i></div>
            <div class="db-action-title">My Settings</div>
            <div class="db-action-desc">Configure preferences</div>
        </a>

    </div>
</div>