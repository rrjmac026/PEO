<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Work Request Review') }}
            </h2>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('admin.work-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    @include('admin.work-requests.show-partials._styles')

    <div class="py-8 wrd-wrap">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @include('admin.work-requests.show-partials._hero')

            @include('admin.work-requests.show-partials._project-details')

            <div class="wrd-section-divider"></div>

            <div class="wrd-card">
                <div class="wrd-card-head">
                    <div class="wrd-card-head-icon blue">🔍</div>
                    <span class="wrd-card-title">Inspection & Review Results</span>
                </div>
                <div class="wrd-card-body">
                    @include('admin.work-requests.show-partials._inspection-results')
                </div>
            </div>

            @include('admin.work-requests.show-partials._review-pipeline')

        </div>
    </div>
</x-app-layout>