<style>
    /* ══════════════════════════════════════════
       CONCRETE POURING — SHARED DESIGN TOKENS
    ══════════════════════════════════════════ */
    :root {
        --cp-surface:   #ffffff;
        --cp-surface2:  #f8fafc;
        --cp-border:    #e2e8f0;
        --cp-text:      #0f172a;
        --cp-text-sec:  #334155;
        --cp-muted:     #64748b;
        --cp-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --cp-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
        --cp-accent:    #0891b2;
        --cp-accent-bg: #ecfeff;
    }
    .dark {
        --cp-surface:   #1a1f2e;
        --cp-surface2:  #1e2335;
        --cp-border:    #2a3050;
        --cp-text:      #e8eaf6;
        --cp-text-sec:  #c5cae9;
        --cp-muted:     #7c85a8;
        --cp-shadow:    0 1px 4px rgba(0,0,0,0.35);
        --cp-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
        --cp-accent:    #22d3ee;
        --cp-accent-bg: rgba(8,145,178,0.12);
    }

    .cp-wrap { font-family: 'Inter', sans-serif; }

    /* ── Card ── */
    .cp-card {
        background: var(--cp-surface);
        border: 1px solid var(--cp-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--cp-shadow);
        transition: box-shadow 0.25s ease;
    }
    .cp-card:hover { box-shadow: var(--cp-shadow-lg); }

    .cp-card-head {
        display: flex; align-items: center; gap: 10px;
        padding: 16px 24px;
        border-bottom: 1px solid var(--cp-border);
        background: var(--cp-surface2);
    }
    .cp-card-title { font-size: 14px; font-weight: 700; color: var(--cp-text); }
    .cp-card-body  { padding: 24px; }

    .cp-card-head-icon {
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; flex-shrink: 0;
    }
    .cp-card-head-icon.cyan   { background: #cffafe; }
    .cp-card-head-icon.blue   { background: #dbeafe; }
    .cp-card-head-icon.green  { background: #d1fae5; }
    .cp-card-head-icon.orange { background: #fed7aa; }
    .cp-card-head-icon.purple { background: #ede9fe; }
    .cp-card-head-icon.slate  { background: #e2e8f0; }
    .dark .cp-card-head-icon.cyan   { background: rgba(8,145,178,0.18); }
    .dark .cp-card-head-icon.blue   { background: rgba(37,99,235,0.15); }
    .dark .cp-card-head-icon.green  { background: rgba(5,150,105,0.13); }
    .dark .cp-card-head-icon.orange { background: rgba(194,65,12,0.15); }
    .dark .cp-card-head-icon.purple { background: rgba(124,58,237,0.14); }
    .dark .cp-card-head-icon.slate  { background: rgba(148,163,184,0.12); }

    /* ── Info grid ── */
    .cp-info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    @media (max-width: 600px) { .cp-info-grid { grid-template-columns: 1fr; } }
    .cp-info-grid.three { grid-template-columns: repeat(3, 1fr); }
    @media (max-width: 700px) { .cp-info-grid.three { grid-template-columns: repeat(2,1fr); } }
    .cp-info-item      { display: flex; flex-direction: column; gap: 4px; }
    .cp-info-item.span2 { grid-column: span 2; }
    .cp-info-label     { font-size: 11px; font-weight: 600; color: var(--cp-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .cp-info-value     { font-size: 14px; font-weight: 500; color: var(--cp-text); line-height: 1.55; }
    .cp-info-value.empty { color: var(--cp-muted); font-style: italic; font-weight: 400; }
    .cp-info-value.mono  {
        font-family: monospace;
        background: var(--cp-surface2);
        border: 1px solid var(--cp-border);
        border-radius: 6px; padding: 3px 10px;
        font-size: 13px; display: inline-block;
    }

    /* ── Status badges ── */
    .cp-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px; border-radius: 20px;
        font-size: 12px; font-weight: 600; border: 1.5px solid;
    }
    .cp-badge-dot { width: 7px; height: 7px; border-radius: 50%; }

    .cp-badge.requested   { color: #2563eb; border-color: rgba(37,99,235,.3);  background: rgba(37,99,235,.07); }
    .cp-badge.approved    { color: #059669; border-color: rgba(5,150,105,.3);  background: rgba(5,150,105,.07); }
    .cp-badge.disapproved { color: #dc2626; border-color: rgba(220,38,38,.3);  background: rgba(220,38,38,.07); }
    .dark .cp-badge.requested   { color: #60a5fa; }
    .dark .cp-badge.approved    { color: #34d399; }
    .dark .cp-badge.disapproved { color: #f87171; }

    /* ── Table ── */
    .cp-table-container {
        background: var(--cp-surface);
        border: 1px solid var(--cp-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--cp-shadow);
    }
    .cp-table { width: 100%; border-collapse: collapse; }
    .cp-table thead { background: var(--cp-surface2); }
    .cp-table th {
        padding: 12px 20px;
        text-align: left; font-size: 11px; font-weight: 600;
        color: var(--cp-muted); text-transform: uppercase; letter-spacing: 0.5px;
        border-bottom: 1px solid var(--cp-border);
    }
    .cp-table td {
        padding: 14px 20px;
        border-bottom: 1px solid var(--cp-border);
        color: var(--cp-text); font-size: 14px;
    }
    .cp-table tr:last-child td { border-bottom: none; }
    .cp-table tbody tr { transition: background 0.15s; }
    .cp-table tbody tr:hover { background: var(--cp-surface2); }

    /* ── Checklist grid ── */
    .cp-checklist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }
    .cp-check-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 14px;
        background: var(--cp-surface2);
        border: 1px solid var(--cp-border);
        border-radius: 8px;
        font-size: 13px; color: var(--cp-text);
    }
    .cp-check-item.checked { border-color: rgba(5,150,105,0.35); background: rgba(5,150,105,0.06); }
    .cp-check-item.unchecked { opacity: 0.55; }
    .cp-check-icon { font-size: 15px; flex-shrink: 0; }

    /* ── Stat cards ── */
    .cp-stat-card {
        background: var(--cp-surface);
        border: 1px solid var(--cp-border);
        border-radius: 12px; overflow: hidden;
        box-shadow: var(--cp-shadow);
        transition: box-shadow 0.25s ease;
    }
    .cp-stat-card:hover { box-shadow: var(--cp-shadow-lg); }
    .cp-stat-label { color: var(--cp-muted); font-size: 13px; font-weight: 500; margin-bottom: 6px; }
    .cp-stat-value { color: var(--cp-text); font-size: 30px; font-weight: 900; line-height: 1; }
    .cp-stat-foot  { padding: 10px 24px; border-top: 1px solid var(--cp-border); }
    .cp-stat-foot a { font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: opacity .15s; }
    .cp-stat-foot a:hover { opacity: .7; }

    .cp-stat-foot.cyan   { background: #ecfeff; }
    .cp-stat-foot.blue   { background: #eff6ff; }
    .cp-stat-foot.green  { background: #f0fdf4; }
    .cp-stat-foot.red    { background: #fef2f2; }
    .dark .cp-stat-foot.cyan  { background: rgba(8,145,178,.08);  }
    .dark .cp-stat-foot.blue  { background: rgba(37,99,235,.08);  }
    .dark .cp-stat-foot.green { background: rgba(5,150,105,.08);  }
    .dark .cp-stat-foot.red   { background: rgba(220,38,38,.08);  }
    .cp-stat-foot.cyan  a { color: #0891b2; }
    .cp-stat-foot.blue  a { color: #2563eb; }
    .cp-stat-foot.green a { color: #059669; }
    .cp-stat-foot.red   a { color: #dc2626; }
    .dark .cp-stat-foot.cyan  a { color: #22d3ee; }
    .dark .cp-stat-foot.blue  a { color: #60a5fa; }
    .dark .cp-stat-foot.green a { color: #34d399; }
    .dark .cp-stat-foot.red   a { color: #f87171; }

    .cp-icon-tray { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
    .cp-icon-tray.cyan   { background: #cffafe; }
    .cp-icon-tray.blue   { background: #dbeafe; }
    .cp-icon-tray.green  { background: #d1fae5; }
    .cp-icon-tray.red    { background: #fee2e2; }
    .dark .cp-icon-tray.cyan  { background: rgba(8,145,178,.18); }
    .dark .cp-icon-tray.blue  { background: rgba(37,99,235,.15); }
    .dark .cp-icon-tray.green { background: rgba(5,150,105,.13); }
    .dark .cp-icon-tray.red   { background: rgba(220,38,38,.13); }
    .cp-icon-tray.cyan  i { color: #0891b2; }
    .cp-icon-tray.blue  i { color: #2563eb; }
    .cp-icon-tray.green i { color: #059669; }
    .cp-icon-tray.red   i { color: #dc2626; }
    .dark .cp-icon-tray.cyan  i { color: #22d3ee; }
    .dark .cp-icon-tray.blue  i { color: #60a5fa; }
    .dark .cp-icon-tray.green i { color: #34d399; }
    .dark .cp-icon-tray.red   i { color: #f87171; }

    /* ── Divider ── */
    .cp-divider { height: 1px; background: var(--cp-border); margin: 20px 0; }

    /* ── Hero ── */
    .cp-hero {
        background: var(--cp-surface);
        border: 1px solid var(--cp-border);
        border-radius: 12px;
        padding: 20px 24px;
        display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
        box-shadow: var(--cp-shadow);
        transition: box-shadow .25s;
    }
    .cp-hero:hover { box-shadow: var(--cp-shadow-lg); }
    .cp-hero-left  { display: flex; align-items: center; gap: 16px; }
    .cp-req-id {
        font-size: 13px; font-weight: 700; color: var(--cp-muted);
        background: var(--cp-surface2); border: 1px solid var(--cp-border);
        padding: 5px 12px; border-radius: 6px; letter-spacing: 0.5px;
    }
    .cp-project-name { font-size: 18px; font-weight: 700; color: var(--cp-text); letter-spacing: -0.2px; }
    .cp-project-loc  { font-size: 13px; color: var(--cp-muted); margin-top: 2px; }

    /* ── Meta chips ── */
    .cp-meta-row  { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .cp-meta-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--cp-surface2); border: 1px solid var(--cp-border);
        border-radius: 6px; padding: 5px 12px;
        font-size: 12px; color: var(--cp-muted);
    }
    .cp-meta-chip strong { color: var(--cp-text); font-weight: 500; }

    /* ── Review step timeline ── */
    .cp-timeline { display: flex; flex-direction: column; gap: 0; }
    .cp-timeline-item {
        display: flex; align-items: flex-start; gap: 14px;
        padding: 16px 0;
        border-bottom: 1px solid var(--cp-border);
    }
    .cp-timeline-item:last-child { border-bottom: none; padding-bottom: 0; }
    .cp-tl-icon-wrap { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; width: 32px; }
    .cp-tl-icon {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; border: 2px solid var(--cp-border);
        background: var(--cp-surface2); color: var(--cp-muted);
    }
    .cp-tl-icon.done    { background: #d1fae5; border-color: #059669; color: #059669; }
    .cp-tl-icon.active  { background: #dbeafe; border-color: #2563eb; color: #2563eb; }
    .cp-tl-icon.waiting { background: var(--cp-surface2); border-color: var(--cp-border); color: var(--cp-muted); }
    .dark .cp-tl-icon.done   { background: rgba(5,150,105,.18); border-color: #34d399; color: #34d399; }
    .dark .cp-tl-icon.active { background: rgba(37,99,235,.18); border-color: #60a5fa; color: #60a5fa; }
    .cp-tl-label  { font-size: 13px; font-weight: 600; color: var(--cp-text); margin-bottom: 2px; }
    .cp-tl-name   { font-size: 13px; color: var(--cp-text-sec); }
    .cp-tl-date   { font-size: 11px; color: var(--cp-muted); margin-top: 4px; }
    .cp-tl-remark { font-size: 12px; color: var(--cp-muted); margin-top: 6px; font-style: italic; line-height: 1.5; }

    /* ── Form inputs ── */
    .cp-input, .cp-textarea, .cp-select {
        background: var(--cp-surface);
        border: 1.5px solid var(--cp-border);
        color: var(--cp-text);
        border-radius: 8px;
        font-size: 14px; padding: 10px 14px;
        width: 100%; outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .cp-input:focus, .cp-textarea:focus, .cp-select:focus {
        border-color: var(--cp-accent);
        box-shadow: 0 0 0 3px rgba(8,145,178,0.12);
    }
    .cp-label { font-size: 13px; font-weight: 600; color: var(--cp-muted); display: block; margin-bottom: 6px; }

    /* ── Danger zone ── */
    .cp-danger-zone {
        background: var(--cp-surface);
        border: 1px solid rgba(220,38,38,0.25);
        border-radius: 12px;
        padding: 18px 24px;
        display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
        box-shadow: var(--cp-shadow);
    }
    .cp-danger-text h4 { font-size: 14px; font-weight: 700; color: var(--cp-text); margin-bottom: 2px; }
    .cp-danger-text p  { font-size: 12px; color: var(--cp-muted); }
    .cp-btn-danger {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px;
        background: rgba(220,38,38,0.08); border: 1.5px solid rgba(220,38,38,0.3);
        border-radius: 8px; color: #dc2626;
        font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all .2s; text-decoration: none;
    }
    .cp-btn-danger:hover { background: rgba(220,38,38,0.15); transform: translateY(-1px); }
    .dark .cp-btn-danger { color: #f87171; }

    /* ── Alert box for reviewer remarks ── */
    .cp-review-box {
        background: var(--cp-surface2);
        border: 1px solid var(--cp-border);
        border-radius: 10px; padding: 18px;
        margin-top: 16px;
    }
    .cp-review-box.my-turn {
        border-color: rgba(8,145,178,0.4);
        background: rgba(8,145,178,0.04);
    }

    /* ── Empty state ── */
    .cp-empty { padding: 60px 24px; text-align: center; }
    .cp-empty-icon { font-size: 40px; margin-bottom: 12px; opacity: .4; }
    .cp-empty-msg  { color: var(--cp-muted); font-size: 14px; margin-bottom: 12px; }
    .cp-empty-action { color: var(--cp-accent); text-decoration: none; font-weight: 600; }
</style>