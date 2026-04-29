{{-- Component: Input field --}}
@props(['label' => null, 'error' => null, 'type' => 'text', 'name'])

<div class="flex-1 min-w-[200px]">
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-medium text-text-secondary mb-1">
            {{ $label }}
        </label>
    @endif
    <input type="{{ $type }}"
           name="{{ $name }}"
           id="{{ $name }}"
           class="w-full rounded-lg border border-border-primary bg-surface-secondary py-2 px-3 text-sm text-text-primary placeholder-text-muted focus:border-action-primary focus:bg-surface-primary focus:outline-none focus:ring-2 focus:ring-token transition"
           {{ $attributes }}>
    @if($error)
        <p class="mt-1 text-xs text-status-error-text">{{ $error }}</p>
    @endif
</div>
