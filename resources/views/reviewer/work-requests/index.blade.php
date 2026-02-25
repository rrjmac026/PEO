<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Work Requests
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400 capitalize">
                {{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}
            </span>
        </div>
    </x-slot>

    @push('styles')
        @include('reviewer.work-requests.partials._index-styles')
    @endpush

    @php $role = Auth::user()->role; @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ── Flash Messages ── --}}
            @if(session('success'))
                <div class="wri-alert success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="wri-alert error">{{ session('error') }}</div>
            @endif

            {{-- ── Filters ── --}}
            @include('reviewer.work-requests.partials._index-filters')

            {{-- ── Role Notice ── --}}
            @include('reviewer.work-requests.partials._index-notice', ['role' => $role])

            {{-- ── Table ── --}}
            @include('reviewer.work-requests.partials._index-table', ['role' => $role])

            {{-- ── Pagination ── --}}
            @if($workRequests->hasPages())
                <div class="wri-pagination">
                    {{ $workRequests->withQueryString()->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>