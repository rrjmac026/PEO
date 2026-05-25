<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    :root {
        --wr-surface:   #ffffff;
        --wr-surface2:  #f8fafc;
        --wr-border:    #e2e8f0;
        --wr-text:      #0f172a;
        --wr-text-sec:  #334155;
        --wr-muted:     #64748b;
        --wr-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --wr-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
    }
    .dark {
        --wr-surface:   #1a1f2e;
        --wr-surface2:  #1e2335;
        --wr-border:    #2a3050;
        --wr-text:      #e8eaf6;
        --wr-text-sec:  #c5cae9;
        --wr-muted:     #7c85a8;
        --wr-shadow:    0 1px 4px rgba(0,0,0,0.35);
        --wr-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
    }

    .wrd-wrap { font-family: 'Inter', sans-serif; }

    .wrd-hero {
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 20px;
        display: flex; align-items: center; justify-content: space-between;
        gap: 16px; flex-wrap: wrap;
        position: relative; overflow: hidden;
        box-shadow: var(--wr-shadow);
        transition: box-shadow 0.25s ease;
    }
    .wrd-hero:hover { box-shadow: var(--wr-shadow-lg); }
    .wrd-hero-left { display: flex; align-items: center; gap: 16px; position: relative; z-index: 1; }
    .wrd-req-id {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px; font-weight: 700;
        color: var(--wr-muted);
        background: var(--wr-surface2);
        border: 1px solid var(--wr-border);
        padding: 5px 12px; border-radius: 6px; letter-spacing: 0.5px;
    }
    .wrd-project-name {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 18px; font-weight: 700;
        color: var(--wr-text); letter-spacing: -0.2px;
    }
    .wrd-project-loc { font-size: 13px; color: var(--wr-muted); margin-top: 2px; display: flex; align-items: center; gap: 5px; }
    .wrd-hero-right { position: relative; z-index: 1; }

    .wrd-status-badge {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 7px 16px; border-radius: 20px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px; font-weight: 600; border: 1.5px solid;
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
        border-radius: 12px; overflow: hidden;
        margin-bottom: 20px;
        box-shadow: var(--wr-shadow);
        transition: box-shadow 0.25s ease;
    }
    .wrd-card:hover { box-shadow: var(--wr-shadow-lg); }
    .wrd-card-head {
        display: flex; align-items: center; gap: 10px;
        padding: 16px 24px;
        border-bottom: 1px solid var(--wr-border);
        background: var(--wr-surface2);
    }
    .wrd-card-head-icon {
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; flex-shrink: 0;
    }
    .wrd-card-head-icon.blue   { background: #dbeafe; }
    .wrd-card-head-icon.green  { background: #d1fae5; }
    .wrd-card-head-icon.orange { background: #fed7aa; }
    .wrd-card-head-icon.purple { background: #ede9fe; }
    .wrd-card-head-icon.slate  { background: #e2e8f0; }
    .dark .wrd-card-head-icon.blue   { background: rgba(37,99,235,0.15); }
    .dark .wrd-card-head-icon.green  { background: rgba(5,150,105,0.13); }
    .dark .wrd-card-head-icon.orange { background: rgba(194,65,12,0.15); }
    .dark .wrd-card-head-icon.purple { background: rgba(124,58,237,0.14); }
    .dark .wrd-card-head-icon.slate  { background: rgba(148,163,184,0.12); }
    .wrd-card-head-icon.blue   i { color: #2563eb; }
    .wrd-card-head-icon.green  i { color: #059669; }
    .wrd-card-head-icon.orange i { color: #ea580c; }
    .wrd-card-head-icon.purple i { color: #7c3aed; }
    .wrd-card-head-icon.slate  i { color: #475569; }
    .dark .wrd-card-head-icon.blue   i { color: #60a5fa; }
    .dark .wrd-card-head-icon.green  i { color: #34d399; }
    .dark .wrd-card-head-icon.orange i { color: #fb923c; }
    .dark .wrd-card-head-icon.purple i { color: #c084fc; }
    .dark .wrd-card-head-icon.slate  i { color: #cbd5e1; }

    .wrd-card-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 700; color: var(--wr-text); }
    .wrd-card-body { padding: 24px; }

    .wrd-info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    @media (max-width: 600px) { .wrd-info-grid { grid-template-columns: 1fr; } }
    .wrd-info-grid.three { grid-template-columns: repeat(3, 1fr); }
    @media (max-width: 700px) { .wrd-info-grid.three { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .wrd-info-grid.three { grid-template-columns: 1fr; } }

    .wrd-info-item { display: flex; flex-direction: column; gap: 4px; }
    .wrd-info-item.span2 { grid-column: span 2; }
    @media (max-width: 600px) { .wrd-info-item.span2 { grid-column: span 1; } }

    .wrd-info-label { font-size: 11px; font-weight: 600; color: var(--wr-muted); font-family: 'Plus Jakarta Sans', sans-serif; text-transform: uppercase; letter-spacing: 0.5px; }
    .wrd-info-value { font-size: 14px; font-weight: 500; color: var(--wr-text); line-height: 1.55; }
    .wrd-info-value.empty { color: var(--wr-muted); font-style: italic; font-weight: 400; }
    .wrd-info-value.pre { white-space: pre-wrap; font-weight: 400; font-size: 13.5px; line-height: 1.65; }
    .wrd-info-value.mono {
        font-family: 'Inter', monospace;
        background: var(--wr-surface2);
        border: 1px solid var(--wr-border);
        border-radius: 6px; padding: 3px 10px;
        font-size: 13px; display: inline-block;
    }

    .wrd-divider { height: 1px; background: var(--wr-border); margin: 22px 0; }

    .wrd-meta-row { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .wrd-meta-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--wr-surface2);
        border: 1px solid var(--wr-border);
        border-radius: 6px; padding: 5px 12px;
        font-size: 12px; color: var(--wr-muted);
        font-family: 'Inter', sans-serif;
    }
    .wrd-meta-chip strong { color: var(--wr-text); font-weight: 500; }

    .wrd-log-item {
        display: flex; align-items: flex-start; gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid var(--wr-border);
        animation: wrdFadeIn 0.3s ease both;
    }
    .wrd-log-item:last-child { border-bottom: none; padding-bottom: 0; }
    @keyframes wrdFadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }

    .wrd-log-dot-wrap { display: flex; flex-direction: column; align-items: center; padding-top: 3px; flex-shrink: 0; }
    .wrd-log-dot {
        width: 10px; height: 10px; border-radius: 50%;
        background: var(--wr-accent);
        border: 2px solid var(--wr-surface);
        box-shadow: 0 0 0 2px var(--wr-border);
    }
    .wrd-log-event { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 600; color: var(--wr-text); margin-bottom: 2px; }
    .wrd-log-desc { font-size: 12px; color: var(--wr-muted); line-height: 1.5; }
    .wrd-log-time { margin-left: auto; flex-shrink: 0; font-size: 11px; color: var(--wr-muted); font-family: 'Inter', sans-serif; white-space: nowrap; padding-top: 2px; }

    .wrd-danger-zone {
        background: var(--wr-surface);
        border: 1px solid rgba(248,113,113,0.25);
        border-radius: 12px;
        padding: 18px 24px;
        display: flex; align-items: center; justify-content: space-between;
        gap: 16px; flex-wrap: wrap;
        box-shadow: var(--wr-shadow);
    }
    .dark .wrd-danger-zone { border-color: rgba(248,113,113,0.35); }
    .wrd-danger-text h4 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 700; color: var(--wr-text); margin-bottom: 2px; }
    .wrd-danger-text p { font-size: 12px; color: var(--wr-muted); }
    .wrd-btn-danger {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px;
        background: rgba(220,38,38,0.08);
        border: 1.5px solid rgba(220,38,38,0.3);
        border-radius: 8px; color: #dc2626;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; text-decoration: none;
    }
    .wrd-btn-danger:hover { background: rgba(220,38,38,0.15); border-color: rgba(220,38,38,0.5); transform: translateY(-1px); }
    .dark .wrd-btn-danger { color: #f87171; border-color: rgba(248,113,113,0.35); background: rgba(248,113,113,0.12); }
    .dark .wrd-btn-danger:hover { background: rgba(248,113,113,0.20); }
</style>