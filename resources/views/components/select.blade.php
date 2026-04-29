{{-- Component: Select field --}}
@props(['label' => null, 'name', 'options' => [], 'error' => null])

<div class="min-w-[160px]">
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-medium text-text-secondary mb-1">
            {{ $label }}
        </label>
    @endif
    <select name="{{ $name }}"
            id="{{ $name }}"
            class="w-full rounded-lg border border-border-primary bg-surface-secondary py-2 pl-3 pr-8 text-sm text-text-primary focus:border-action-primary focus:bg-surface-primary focus:outline-none focus:ring-2 focus:ring-token transition appearance-none"
            {{ $attributes }}>
        {{ $slot }}
    </select>
    @if($error)
        <p class="mt-1 text-xs text-status-error-text">{{ $error }}</p>
    @endif
</div>
