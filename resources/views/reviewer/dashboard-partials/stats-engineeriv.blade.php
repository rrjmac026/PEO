{{-- resources/views/reviewer/dashboard-partials/stats-engineeriv.blade.php --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Pending MTQA Check</p>
                <p class="db-stat-value">{{ $stats['pending'] }}</p>
                <p class="db-stat-sub">Awaiting your review</p>
            </div>
            <div class="db-icon-tray blue"><i class="fas fa-clipboard-list"></i></div>
        </div>
        <div class="db-stat-foot blue">
            <a href="{{ route('reviewer.work-requests.index') }}">View All <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Checked</p>
                <p class="db-stat-value">{{ $stats['done'] }}</p>
                <p class="db-stat-sub">Completed</p>
            </div>
            <div class="db-icon-tray green"><i class="fas fa-circle-check"></i></div>
        </div>
        <div class="db-stat-foot green">
            <a href="#">View History <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="db-stat-label">Total Requests</p>
                <p class="db-stat-value">{{ $stats['total'] }}</p>
                <p class="db-stat-sub">In system</p>
            </div>
            <div class="db-icon-tray purple"><i class="fas fa-file-contract"></i></div>
        </div>
        <div class="db-stat-foot purple">
            <a href="#">View All <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>
</div>