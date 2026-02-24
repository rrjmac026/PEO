<div class="wrd-card" style="border-top: 3px solid #f97316;">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon orange">
            <i class="fas fa-user-tie" style="color:#f97316;"></i>
        </div>
        <span class="wrd-card-title">Provincial Engineer — Add Note</span>
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

            <div>
                <label class="wrd-info-label block mb-1">
                    Notes <span style="color:#f87171;">*</span>
                </label>
                <textarea name="approved_notes" rows="5"
                          placeholder="Enter provincial engineer notes..."
                          required
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('approved_notes', $workRequest->approved_notes) }}</textarea>
                @error('approved_notes')
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
                        style="padding:9px 24px; background:#f97316; color:#fff;
                               border:none; border-radius:8px; font-size:13px;
                               font-weight:600; cursor:pointer;">
                    <i class="fas fa-pen-fancy mr-2"></i> Save Note
                </button>
            </div>
        </form>
    </div>
</div>