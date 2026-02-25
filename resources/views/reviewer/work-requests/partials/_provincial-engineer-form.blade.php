<div class="wrd-card" style="border-top: 3px solid #14b8a6;">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon teal">
            <i class="fas fa-user-tie" style="color:#14b8a6;"></i>
        </div>
        <span class="wrd-card-title">Provincial Engineer — Approval</span>
    </div>
    <div class="wrd-card-body">
        <form method="POST" action="{{ route('reviewer.work-requests.store-provincial-note', $workRequest) }}"
              class="space-y-4">
            @csrf

            @if(session('success'))
                <div class="p-3 rounded-lg bg-green-100 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Auto-filled name display --}}
            <div>
                <label class="wrd-info-label block mb-1">Provincial Engineer</label>
                <input type="text"
                       value="{{ Auth::user()->name }}"
                       disabled
                       style="width:100%; padding:9px 12px; border-radius:8px;
                              background:var(--wr-surface2); border:1px solid var(--wr-border);
                              color:var(--wr-muted); font-size:14px; outline:none; cursor:not-allowed;">
            </div>

            {{-- Recommendation Action --}}
            <div>
                <label class="wrd-info-label block mb-1">
                    Recommendation Action <span style="color:#f87171;">*</span>
                </label>
                <textarea name="approved_recommendation_action" rows="5"
                          placeholder="Enter recommendation action..."
                          required
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('approved_recommendation_action', $workRequest->approved_recommendation_action) }}</textarea>
                @error('approved_recommendation_action')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            @include('reviewer.work-requests.partials._signature-pad', [
                'prefix'     => 'pe',
                'radioName'  => 'pe_signature_mode',
                'hiddenName' => 'approved_signature',
            ])

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit"
                        style="padding:9px 24px; background:#14b8a6; color:#fff;
                               border:none; border-radius:8px; font-size:13px;
                               font-weight:600; cursor:pointer;">
                    <i class="fas fa-check-circle mr-2"></i> Submit Approval
                </button>
            </div>
        </form>
    </div>
</div>