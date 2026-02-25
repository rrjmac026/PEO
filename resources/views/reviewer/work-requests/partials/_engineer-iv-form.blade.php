<div class="wrd-card" style="border-top: 3px solid #818cf8;">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon purple">
            <i class="fas fa-user-check" style="color:#818cf8;"></i>
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

            {{-- Reviewed By (auto-filled from Auth) --}}
            <div>
                <label class="wrd-info-label block mb-1">Engineer IV Name</label>
                <input type="text"
                       value="{{ Auth::user()->name }}"
                       disabled
                       style="width:100%; padding:9px 12px; border-radius:8px;
                              background:var(--wr-surface2); border:1px solid var(--wr-border);
                              color:var(--wr-muted); font-size:14px; outline:none; cursor:not-allowed;">
            </div>

            {{-- Recommendation Action --}}
            <div>
                <label class="wrd-info-label block mb-1">Recommendation Action</label>
                <textarea name="reviewed_by_recommendation_action" rows="4"
                          placeholder="Enter recommendation action..."
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('reviewed_by_recommendation_action', $workRequest->reviewed_by_recommendation_action) }}</textarea>
                @error('reviewed_by_recommendation_action')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Signature Pad --}}
            @include('reviewer.work-requests.partials._signature-pad', [
                'prefix'     => 'eiv',
                'radioName'  => 'eiv_signature_mode',
                'hiddenName' => 'reviewer_signature',
            ])

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit"
                        style="padding:9px 24px; background:#818cf8; color:#fff;
                               border:none; border-radius:8px; font-size:13px;
                               font-weight:600; cursor:pointer;">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Review
                </button>
            </div>
        </form>
    </div>
</div>