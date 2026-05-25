@if($workRequest->logs->count() > 0)
    <div class="wrd-card">
        <div class="wrd-card-head">
            <div class="wrd-card-head-icon slate">🕐</div>
            <span class="wrd-card-title">Activity Log</span>
            <span style="margin-left:auto; font-size:12px; color:var(--wr-muted); font-family:'Inter',sans-serif;">
                {{ $workRequest->logs->count() }} {{ Str::plural('event', $workRequest->logs->count()) }}
            </span>
        </div>
        <div class="wrd-card-body" style="padding-top:8px; padding-bottom:8px;">
            @foreach($workRequest->logs as $log)
                <div class="wrd-log-item" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    <div class="wrd-log-dot-wrap">
                        <div class="wrd-log-dot"></div>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div class="wrd-log-event">{{ ucfirst(str_replace('_', ' ', $log->event)) }}</div>
                        @if($log->description)
                            <div class="wrd-log-desc">{{ $log->description }}</div>
                        @endif
                    </div>
                    <div class="wrd-log-time">{{ $log->created_at->diffForHumans() }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif