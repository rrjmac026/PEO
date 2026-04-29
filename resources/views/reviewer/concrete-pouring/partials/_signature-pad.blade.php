{{--
    Concrete Pouring — reusable e-signature pad partial
    ─────────────────────────────────────────────────────
    Variables expected:
        $cp_prefix      — unique prefix per step  (e.g. 'mtqa', 're', 'pe')
        $cp_radioName   — name for the radio inputs
        $cp_hiddenName  — name for the hidden input that holds the final value
--}}

<div class="cp-sig-wrap">
    <label class="cp-label" style="margin-bottom:8px;display:block;">
        E-Signature <span style="color:#f87171;font-size:11px;">(required)</span>
    </label>

    {{-- ── Option A: use saved signature ─────────────────────────────────── --}}
    @if(Auth::user()->signature_path)
        <div class="cp-sig-option-box">
            <p class="cp-sig-option-title">Use your saved signature:</p>
            <img src="{{ asset('storage/' . Auth::user()->signature_path) }}"
                 alt="Your Signature"
                 class="cp-sig-saved-img">
            <div style="display:flex;gap:20px;margin-top:10px;">
                <label class="cp-sig-radio-label">
                    <input type="radio" name="{{ $cp_radioName }}" value="saved" checked>
                    Use this signature
                </label>
                <label class="cp-sig-radio-label">
                    <input type="radio" name="{{ $cp_radioName }}" value="draw">
                    Draw a new one
                </label>
            </div>
        </div>
    @endif

    {{-- ── Option B: draw pad ─────────────────────────────────────────────── --}}
    <div id="{{ $cp_prefix }}-cp-pad-wrap"
         @if(Auth::user()->signature_path) style="display:none" @endif>

        <p class="cp-sig-draw-hint">Draw your signature below:</p>

        <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;">
            {{-- Canvas --}}
            <div>
                <canvas id="{{ $cp_prefix }}-cp-canvas"
                    class="cp-sig-canvas"
                    style="width:480px;height:160px;"></canvas>
                <button type="button" id="{{ $cp_prefix }}-cp-clear"
                        class="cp-sig-clear-btn">
                    <i class="fas fa-redo" style="margin-right:4px;"></i> Clear
                </button>
            </div>

            {{-- Live preview --}}
            <div>
                <p class="cp-sig-preview-label">Preview:</p>
                <img id="{{ $cp_prefix }}-cp-preview"
                     src="" alt="Signature Preview"
                     class="cp-sig-preview-img" style="display:none;">
                <div id="{{ $cp_prefix }}-cp-preview-empty"
                     class="cp-sig-preview-empty">
                    Signature preview
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden output carried in form POST --}}
    <input type="hidden"
           id="{{ $cp_prefix }}-cp-output"
           name="{{ $cp_hiddenName }}"
           value="{{ Auth::user()->signature_path
                        ? asset('storage/' . Auth::user()->signature_path)
                        : '' }}">
</div>