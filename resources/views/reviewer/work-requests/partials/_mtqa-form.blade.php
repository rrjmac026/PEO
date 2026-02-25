<div class="wrd-card" style="border-top: 3px solid #f59e0b;">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon orange">
            <i class="fas fa-clipboard-check" style="color:#f59e0b;"></i>
        </div>
        <span class="wrd-card-title">MTQA — Submit Check</span>
    </div>
    <div class="wrd-card-body">
        <form method="POST" action="{{ route('reviewer.work-requests.store-mtqa-check', $workRequest) }}"
              class="space-y-4">
            @csrf

            <div>
                <label class="wrd-info-label block mb-1">Checked By</label>
                <input type="text"
                       value="{{ Auth::user()->name }}"
                       disabled
                       style="width:100%; padding:9px 12px; border-radius:8px;
                              background:var(--wr-surface2); border:1px solid var(--wr-border);
                              color:var(--wr-muted); font-size:14px; outline:none; cursor:not-allowed;">
            </div>

            <div>
                <label class="wrd-info-label block mb-1">Recommended Action</label>
                <textarea name="recommended_action" rows="4"
                          placeholder="Enter recommended action..."
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('recommended_action', $workRequest->recommended_action) }}</textarea>
                @error('recommended_action')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            @include('reviewer.work-requests.partials._signature-pad', [
                'prefix'     => 'mq',
                'radioName'  => 'mq_signature_mode',
                'hiddenName' => 'mtqa_signature',
            ])

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit"
                        style="padding:9px 24px; background:#f59e0b; color:#fff;
                               border:none; border-radius:8px; font-size:13px;
                               font-weight:600; cursor:pointer;">
                    <i class="fas fa-clipboard-check mr-2"></i> Submit MTQA Check
                </button>
            </div>
        </form>
    </div>
</div>