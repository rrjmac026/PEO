{{-- resources/views/reviewer/concrete-pouring/partials/_scripts.blade.php --}}
<script>
function initCpSignaturePad(prefix, radioName) {
    const canvas       = document.getElementById(`${prefix}-cp-canvas`);
    const output       = document.getElementById(`${prefix}-cp-output`);
    const clearBtn     = document.getElementById(`${prefix}-cp-clear`);
    const preview      = document.getElementById(`${prefix}-cp-preview`);
    const previewEmpty = document.getElementById(`${prefix}-cp-preview-empty`);
    const padWrap      = document.getElementById(`${prefix}-cp-pad-wrap`);

    if (!canvas) return;

    // ── HiDPI fix: scale canvas backing store to device pixel ratio ──
    const dpr  = window.devicePixelRatio || 1;
    const cssW = canvas.offsetWidth  || 480;
    const cssH = canvas.offsetHeight || 160;
    canvas.width  = cssW * dpr;
    canvas.height = cssH * dpr;

    const ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);
    // ── NO white fill — keep background transparent so only ink strokes
    //    are exported, matching WorkRequest behaviour ──────────────────

    let drawing = false;

    const startDraw = (x, y) => {
        drawing = true;
        ctx.beginPath();
        const rect = canvas.getBoundingClientRect();
        ctx.moveTo(x - rect.left, y - rect.top);
    };

    const endDraw = () => {
        if (!drawing) return;
        drawing = false;
        const dataUrl = canvas.toDataURL('image/png');
        output.value = dataUrl;
        if (preview && previewEmpty) {
            preview.src = dataUrl;
            preview.style.display = 'block';
            previewEmpty.style.display = 'none';
        }
    };

    const moveDraw = (x, y) => {
        if (!drawing) return;
        const rect = canvas.getBoundingClientRect();
        ctx.lineTo(x - rect.left, y - rect.top);
        ctx.lineWidth   = 2;
        ctx.lineCap     = 'round';
        ctx.lineJoin    = 'round';
        ctx.strokeStyle = '#1e293b';
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x - rect.left, y - rect.top);
    };

    canvas.addEventListener('mousedown',  e => startDraw(e.clientX, e.clientY));
    canvas.addEventListener('mouseup',    endDraw);
    canvas.addEventListener('mouseleave', endDraw);
    canvas.addEventListener('mousemove',  e => moveDraw(e.clientX, e.clientY));
    canvas.addEventListener('touchstart', e => { e.preventDefault(); const t = e.touches[0]; startDraw(t.clientX, t.clientY); }, { passive: false });
    canvas.addEventListener('touchend',   e => { e.preventDefault(); endDraw(); }, { passive: false });
    canvas.addEventListener('touchmove',  e => { e.preventDefault(); const t = e.touches[0]; moveDraw(t.clientX, t.clientY); }, { passive: false });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            ctx.clearRect(0, 0, cssW, cssH);
            // ── No white refill on clear either ──────────────────────
            output.value = '';
            if (preview && previewEmpty) {
                preview.style.display = 'none';
                preview.src = '';
                previewEmpty.style.display = 'flex';
            }
        });
    }

    document.querySelectorAll(`input[name="${radioName}"]`).forEach(radio => {
        radio.addEventListener('change', e => {
            if (e.target.value === 'draw') {
                padWrap.style.display = 'block';
                output.value = '';
                if (preview && previewEmpty) {
                    preview.style.display = 'none';
                    preview.src = '';
                    previewEmpty.style.display = 'flex';
                }
            } else {
                padWrap.style.display = 'none';
                const savedUrl = "{{ Auth::user()->signature_path ? asset('storage/' . Auth::user()->signature_path) : '' }}";
                output.value = savedUrl;
            }
        });
    });
}

initCpSignaturePad('re',   're_sig_mode');
initCpSignaturePad('pe',   'pe_sig_mode');
initCpSignaturePad('mtqa', 'mtqa_sig_mode');
</script>