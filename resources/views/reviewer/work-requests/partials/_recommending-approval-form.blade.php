<div class="wrd-card" style="border-top: 3px solid #06b6d4;">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon cyan">
            <i class="fas fa-thumbs-up" style="color:#06b6d4;"></i>
        </div>
        <span class="wrd-card-title">Engineer III — Recommending Approval</span>
    </div>
    <div class="wrd-card-body">
        <form method="POST" action="{{ route('reviewer.work-requests.store-recommending-approval', $workRequest) }}"
              class="space-y-4">
            @csrf

            @if(session('success'))
                <div class="p-3 rounded-lg bg-green-100 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Reviewed By (auto-filled from Auth) --}}
            <div>
                <label class="wrd-info-label block mb-1">Engineer III Name</label>
                <input type="text"
                       value="{{ Auth::user()->name }}"
                       disabled
                       style="width:100%; padding:9px 12px; border-radius:8px;
                              background:var(--wr-surface2); border:1px solid var(--wr-border);
                              color:var(--wr-muted); font-size:14px; outline:none; cursor:not-allowed;">
            </div>

            {{-- Approval Status --}}
            <div>
                <label class="wrd-info-label block mb-1">
                    Recommendation <span style="color:#f87171;">*</span>
                </label>
                <select name="eiii_recommendation" required
                        style="width:100%; padding:9px 12px; border-radius:8px;
                               background:var(--wr-surface2); border:1px solid var(--wr-border);
                               color:var(--wr-text); font-size:14px; outline:none;">
                    <option value="">-- Select recommendation --</option>
                    <option value="approved" {{ old('eiii_recommendation', $workRequest->eiii_recommendation ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="revision_needed" {{ old('eiii_recommendation', $workRequest->eiii_recommendation ?? '') === 'revision_needed' ? 'selected' : '' }}>Revision Needed</option>
                    <option value="rejected" {{ old('eiii_recommendation', $workRequest->eiii_recommendation ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                @error('eiii_recommendation')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Approval Notes --}}
            <div>
                <label class="wrd-info-label block mb-1">
                    Notes <span style="color:#f87171;">*</span>
                </label>
                <textarea name="eiii_notes" rows="5"
                          placeholder="Enter approval notes..."
                          required
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('eiii_notes', $workRequest->eiii_notes ?? '') }}</textarea>
                @error('eiii_notes')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Signature Pad --}}
            @include('reviewer.work-requests.partials._signature-pad', [
                'prefix'     => 'eiii',
                'radioName'  => 'eiii_signature_mode',
                'hiddenName' => 'eiii_signature',
            ])

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit"
                        style="padding:9px 24px; background:#06b6d4; color:#fff;
                               border:none; border-radius:8px; font-size:13px;
                               font-weight:600; cursor:pointer;">
                    <i class="fas fa-check-circle mr-2"></i> Submit Recommendation
                </button>
            </div>
        </form>
    </div>
</div>
