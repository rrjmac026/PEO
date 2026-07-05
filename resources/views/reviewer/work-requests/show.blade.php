<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Work Request Review') }}
            </h2>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('reviewer.work-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    @push('styles')
        @include('reviewer.work-requests.show-partials._styles')
    @endpush

    <div class="py-8 wrd-wrap">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @include('reviewer.work-requests.show-partials._hero')
            @include('reviewer.work-requests.show-partials._project-information')
            @include('reviewer.work-requests.show-partials._pay-item-details')
            @include('reviewer.work-requests.show-partials._reception')

            <div class="wrd-section-divider"></div>

            @include('reviewer.work-requests.show-partials._inspection-results')

            <div class="wrd-section-divider"></div>

            @include('reviewer.work-requests.show-partials._action-panel')

        </div>
    </div>

    @push('scripts')
        @include('reviewer.work-requests.show-partials._scripts')
    @endpush

</x-app-layout>