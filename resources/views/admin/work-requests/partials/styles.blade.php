@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    /* =============================================
       DARK MODE TOKENS  (default / .dark parent)
       ============================================= */
    :root {
        --wr-bg:       #0f1117;
        --wr-surface:  #181c27;
        --wr-surface2: #1e2335;
        --wr-border:   #2a3050;
        --wr-accent:   #4f8dff;
        --wr-accent2:  #00d4aa;
        --wr-accent3:  #ff6b6b;
        --wr-text:     #e8eaf6;
        --wr-muted:    #7c85a8;
        --wr-label:    #a8b3d8;
        --wr-error:    #ff6b6b;
        --wr-focus-bg: #1a1f30;
        --wr-glow-1:   rgba(79,141,255,0.05);
        --wr-glow-2:   rgba(0,212,170,0.04);
        --wr-radius:   12px;
        --wr-radius-sm:8px;
    }

    /* =============================================
       LIGHT MODE TOKENS  (html without .dark)
       ============================================= */
    html:not(.dark) {
        --wr-bg:       #f1f5f9;
        --wr-surface:  #ffffff;
        --wr-surface2: #f8fafc;
        --wr-border:   #cbd5e1;
        --wr-accent:   #2563eb;
        --wr-accent2:  #059669;
        --wr-accent3:  #dc2626;
        --wr-text:     #0f172a;
        --wr-muted:    #64748b;
        --wr-label:    #475569;
        --wr-error:    #dc2626;
        --wr-focus-bg: #eff6ff;
        --wr-glow-1:   rgba(37,99,235,0.04);
        --wr-glow-2:   rgba(5,150,105,0.03);
    }

    .wr-wrap { font-family: 'Inter', sans-serif; }

    /* Progress bar */
    .wr-progress-bar {
        background: var(--wr-surface);
        border-radius: var(--wr-radius);
        padding: 14px 20px;
        margin-bottom: 24px;
        border: 1px solid var(--wr-border);
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .wr-progress-inner { display: flex; align-items: center; }
    .wr-step-item {
        display: flex; align-items: center; gap: 8px;
        flex: 1; cursor: pointer; position: relative;
    }
    .wr-step-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 36px; right: 0; top: 50%;
        transform: translateY(-50%);
        height: 1px;
        background: var(--wr-border);
        z-index: 0;
        transition: background 0.4s;
    }
    .wr-step-item.done:not(:last-child)::after { background: var(--wr-accent); }
    .wr-step-num {
        width: 30px; height: 30px;
        border-radius: 50%;
        border: 2px solid var(--wr-border);
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700;
        color: var(--wr-muted);
        background: var(--wr-bg);
        transition: all 0.3s;
        flex-shrink: 0;
        font-family: 'Plus Jakarta Sans', sans-serif;
        position: relative; z-index: 1;
    }
    .wr-step-item.active .wr-step-num {
        background: var(--wr-accent);
        border-color: var(--wr-accent);
        color: white;
        box-shadow: 0 0 16px rgba(79,141,255,0.4);
    }
    .wr-step-item.done .wr-step-num {
        background: var(--wr-accent2);
        border-color: var(--wr-accent2);
        color: white;
    }
    .wr-step-label {
        font-size: 12px; font-weight: 500;
        color: var(--wr-muted);
        white-space: nowrap;
        transition: color 0.3s;
        font-family: 'Inter', sans-serif;
    }
    .wr-step-item.active .wr-step-label { color: var(--wr-accent); }
    .wr-step-item.done  .wr-step-label { color: var(--wr-accent2); }
    @media (max-width: 500px) { .wr-step-label { display: none; } }

    /* Main card */
    .wr-card {
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: var(--wr-radius);
        overflow: hidden;
        position: relative;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
    .wr-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background:
            radial-gradient(ellipse 60% 40% at 20% 10%, var(--wr-glow-1) 0%, transparent 60%),
            radial-gradient(ellipse 50% 30% at 80% 80%, var(--wr-glow-2) 0%, transparent 60%);
        pointer-events: none;
    }
    .wr-card-body { padding: 2rem; position: relative; z-index: 1; }

    /* Panels */
    .wr-panel { display: none; animation: wrFadeSlide 0.35s ease both; }
    .wr-panel.active { display: block; }
    @keyframes wrFadeSlide {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Panel tags */
    .wr-panel-tag {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 12px; font-weight: 600;
        letter-spacing: 0.2px;
        color: var(--wr-accent);
        background: rgba(79,141,255,0.1);
        border: 1px solid rgba(79,141,255,0.25);
        padding: 5px 14px; border-radius: 20px; margin-bottom: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        text-transform: none;
    }
    html:not(.dark) .wr-panel-tag {
        background: rgba(37,99,235,0.08);
        border-color: rgba(37,99,235,0.2);
    }
    .wr-panel-tag.green {
        color: var(--wr-accent2);
        background: rgba(0,212,170,0.1); border-color: rgba(0,212,170,0.25);
    }
    html:not(.dark) .wr-panel-tag.green {
        background: rgba(5,150,105,0.08); border-color: rgba(5,150,105,0.2);
    }
    .wr-panel-tag.orange {
        color: #f59e0b;
        background: rgba(245,158,11,0.1); border-color: rgba(245,158,11,0.25);
    }
    html:not(.dark) .wr-panel-tag.orange {
        color: #d97706;
        background: rgba(217,119,6,0.08); border-color: rgba(217,119,6,0.2);
    }
    .wr-panel-tag.purple {
        color: #a78bfa;
        background: rgba(167,139,250,0.1); border-color: rgba(167,139,250,0.25);
    }
    html:not(.dark) .wr-panel-tag.purple {
        color: #7c3aed;
        background: rgba(124,58,237,0.08); border-color: rgba(124,58,237,0.2);
    }
    .wr-panel-tag.red {
        color: #f87171;
        background: rgba(248,113,113,0.1); border-color: rgba(248,113,113,0.25);
    }
    html:not(.dark) .wr-panel-tag.red {
        color: #dc2626;
        background: rgba(220,38,38,0.08); border-color: rgba(220,38,38,0.2);
    }

    .wr-panel-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 22px; font-weight: 700;
        color: var(--wr-text);
        letter-spacing: -0.3px; margin-bottom: 4px;
        line-height: 1.3;
    }
    .wr-panel-sub { font-size: 14px; color: var(--wr-muted); margin-bottom: 1.75rem; line-height: 1.6; font-weight: 400; }

    /* Fields */
    .wr-fields { display: grid; gap: 16px; }
    .wr-two-col { grid-template-columns: 1fr 1fr; }
    .wr-three-col { grid-template-columns: 1fr 1fr 1fr; }
    @media (max-width: 560px) { .wr-two-col, .wr-three-col { grid-template-columns: 1fr; } }
    .wr-field { display: flex; flex-direction: column; gap: 6px; }

    .wr-label {
        font-size: 13px; font-weight: 600;
        letter-spacing: 0.1px;
        color: var(--wr-label);
        display: flex; align-items: center; gap: 5px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        text-transform: none;
    }
    .wr-req { color: var(--wr-accent3); font-size: 14px; }

    .wr-input-wrap { position: relative; }
    .wr-icon {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%);
        color: var(--wr-muted);
        font-style: normal; pointer-events: none;
        font-size: 14px; transition: color 0.2s; z-index: 1;
    }
    .wr-input-wrap.textarea-wrap .wr-icon { top: 14px; transform: none; }

    .wr-input-wrap input,
    .wr-input-wrap select,
    .wr-input-wrap textarea {
        width: 100%;
        background: var(--wr-surface2);
        border: 1.5px solid var(--wr-border);
        border-radius: var(--wr-radius-sm);
        color: var(--wr-text);
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        line-height: 1.5;
        padding: 11px 14px 11px 36px;
        outline: none;
        transition: all 0.2s;
        appearance: none; -webkit-appearance: none;
    }
    .wr-input-wrap.no-icon input,
    .wr-input-wrap.no-icon select,
    .wr-input-wrap.no-icon textarea { padding-left: 14px; }

    .wr-input-wrap:focus-within input,
    .wr-input-wrap:focus-within select,
    .wr-input-wrap:focus-within textarea {
        border-color: var(--wr-accent);
        background: var(--wr-focus-bg);
        box-shadow: 0 0 0 3px rgba(79,141,255,0.12);
    }
    html:not(.dark) .wr-input-wrap:focus-within input,
    html:not(.dark) .wr-input-wrap:focus-within select,
    html:not(.dark) .wr-input-wrap:focus-within textarea {
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
    .wr-input-wrap:focus-within .wr-icon { color: var(--wr-accent); }

    .wr-input-wrap input[readonly] {
        color: var(--wr-muted); cursor: not-allowed; border-style: dashed;
    }
    .wr-input-wrap select option { background: var(--wr-surface2); color: var(--wr-text); }
    .wr-input-wrap textarea { resize: vertical; min-height: 90px; }

    .wr-input-wrap input.wr-error,
    .wr-input-wrap select.wr-error,
    .wr-input-wrap textarea.wr-error {
        border-color: var(--wr-error) !important;
        box-shadow: 0 0 0 3px rgba(220,38,38,0.1) !important;
    }

    .wr-readonly-badge {
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
        color: var(--wr-muted);
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: 4px; padding: 2px 7px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .wr-field-hint { font-size: 11px; color: var(--wr-muted); font-style: italic; }
    .wr-err-msg {
        font-size: 11px; color: var(--wr-error);
        display: none; align-items: center; gap: 4px;
    }
    .wr-err-msg.show { display: flex; }

    /* Status options */
    .wr-status-options {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 8px;
    }
    .wr-status-opt {
        display: flex; align-items: center; gap: 8px;
        padding: 10px 12px;
        border: 1.5px solid var(--wr-border);
        border-radius: var(--wr-radius-sm);
        cursor: pointer; transition: all 0.2s;
        background: var(--wr-surface2);
    }
    .wr-status-opt:hover { border-color: var(--wr-accent); background: rgba(79,141,255,0.06); }
    html:not(.dark) .wr-status-opt:hover { background: rgba(37,99,235,0.05); }
    .wr-status-opt.selected { border-color: var(--wr-accent); background: rgba(79,141,255,0.12); }
    html:not(.dark) .wr-status-opt.selected { background: rgba(37,99,235,0.08); }
    .wr-status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .wr-status-opt-label { font-size: 13px; font-weight: 500; color: var(--wr-text); }
    .wr-status-opt input[type=radio] { display: none; }

    /* Nav */
    .wr-nav {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 2rem; padding-top: 1.5rem;
        border-top: 1px solid var(--wr-border);
    }

    .wr-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 22px; border: none;
        border-radius: var(--wr-radius-sm);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px; font-weight: 700;
        letter-spacing: 0.1px;
        text-transform: none;
        cursor: pointer; transition: all 0.22s; text-decoration: none;
    }
    .wr-btn-ghost {
        background: transparent;
        border: 1.5px solid var(--wr-border);
        color: var(--wr-muted);
    }
    .wr-btn-ghost:hover { border-color: var(--wr-accent); color: var(--wr-accent); background: rgba(79,141,255,0.06); }
    html:not(.dark) .wr-btn-ghost:hover { background: rgba(37,99,235,0.05); }

    .wr-btn-primary {
        background: var(--wr-accent); color: white;
        box-shadow: 0 4px 18px rgba(79,141,255,0.3);
    }
    .wr-btn-primary:hover { filter: brightness(1.1); transform: translateY(-1px); }
    html:not(.dark) .wr-btn-primary { box-shadow: 0 4px 18px rgba(37,99,235,0.25); }

    .wr-btn-success {
        background: var(--wr-accent2); color: #0f1117;
        box-shadow: 0 4px 18px rgba(0,212,170,0.3);
        font-weight: 800;
    }
    html:not(.dark) .wr-btn-success { color: white; box-shadow: 0 4px 18px rgba(5,150,105,0.25); }
    .wr-btn-success:hover { filter: brightness(1.08); transform: translateY(-1px); }
    .wr-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none !important; }

    /* Summary */
    .wr-summary-grid { display: grid; gap: 12px; }
    .wr-summary-section {
        background: var(--wr-surface2);
        border: 1px solid var(--wr-border);
        border-radius: var(--wr-radius-sm);
        overflow: hidden;
    }
    .wr-summary-head {
        display: flex; align-items: center; gap: 8px;
        padding: 10px 16px;
        background: rgba(0,0,0,0.03);
        border-bottom: 1px solid var(--wr-border);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 12px; font-weight: 700;
        letter-spacing: 0.1px;
        color: var(--wr-label);
    }
    html:not(.dark) .wr-summary-head { background: rgba(0,0,0,0.02); }
    .wr-summary-rows { padding: 12px 16px; display: grid; gap: 10px; }
    .wr-summary-row {
        display: flex; justify-content: space-between;
        align-items: flex-start; gap: 12px;
    }
    .wr-summary-key {
        font-size: 12px; font-weight: 600;
        color: var(--wr-muted); min-width: 130px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .wr-summary-val { font-size: 14px; color: var(--wr-text); text-align: right; word-break: break-word; font-weight: 400; line-height: 1.5; }
    .wr-summary-val.empty { color: var(--wr-muted); font-style: italic; }

    /* Float save */
    .wr-float-save {
        position: fixed; bottom: 20px; right: 20px;
        display: flex; align-items: center; gap: 8px;
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: 20px; padding: 7px 14px;
        font-size: 11px; color: var(--wr-muted);
        z-index: 9999; opacity: 0; transform: translateY(8px);
        transition: all 0.3s;
        font-family: 'Inter', sans-serif;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }
    .wr-float-save.show { opacity: 1; transform: translateY(0); }
    .wr-float-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: var(--wr-accent2);
        animation: wrBlink 1.5s ease infinite;
    }
    @keyframes wrBlink { 0%,100%{opacity:1;} 50%{opacity:0.3;} }

    /* Collapsible section headers */
    .wr-section-header {
        display: flex; align-items: center; gap: 8px;
        padding: 12px 0 8px;
        margin-top: 1.5rem;
        border-bottom: 1px solid var(--wr-border);
        font-size: 13px;
        font-weight: 700;
        color: var(--wr-label);
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.1px;
    }
    .wr-section-header:first-child { margin-top: 0; }
    .wr-section-icon { transition: transform 0.3s; }
    .wr-section-header.collapsed .wr-section-icon { transform: rotate(-90deg); }
    .wr-section-content { display: none; padding-top: 16px; }
    .wr-section-content.show { display: block; }
</style>
@endpush
