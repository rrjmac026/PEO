<div class="wrd-card" style="border-top: 3px solid #60a5fa;">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon blue">
            <i class="fas fa-hard-hat" style="color:#60a5fa;"></i>
        </div>
        <span class="wrd-card-title">Site Inspector — Submit Findings</span>
    </div>
    <div class="wrd-card-body">
        <form method="POST" action="{{ route('reviewer.work-requests.store-inspection', $workRequest) }}"
              class="space-y-4">
            @csrf

            @if(session('success'))
                <div class="p-3 rounded-lg bg-green-100 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div>
                <label class="wrd-info-label block mb-1">
                    Inspector Name <span style="color:#f87171;">*</span>
                </label>
                <input type="text" name="inspected_by_site_inspector"
                       value="{{ old('inspected_by_site_inspector', $workRequest->inspected_by_site_inspector ?? Auth::user()->name) }}"
                       placeholder="Enter inspector name"
                       required
                       style="width:100%; padding:9px 12px; border-radius:8px;
                              background:var(--wr-surface2); border:1px solid var(--wr-border);
                              color:var(--wr-text); font-size:14px; outline:none;"
                />
                @error('inspected_by_site_inspector')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="wrd-info-label block mb-1">Findings & Comments</label>
                <textarea name="findings_comments" rows="4"
                          placeholder="Enter findings and comments..."
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('findings_comments', $workRequest->findings_comments) }}</textarea>
                @error('findings_comments')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="wrd-info-label block mb-1">Recommendation</label>
                <textarea name="recommendation" rows="3"
                          placeholder="Enter recommendation..."
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('recommendation', $workRequest->recommendation) }}</textarea>
                @error('recommendation')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            @include('reviewer.work-requests.partials._signature-pad', [
                'prefix'        => 'si',
                'radioName'     => 'si_signature_mode',
                'hiddenName'    => 'site_inspector_signature',
            ])

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit"
                        style="padding:9px 24px; background:#3b82f6; color:#fff;
                               border:none; border-radius:8px; font-size:13px;
                               font-weight:600; cursor:pointer;">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Inspection
                </button>
            </div>
        </form>
    </div>
</div>