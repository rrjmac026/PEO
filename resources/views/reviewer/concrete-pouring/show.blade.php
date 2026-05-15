{{-- resources/views/reviewer/concrete-pouring/show.blade.php --}}
<x-app-layout>

    @push('styles')
        @include('user.concrete-pouring._cp-styles')
        @include('reviewer.concrete-pouring.partials._reviewer-styles')
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Concrete Pouring — Review
            </h2>
            <a href="{{ route('reviewer.concrete-pouring.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Queue
            </a>
        </div>
    </x-slot>

    <div class="py-8 cp-wrap">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @include('reviewer.concrete-pouring.partials._hero')
            @include('reviewer.concrete-pouring.partials._project-info')
            @include('reviewer.concrete-pouring.partials._checklist')
            @include('reviewer.concrete-pouring.partials._pipeline')

            {{-- Not my turn notice --}}
            @if(!$isMyTurn && !in_array($concretePouring->status, ['approved','disapproved']))
                <div class="p-4 rounded-lg text-sm font-medium"
                     style="background:rgba(8,145,178,0.07);border:1px solid rgba(8,145,178,0.3);color:var(--cp-accent)">
                    <i class="fas fa-info-circle mr-2"></i>
                    You are assigned to this request, but it is not currently your turn to review.
                    The current step is <strong>{{ $concretePouring->current_step_label }}</strong>.
                </div>
            @endif

        </div>
    </div>

    @push('scripts')
        @include('reviewer.concrete-pouring.partials._scripts')
    @endpush

</x-app-layout>
