{{-- resources/views/reviewer/work-requests/partials/_provincial-engineer-form.blade.php --}}
<div class="wrd-card" style="border-top: 3px solid #14b8a6;">
    <div class="wrd-card-head">
        <div class="wrd-card-head-icon" style="background:rgba(20,184,166,0.12);">
            <i class="fas fa-user-tie" style="color:#14b8a6;"></i>
        </div>
        <span class="wrd-card-title">Provincial Engineer — Final Decision</span>
        <span style="margin-left:auto; padding:4px 12px; background:rgba(20,184,166,0.15);
                     color:#14b8a6; border-radius:20px; font-size:11px; font-weight:700;
                     letter-spacing:0.5px; text-transform:uppercase;">
            Final Decision
        </span>
    </div>
    <div class="wrd-card-body">

        @if(session('success'))
            <div style="padding:12px 16px; border-radius:8px; background:rgba(52,211,153,0.15);
                        color:#34d399; font-size:13px; margin-bottom:16px; border:1px solid rgba(52,211,153,0.3);">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('reviewer.work-requests.store-provincial-decision', $workRequest) }}"
              class="space-y-5">
            @csrf

            {{-- Auto-filled name --}}
            <div>
                <label class="wrd-info-label block mb-1">Provincial Engineer</label>
                <input type="text"
                       value="{{ Auth::user()->name }}"
                       disabled
                       style="width:100%; padding:9px 12px; border-radius:8px;
                              background:var(--wr-surface2); border:1px solid var(--wr-border);
                              color:var(--wr-muted); font-size:14px; outline:none; cursor:not-allowed;">
            </div>

            {{-- Decision radio —— THE KEY CHANGE --}}
            <div>
                <label class="wrd-info-label block mb-3">
                    Decision <span style="color:#f87171;">*</span>
                </label>
                <div style="display:flex; gap:24px;">
                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer;
                                  padding:12px 20px; border-radius:10px; border:2px solid;
                                  border-color: {{ old('decision') === 'approved' ? '#14b8a6' : 'var(--wr-border)' }};
                                  background: {{ old('decision') === 'approved' ? 'rgba(20,184,166,0.1)' : 'var(--wr-surface2)' }};
                                  transition:all 0.2s;"
                           onclick="this.style.borderColor='#14b8a6'; this.style.background='rgba(20,184,166,0.1)';
                                    document.getElementById('reject-label').style.borderColor='var(--wr-border)';
                                    document.getElementById('reject-label').style.background='var(--wr-surface2)';">
                        <input type="radio" name="decision" value="approved"
                               {{ old('decision') === 'approved' ? 'checked' : '' }}
                               style="accent-color:#14b8a6;">
                        <span style="font-size:14px; font-weight:600; color:#14b8a6;">
                            <i class="fas fa-check-circle mr-1"></i> Approve
                        </span>
                    </label>

                    <label id="reject-label"
                           style="display:flex; align-items:center; gap:10px; cursor:pointer;
                                  padding:12px 20px; border-radius:10px; border:2px solid;
                                  border-color: {{ old('decision') === 'rejected' ? '#f87171' : 'var(--wr-border)' }};
                                  background: {{ old('decision') === 'rejected' ? 'rgba(248,113,113,0.1)' : 'var(--wr-surface2)' }};
                                  transition:all 0.2s;"
                           onclick="this.style.borderColor='#f87171'; this.style.background='rgba(248,113,113,0.1)';
                                    document.querySelector('label[onclick*=approve]').style.borderColor='var(--wr-border)';
                                    document.querySelector('label[onclick*=approve]').style.background='var(--wr-surface2)';">
                        <input type="radio" name="decision" value="rejected"
                               {{ old('decision') === 'rejected' ? 'checked' : '' }}
                               style="accent-color:#f87171;">
                        <span style="font-size:14px; font-weight:600; color:#f87171;">
                            <i class="fas fa-times-circle mr-1"></i> Reject
                        </span>
                    </label>
                </div>
                @error('decision')
                    <p style="color:#f87171; font-size:12px; margin-top:6px;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remarks / Action --}}
            <div>
                <label class="wrd-info-label block mb-1">
                    Remarks / Action <span style="color:#f87171;">*</span>
                </label>
                <textarea name="approved_recommendation_action" rows="5"
                          placeholder="Enter your remarks or reason for decision..."
                          required
                          style="width:100%; padding:9px 12px; border-radius:8px;
                                 background:var(--wr-surface2); border:1px solid var(--wr-border);
                                 color:var(--wr-text); font-size:14px; outline:none; resize:vertical;">{{ old('approved_recommendation_action', $workRequest->approved_recommendation_action) }}</textarea>
                @error('approved_recommendation_action')
                    <p style="color:#f87171; font-size:12px; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Signature Pad --}}
            @include('reviewer.work-requests.partials._signature-pad', [
                'prefix'     => 'pe',
                'radioName'  => 'pe_signature_mode',
                'hiddenName' => 'approved_signature',
            ])

            {{-- Info note --}}
            <div style="padding:12px 16px; border-radius:8px; background:rgba(20,184,166,0.08);
                        border:1px solid rgba(20,184,166,0.25); font-size:13px; color:var(--wr-muted);">
                <i class="fas fa-info-circle mr-2" style="color:#14b8a6;"></i>
                This is the <strong>final decision</strong>. Once submitted, the work request will be
                marked as <strong>approved</strong> or <strong>rejected</strong>.
                If approved, <strong>MTQA</strong> will be notified to print the document.
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit"
                        style="padding:10px 28px; background:#14b8a6; color:#fff;
                               border:none; border-radius:8px; font-size:13px;
                               font-weight:700; cursor:pointer; transition:opacity 0.2s;"
                        onmouseover="this.style.opacity='0.85'"
                        onmouseout="this.style.opacity='1'">
                    <i class="fas fa-gavel mr-2"></i> Submit Final Decision
                </button>
            </div>
        </form>
    </div>
</div>