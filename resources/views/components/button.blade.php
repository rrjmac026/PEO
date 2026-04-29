{{-- Component: Button --}}
@props(['variant' => 'primary', 'size' => 'md'])

@php
    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
        default => 'px-4 py-2 text-sm',
    };

    $variantClasses = match($variant) {
        'primary' => 'btn btn-primary',
        'secondary' => 'btn btn-secondary',
        default => 'btn btn-primary',
    };
@endphp

<button class="{{ $variantClasses }} {{ $sizeClasses }}" {{ $attributes }}>
    {{ $slot }}
</button>
