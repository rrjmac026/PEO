<style>
    /* ══════════════════════════════════════════
       LIGHT MODE TOKENS
    ══════════════════════════════════════════ */
    :root {
        --db-surface:   #ffffff;
        --db-surface2:  #f8fafc;
        --db-border:    #e2e8f0;
        --db-text:      #0f172a;
        --db-text-sec:  #334155;
        --db-muted:     #64748b;
        --db-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --db-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
    }

    /* ══════════════════════════════════════════
       DARK MODE TOKENS
    ══════════════════════════════════════════ */
    .dark {
        --db-surface:   #1a1f2e;
        --db-surface2:  #1e2335;
        --db-border:    #2a3050;
        --db-text:      #e8eaf6;
        --db-text-sec:  #c5cae9;
        --db-muted:     #7c85a8;
        --db-shadow:    0 1px 4px rgba(0,0,0,0.35);
        --db-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
    }

    /* ── Stat cards ── */
    .db-stat-card {
        background: var(--db-surface);
        border: 1px solid var(--db-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--db-shadow);
        transition: box-shadow 0.25s ease;
    }
    .db-stat-card:hover { box-shadow: var(--db-shadow-lg); }

    .db-stat-label { color: var(--db-muted);  font-size: 13px; font-weight: 500; margin-bottom: 6px; }
    .db-stat-value { color: var(--db-text);   font-size: 30px; font-weight: 900; line-height: 1; }
    .db-stat-sub   { color: var(--db-muted);  font-size: 11px; margin-top: 6px; }

    /* icon trays */
    .db-icon-tray        { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
    .db-icon-tray.blue   { background: #dbeafe; }
    .db-icon-tray.green  { background: #d1fae5; }
    .db-icon-tray.purple { background: #ede9fe; }
    .dark .db-icon-tray.blue   { background: rgba(79,141,255,.15); }
    .dark .db-icon-tray.green  { background: rgba(0,212,170,.13);  }
    .dark .db-icon-tray.purple { background: rgba(167,139,250,.14); }
    .db-icon-tray.blue   i { color: #2563eb; }
    .db-icon-tray.green  i { color: #059669; }
    .db-icon-tray.purple i { color: #7c3aed; }
    .dark .db-icon-tray.blue   i { color: #60a5fa; }
    .dark .db-icon-tray.green  i { color: #34d399; }
    .dark .db-icon-tray.purple i { color: #c084fc; }

    /* stat card footer strips */
    .db-stat-foot         { padding: 10px 24px; border-top: 1px solid var(--db-border); }
    .db-stat-foot.blue    { background: #eff6ff; }
    .db-stat-foot.green   { background: #f0fdf4; }
    .db-stat-foot.purple  { background: #f5f3ff; }
    .dark .db-stat-foot.blue   { background: rgba(37,99,235,.08);  }
    .dark .db-stat-foot.green  { background: rgba(5,150,105,.08);  }
    .dark .db-stat-foot.purple { background: rgba(124,58,237,.08); }
    .db-stat-foot a      { font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: opacity .15s; text-decoration: none; }
    .db-stat-foot.blue   a { color: #2563eb; }
    .db-stat-foot.green  a { color: #059669; }
    .db-stat-foot.purple a { color: #7c3aed; }
    .dark .db-stat-foot.blue   a { color: #60a5fa; }
    .dark .db-stat-foot.green  a { color: #34d399; }
    .dark .db-stat-foot.purple a { color: #c084fc; }
    .db-stat-foot a:hover { opacity: .7; }

    /* ── Quick action cards ── */
    .db-action-card {
        position: relative;
        background: var(--db-surface);
        border: 1px solid var(--db-border);
        border-radius: 12px;
        padding: 24px;
        overflow: hidden;
        box-shadow: var(--db-shadow);
        transition: box-shadow 0.25s ease, transform 0.25s ease;
        display: block; text-decoration: none;
    }
    .db-action-card:hover { box-shadow: var(--db-shadow-lg); transform: translateY(-3px); }

    .db-action-overlay {
        position: absolute; inset: 0; opacity: 0; border-radius: 12px;
        transition: opacity 0.25s ease; pointer-events: none;
    }
    .db-action-card:hover .db-action-overlay { opacity: 1; }

    .db-action-card.orange .db-action-overlay { background: linear-gradient(135deg, #fff7ed 0%, transparent 70%); }
    .db-action-card.green  .db-action-overlay { background: linear-gradient(135deg, #f0fdf4 0%, transparent 70%); }
    .db-action-card.blue   .db-action-overlay { background: linear-gradient(135deg, #eff6ff 0%, transparent 70%); }
    .db-action-card.purple .db-action-overlay { background: linear-gradient(135deg, #f5f3ff 0%, transparent 70%); }
    .dark .db-action-card.orange .db-action-overlay { background: linear-gradient(135deg, rgba(194,65,12,.15) 0%, transparent 70%); }
    .dark .db-action-card.green  .db-action-overlay { background: linear-gradient(135deg, rgba(5,150,105,.12) 0%, transparent 70%); }
    .dark .db-action-card.blue   .db-action-overlay { background: linear-gradient(135deg, rgba(37,99,235,.12) 0%, transparent 70%); }
    .dark .db-action-card.purple .db-action-overlay { background: linear-gradient(135deg, rgba(124,58,237,.12) 0%, transparent 70%); }

    .db-action-icon        { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 16px; position: relative; z-index: 1; }
    .db-action-icon.orange { background: #fff7ed; }
    .db-action-icon.green  { background: #f0fdf4; }
    .db-action-icon.blue   { background: #eff6ff; }
    .db-action-icon.purple { background: #f5f3ff; }
    .dark .db-action-icon.orange { background: rgba(194,65,12,.18);  }
    .dark .db-action-icon.green  { background: rgba(5,150,105,.16);  }
    .dark .db-action-icon.blue   { background: rgba(37,99,235,.16);  }
    .dark .db-action-icon.purple { background: rgba(124,58,237,.16); }
    .db-action-icon.orange i { color: #ea580c; }
    .db-action-icon.green  i { color: #059669; }
    .db-action-icon.blue   i { color: #2563eb; }
    .db-action-icon.purple i { color: #7c3aed; }
    .dark .db-action-icon.orange i { color: #fb923c; }
    .dark .db-action-icon.green  i { color: #34d399; }
    .dark .db-action-icon.blue   i { color: #60a5fa; }
    .dark .db-action-icon.purple i { color: #c084fc; }

    .db-action-title { font-size: 15px; font-weight: 600; color: var(--db-text);  margin-bottom: 4px; position: relative; z-index: 1; }
    .db-action-desc  { font-size: 13px; color: var(--db-muted); position: relative; z-index: 1; }

    /* ── Section headings ── */
    .db-section-heading {
        font-size: 22px; font-weight: 700;
        color: var(--db-text);
        display: flex; align-items: center; gap: 8px;
        margin-bottom: 24px;
    }

    /* ── Table styling ── */
    .db-table-container {
        background: var(--db-surface);
        border: 1px solid var(--db-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--db-shadow);
    }
    .db-table-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--db-border);
        display: flex; align-items: center; justify-content: space-between;
        background: var(--db-surface2);
    }
    .db-table-title { font-size: 16px; font-weight: 600; color: var(--db-text); }
    .db-table-link  { font-size: 13px; font-weight: 600; color: #ea580c; text-decoration: none; transition: opacity 0.15s; }
    .dark .db-table-link { color: #fb923c; }
    .db-table-link:hover { opacity: 0.7; }

    .db-table { width: 100%; border-collapse: collapse; }
    .db-table thead { background: var(--db-surface2); }
    .db-table th {
        padding: 12px 24px; text-align: left;
        font-size: 12px; font-weight: 600; color: var(--db-muted);
        text-transform: uppercase; letter-spacing: 0.5px;
        border-bottom: 1px solid var(--db-border);
    }
    .db-table td {
        padding: 16px 24px;
        border-bottom: 1px solid var(--db-border);
        color: var(--db-text); font-size: 14px;
    }
    .db-table tr:last-child td { border-bottom: none; }
    .db-table tbody tr { transition: background 0.15s; }
    .db-table tbody tr:hover { background: var(--db-surface2); }
    .db-table-secondary { color: var(--db-text-sec); font-size: 13px; }

    /* ── Badges ── */
    .db-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
    .db-badge.approved  { background: #dcfce7; color: #166534; }
    .db-badge.pending   { background: #dbeafe; color: #1e40af; }
    .db-badge.rejected  { background: #fee2e2; color: #991b1b; }
    .db-badge.draft     { background: #f1f5f9; color: #475569; }
    .dark .db-badge.approved  { background: rgba(5,150,105,.15);  color: #34d399; }
    .dark .db-badge.pending   { background: rgba(37,99,235,.15);  color: #60a5fa; }
    .dark .db-badge.rejected  { background: rgba(239,68,68,.15);  color: #fca5a5; }
    .dark .db-badge.draft     { background: rgba(125,132,168,.15); color: #cbd5e1; }

    /* ── Empty state ── */
    .db-empty-state   { padding: 40px 24px; text-align: center; }
    .db-empty-message { color: var(--db-muted); font-size: 14px; margin-bottom: 12px; }
    .db-empty-action  { color: #ea580c; text-decoration: none; font-weight: 600; transition: opacity 0.15s; }
    .dark .db-empty-action { color: #fb923c; }
    .db-empty-action:hover { opacity: 0.7; }
</style>