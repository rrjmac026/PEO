<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create New Work Request') }}
            </h2>
            <a href="{{ route('user.work-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    @push('styles')
        @include('user.work-requests.partials._create-styles')
    @endpush

    <div class="py-8 wr-wrap">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Progress Steps --}}
            <div class="wr-progress-bar mb-6">
                <div class="wr-progress-inner">
                    <div class="wr-step-item active" id="step-1" onclick="wrGoToStep(1)">
                        <div class="wr-step-num">1</div>
                        <span class="wr-step-label">Project Info</span>
                    </div>
                    <div class="wr-step-item" id="step-2" onclick="wrGoToStep(2)">
                        <div class="wr-step-num">2</div>
                        <span class="wr-step-label">Request Details</span>
                    </div>
                    <div class="wr-step-item" id="step-3" onclick="wrGoToStep(3)">
                        <div class="wr-step-num">3</div>
                        <span class="wr-step-label">Reviewer</span>
                    </div>
                    <div class="wr-step-item" id="step-4" onclick="wrGoToStep(4)">
                        <div class="wr-step-num">4</div>
                        <span class="wr-step-label">Pay Items</span>
                    </div>
                    <div class="wr-step-item" id="step-5" onclick="wrGoToStep(5)">
                        <div class="wr-step-num">5</div>
                        <span class="wr-step-label">Review & Submit</span>
                    </div>
                </div>
            </div>

            {{-- Main Card --}}
            <div class="wr-card">
                <div class="wr-card-body">
                    <form id="wr-form" action="{{ route('user.work-requests.store') }}" method="POST" novalidate>
                        @csrf

                        @include('user.work-requests.partials._step-1-project-info')
                        @include('user.work-requests.partials._step-2-request-details')
                        @include('user.work-requests.partials._step-3-reviewer-selection')
                        @include('user.work-requests.partials._step-4-pay-items')
                        @include('user.work-requests.partials._step-5-review')

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating auto-save indicator --}}
    <div class="wr-float-save" id="wr-float-save">
        <span class="wr-float-dot"></span>
        Draft saved
    </div>

    @push('scripts')
        @include('user.work-requests.partials._create-scripts')
    @endpush

</x-app-layout>