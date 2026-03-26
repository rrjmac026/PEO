<div class="wr-panel" id="panel-4">
    <div class="wr-panel-tag purple">✅ Step 4 of 4</div>
    <h2 class="wr-panel-title">Review & Submit</h2>
    <p class="wr-panel-sub">Double-check your details before submitting the work request.</p>

    <div class="wr-summary-grid" id="wr-summary-grid">
        {{-- Filled by JS --}}
    </div>

    {{-- ── Contractor Signature ── --}}
    <div style="margin-top: 24px; background: var(--wr-surface2); border: 1.5px solid var(--wr-border);
                border-radius: var(--wr-radius); padding: 20px;">

        <label class="wr-label" style="margin-bottom: 12px; display:block;">
            ✍️ Contractor Signature <span class="wr-req">*</span>
        </label>

        {{-- If user has a saved signature, show it with choice radios --}}
        @if(Auth::user()->signature_path)
            <div style="margin-bottom:16px; padding:12px; background:var(--wr-surface);
                        border-radius:8px; border:1px solid var(--wr-border);">
                <p style="font-size:13px; color:var(--wr-label); margin-bottom:12px;">
                    Use your saved signature:
                </p>
                <img src="{{ asset('storage/' . Auth::user()->signature_path) }}"
                     alt="Your Signature"
                     style="max-width:250px; margin-bottom:12px; border:1px solid var(--wr-border);
                            border-radius:4px; background:var(--wr-surface); display:block;">
                <div style="display:flex; gap:16px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="radio" name="ct_signature_mode" value="saved" checked>
                        <span style="font-size:13px; color:var(--wr-text);">Use this signature</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="radio" name="ct_signature_mode" value="draw">
                        <span style="font-size:13px; color:var(--wr-text);">Draw a new one</span>
                    </label>
                </div>
            </div>
        @endif

        {{-- Draw pad (hidden if saved sig exists, shown by default otherwise) --}}
        <div id="ct-signature-pad-wrap" {{ Auth::user()->signature_path ? 'style=display:none' : '' }}>
            <p style="font-size:13px; color:var(--wr-label); margin-bottom:12px;">
                Draw your signature below:
            </p>
            <div style="display:flex; gap:16px; align-items:flex-start; flex-wrap:wrap;">
                <div>
                    <canvas id="ct-signature-pad" width="400" height="150"
                        style="border:2px solid var(--wr-border); border-radius:8px;
                               background:var(--wr-surface); cursor:crosshair; display:block;
                               touch-action:none;">
                    </canvas>
                    <button type="button" id="ct-clear-signature"
                            style="margin-top:8px; padding:6px 16px; background:#ef4444;
                                   color:#fff; border:none; border-radius:6px;
                                   font-size:12px; cursor:pointer;">
                        <i class="fas fa-redo mr-1"></i> Clear
                    </button>
                </div>
                <div>
                    <p style="font-size:12px; color:var(--wr-label); margin-bottom:8px; font-weight:600;">
                        Preview:
                    </p>
                    <img id="ct-signature-preview" src="" alt="Signature Preview"
                         style="border:2px solid var(--wr-border); border-radius:8px;
                                background:var(--wr-surface); min-width:200px; height:100px; display:none;">
                    <div id="ct-signature-empty"
                         style="border:2px dashed var(--wr-border); border-radius:8px;
                                background:var(--wr-surface2); min-width:200px; height:100px;
                                display:flex; align-items:center; justify-content:center;
                                color:var(--wr-muted); font-size:12px;">
                        Signature preview
                    </div>
                </div>
            </div>
        </div>

        <p class="wr-err-msg" id="err-contractor_signature" style="margin-top:10px;">
            ⚠ Please provide your signature before submitting.
        </p>
    </div>

    {{-- Hidden field that carries the actual signature data --}}
    <input type="hidden"
           name="contractor_signature"
           id="ct-signature-output"
           value="{{ Auth::user()->signature_path ? asset('storage/' . Auth::user()->signature_path) : '' }}">

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(4)">← Edit</button>
        <button type="submit" class="wr-btn wr-btn-success" id="wr-submit-btn"
                onclick="return wrValidateContractorSignature()">
            🚀 Submit Request
        </button>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ── Canvas init ── */
    function initContractorPad() {
        const canvas   = document.getElementById('ct-signature-pad');
        const output   = document.getElementById('ct-signature-output');
        const clearBtn = document.getElementById('ct-clear-signature');
        const preview  = document.getElementById('ct-signature-preview');
        const empty    = document.getElementById('ct-signature-empty');

        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let drawing = false;

        /* ── Mouse ── */
        canvas.addEventListener('mousedown', () => {
            drawing = true;
            ctx.beginPath();
        });

        canvas.addEventListener('mouseup', () => {
            drawing = false;
            syncOutput();
        });

        canvas.addEventListener('mouseleave', () => {
            drawing = false;
        });

        canvas.addEventListener('mousemove', (e) => {
            if (!drawing) return;
            const rect = canvas.getBoundingClientRect();
            ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            ctx.lineWidth = 2;
            ctx.lineCap  = 'round';
            ctx.lineJoin = 'round';
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        });

        /* ── Touch ── */
        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            drawing = true;
            ctx.beginPath();
        }, { passive: false });

        canvas.addEventListener('touchend', (e) => {
            e.preventDefault();
            drawing = false;
            syncOutput();
        }, { passive: false });

        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (!drawing) return;
            const rect  = canvas.getBoundingClientRect();
            const touch = e.touches[0];
            ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
            ctx.lineWidth = 2;
            ctx.lineCap  = 'round';
            ctx.lineJoin = 'round';
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
        }, { passive: false });

        /* ── Sync canvas → hidden input + preview ── */
        function syncOutput() {
            const dataUrl = canvas.toDataURL('image/png');
            output.value = dataUrl;
            if (preview && empty) {
                preview.src = dataUrl;
                preview.style.display = 'block';
                empty.style.display   = 'none';
            }
        }

        /* ── Clear ── */
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                output.value = '';
                if (preview && empty) {
                    preview.style.display = 'none';
                    preview.src = '';
                    empty.style.display = 'flex';
                }
            });
        }

        /* ── Radio toggle (saved vs draw) ── */
        document.querySelectorAll('input[name="ct_signature_mode"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const padWrap = document.getElementById('ct-signature-pad-wrap');
                if (e.target.value === 'draw') {
                    padWrap.style.display = 'block';
                    output.value = '';
                    if (preview && empty) {
                        preview.style.display = 'none';
                        preview.src = '';
                        empty.style.display = 'flex';
                    }
                } else {
                    padWrap.style.display = 'none';
                    output.value = '{{ Auth::user()->signature_path ? asset("storage/" . Auth::user()->signature_path) : "" }}';
                }
            });
        });
    }

    /* ── Validation before submit ── */
    window.wrValidateContractorSignature = function () {
        const output = document.getElementById('ct-signature-output');
        const err    = document.getElementById('err-contractor_signature');

        if (!output || !output.value.trim()) {
            if (err) err.classList.add('show');
            const padWrap = document.getElementById('ct-signature-pad-wrap');
            if (padWrap) padWrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        if (err) err.classList.remove('show');
        return true;
    };

    /* ── Init when step 4 becomes active ── */
    const origShow = window.wrShowStep;
    window.wrShowStep = function (n) {
        origShow(n);
        if (n === 4) requestAnimationFrame(initContractorPad);
    };

    /* ── Also init if landing on step 4 directly (after validation error) ── */
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('panel-4')?.classList.contains('active')) {
            initContractorPad();
        }
    });
})();
</script>