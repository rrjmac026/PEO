<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Work Request Details') }}
            </h2>
            <div class="flex gap-2 flex-wrap">
                @if($workRequest->canEdit())
                    <a href="{{ route('user.work-requests.edit', $workRequest) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i>{{ __('Edit') }}
                    </a>
                @endif
                <a href="{{ route('user.work-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    @push('styles')
        @include('user.work-requests.partials._show-styles')
    @endpush

    @php
        $statusSlug = $workRequest->status ?? 'draft';
        $statusDots = [
            'draft'     => '#94a3b8',
            'submitted' => '#60a5fa',
            'inspected' => '#c084fc',
            'reviewed'  => '#818cf8',
            'approved'  => '#34d399',
            'accepted'  => '#34d399',
            'rejected'  => '#f87171',
        ];
        $dotColor = $statusDots[$statusSlug] ?? '#94a3b8';
    @endphp

    <div class="py-8 wrd-wrap">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @include('user.work-requests.partials._show-hero', compact('statusSlug', 'dotColor'))

            {{-- Meta chips --}}
            <div class="wrd-meta-row mb-5">
                <div class="wrd-meta-chip">
                    🕐 Created <strong>{{ $workRequest->created_at->format('M d, Y · H:i') }}</strong>
                </div>
                <div class="wrd-meta-chip">
                    ✏️ Updated <strong>{{ $workRequest->updated_at->format('M d, Y · H:i') }}</strong>
                </div>
                @if($workRequest->submitted_date)
                    <div class="wrd-meta-chip">
                        📨 Submitted <strong>{{ $workRequest->submitted_date->format('M d, Y') }}</strong>
                    </div>
                @endif
            </div>

            @include('user.work-requests.partials._show-project-info')
            @include('user.work-requests.partials._show-request-details')
            @include('user.work-requests.partials._show-pay-items')
            @include('user.work-requests.partials._show-submission')
            @include('user.work-requests.partials._show-activity-log')
            @include('user.work-requests.partials._show-danger-zone')

        </div>
    </div>
</x-app-layout>