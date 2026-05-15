{{-- resources/views/reviewer/concrete-pouring/partials/_reviewer-styles.blade.php --}}
<style>
    .rv-form-box {
        background: var(--cp-surface2);
        border: 1.5px solid rgba(8,145,178,0.35);
        border-radius: 10px; padding: 20px;
        margin-top: 20px;
    }
    .dark .rv-form-box { border-color: rgba(34,211,238,0.25); background: rgba(8,145,178,0.06); }
    .rv-form-title { font-size: 14px; font-weight: 700; color: var(--cp-text); margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .rv-readonly-box {
        background: var(--cp-surface2);
        border: 1px solid var(--cp-border);
        border-radius: 10px; padding: 16px;
        margin-top: 16px;
        font-size: 13px; color: var(--cp-muted);
    }

    /* MTQA final decision form */
    .rv-decision-box {
        background: var(--cp-surface2);
        border: 1.5px solid rgba(16,185,129,0.4);
        border-radius: 10px; padding: 20px;
        margin-top: 20px;
    }
    .dark .rv-decision-box { border-color: rgba(52,211,153,0.3); background: rgba(16,185,129,0.06); }
    .rv-decision-title { font-size: 14px; font-weight: 700; color: var(--cp-text); margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .rv-decision-radios { display: flex; gap: 20px; margin-bottom: 16px; flex-wrap: wrap; }
    .rv-decision-radio { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .rv-decision-approve  { font-size: 14px; font-weight: 600; color: #16a34a; }
    .rv-decision-disapprove { font-size: 14px; font-weight: 600; color: #dc2626; }

    /* ── E-Signature Styles ─────────────────────────────────────────── */
    .cp-sig-wrap { margin-top: 16px; }
    .cp-sig-option-box {
        padding: 14px 16px;
        background: var(--cp-surface2);
        border: 1px solid var(--cp-border);
        border-radius: 8px;
        margin-bottom: 14px;
    }
    .cp-sig-option-title { font-size: 13px; color: var(--cp-muted); margin-bottom: 10px; font-weight: 600; }
    .cp-sig-saved-img {
        max-width: 240px; max-height: 80px;
        border: 1px solid var(--cp-border);
        border-radius: 6px;
        padding: 4px;
        background: var(--cp-surface);
        display: block;
    }
    .cp-sig-radio-label {
        display: flex; align-items: center; gap: 7px;
        font-size: 13px; color: var(--cp-text); cursor: pointer;
    }
    .cp-sig-draw-hint { font-size: 13px; color: var(--cp-muted); margin-bottom: 10px; font-weight: 600; }
    .cp-sig-canvas {
        border: 2px solid var(--cp-border);
        border-radius: 8px;
        background: var(--cp-surface);
        cursor: crosshair;
        display: block;
        touch-action: none;
    }
    .cp-sig-canvas:focus { outline: none; box-shadow: 0 0 0 3px rgba(8,145,178,0.25); }
    .cp-sig-clear-btn {
        margin-top: 8px;
        padding: 5px 14px;
        background: #ef4444; color: #fff;
        border: none; border-radius: 6px;
        font-size: 12px; cursor: pointer;
        transition: opacity 0.2s;
    }
    .cp-sig-clear-btn:hover { opacity: 0.85; }
    .cp-sig-preview-label { font-size: 12px; color: var(--cp-muted); margin-bottom: 6px; font-weight: 600; }
    .cp-sig-preview-img {
        border: 2px solid var(--cp-border);
        border-radius: 8px;
        background: var(--cp-surface);
        min-width: 190px; height: 90px;
        object-fit: contain;
    }
    .cp-sig-preview-empty {
        border: 2px dashed var(--cp-border);
        border-radius: 8px;
        background: var(--cp-surface2);
        min-width: 190px; height: 90px;
        display: flex; align-items: center; justify-content: center;
        color: var(--cp-muted); font-size: 12px;
    }

    /* ── Signature display badge (show view, read-only) ─────────────── */
    .cp-sig-display {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 14px;
        background: rgba(5,150,105,0.06);
        border: 1px solid rgba(5,150,105,0.25);
        border-radius: 8px;
        margin-top: 8px;
    }
    .cp-sig-display img {
        max-width: 260px; max-height: 100px;
        width: auto; height: auto;
        border: 1px solid var(--cp-border);
        border-radius: 5px;
        background: #ffffff;
        padding: 4px;
    }
    .cp-sig-display-label {
        font-size: 11px; font-weight: 700;
        color: #059669; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .dark .cp-sig-display { background: rgba(52,211,153,0.06); border-color: rgba(52,211,153,0.2); }
    .dark .cp-sig-display-label { color: #34d399; }
</style>
