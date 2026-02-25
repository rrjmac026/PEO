<div class="wrd-hero">
    <div class="wrd-hero-left">
        <div class="wrd-req-id"># {{ str_pad($workRequest->id, 6, '0', STR_PAD_LEFT) }}</div>
        <div>
            <div class="wrd-project-name">{{ $workRequest->name_of_project }}</div>
            <div class="wrd-project-loc">
                <span>📍</span> {{ $workRequest->project_location }}
            </div>
            @if($workRequest->reference_number)
                <div style="font-size:12px; color:var(--wr-muted); margin-top:2px;">
                    🔖 Ref: <strong>{{ $workRequest->reference_number }}</strong>
                </div>
            @endif
        </div>
    </div>
    <div class="wrd-hero-right">
        <div class="wrd-status-badge wrd-status--{{ $statusSlug }}">
            <span class="wrd-status-dot" style="background: {{ $dotColor }};"></span>
            {{ ucfirst($workRequest->status) }}
        </div>
    </div>
</div>