{{-- Component: Stat card --}}
@props(['label', 'value', 'type' => 'default'])

@php
    $typeMap = [
        'default'   => ['bg' => 'bg-surface-secondary', 'text' => 'text-gray-900', 'label' => 'text-gray-500'],
        'success'   => ['bg' => 'var(--color-success-bg)', 'text' => 'text-status-success-text', 'label' => 'text-status-success-text'],
        'error'     => ['bg' => 'var(--color-error-bg)', 'text' => 'text-status-error-text', 'label' => 'text-status-error-text'],
        'warning'   => ['bg' => 'var(--color-warning-bg)', 'text' => 'text-status-warning-text', 'label' => 'text-status-warning-text'],
        'info'      => ['bg' => 'var(--color-info-bg)', 'text' => 'text-status-info-text', 'label' => 'text-status-info-text'],
    ];
    $style = $typeMap[$type] ?? $typeMap['default'];
@endphp

<div class="rounded-xl ring-1 px-4 py-3" style="background-color: {{ $style['bg'] }}; border-color: {{ $style['label'] }};">
    <p class="text-xs font-medium uppercase tracking-wider" style="color: {{ $style['label'] }};">{{ $label }}</p>
    <p class="mt-1 text-2xl font-bold" style="color: {{ $style['text'] }};">{{ $value }}</p>
</div>
