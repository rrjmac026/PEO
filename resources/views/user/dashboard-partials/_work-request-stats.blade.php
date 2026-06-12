{{-- ============================================================
     Work Request Stats Grid
     ============================================================ --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Total Work Requests</p>
                <p class="db-stat-value">{{ $workRequestStats['total'] }}</p>
                <p class="db-stat-sub">All requests</p>
            </div>
            <div class="db-icon-tray blue"><i class="fas fa-file-contract"></i></div>
        </div>
        <div class="db-stat-foot blue">
            <a href="{{ route('user.work-requests.index') }}">View All <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Pending</p>
                <p class="db-stat-value">{{ $workRequestStats['submitted'] }}</p>
                <p class="db-stat-sub">Awaiting review</p>
            </div>
            <div class="db-icon-tray blue"><i class="fas fa-hourglass-half"></i></div>
        </div>
        <div class="db-stat-foot blue">
            <a href="#">Browse <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Approved</p>
                <p class="db-stat-value">{{ $workRequestStats['approved'] }}</p>
                <p class="db-stat-sub">Completed</p>
            </div>
            <div class="db-icon-tray green"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="db-stat-foot green">
            <a href="#">View <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Rejected</p>
                <p class="db-stat-value">{{ $workRequestStats['rejected'] }}</p>
                <p class="db-stat-sub">Need revision</p>
            </div>
            <div class="db-icon-tray purple"><i class="fas fa-exclamation-circle"></i></div>
        </div>
        <div class="db-stat-foot purple">
            <a href="#">Review <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>

</div>