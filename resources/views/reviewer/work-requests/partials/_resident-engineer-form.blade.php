<div class="wrd-card" style="border-top: 3px solid #34d399;">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon green">
            <i class="fas fa-hard-hat" style="color:#34d399;"></i>
        </div>
        <span class="wrd-card-title">Resident Engineer — Submit Review</span>
    </div>
    <div class="wrd-card-body">
        <form method="POST" action="{{ route('reviewer.work-requests.store-engineer-review', $workRequest) }}"
              class="space-y-4">
            @csrf

            @if(session('success'))
                <div class="p-3 rounded-lg bg-green-100 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div>
                <label class="wrd-info-label block mb-1">
                    Engineer Name <span style="color:#f87171;">*</span>
                </label>
                <input type="text" name="resident_engineer_name"
                       value="{{ old('resident_engineer_name', $workRequest->resident_engineer_name ?? Auth::user()->name) }}"
                       placeholder="Enter engineer name"
                       required
                       style="width:100%; padding:9px 12px; border-radius:8px;
                              background:var(--wr-surface2); border:1px solid var(--wr-border);
                              color:var(--wr-text); font-size:14px; outline:none;"
                />
                @error('resident_engineer_name')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="wrd-info-label block mb-1">Findings</label>
                <textarea name="findings_engineer" rows="4"
                          placeholder="Enter findings..."
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('findings_engineer', $workRequest->findings_engineer) }}</textarea>
                @error('findings_engineer')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="wrd-info-label block mb-1">Recommendation</label>
                <textarea name="recommendation_engineer" rows="3"
                          placeholder="Enter recommendation..."
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('recommendation_engineer', $workRequest->recommendation_engineer) }}</textarea>
                @error('recommendation_engineer')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            @include('reviewer.work-requests.partials._signature-pad', [
                'prefix'     => 're',
                'radioName'  => 're_signature_mode',
                'hiddenName' => 'resident_engineer_signature',
            ])

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit"
                        style="padding:9px 24px; background:#10b981; color:#fff;
                               border:none; border-radius:8px; font-size:13px;
                               font-weight:600; cursor:pointer;">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Review
                </button>
            </div>
        </form>
    </div>
</div>