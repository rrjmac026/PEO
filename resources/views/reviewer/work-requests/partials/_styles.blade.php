<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    :root {
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
        --wr-glow-1:   rgba(37,99,235,0.04);
        --wr-glow-2:   rgba(5,150,105,0.03);
        --wr-radius:   12px;
        --wr-radius-sm:8px;
    }

    .dark {
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
        --wr-glow-1:   rgba(79,141,255,0.05);
        --wr-glow-2:   rgba(0,212,170,0.04);
    }

    .wrd-wrap { font-family: 'Inter', sans-serif; }

    .wrd-hero {
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: var(--wr-radius);
        padding: 20px 24px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .wrd-hero::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background:
            radial-gradient(ellipse 60% 100% at 0% 50%, var(--wr-glow-1) 0%, transparent 70%),
            radial-gradient(ellipse 40% 100% at 100% 50%, var(--wr-glow-2) 0%, transparent 70%);
        pointer-events: none;
    }
    .wrd-hero-left {
        display: flex; align-items: center; gap: 16px;
        position: relative; z-index: 1;
    }
    .wrd-req-id {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px; font-weight: 700;
        color: var(--wr-muted);
        background: var(--wr-surface2);
        border: 1px solid var(--wr-border);
        padding: 5px 12px;
        border-radius: 6px;
        letter-spacing: 0.5px;
    }
    .wrd-project-name {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 18px; font-weight: 700;
        color: var(--wr-text);
        letter-spacing: -0.2px;
    }
    .wrd-project-loc {
        font-size: 13px; color: var(--wr-muted); margin-top: 2px;
        display: flex; align-items: center; gap: 5px;
    }
    .wrd-hero-right { position: relative; z-index: 1; }

    .wrd-status-badge {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 7px 16px; border-radius: 20px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px; font-weight: 600;
        border: 1.5px solid;
    }
    .wrd-status-dot { width: 7px; height: 7px; border-radius: 50%; }

    .wrd-status--draft     { color: #94a3b8; border-color: rgba(148,163,184,0.3); background: rgba(148,163,184,0.08); }
    .wrd-status--submitted { color: #60a5fa; border-color: rgba(96,165,250,0.3);  background: rgba(96,165,250,0.08); }
    .wrd-status--inspected { color: #c084fc; border-color: rgba(192,132,252,0.3); background: rgba(192,132,252,0.08); }
    .wrd-status--reviewed  { color: #818cf8; border-color: rgba(129,140,248,0.3); background: rgba(129,140,248,0.08); }
    .wrd-status--approved  { color: #34d399; border-color: rgba(52,211,153,0.3);  background: rgba(52,211,153,0.08); }
    .wrd-status--accepted  { color: #34d399; border-color: rgba(52,211,153,0.3);  background: rgba(52,211,153,0.08); }
    .wrd-status--rejected  { color: #f87171; border-color: rgba(248,113,113,0.3); background: rgba(248,113,113,0.08); }

    html:not(.dark) .wrd-status--draft     { color: #64748b;  border-color: rgba(100,116,139,0.25); background: rgba(100,116,139,0.06); }
    html:not(.dark) .wrd-status--submitted { color: #2563eb;  border-color: rgba(37,99,235,0.25);  background: rgba(37,99,235,0.06); }
    html:not(.dark) .wrd-status--inspected { color: #7c3aed;  border-color: rgba(124,58,237,0.25); background: rgba(124,58,237,0.06); }
    html:not(.dark) .wrd-status--reviewed  { color: #4338ca;  border-color: rgba(67,56,202,0.25);  background: rgba(67,56,202,0.06); }
    html:not(.dark) .wrd-status--approved  { color: #059669;  border-color: rgba(5,150,105,0.25);  background: rgba(5,150,105,0.06); }
    html:not(.dark) .wrd-status--accepted  { color: #059669;  border-color: rgba(5,150,105,0.25);  background: rgba(5,150,105,0.06); }
    html:not(.dark) .wrd-status--rejected  { color: #dc2626;  border-color: rgba(220,38,38,0.25);  background: rgba(220,38,38,0.06); }

    .wrd-card {
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: var(--wr-radius);
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .wrd-card-head {
        display: flex; align-items: center; gap: 10px;
        padding: 16px 24px;
        border-bottom: 1px solid var(--wr-border);
        background: var(--wr-surface2);
    }
    .wrd-card-head-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }
    .wrd-card-head-icon.blue   { background: rgba(79,141,255,0.12); }
    .wrd-card-head-icon.green  { background: rgba(0,212,170,0.12); }
    .wrd-card-head-icon.orange { background: rgba(245,158,11,0.12); }
    .wrd-card-head-icon.purple { background: rgba(167,139,250,0.12); }
    .wrd-card-head-icon.slate  { background: rgba(148,163,184,0.12); }

    html:not(.dark) .wrd-card-head-icon.blue   { background: rgba(37,99,235,0.08); }
    html:not(.dark) .wrd-card-head-icon.green  { background: rgba(5,150,105,0.08); }
    html:not(.dark) .wrd-card-head-icon.orange { background: rgba(217,119,6,0.08); }
    html:not(.dark) .wrd-card-head-icon.purple { background: rgba(124,58,237,0.08); }

    .wrd-card-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px; font-weight: 700;
        color: var(--wr-text);
    }
    .wrd-card-body { padding: 24px; }

    .wrd-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    @media (max-width: 600px) { .wrd-info-grid { grid-template-columns: 1fr; } }
    .wrd-info-grid.three { grid-template-columns: repeat(3, 1fr); }
    @media (max-width: 700px) { .wrd-info-grid.three { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .wrd-info-grid.three { grid-template-columns: 1fr; } }
    .wrd-info-grid.full { grid-template-columns: 1fr; }

    .wrd-info-item { display: flex; flex-direction: column; gap: 4px; }
    .wrd-info-item.span2 { grid-column: span 2; }
    @media (max-width: 600px) { .wrd-info-item.span2 { grid-column: span 1; } }

    .wrd-info-label {
        font-size: 11px; font-weight: 600;
        color: var(--wr-muted);
        font-family: 'Plus Jakarta Sans', sans-serif;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .wrd-info-value {
        font-size: 14px; font-weight: 500;
        color: var(--wr-text);
        line-height: 1.55;
    }
    .wrd-info-value.empty { color: var(--wr-muted); font-style: italic; font-weight: 400; }
    .wrd-info-value.pre { white-space: pre-wrap; font-weight: 400; font-size: 13.5px; line-height: 1.65; }
    .wrd-info-value.mono {
        font-family: 'Inter', monospace;
        background: var(--wr-surface2);
        border: 1px solid var(--wr-border);
        border-radius: 6px;
        padding: 3px 10px;
        font-size: 13px;
        display: inline-block;
    }

    .wrd-divider {
        height: 1px;
        background: var(--wr-border);
        margin: 22px 0;
    }

    .wrd-meta-row {
        display: flex; align-items: center; gap: 6px;
        flex-wrap: wrap;
    }
    .wrd-meta-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--wr-surface2);
        border: 1px solid var(--wr-border);
        border-radius: 6px;
        padding: 5px 12px;
        font-size: 12px; color: var(--wr-muted);
        font-family: 'Inter', sans-serif;
    }
    .wrd-meta-chip strong { color: var(--wr-text); font-weight: 500; }

    .wrd-section-divider {
        height: 2px;
        background: var(--wr-border);
        margin: 24px 0;
    }
</style>