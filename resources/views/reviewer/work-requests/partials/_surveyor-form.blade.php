<div class="wrd-card" style="border-top: 3px solid #c084fc;">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon purple">
            <i class="fas fa-drafting-compass" style="color:#c084fc;"></i>
        </div>
        <span class="wrd-card-title">Surveyor — Submit Findings</span>
    </div>
    <div class="wrd-card-body">
        <form method="POST" action="{{ route('reviewer.work-requests.store-survey', $workRequest) }}"
              class="space-y-4">
            @csrf

            @if(session('success'))
                <div class="p-3 rounded-lg bg-green-100 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div>
                <label class="wrd-info-label block mb-1">
                    Surveyor Name <span style="color:#f87171;">*</span>
                </label>
                <input type="text" name="surveyor_name"
                       value="{{ old('surveyor_name', $workRequest->surveyor_name ?? Auth::user()->name) }}"
                       placeholder="Enter surveyor name"
                       required
                       style="width:100%; padding:9px 12px; border-radius:8px;
                              background:var(--wr-surface2); border:1px solid var(--wr-border);
                              color:var(--wr-text); font-size:14px; outline:none;"
                />
                @error('surveyor_name')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="wrd-info-label block mb-1">Findings</label>
                <textarea name="findings_surveyor" rows="4"
                          placeholder="Enter survey findings..."
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('findings_surveyor', $workRequest->findings_surveyor) }}</textarea>
                @error('findings_surveyor')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="wrd-info-label block mb-1">Recommendation</label>
                <textarea name="recommendation_surveyor" rows="3"
                          placeholder="Enter recommendation..."
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('recommendation_surveyor', $workRequest->recommendation_surveyor) }}</textarea>
                @error('recommendation_surveyor')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            @include('reviewer.work-requests.partials._signature-pad', [
                'prefix'     => 'sv',
                'radioName'  => 'sv_signature_mode',
                'hiddenName' => 'surveyor_signature',
            ])

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit"
                        style="padding:9px 24px; background:#a855f7; color:#fff;
                               border:none; border-radius:8px; font-size:13px;
                               font-weight:600; cursor:pointer;">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Survey
                </button>
            </div>
        </form>
    </div>
</div>