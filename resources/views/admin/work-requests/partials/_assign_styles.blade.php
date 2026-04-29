<style>
        /* =============================================
           DARK / LIGHT MODE TOKENS  (mirrors create blade)
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
            --wr-radius-sm: 8px;
        }
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

        /* ── Page wrap ── */
        .wr-wrap { font-family: 'Inter', sans-serif; }

        /* ── Main Card ── */
        .wr-card {
            background: var(--wr-surface);
            border: 1px solid var(--wr-border);
            border-radius: var(--wr-radius);
            overflow: hidden; position: relative;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .wr-card::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 60% 40% at 20% 10%, var(--wr-glow-1) 0%, transparent 60%),
                radial-gradient(ellipse 50% 30% at 80% 80%, var(--wr-glow-2) 0%, transparent 60%);
            pointer-events: none;
        }
        .wr-card-body { padding: 2rem; position: relative; z-index: 1; }

        /* ── Page header card ── */
        .wr-header-card {
            background: var(--wr-surface);
            border: 1px solid var(--wr-border);
            border-radius: var(--wr-radius);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            position: relative; overflow: hidden;
        }
        .wr-header-card::before {
            content: '';
            position: absolute; left: 0; top: 0; bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--wr-accent), var(--wr-accent2));
        }
        .wr-header-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18px; font-weight: 700;
            color: var(--wr-text); letter-spacing: -0.2px;
            line-height: 1.3;
        }
        .wr-header-sub {
            font-size: 12px; color: var(--wr-muted);
            margin-top: 2px;
        }
        .wr-back-btn {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 13px; font-weight: 600;
            color: var(--wr-muted);
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: none; transition: color 0.2s;
            padding: 7px 14px;
            border: 1.5px solid var(--wr-border);
            border-radius: var(--wr-radius-sm);
        }
        .wr-back-btn:hover { color: var(--wr-accent); border-color: var(--wr-accent); background: rgba(79,141,255,0.06); }

        /* ── Info banner ── */
        .wr-info-banner {
            background: rgba(79,141,255,0.08);
            border: 1px solid rgba(79,141,255,0.22);
            border-radius: var(--wr-radius-sm);
            padding: 1rem 1.25rem;
            margin-bottom: 1.75rem;
            display: flex; gap: 12px; align-items: flex-start;
        }
        html:not(.dark) .wr-info-banner {
            background: rgba(37,99,235,0.06);
            border-color: rgba(37,99,235,0.18);
        }
        .wr-info-icon {
            font-size: 18px; flex-shrink: 0; margin-top: 1px;
        }
        .wr-info-text { font-size: 13px; color: var(--wr-muted); line-height: 1.6; }
        .wr-info-text strong { color: var(--wr-text); }
        .wr-pipeline {
            display: flex; flex-wrap: wrap; gap: 4px; align-items: center;
            margin-top: 10px;
        }
        .wr-pipeline-step {
            font-size: 11px; font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 3px 10px; border-radius: 20px;
            white-space: nowrap;
        }
        .wr-pipeline-step.regular {
            background: rgba(79,141,255,0.12); color: var(--wr-accent);
            border: 1px solid rgba(79,141,255,0.2);
        }
        html:not(.dark) .wr-pipeline-step.regular {
            background: rgba(37,99,235,0.08); color: #2563eb; border-color: rgba(37,99,235,0.2);
        }
        .wr-pipeline-step.final {
            background: rgba(0,212,170,0.12); color: var(--wr-accent2);
            border: 1px solid rgba(0,212,170,0.22);
        }
        html:not(.dark) .wr-pipeline-step.final {
            background: rgba(5,150,105,0.08); color: #059669; border-color: rgba(5,150,105,0.2);
        }
        .wr-pipeline-arrow {
            color: var(--wr-muted); font-size: 12px;
        }
        .wr-note-badge {
            display: inline-flex; align-items: center; gap: 5px;
            margin-top: 10px;
            font-size: 12px; font-weight: 600;
            color: var(--wr-accent2);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ── Error list ── */
        .wr-error-box {
            background: rgba(220,38,38,0.08);
            border: 1px solid rgba(220,38,38,0.25);
            border-radius: var(--wr-radius-sm);
            padding: 0.875rem 1.125rem;
            margin-bottom: 1.5rem;
        }
        .wr-error-box ul { list-style: disc inside; }
        .wr-error-box li { font-size: 13px; color: var(--wr-error); line-height: 1.6; }

        /* ── Section label ── */
        .wr-section-label {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 10px; font-weight: 800;
            letter-spacing: 1px; text-transform: uppercase;
            color: var(--wr-muted);
            padding: 0.5rem 0 0.75rem;
            border-bottom: 1px solid var(--wr-border);
            margin-bottom: 0;
        }

        /* ── Slot rows ── */
        .wr-slot-list { display: flex; flex-direction: column; }
        .wr-slot {
            display: flex; align-items: center; gap: 16px;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--wr-border);
            transition: background 0.18s;
        }
        .wr-slot:last-child { border-bottom: none; }
        .wr-slot:hover { background: var(--wr-surface2); }

        /* Step badge */
        .wr-step-badge {
            flex-shrink: 0;
            width: 34px; height: 34px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            border: 2px solid transparent;
            transition: all 0.2s;
        }
        .wr-step-badge.regular {
            background: rgba(79,141,255,0.1);
            color: var(--wr-accent);
            border-color: rgba(79,141,255,0.2);
        }
        html:not(.dark) .wr-step-badge.regular {
            background: rgba(37,99,235,0.08);
            color: #2563eb;
            border-color: rgba(37,99,235,0.2);
        }
        .wr-step-badge.final {
            background: rgba(0,212,170,0.12);
            color: var(--wr-accent2);
            border-color: rgba(0,212,170,0.25);
        }
        html:not(.dark) .wr-step-badge.final {
            background: rgba(5,150,105,0.08);
            color: #059669;
            border-color: rgba(5,150,105,0.2);
        }

        /* Slot label column */
        .wr-slot-label-col { width: 200px; flex-shrink: 0; }
        .wr-slot-label-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 700;
            color: var(--wr-text);
        }
        .wr-slot-label-name.final-label { color: var(--wr-accent2); }
        html:not(.dark) .wr-slot-label-name.final-label { color: #059669; }
        .wr-slot-label-hint {
            font-size: 11px; color: var(--wr-muted); margin-top: 2px;
        }
        .wr-final-pill {
            display: inline-block;
            font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px;
            padding: 2px 7px; border-radius: 20px;
            background: rgba(0,212,170,0.12); color: var(--wr-accent2);
            border: 1px solid rgba(0,212,170,0.2);
            margin-left: 5px; vertical-align: middle;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        html:not(.dark) .wr-final-pill {
            background: rgba(5,150,105,0.08); color: #059669; border-color: rgba(5,150,105,0.2);
        }

        /* Slot select */
        .wr-slot-select-col { flex: 1; }
        .wr-slot-select-wrap { position: relative; }
        .wr-slot-select-wrap select {
            width: 100%;
            background: var(--wr-surface2);
            border: 1.5px solid var(--wr-border);
            border-radius: var(--wr-radius-sm);
            color: var(--wr-text);
            font-family: 'Inter', sans-serif;
            font-size: 13px; line-height: 1.5;
            padding: 9px 36px 9px 14px;
            outline: none; transition: all 0.2s;
            appearance: none; -webkit-appearance: none; cursor: pointer;
        }
        .wr-slot-select-wrap select:focus {
            border-color: var(--wr-accent);
            background: var(--wr-focus-bg);
            box-shadow: 0 0 0 3px rgba(79,141,255,0.12);
        }
        html:not(.dark) .wr-slot-select-wrap select:focus {
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .wr-slot-select-wrap select option { background: var(--wr-surface2); color: var(--wr-text); }
        .wr-slot-select-wrap::after {
            content: '▾';
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            color: var(--wr-muted); font-size: 13px;
            pointer-events: none;
        }
        /* Final step select highlight */
        .wr-slot-select-wrap.final-select select {
            border-color: rgba(0,212,170,0.3);
        }
        .wr-slot-select-wrap.final-select select:focus {
            border-color: var(--wr-accent2);
            box-shadow: 0 0 0 3px rgba(0,212,170,0.12);
        }
        html:not(.dark) .wr-slot-select-wrap.final-select select {
            border-color: rgba(5,150,105,0.25);
        }

        /* Error msg */
        .wr-field-error {
            font-size: 11px; color: var(--wr-error);
            margin-top: 4px; display: flex; align-items: center; gap: 4px;
        }

        /* ── Footer info row ── */
        .wr-footer-note {
            display: flex; align-items: center; gap: 12px;
            padding: 0.875rem 1.5rem;
            background: var(--wr-surface2);
            border-top: 1px solid var(--wr-border);
        }
        .wr-footer-note-icon {
            width: 34px; height: 34px; flex-shrink: 0;
            background: var(--wr-surface);
            border: 1.5px solid var(--wr-border);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
        }
        .wr-footer-note-text { font-size: 12px; color: var(--wr-muted); line-height: 1.5; }
        .wr-footer-note-text strong { color: var(--wr-text); }

        /* ── Actions ── */
        .wr-actions {
            display: flex; justify-content: flex-end; align-items: center; gap: 10px;
            padding: 1.125rem 1.5rem;
            border-top: 1px solid var(--wr-border);
        }
        .wr-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 20px; border: none; border-radius: var(--wr-radius-sm);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 700;
            cursor: pointer; transition: all 0.22s; text-decoration: none;
        }
        .wr-btn-ghost {
            background: transparent; border: 1.5px solid var(--wr-border); color: var(--wr-muted);
        }
        .wr-btn-ghost:hover { border-color: var(--wr-accent); color: var(--wr-accent); background: rgba(79,141,255,0.06); }
        .wr-btn-primary {
            background: var(--wr-accent); color: white;
            box-shadow: 0 4px 18px rgba(79,141,255,0.3);
        }
        .wr-btn-primary:hover { filter: brightness(1.1); transform: translateY(-1px); }
        html:not(.dark) .wr-btn-primary { box-shadow: 0 4px 18px rgba(37,99,235,0.25); }

        @media (max-width: 640px) {
            .wr-slot { flex-wrap: wrap; gap: 10px; padding: 0.875rem 1rem; }
            .wr-slot-label-col { width: 100%; }
            .wr-slot-select-col { width: 100%; flex: none; }
            .wr-header-card { flex-direction: column; align-items: flex-start; gap: 10px; }
        }
    </style>