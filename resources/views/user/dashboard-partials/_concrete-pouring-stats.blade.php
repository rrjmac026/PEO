{{-- ============================================================
     Concrete Pouring Stats Grid (Employee only)
     ============================================================ --}}
@if(Auth::user()->employee)
    <div>
        <h2 class="db-section-heading">
            <i class="fas fa-cement text-orange-500 dark:text-orange-400"></i>
            Concrete Pouring Requests
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="db-stat-card">
                <div class="p-6 flex items-center justify-between">
                    <div>
                        <p class="db-stat-label">Total Requests</p>
                        <p class="db-stat-value">{{ $concretePouringStats['total'] }}</p>
                        <p class="db-stat-sub">All requests</p>
                    </div>
                    <div class="db-icon-tray blue"><i class="fas fa-layer-group"></i></div>
                </div>
                <div class="db-stat-foot blue">
                    <a href="{{ route('user.concrete-pouring.index') }}">View All <i class="fas fa-arrow-right text-xs"></i></a>
                </div>
            </div>

            <div class="db-stat-card">
                <div class="p-6 flex items-center justify-between">
                    <div>
                        <p class="db-stat-label">Pending</p>
                        <p class="db-stat-value">{{ $concretePouringStats['pending'] }}</p>
                        <p class="db-stat-sub">Awaiting approval</p>
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
                        <p class="db-stat-value">{{ $concretePouringStats['approved'] }}</p>
                        <p class="db-stat-sub">Ready to proceed</p>
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
                        <p class="db-stat-label">Disapproved</p>
                        <p class="db-stat-value">{{ $concretePouringStats['disapproved'] }}</p>
                        <p class="db-stat-sub">Need revision</p>
                    </div>
                    <div class="db-icon-tray purple"><i class="fas fa-exclamation-circle"></i></div>
                </div>
                <div class="db-stat-foot purple">
                    <a href="#">Review <i class="fas fa-arrow-right text-xs"></i></a>
                </div>
            </div>

        </div>
    </div>
@endif