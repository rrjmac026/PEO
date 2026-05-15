{{-- resources/views/reviewer/concrete-pouring/partials/_project-info.blade.php --}}
{{-- Variables expected: $concretePouring --}}

<div class="cp-card">
    <div class="cp-card-head">
        <div class="cp-card-head-icon cyan"><i class="fas fa-hard-hat"></i></div>
        <span class="cp-card-title">Project Information</span>
    </div>
    <div class="cp-card-body">
        <div class="cp-info-grid" style="grid-template-columns:repeat(3,1fr)">
            <div class="cp-info-item">
                <span class="cp-info-label">Part of Structure</span>
                <span class="cp-info-value">{{ $concretePouring->part_of_structure }}</span>
            </div>
            <div class="cp-info-item">
                <span class="cp-info-label">Station / Section</span>
                <span class="cp-info-value {{ !$concretePouring->station_limits_section ? 'empty' : '' }}">
                    {{ $concretePouring->station_limits_section ?? 'Not specified' }}
                </span>
            </div>
            <div class="cp-info-item">
                <span class="cp-info-label">Requested By</span>
                <span class="cp-info-value">{{ $concretePouring->requestedBy?->name ?? '—' }}</span>
            </div>
        </div>
    </div>
</div>
