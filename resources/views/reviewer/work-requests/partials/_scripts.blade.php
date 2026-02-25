<script>
    const initSignaturePad = (canvasId, outputId, clearBtnId, radioName) => {
        const canvas    = document.getElementById(canvasId);
        const output    = document.getElementById(outputId);
        const clearBtn  = document.getElementById(clearBtnId);
        const previewImg = document.getElementById(canvasId.replace('-signature-pad', '-signature-preview'));
        const emptyDiv   = document.getElementById(canvasId.replace('-signature-pad', '-signature-empty'));

        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let drawing = false;

        canvas.addEventListener('mousedown', () => {
            drawing = true;
            ctx.beginPath();
        });
        canvas.addEventListener('mouseup', () => {
            drawing = false;
            const dataUrl = canvas.toDataURL('image/png');
            output.value = dataUrl;
            if (previewImg && emptyDiv) {
                previewImg.src = dataUrl;
                previewImg.style.display = 'block';
                emptyDiv.style.display = 'none';
            }
        });
        canvas.addEventListener('mousemove', (e) => {
            if (!drawing) return;
            const rect = canvas.getBoundingClientRect();
            ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        });

        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            drawing = true;
            ctx.beginPath();
        });
        canvas.addEventListener('touchend', (e) => {
            e.preventDefault();
            drawing = false;
            const dataUrl = canvas.toDataURL('image/png');
            output.value = dataUrl;
            if (previewImg && emptyDiv) {
                previewImg.src = dataUrl;
                previewImg.style.display = 'block';
                emptyDiv.style.display = 'none';
            }
        });
        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (!drawing) return;
            const rect  = canvas.getBoundingClientRect();
            const touch = e.touches[0];
            ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                output.value = '';
                if (previewImg && emptyDiv) {
                    previewImg.style.display = 'none';
                    previewImg.src = '';
                    emptyDiv.style.display = 'flex';
                }
            });
        }

        document.querySelectorAll(`input[name="${radioName}"]`).forEach(radio => {
            radio.addEventListener('change', (e) => {
                const prefix  = canvasId.replace('-signature-pad', '');
                const padWrap = document.getElementById(`${prefix}-signature-pad-wrap`);
                if (e.target.value === 'draw') {
                    padWrap.style.display = 'block';
                    output.value = '';
                    if (previewImg && emptyDiv) {
                        previewImg.style.display = 'none';
                        previewImg.src = '';
                        emptyDiv.style.display = 'flex';
                    }
                } else {
                    padWrap.style.display = 'none';
                    output.value = '{{ Auth::user()->signature_path ? asset("storage/" . Auth::user()->signature_path) : "" }}';
                }
            });
        });
    };

    // Initialize all pads — only the one matching the current role's canvas will actually exist in the DOM
    initSignaturePad('si-signature-pad', 'si-signature-output', 'si-clear-signature', 'si_signature_mode');
    initSignaturePad('sv-signature-pad', 'sv-signature-output', 'sv-clear-signature', 'sv_signature_mode');
    initSignaturePad('re-signature-pad', 're-signature-output', 're-clear-signature', 're_signature_mode');
    initSignaturePad('mq-signature-pad', 'mq-signature-output', 'mq-clear-signature', 'mq_signature_mode');
    initSignaturePad('ra-signature-pad', 'ra-signature-output', 'ra-clear-signature', 'ra_signature_mode');
    initSignaturePad('pe-signature-pad', 'pe-signature-output', 'pe-clear-signature', 'pe_signature_mode');
</script>