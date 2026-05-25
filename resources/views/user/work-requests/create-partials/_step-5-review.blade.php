<div class="wr-panel" id="panel-5">
    <div class="wr-panel-tag purple">✅ Step 5 of 5</div>
    <h2 class="wr-panel-title">Review & Submit</h2>
    <p class="wr-panel-sub">Double-check your details before submitting the work request.</p>

    <div class="wr-summary-grid" id="wr-summary-grid">
        {{-- Filled by JS --}}
    </div>

    {{-- E-Signature Section --}}
    <div class="wr-sig-section">
        <div class="wr-sig-header">
            <span class="wr-sig-icon">✍️</span>
            <div>
                <div class="wr-sig-title">Contractor Signature</div>
                <div class="wr-sig-sub">Sign below to confirm and authorize this work request.</div>
            </div>
            <button type="button" class="wr-sig-clear-btn" onclick="wrSigClear()" title="Clear signature">
                ↺ Clear
            </button>
        </div>

        <div class="wr-sig-canvas-wrap" id="wr-sig-wrap">
            <canvas id="wr-sig-canvas" style="display:block; touch-action:none; cursor:crosshair;"></canvas>
            <div class="wr-sig-placeholder" id="wr-sig-placeholder">
                <span>✍</span>
                <span>Draw your signature here</span>
            </div>
            <div class="wr-sig-baseline"></div>
        </div>

        <div class="wr-sig-meta">
            <span id="wr-sig-name">{{ Auth::user()->name }}</span>
            <span>·</span>
            <span id="wr-sig-date"></span>
        </div>

        <p class="wr-err-msg" id="err-contractor_signature">
            ⚠ Please sign before submitting.
        </p>
    </div>

    <input type="hidden" name="contractor_signature" id="wr-sig-data">

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(5)">← Edit</button>
        <button type="submit" class="wr-btn wr-btn-success" id="wr-submit-btn" onclick="return wrValidateSignature()">
            🚀 Submit Request
        </button>
    </div>
</div>

<style>
.wr-sig-section {
    margin-top: 24px;
    background: var(--wr-surface2);
    border: 1.5px solid var(--wr-border);
    border-radius: var(--wr-radius);
    padding: 20px;
    position: relative;
}
.wr-sig-header {
    display: flex; align-items: flex-start; gap: 12px;
    margin-bottom: 16px; flex-wrap: wrap;
}
.wr-sig-icon { font-size: 22px; flex-shrink: 0; margin-top: 2px; }
.wr-sig-title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 15px; font-weight: 700; color: var(--wr-text);
}
.wr-sig-sub { font-size: 12px; color: var(--wr-muted); margin-top: 2px; }
.wr-sig-clear-btn {
    margin-left: auto; background: none;
    border: 1.5px solid var(--wr-border); border-radius: 6px;
    color: var(--wr-muted); font-size: 12px; font-weight: 600;
    font-family: 'Plus Jakarta Sans', sans-serif;
    padding: 5px 12px; cursor: pointer; transition: all 0.18s;
}
.wr-sig-clear-btn:hover {
    border-color: var(--wr-accent3); color: var(--wr-accent3);
    background: rgba(220,38,38,0.06);
}
.wr-sig-canvas-wrap {
    position: relative; background: var(--wr-surface);
    border: 1.5px solid var(--wr-border); border-radius: 8px;
    overflow: hidden; cursor: crosshair; transition: border-color 0.2s;
    height: 180px;
}
.wr-sig-canvas-wrap.signing {
    border-color: var(--wr-accent);
    box-shadow: 0 0 0 3px rgba(79,141,255,0.10);
}
html:not(.dark) .wr-sig-canvas-wrap.signing {
    box-shadow: 0 0 0 3px rgba(37,99,235,0.09);
}
#wr-sig-canvas { position: absolute; top:0; left:0; width:100%; height:100%; }
.wr-sig-placeholder {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 6px; pointer-events: none; transition: opacity 0.3s;
}
.wr-sig-placeholder span:first-child { font-size: 36px; opacity: 0.13; }
.wr-sig-placeholder span:last-child {
    font-size: 13px; color: var(--wr-muted); opacity: 0.7; font-style: italic;
}
.wr-sig-placeholder.hidden { opacity: 0; }
.wr-sig-baseline {
    position: absolute; bottom: 38px; left: 5%; right: 5%; height: 1px;
    background: repeating-linear-gradient(
        90deg, var(--wr-border) 0, var(--wr-border) 8px,
        transparent 8px, transparent 14px
    );
    pointer-events: none;
}
.wr-sig-meta {
    display: flex; align-items: center; gap: 8px; margin-top: 10px;
    font-size: 12px; color: var(--wr-muted); font-family: 'Inter', sans-serif;
}
.wr-sig-section .wr-err-msg { margin-top: 8px; }
</style>

<script>
(function () {
    'use strict';

    var canvas, ctx;
    var isDrawing = false;
    var lastX = 0, lastY = 0;
    var hasSig = false;
    var initialized = false;

    var d = document.getElementById('wr-sig-date');
    if (d) d.textContent = new Date().toLocaleDateString('en-US', {
        year: 'numeric', month: 'long', day: 'numeric'
    });

    function initCanvas() {
        canvas = document.getElementById('wr-sig-canvas');
        if (!canvas) return;
        var wrap = document.getElementById('wr-sig-wrap');
        var w = wrap.offsetWidth  || 600;
        var h = wrap.offsetHeight || 180;
        canvas.width  = w;
        canvas.height = h;
        ctx = canvas.getContext('2d');
        ctx.strokeStyle = '#1e293b';
        ctx.lineWidth   = 2;
        ctx.lineCap     = 'round';
        ctx.lineJoin    = 'round';
        if (!initialized) {
            initialized = true;
            canvas.addEventListener('mousedown',  onStart);
            canvas.addEventListener('mousemove',  onMove);
            canvas.addEventListener('mouseup',    onEnd);
            canvas.addEventListener('mouseleave', onEnd);
            canvas.addEventListener('touchstart', onStart, { passive: false });
            canvas.addEventListener('touchmove',  onMove,  { passive: false });
            canvas.addEventListener('touchend',   onEnd,   { passive: false });
        }
    }

    function getXY(e) {
        var r = canvas.getBoundingClientRect();
        var scaleX = canvas.width  / r.width;
        var scaleY = canvas.height / r.height;
        var src = e.touches ? e.touches[0] : e;
        return [(src.clientX - r.left) * scaleX, (src.clientY - r.top) * scaleY];
    }

    function onStart(e) {
        e.preventDefault();
        isDrawing = true;
        var xy = getXY(e);
        lastX = xy[0]; lastY = xy[1];
        document.getElementById('wr-sig-wrap').classList.add('signing');
    }

    function onMove(e) {
        e.preventDefault();
        if (!isDrawing) return;
        var xy = getXY(e);
        var x = xy[0], y = xy[1];
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(x, y);
        ctx.stroke();
        lastX = x; lastY = y;
        if (!hasSig) {
            hasSig = true;
            var ph = document.getElementById('wr-sig-placeholder');
            if (ph) ph.classList.add('hidden');
        }
        document.getElementById('wr-sig-data').value = canvas.toDataURL('image/png');
    }

    function onEnd(e) {
        if (e && e.cancelable) e.preventDefault();
        isDrawing = false;
    }

    window.wrSigClear = function () {
        if (!canvas) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSig = false;
        document.getElementById('wr-sig-data').value = '';
        var ph = document.getElementById('wr-sig-placeholder');
        if (ph) ph.classList.remove('hidden');
        var wrap = document.getElementById('wr-sig-wrap');
        if (wrap) wrap.classList.remove('signing');
        var err = document.getElementById('err-contractor_signature');
        if (err) err.classList.remove('show');
    };

    window.wrValidateSignature = function () {
        var err = document.getElementById('err-contractor_signature');
        if (!hasSig) {
            if (err) err.classList.add('show');
            var wrap = document.getElementById('wr-sig-wrap');
            if (wrap) wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
        if (err) err.classList.remove('show');
        return true;
    };

    function hookStepShow() {
        var orig = window.wrShowStep;
        if (typeof orig !== 'function') { setTimeout(hookStepShow, 50); return; }
        window.wrShowStep = function (n) {
            orig(n);
            if (n === 5) {
                initialized = false;
                hasSig = false;
                setTimeout(initCanvas, 80);
            }
        };
    }

    hookStepShow();

    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('panel-5') &&
            document.getElementById('panel-5').classList.contains('active')) {
            setTimeout(initCanvas, 80);
        }
    });
})();
</script>