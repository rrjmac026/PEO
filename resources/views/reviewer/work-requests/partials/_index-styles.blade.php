<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    :root {
        --wri-bg:       #f1f5f9;
        --wri-surface:  #ffffff;
        --wri-surface2: #f8fafc;
        --wri-border:   #cbd5e1;
        --wri-accent:   #2563eb;
        --wri-accent2:  #059669;
        --wri-accent3:  #dc2626;
        --wri-text:     #0f172a;
        --wri-muted:    #64748b;
        --wri-label:    #475569;
        --wri-glow-1:   rgba(37,99,235,0.04);
        --wri-glow-2:   rgba(5,150,105,0.03);
        --wri-radius:   12px;
        --wri-radius-sm:8px;
        --wri-shadow:    0 1px 4px rgba(0,0,0,0.06);
        --wri-shadow-lg: 0 4px 16px rgba(0,0,0,0.10);
    }

    .dark {
        --wri-bg:       #0f1117;
        --wri-surface:  #181c27;
        --wri-surface2: #1e2335;
        --wri-border:   #2a3050;
        --wri-accent:   #4f8dff;
        --wri-accent2:  #00d4aa;
        --wri-accent3:  #ff6b6b;
        --wri-text:     #e8eaf6;
        --wri-muted:    #7c85a8;
        --wri-label:    #a8b3d8;
        --wri-glow-1:   rgba(79,141,255,0.05);
        --wri-glow-2:   rgba(0,212,170,0.04);
        --wri-shadow:    0 1px 4px rgba(0,0,0,0.35);
        --wri-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
    }

    .wri-wrap { font-family: 'Inter', sans-serif; }

    .wri-filter-card {
        background: var(--wri-surface);
        border: 1px solid var(--wri-border);
        border-radius: var(--wri-radius);
        padding: 24px;
        box-shadow: var(--wri-shadow);
        margin-bottom: 24px;
    }

    .wri-form-group { display: flex; flex-direction: column; gap: 6px; }

    .wri-label {
        font-size: 13px; font-weight: 600;
        color: var(--wri-text);
        text-transform: uppercase; letter-spacing: 0.5px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .wri-input, .wri-select {
        background: var(--wri-surface);
        border: 1px solid var(--wri-border);
        color: var(--wri-text);
        border-radius: var(--wri-radius-sm);
        padding: 8px 12px;
        font-size: 13px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .wri-input:focus, .wri-select:focus {
        outline: none;
        border-color: var(--wri-accent);
        box-shadow: 0 0 0 3px var(--wri-glow-1);
    }

    .wri-input::placeholder { color: var(--wri-muted); }

    .wri-btn {
        padding: 8px 16px;
        border-radius: var(--wri-radius-sm);
        font-size: 13px; font-weight: 600;
        border: none; cursor: pointer;
        transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 6px;
    }

    .wri-btn-primary { background: var(--wri-accent); color: white; }
    .wri-btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .wri-btn-secondary { background: var(--wri-surface2); color: var(--wri-text); border: 1px solid var(--wri-border); }
    .wri-btn-secondary:hover { background: var(--wri-border); }

    .wri-notice {
        border-radius: var(--wri-radius-sm);
        padding: 16px; margin-bottom: 24px;
        border-left: 4px solid;
        background: var(--wri-surface);
        border: 1px solid var(--wri-border);
    }

    .wri-notice.blue   { background: rgba(37,99,235,0.08);   border-left-color: #2563eb; color: #1e40af; }
    .wri-notice.purple { background: rgba(124,58,237,0.08);  border-left-color: #7c3aed; color: #6d28d9; }
    .wri-notice.green  { background: rgba(5,150,105,0.08);   border-left-color: #059669; color: #047857; }
    .wri-notice.yellow { background: rgba(180,83,9,0.08);    border-left-color: #b45309; color: #92400e; }
    .wri-notice.amber  { background: rgba(245,158,11,0.08);  border-left-color: #f59e0b; color: #92400e; }

    .dark .wri-notice.blue   { background: var(--wri-glow-1);             border-left-color: #4f8dff; color: #93c5fd; }
    .dark .wri-notice.purple { background: rgba(167,139,250,0.08);        border-left-color: #a855f7; color: #d8b4fe; }
    .dark .wri-notice.green  { background: var(--wri-glow-2);             border-left-color: #00d4aa; color: #6ee7b7; }
    .dark .wri-notice.yellow { background: rgba(234,179,8,0.08);          border-left-color: #eab308; color: #facc15; }
    .dark .wri-notice.amber  { background: rgba(245,158,11,0.08);         border-left-color: #f59e0b; color: #fcd34d; }

    .wri-table-wrapper {
        background: var(--wri-surface);
        border: 1px solid var(--wri-border);
        border-radius: var(--wri-radius);
        overflow: hidden; box-shadow: var(--wri-shadow);
    }

    .wri-table { width: 100%; border-collapse: collapse; font-family: 'Inter', sans-serif; }
    .wri-table-head { background: var(--wri-surface2); }

    .wri-th {
        padding: 16px 24px; text-align: left;
        font-size: 11px; font-weight: 700;
        color: var(--wri-muted); text-transform: uppercase; letter-spacing: 0.5px;
        border-bottom: 1px solid var(--wri-border);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .wri-td {
        padding: 16px 24px;
        border-bottom: 1px solid var(--wri-border);
        color: var(--wri-text); font-size: 13px;
        background: var(--wri-surface); transition: background 0.2s;
    }

    .wri-table-row:hover .wri-td { background: var(--wri-surface2); }
    .wri-table-row:last-child .wri-td { border-bottom: none; }

    .wri-project-name {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700; color: var(--wri-text);
        margin-bottom: 4px; font-size: 14px;
    }
    .wri-project-id { font-size: 12px; color: var(--wri-muted); }

    .wri-badge {
        display: inline-flex; align-items: center;
        padding: 6px 12px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
        font-family: 'Plus Jakarta Sans', sans-serif;
        border: 1px solid; transition: all 0.2s;
    }

    .wri-badge.approved  { background: rgba(52,211,153,0.15);  color: #34d399; border-color: rgba(52,211,153,0.3); }
    .wri-badge.rejected  { background: rgba(248,113,113,0.15); color: #f87171; border-color: rgba(248,113,113,0.3); }
    .wri-badge.pending   { background: rgba(234,179,8,0.15);   color: #eab308; border-color: rgba(234,179,8,0.3); }
    .wri-badge.submitted { background: rgba(96,165,250,0.15);  color: #60a5fa; border-color: rgba(96,165,250,0.3); }
    .wri-badge.draft     { background: rgba(148,163,184,0.15); color: #94a3b8; border-color: rgba(148,163,184,0.3); }
    .wri-badge.inspected { background: rgba(192,132,252,0.15); color: #c084fc; border-color: rgba(192,132,252,0.3); }
    .wri-badge.reviewed  { background: rgba(129,140,248,0.15); color: #818cf8; border-color: rgba(129,140,248,0.3); }
    .wri-badge.accepted  { background: rgba(52,211,153,0.15);  color: #34d399; border-color: rgba(52,211,153,0.3); }

    html:not(.dark) .wri-badge.approved  { background: rgba(5,150,105,0.12);   color: #059669; border-color: rgba(5,150,105,0.25); }
    html:not(.dark) .wri-badge.rejected  { background: rgba(220,38,38,0.12);   color: #dc2626; border-color: rgba(220,38,38,0.25); }
    html:not(.dark) .wri-badge.pending   { background: rgba(180,83,9,0.12);    color: #b45309; border-color: rgba(180,83,9,0.25); }
    html:not(.dark) .wri-badge.submitted { background: rgba(37,99,235,0.12);   color: #2563eb; border-color: rgba(37,99,235,0.25); }
    html:not(.dark) .wri-badge.draft     { background: rgba(100,116,139,0.12); color: #64748b; border-color: rgba(100,116,139,0.25); }
    html:not(.dark) .wri-badge.inspected { background: rgba(124,58,237,0.12);  color: #7c3aed; border-color: rgba(124,58,237,0.25); }
    html:not(.dark) .wri-badge.reviewed  { background: rgba(67,56,202,0.12);   color: #4338ca; border-color: rgba(67,56,202,0.25); }
    html:not(.dark) .wri-badge.accepted  { background: rgba(5,150,105,0.12);   color: #059669; border-color: rgba(5,150,105,0.25); }

    .wri-status-done    { color: #34d399; font-weight: 600; }
    .wri-status-pending { color: #eab308; font-weight: 600; }
    html:not(.dark) .wri-status-done    { color: #059669; }
    html:not(.dark) .wri-status-pending { color: #b45309; }

    .wri-link {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px; background: var(--wri-accent);
        color: white; border-radius: var(--wri-radius-sm);
        font-size: 12px; font-weight: 600;
        text-decoration: none; transition: background 0.2s;
        font-family: 'Inter', sans-serif;
    }
    .wri-link:hover { opacity: 0.9; transform: translateY(-1px); }

    .wri-alert {
        margin-bottom: 16px; padding: 12px 16px;
        border-radius: var(--wri-radius-sm);
        border-left: 4px solid; font-size: 13px;
        background: var(--wri-surface); border: 1px solid var(--wri-border);
    }

    .wri-alert.success { background: rgba(52,211,153,0.12);  border-left-color: #34d399; color: #34d399; }
    .wri-alert.error   { background: rgba(248,113,113,0.12); border-left-color: #f87171; color: #f87171; }
    html:not(.dark) .wri-alert.success { background: rgba(5,150,105,0.12);  color: #059669; border-left-color: #059669; }
    html:not(.dark) .wri-alert.error   { background: rgba(220,38,38,0.12);  color: #dc2626; border-left-color: #dc2626; }

    .wri-pagination {
        padding: 16px 24px;
        border-top: 1px solid var(--wri-border);
        background: var(--wri-surface);
    }
</style>