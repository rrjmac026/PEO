<div class="wrd-card" style="border-top: 3px solid #0891b2;">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon cyan">
            <i class="fas fa-briefcase" style="color:#0891b2;"></i>
        </div>
        <span class="wrd-card-title">Engineer IV — Submit Review</span>
    </div>
    <div class="wrd-card-body">
        <form method="POST" action="{{ route('reviewer.work-requests.store-engineer-iv-review', $workRequest) }}"
              class="space-y-4">
            @csrf

            @if(session('success'))
                <div class="p-3 rounded-lg bg-green-100 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div>
                <label class="wrd-info-label block mb-1">
                    Engineer IV Name <span style="color:#f87171;">*</span>
                </label>
                <input type="text" name="engineer_iv_name"
                       value="{{ old('engineer_iv_name', $workRequest->engineer_iv_name ?? Auth::user()->name) }}"
                       placeholder="Enter engineer IV name"
                       required
                       style="width:100%; padding:9px 12px; border-radius:8px;
                              background:var(--wr-surface2); border:1px solid var(--wr-border);
                              color:var(--wr-text); font-size:14px; outline:none;"
                />
                @error('engineer_iv_name')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="wrd-info-label block mb-1">Findings</label>
                <textarea name="findings_engineer_iv" rows="4"
                          placeholder="Enter findings..."
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('findings_engineer_iv', $workRequest->findings_engineer_iv) }}</textarea>
                @error('findings_engineer_iv')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="wrd-info-label block mb-1">Recommendation</label>
                <textarea name="recommendation_engineer_iv" rows="3"
                          placeholder="Enter recommendation..."
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('recommendation_engineer_iv', $workRequest->recommendation_engineer_iv) }}</textarea>
                @error('recommendation_engineer_iv')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            @include('reviewer.work-requests.partials._signature-pad', [
                'prefix'     => 'eiv',
                'radioName'  => 'eiv_signature_mode',
                'hiddenName' => 'engineer_iv_signature',
            ])

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit"
                        style="padding:9px 24px; background:#0891b2; color:#fff;
                               border:none; border-radius:8px; font-size:13px;
                               font-weight:600; cursor:pointer;">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Review
                </button>
            </div>
        </form>
    </div>
</div>
