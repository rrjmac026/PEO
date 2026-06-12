{{-- resources/views/reviewer/dashboard.blade.php --}}
<x-app-layout>

    @include('reviewer.dashboard-partials.styles')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- ── Welcome Hero ── --}}
            @include('reviewer.dashboard-partials.hero')

            <br>
            @include('partials.employee-info-alert')

            @include('reviewer.dashboard-partials.action-alerts')

            {{-- ── Stats Grid ── --}}
            @if($role === 'site_inspector')
                @include('reviewer.dashboard-partials.stats-site-inspector')

            @elseif($role === 'surveyor')
                @include('reviewer.dashboard-partials.stats-surveyor')

            @elseif($role === 'resident_engineer')
                @include('reviewer.dashboard-partials.stats-resident-engineer')

            @elseif($role === 'engineeriv')
                @include('reviewer.dashboard-partials.stats-engineeriv')

            @elseif($role === 'engineeriii')
                @include('reviewer.dashboard-partials.stats-engineeriii')

            @elseif($role === 'provincial_engineer')
                @include('reviewer.dashboard-partials.stats-provincial-engineer')
            @endif

            {{-- ── Quick Actions ── --}}
            @include('reviewer.dashboard-partials.quick-actions')

            {{-- ── Recent Requests ── --}}
            @include('reviewer.dashboard-partials.recent-requests')

            {{-- ── Footer Stats ── --}}
            <div class="db-footer-divider pt-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <p class="db-footer-label">Last Updated</p>
                        <p class="db-footer-value">{{ now()->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="db-footer-label">Your Role</p>
                        <p class="db-footer-value">{{ ucfirst(str_replace('_', ' ', $role)) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="db-footer-label">System Status</p>
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-2 h-2 bg-green-500 dark:bg-green-400 rounded-full animate-pulse"></div>
                            <p class="db-footer-value">Operational</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>