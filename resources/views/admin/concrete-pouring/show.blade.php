<x-app-layout>

    @push('styles')
        @include('user.concrete-pouring._cp-styles')
        @include('reviewer.concrete-pouring.partials._reviewer-styles')
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Concrete Pouring — Detail
            </h2>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.concrete-pouring.assign-form', $concretePouring) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    <i class="fas fa-user-plus mr-2"></i> Assign Reviewers
                </a>
                <a href="{{ route('admin.concrete-pouring.print', $concretePouring) }}"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-gray-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                    <i class="fas fa-print mr-2"></i> Print
                </a>
                <a href="{{ route('admin.concrete-pouring.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 cp-wrap">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

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

            {{-- Admin-only: Logs section --}}
            @if($concretePouring->logs->isNotEmpty())
                <div class="cp-card">
                    <div class="cp-card-head">
                        <div class="cp-card-head-icon blue"><i class="fas fa-history"></i></div>
                        <span class="cp-card-title">Activity Log</span>
                        <a href="{{ route('admin.concrete-pouring.logs.show', $concretePouring) }}"
                           class="ml-auto text-xs text-blue-500 hover:underline">
                            View full log →
                        </a>
                    </div>
                    <div class="cp-card-body">
                        <div class="space-y-2">
                            @foreach($concretePouring->logs->take(5) as $log)
                                <div class="flex items-start gap-3 text-sm">
                                    <span class="text-gray-400 text-xs whitespace-nowrap mt-0.5">
                                        {{ $log->created_at->format('M d, Y H:i') }}
                                    </span>
                                    <span style="color:var(--cp-text)">
                                        <strong>{{ $log->user?->name ?? 'System' }}</strong>
                                        — {{ $log->description ?? $log->event }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Admin-only: Danger zone --}}
            <div class="cp-card" style="border-color: rgba(220,38,38,0.3);">
                <div class="cp-card-head">
                    <div class="cp-card-head-icon" style="background:rgba(220,38,38,0.1)">
                        <i class="fas fa-trash" style="color:#dc2626"></i>
                    </div>
                    <span class="cp-card-title" style="color:#dc2626">Danger Zone</span>
                </div>
                <div class="cp-card-body">
                    <form action="{{ route('admin.concrete-pouring.destroy', $concretePouring) }}"
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this concrete pouring request? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash mr-2"></i> Delete This Request
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- No @push('scripts') needed — admin never draws a signature --}}

</x-app-layout>