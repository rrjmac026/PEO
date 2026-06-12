<x-app-layout>

    @push('styles')
        @include('user.dashboard-partials._dashboard-styles')
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Welcome Hero --}}
            @include('user.dashboard-partials._welcome-hero')

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <br>

            {{-- Employee Info Alert --}}
            @include('partials.employee-info-alert')

            {{-- Work Request Stats --}}
            @include('user.dashboard-partials._work-request-stats')

            {{-- Concrete Pouring Stats (Employee only) --}}
            @include('user.dashboard-partials._concrete-pouring-stats')

            {{-- Quick Actions --}}
            @include('user.dashboard-partials._quick-actions')

            {{-- Recent Work Requests --}}
            @include('user.dashboard-partials._recent-work-requests')

            {{-- Recent Concrete Pourings (Employee only) --}}
            @include('user.dashboard-partials._recent-concrete-pourings')

        </div>
    </div>

</x-app-layout>