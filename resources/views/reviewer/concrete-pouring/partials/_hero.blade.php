{{-- resources/views/reviewer/concrete-pouring/partials/_hero.blade.php --}}
{{-- Variables expected: $concretePouring, $isMyTurn --}}

<div class="cp-hero">
    <div class="cp-hero-left">
        <span class="cp-req-id">{{ $concretePouring->reference_number }}</span>
        <div>
            <div class="cp-project-name">{{ $concretePouring->project_name }}</div>
            <div class="cp-project-loc">
                <i class="fas fa-map-marker-alt text-xs mr-1"></i> {{ $concretePouring->location }}
            </div>
        </div>
    </div>
    <div class="flex items-center gap-3">
        @if($isMyTurn)
            <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
                <i class="fas fa-user-check mr-1"></i> Your Turn
            </span>
        @endif
        <span class="cp-badge {{ $concretePouring->status }}">
            <span class="cp-badge-dot" style="background:currentColor;border-radius:50%"></span>
            {{ ucfirst($concretePouring->status) }}
        </span>
    </div>
</div>

<div class="cp-meta-row">
    <div class="cp-meta-chip">🏗 <strong>{{ $concretePouring->contractor }}</strong></div>
    <div class="cp-meta-chip">📐 <strong>{{ number_format($concretePouring->estimated_volume, 2) }} m³</strong></div>
    <div class="cp-meta-chip">🗓 Pouring: <strong>{{ $concretePouring->pouring_datetime?->format('M d, Y H:i') ?? '—' }}</strong></div>
    <div class="cp-meta-chip">📋 Current Step: <strong>{{ $concretePouring->current_step_label }}</strong></div>
</div>
