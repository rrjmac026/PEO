{{-- Component: Avatar --}}
@props(['name' => 'U', 'size' => 'md'])

@php
    $sizeClasses = match($size) {
        'sm' => 'h-6 w-6 text-xs',
        'md' => 'h-7 w-7 text-xs',
        'lg' => 'h-10 w-10 text-sm',
        default => 'h-7 w-7 text-xs',
    };
@endphp

<div class="rounded-full bg-surface-tertiary flex items-center justify-center font-bold text-text-primary shrink-0 {{ $sizeClasses }}"
     {{ $attributes }}>
    {{ strtoupper(substr($name, 0, 1)) }}
</div>
