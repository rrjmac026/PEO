@php
    $statusSlug = $workRequest->status ?? 'draft';
    $statusDots = [
        'draft'     => '#94a3b8',
        'submitted' => '#60a5fa',
        'inspected' => '#c084fc',
        'reviewed'  => '#818cf8',
        'approved'  => '#34d399',
        'accepted'  => '#34d399',
        'rejected'  => '#f87171',
    ];
    $dotColor = $statusDots[$statusSlug] ?? '#94a3b8';
@endphp

{{-- ── Hero Bar ── --}}
<div class="wrd-hero">
    <div class="wrd-hero-left">
        <div class="wrd-req-id"># {{ str_pad($workRequest->id, 6, '0', STR_PAD_LEFT) }}</div>
        <div>
            <div class="wrd-project-name">{{ $workRequest->name_of_project }}</div>
            <div class="wrd-project-loc">
                <span>📍</span> {{ $workRequest->project_location }}
            </div>
        </div>
    </div>
    <div class="wrd-hero-right">
        <div class="wrd-status-badge wrd-status--{{ $statusSlug }}">
            <span class="wrd-status-dot" style="background: {{ $dotColor }};"></span>
            {{ ucfirst($workRequest->status) }}
        </div>
    </div>
</div>

{{-- ── Meta chips ── --}}
<div class="wrd-meta-row mb-5">
    <div class="wrd-meta-chip">
        🕐 Created <strong>{{ $workRequest->created_at->format('M d, Y · H:i') }}</strong>
    </div>
    <div class="wrd-meta-chip">
        ✏️ Updated <strong>{{ $workRequest->updated_at->format('M d, Y · H:i') }}</strong>
    </div>
    @if($workRequest->submitted_date)
        <div class="wrd-meta-chip">
            📨 Submitted <strong>{{ $workRequest->submitted_date->format('M d, Y') }}</strong>
        </div>
    @endif
</div>

{{-- ── Flash Messages ── --}}
@if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700 text-sm font-medium">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-700 text-sm font-medium">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
@endif