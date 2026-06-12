{{-- resources/views/reviewer/dashboard-partials/stats-provincial-engineer.blade.php --}}

{{-- Work Request Stats ── 4-column grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Total Requests</p>
                <p class="db-stat-value">{{ $stats['total'] }}</p>
                <p class="db-stat-sub">In system</p>
            </div>
            <div class="db-icon-tray blue"><i class="fas fa-file-contract"></i></div>
        </div>
        <div class="db-stat-foot blue">
            <a href="{{ route('reviewer.work-requests.index') }}">View All <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Approved</p>
                <p class="db-stat-value">{{ $stats['approved'] }}</p>
                <p class="db-stat-sub">Completed</p>
            </div>
            <div class="db-icon-tray green"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="db-stat-foot green">
            <a href="#">View Approved <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Pending</p>
                <p class="db-stat-value">{{ $stats['pending'] }}</p>
                <p class="db-stat-sub">Awaiting approval</p>
            </div>
            <div class="db-icon-tray blue"><i class="fas fa-hourglass-end"></i></div>
        </div>
        <div class="db-stat-foot blue">
            <a href="#">Review Now <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Rejected</p>
                <p class="db-stat-value">{{ $stats['rejected'] }}</p>
                <p class="db-stat-sub">Not approved</p>
            </div>
            <div class="db-icon-tray purple"><i class="fas fa-times-circle"></i></div>
        </div>
        <div class="db-stat-foot purple">
            <a href="#">View Rejected <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>
</div>

{{-- Concrete Pouring Overview ── 3-column grid --}}
<h2 class="db-section-heading mt-8">
    <i class="fas fa-hard-hat text-blue-500 dark:text-blue-400"></i>
    Concrete Pouring Overview
</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Total Pourings</p>
                <p class="db-stat-value">{{ $stats['cp_total'] }}</p>
                <p class="db-stat-sub">In system</p>
            </div>
            <div class="db-icon-tray blue"><i class="fas fa-cube"></i></div>
        </div>
        <div class="db-stat-foot blue">
            <a href="#">View All <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Approved</p>
                <p class="db-stat-value">{{ $stats['cp_approved'] }}</p>
                <p class="db-stat-sub">Completed</p>
            </div>
            <div class="db-icon-tray green"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="db-stat-foot green">
            <a href="#">View Approved <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Pending</p>
                <p class="db-stat-value">{{ $stats['cp_pending'] }}</p>
                <p class="db-stat-sub">Awaiting approval</p>
            </div>
            <div class="db-icon-tray blue"><i class="fas fa-hourglass-end"></i></div>
        </div>
        <div class="db-stat-foot blue">
            <a href="#">Review Now <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>
</div>