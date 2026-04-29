{{-- Component: Badge for event types --}}
@props(['event' => 'status_changed', 'label' => null])

@php
    $eventLabel = $label ?? ucwords(str_replace('_', ' ', $event));
    $dotColorMap = [
        'submitted'      => 'var(--event-submitted-dot)',
        'updated'        => 'var(--event-updated-dot)',
        'deleted'        => 'var(--event-deleted-dot)',
        'assigned'       => 'var(--event-assigned-dot)',
        're_reviewed'    => 'var(--event-re-reviewed-dot)',
        'pe_noted'       => 'var(--event-pe-noted-dot)',
        'mtqa_decided'   => 'var(--event-mtqa-decided-dot)',
        'approved'       => 'var(--event-approved-dot)',
        'disapproved'    => 'var(--event-disapproved-dot)',
        'status_changed' => 'var(--event-status-changed-dot)',
    ];
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold badge tag-event-{{ $event }}"
      {{ $attributes }}>
    <span class="h-1.5 w-1.5 rounded-full badge-dot"
          style="background-color: {{ $dotColorMap[$event] ?? 'currentColor' }}"></span>
    {{ $eventLabel }}
</span>
