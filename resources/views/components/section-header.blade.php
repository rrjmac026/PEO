{{-- Component: Section header --}}
@props(['title', 'subtitle' => null, 'action' => null])

<div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
        @if($subtitle)
            <p class="flex items-center gap-2 text-sm text-text-secondary mb-2">
                {{ $subtitle }}
            </p>
        @endif
        <h1 class="text-2xl font-bold text-text-primary tracking-tight">{{ $title }}</h1>
    </div>
    @if($action)
        <div class="shrink-0">
            {{ $action }}
        </div>
    @endif
</div>
