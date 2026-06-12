<style>
    /* ══════════════════════════════════════════
       LIGHT MODE TOKENS (primary / default)
    ══════════════════════════════════════════ */
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

    /* ══════════════════════════════════════════
       DARK MODE TOKENS (override on .dark)
    ══════════════════════════════════════════ */
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

    .wre-wrap { font-family: 'Inter', sans-serif; }

    /* ── Form container ── */
    .wre-card {
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--wr-shadow);
        transition: box-shadow 0.25s ease;
    }
    .wre-card:hover { box-shadow: var(--wr-shadow-lg); }

    /* ── Form inputs ── */
    .wre-input, .wre-textarea, .wre-select {
        background: var(--wr-surface);
        border: 1px solid var(--wr-border);
        color: var(--wr-text);
        border-radius: 6px;
    }
    .wre-input:focus, .wre-textarea:focus, .wre-select:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .wre-label        { color: var(--wr-text); font-weight: 500; }
    .wre-section-title { color: var(--wr-text); }
    .wre-section-divider { border-bottom: 1px solid var(--wr-border); }
    .wre-readonly     { background: var(--wr-surface2); color: var(--wr-muted); }
</style>