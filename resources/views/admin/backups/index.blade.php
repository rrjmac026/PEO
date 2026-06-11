{{-- resources/views/admin/backups/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Database Backups')

@push('styles')
<style>
    /* ══════════════════════════════════════════
       BACKUP PAGE — Design tokens (mirrors sidebar)
    ══════════════════════════════════════════ */
    :root {
        --bp-surface:        #ffffff;
        --bp-surface2:       #f8fafc;
        --bp-surface3:       #f1f5f9;
        --bp-border:         #e2e8f0;
        --bp-border2:        #cbd5e1;
        --bp-text:           #0f172a;
        --bp-text-sec:       #334155;
        --bp-muted:          #64748b;
        --bp-shadow-sm:      0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --bp-shadow:         0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
        --bp-shadow-md:      0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
        --bp-accent:         #ea580c;
        --bp-accent-light:   #fff7ed;
        --bp-accent-hover:   #ffedd5;
        --bp-accent-dark:    #c2410c;
        --bp-accent-ring:    rgba(234,88,12,.25);
        /* Status colors */
        --bp-green:          #16a34a;
        --bp-green-bg:       #f0fdf4;
        --bp-green-border:   #bbf7d0;
        --bp-yellow:         #d97706;
        --bp-yellow-bg:      #fffbeb;
        --bp-yellow-border:  #fde68a;
        --bp-red:            #dc2626;
        --bp-red-bg:         #fef2f2;
        --bp-red-border:     #fecaca;
        --bp-blue:           #2563eb;
        --bp-blue-bg:        #eff6ff;
        --bp-blue-border:    #bfdbfe;
        --bp-slate:          #475569;
        --bp-slate-bg:       #f8fafc;
        --bp-slate-border:   #e2e8f0;
        /* Radius */
        --bp-radius-sm:      8px;
        --bp-radius:         12px;
        --bp-radius-lg:      16px;
    }

    .dark {
        --bp-surface:        #1a1f2e;
        --bp-surface2:       #1e2335;
        --bp-surface3:       #222840;
        --bp-border:         #2a3050;
        --bp-border2:        #374168;
        --bp-text:           #e8eaf6;
        --bp-text-sec:       #c5cae9;
        --bp-muted:          #7c85a8;
        --bp-accent-light:   rgba(194,65,12,.18);
        --bp-accent-hover:   rgba(194,65,12,.12);
        --bp-green-bg:       rgba(22,163,74,.12);
        --bp-green-border:   rgba(22,163,74,.30);
        --bp-yellow-bg:      rgba(217,119,6,.12);
        --bp-yellow-border:  rgba(217,119,6,.30);
        --bp-red-bg:         rgba(220,38,38,.12);
        --bp-red-border:     rgba(220,38,38,.30);
        --bp-blue-bg:        rgba(37,99,235,.12);
        --bp-blue-border:    rgba(37,99,235,.30);
        --bp-slate-bg:       rgba(71,85,105,.12);
        --bp-slate-border:   rgba(71,85,105,.30);
    }

    /* ── Page header ── */
    .bp-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 28px;
    }
    .bp-page-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--bp-text);
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .bp-page-title .title-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--bp-accent-light);
        color: var(--bp-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .bp-page-subtitle {
        font-size: 13px;
        color: var(--bp-muted);
        margin-top: 3px;
    }
    .bp-header-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    /* ── Buttons ── */
    .bp-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        border-radius: var(--bp-radius-sm);
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.18s, box-shadow 0.18s, transform 0.12s, opacity 0.18s;
        white-space: nowrap;
    }
    .bp-btn:active { transform: scale(0.97); }
    .bp-btn-primary {
        background: var(--bp-accent);
        color: #fff;
        box-shadow: 0 2px 8px var(--bp-accent-ring);
    }
    .bp-btn-primary:hover { background: var(--bp-accent-dark); box-shadow: 0 4px 14px var(--bp-accent-ring); }
    .bp-btn-secondary {
        background: var(--bp-surface);
        color: var(--bp-text-sec);
        border: 1px solid var(--bp-border);
        box-shadow: var(--bp-shadow-sm);
    }
    .bp-btn-secondary:hover { background: var(--bp-surface2); border-color: var(--bp-border2); }
    .bp-btn-ghost {
        background: transparent;
        color: var(--bp-muted);
        border: 1px solid var(--bp-border);
    }
    .bp-btn-ghost:hover { background: var(--bp-surface2); color: var(--bp-text-sec); }
    .bp-btn-danger {
        background: var(--bp-red-bg);
        color: var(--bp-red);
        border: 1px solid var(--bp-red-border);
    }
    .bp-btn-danger:hover { background: #fee2e2; }
    .bp-btn-sm { padding: 6px 11px; font-size: 12px; gap: 5px; }
    .bp-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    /* ── Stat cards ── */
    .bp-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }
    .bp-stat-card {
        background: var(--bp-surface);
        border: 1px solid var(--bp-border);
        border-radius: var(--bp-radius);
        padding: 18px 16px 14px;
        box-shadow: var(--bp-shadow-sm);
        position: relative;
        overflow: hidden;
        transition: box-shadow 0.2s;
    }
    .bp-stat-card:hover { box-shadow: var(--bp-shadow); }
    .bp-stat-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: var(--bp-radius);
        border-top: 2px solid transparent;
        pointer-events: none;
    }
    .bp-stat-card.accent::after  { border-top-color: var(--bp-accent); }
    .bp-stat-card.green::after   { border-top-color: var(--bp-green); }
    .bp-stat-card.red::after     { border-top-color: var(--bp-red); }
    .bp-stat-card.yellow::after  { border-top-color: var(--bp-yellow); }
    .bp-stat-card.blue::after    { border-top-color: var(--bp-blue); }
    .bp-stat-card.slate::after   { border-top-color: var(--bp-slate); }

    .bp-stat-label {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--bp-muted);
        margin-bottom: 10px;
    }
    .bp-stat-value {
        font-size: 26px;
        font-weight: 800;
        color: var(--bp-text);
        letter-spacing: -0.5px;
        line-height: 1;
        margin-bottom: 4px;
    }
    .bp-stat-sub {
        font-size: 11px;
        color: var(--bp-muted);
    }
    .bp-stat-icon {
        position: absolute;
        right: 14px;
        top: 14px;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }
    .bp-stat-card.accent .bp-stat-icon { background: var(--bp-accent-light);  color: var(--bp-accent); }
    .bp-stat-card.green  .bp-stat-icon { background: var(--bp-green-bg);       color: var(--bp-green); }
    .bp-stat-card.red    .bp-stat-icon { background: var(--bp-red-bg);         color: var(--bp-red); }
    .bp-stat-card.yellow .bp-stat-icon { background: var(--bp-yellow-bg);      color: var(--bp-yellow); }
    .bp-stat-card.blue   .bp-stat-icon { background: var(--bp-blue-bg);        color: var(--bp-blue); }
    .bp-stat-card.slate  .bp-stat-icon { background: var(--bp-slate-bg);       color: var(--bp-slate); }

    /* ── Section cards (panels) ── */
    .bp-card {
        background: var(--bp-surface);
        border: 1px solid var(--bp-border);
        border-radius: var(--bp-radius);
        box-shadow: var(--bp-shadow-sm);
        overflow: hidden;
    }
    .bp-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--bp-border);
        background: var(--bp-surface2);
    }
    .bp-card-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--bp-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .bp-card-title .card-icon {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: var(--bp-accent-light);
        color: var(--bp-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }
    .bp-card-body { padding: 20px; }

    /* ── Latest backup info strip ── */
    .bp-latest-strip {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: var(--bp-green-bg);
        border: 1px solid var(--bp-green-border);
        border-radius: var(--bp-radius-sm);
        margin-bottom: 20px;
        font-size: 13px;
        color: var(--bp-green);
    }
    .bp-latest-strip.warning {
        background: var(--bp-yellow-bg);
        border-color: var(--bp-yellow-border);
        color: var(--bp-yellow);
    }
    .bp-latest-strip .strip-icon { font-size: 15px; }

    /* ── Quick actions row ── */
    .bp-quick-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
        margin-bottom: 24px;
    }
    .bp-quick-card {
        background: var(--bp-surface);
        border: 1px solid var(--bp-border);
        border-radius: var(--bp-radius);
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 13px;
        cursor: pointer;
        transition: background 0.18s, border-color 0.18s, box-shadow 0.18s, transform 0.12s;
        box-shadow: var(--bp-shadow-sm);
        text-decoration: none;
        color: inherit;
    }
    .bp-quick-card:hover {
        background: var(--bp-surface2);
        border-color: var(--bp-border2);
        box-shadow: var(--bp-shadow);
        transform: translateY(-1px);
    }
    .bp-quick-card:active { transform: translateY(0); }
    .bp-quick-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .bp-quick-icon.orange { background: var(--bp-accent-light); color: var(--bp-accent); }
    .bp-quick-icon.green  { background: var(--bp-green-bg);     color: var(--bp-green); }
    .bp-quick-icon.blue   { background: var(--bp-blue-bg);      color: var(--bp-blue); }
    .bp-quick-icon.slate  { background: var(--bp-slate-bg);     color: var(--bp-slate); }
    .bp-quick-title { font-size: 13px; font-weight: 600; color: var(--bp-text); }
    .bp-quick-desc  { font-size: 11.5px; color: var(--bp-muted); margin-top: 2px; }

    /* ── Filter bar ── */
    .bp-filter-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        padding: 14px 20px;
        border-bottom: 1px solid var(--bp-border);
        background: var(--bp-surface2);
    }
    .bp-filter-bar label {
        font-size: 12px;
        font-weight: 600;
        color: var(--bp-muted);
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .bp-select {
        padding: 7px 30px 7px 11px;
        border: 1px solid var(--bp-border);
        border-radius: var(--bp-radius-sm);
        background: var(--bp-surface);
        color: var(--bp-text-sec);
        font-size: 13px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        cursor: pointer;
        transition: border-color 0.18s;
    }
    .bp-select:focus { outline: none; border-color: var(--bp-accent); }

    /* ── Table ── */
    .bp-table-wrap { overflow-x: auto; }
    .bp-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .bp-table thead tr {
        background: var(--bp-surface2);
        border-bottom: 2px solid var(--bp-border);
    }
    .bp-table thead th {
        padding: 11px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--bp-muted);
        white-space: nowrap;
    }
    .bp-table tbody tr {
        border-bottom: 1px solid var(--bp-border);
        transition: background 0.15s;
    }
    .bp-table tbody tr:last-child { border-bottom: none; }
    .bp-table tbody tr:hover { background: var(--bp-surface2); }
    .bp-table td {
        padding: 13px 16px;
        color: var(--bp-text-sec);
        vertical-align: middle;
    }
    .bp-table td.primary { color: var(--bp-text); font-weight: 600; }

    /* ── Badge / pill ── */
    .bp-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid transparent;
        white-space: nowrap;
    }
    .bp-badge.completed { background: var(--bp-green-bg);  color: var(--bp-green);  border-color: var(--bp-green-border); }
    .bp-badge.failed    { background: var(--bp-red-bg);    color: var(--bp-red);    border-color: var(--bp-red-border); }
    .bp-badge.processing{ background: var(--bp-blue-bg);   color: var(--bp-blue);   border-color: var(--bp-blue-border); }
    .bp-badge.pending   { background: var(--bp-yellow-bg); color: var(--bp-yellow); border-color: var(--bp-yellow-border); }
    .bp-badge.database  { background: var(--bp-accent-light); color: var(--bp-accent); border-color: #fed7aa; }
    .bp-badge.full      { background: var(--bp-slate-bg);  color: var(--bp-slate);  border-color: var(--bp-slate-border); }
    .bp-badge .dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
        display: inline-block;
    }
    .bp-badge.processing .dot { animation: pulse-dot 1.2s infinite; }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.3; }
    }

    /* ── Row actions ── */
    .bp-row-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .bp-icon-btn {
        width: 30px;
        height: 30px;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border: 1px solid var(--bp-border);
        background: var(--bp-surface);
        color: var(--bp-muted);
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
        text-decoration: none;
    }
    .bp-icon-btn:hover { background: var(--bp-surface2); border-color: var(--bp-border2); color: var(--bp-text-sec); }
    .bp-icon-btn.download:hover { background: var(--bp-blue-bg);   border-color: var(--bp-blue-border);  color: var(--bp-blue); }
    .bp-icon-btn.destroy:hover  { background: var(--bp-red-bg);    border-color: var(--bp-red-border);   color: var(--bp-red); }
    .bp-icon-btn.status:hover   { background: var(--bp-green-bg);  border-color: var(--bp-green-border); color: var(--bp-green); }

    /* ── Empty state ── */
    .bp-empty {
        text-align: center;
        padding: 56px 24px;
        color: var(--bp-muted);
    }
    .bp-empty-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: var(--bp-surface2);
        color: var(--bp-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin: 0 auto 14px;
    }
    .bp-empty-title { font-size: 15px; font-weight: 700; color: var(--bp-text-sec); margin-bottom: 6px; }
    .bp-empty-desc  { font-size: 13px; line-height: 1.6; }

    /* ── Modals ── */
    .bp-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.5);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s;
    }
    .bp-modal-backdrop.open {
        opacity: 1;
        pointer-events: all;
    }
    .bp-modal {
        background: var(--bp-surface);
        border-radius: var(--bp-radius-lg);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        width: 100%;
        max-width: 480px;
        transform: scale(0.96) translateY(8px);
        transition: transform 0.2s;
        border: 1px solid var(--bp-border);
    }
    .bp-modal-backdrop.open .bp-modal { transform: scale(1) translateY(0); }
    .bp-modal-header {
        padding: 20px 24px 16px;
        border-bottom: 1px solid var(--bp-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .bp-modal-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--bp-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .bp-modal-title .modal-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--bp-accent-light);
        color: var(--bp-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .bp-modal-body  { padding: 20px 24px; }
    .bp-modal-footer {
        padding: 14px 24px 20px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        border-top: 1px solid var(--bp-border);
    }
    .bp-modal-close {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bp-surface2);
        border: 1px solid var(--bp-border);
        color: var(--bp-muted);
        cursor: pointer;
        font-size: 12px;
        transition: background 0.15s, color 0.15s;
    }
    .bp-modal-close:hover { background: var(--bp-surface3); color: var(--bp-text); }

    /* ── Form fields inside modal ── */
    .bp-form-group { margin-bottom: 16px; }
    .bp-form-group:last-child { margin-bottom: 0; }
    .bp-form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--bp-text-sec);
        margin-bottom: 6px;
    }
    .bp-form-control {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid var(--bp-border);
        border-radius: var(--bp-radius-sm);
        background: var(--bp-surface);
        color: var(--bp-text);
        font-size: 13.5px;
        transition: border-color 0.18s, box-shadow 0.18s;
        box-sizing: border-box;
    }
    .bp-form-control:focus {
        outline: none;
        border-color: var(--bp-accent);
        box-shadow: 0 0 0 3px var(--bp-accent-ring);
    }
    .bp-form-hint { font-size: 11.5px; color: var(--bp-muted); margin-top: 4px; }
    .bp-checkbox-row {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 10px 12px;
        border: 1px solid var(--bp-border);
        border-radius: var(--bp-radius-sm);
        background: var(--bp-surface2);
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
    }
    .bp-checkbox-row:has(input:checked) {
        border-color: var(--bp-accent);
        background: var(--bp-accent-light);
    }
    .bp-checkbox-row input[type="checkbox"] { accent-color: var(--bp-accent); width: 15px; height: 15px; }
    .bp-checkbox-label { font-size: 13px; font-weight: 600; color: var(--bp-text-sec); }
    .bp-checkbox-desc  { font-size: 11.5px; color: var(--bp-muted); }
    .bp-option-group { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .bp-option-card {
        border: 2px solid var(--bp-border);
        border-radius: var(--bp-radius-sm);
        padding: 12px;
        cursor: pointer;
        transition: border-color 0.18s, background 0.18s;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    .bp-option-card:has(input:checked) {
        border-color: var(--bp-accent);
        background: var(--bp-accent-light);
    }
    .bp-option-card input[type="radio"] { accent-color: var(--bp-accent); margin-top: 2px; }
    .bp-option-label { font-size: 13px; font-weight: 600; color: var(--bp-text-sec); }
    .bp-option-desc  { font-size: 11px; color: var(--bp-muted); margin-top: 2px; }

    /* ── Diagnostics result panel ── */
    .bp-diag-panel {
        background: var(--bp-surface2);
        border: 1px solid var(--bp-border);
        border-radius: var(--bp-radius-sm);
        padding: 14px;
        margin-top: 14px;
        display: none;
    }
    .bp-diag-panel.visible { display: block; }
    .bp-diag-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 0;
        border-bottom: 1px solid var(--bp-border);
        font-size: 12.5px;
    }
    .bp-diag-row:last-child { border-bottom: none; }
    .bp-diag-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .bp-diag-dot.ok      { background: var(--bp-green); }
    .bp-diag-dot.warn    { background: var(--bp-yellow); }
    .bp-diag-dot.error   { background: var(--bp-red); }
    .bp-diag-dot.loading { background: var(--bp-blue); animation: pulse-dot 1s infinite; }
    .bp-diag-key   { font-weight: 600; color: var(--bp-text-sec); min-width: 100px; }
    .bp-diag-value { color: var(--bp-muted); flex: 1; }
    .bp-diag-status { margin-left: auto; font-weight: 700; }
    .bp-diag-status.ok    { color: var(--bp-green); }
    .bp-diag-status.warn  { color: var(--bp-yellow); }
    .bp-diag-status.error { color: var(--bp-red); }

    /* ── Progress bar ── */
    .bp-progress-wrap {
        background: var(--bp-surface2);
        border-radius: 999px;
        height: 4px;
        overflow: hidden;
        margin-top: 8px;
    }
    .bp-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--bp-accent), #f97316);
        border-radius: 999px;
        transition: width 0.4s;
    }

    /* ── Alert / flash ── */
    .bp-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 16px;
        border-radius: var(--bp-radius-sm);
        font-size: 13px;
        margin-bottom: 20px;
        border: 1px solid transparent;
    }
    .bp-alert.success { background: var(--bp-green-bg);  color: var(--bp-green);  border-color: var(--bp-green-border); }
    .bp-alert.error   { background: var(--bp-red-bg);    color: var(--bp-red);    border-color: var(--bp-red-border); }
    .bp-alert.warning { background: var(--bp-yellow-bg); color: var(--bp-yellow); border-color: var(--bp-yellow-border); }
    .bp-alert-icon { font-size: 14px; margin-top: 1px; }
    .bp-alert-body { flex: 1; }
    .bp-alert-close { margin-left: auto; cursor: pointer; opacity: .7; font-size: 12px; background: none; border: none; color: inherit; padding: 2px; }
    .bp-alert-close:hover { opacity: 1; }

    /* ── Pagination ── */
    .bp-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-top: 1px solid var(--bp-border);
        font-size: 13px;
        color: var(--bp-muted);
        flex-wrap: wrap;
        gap: 10px;
    }

    /* ── Quick-backup floating toast ── */
    #quick-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 2000;
        background: var(--bp-surface);
        border: 1px solid var(--bp-border);
        border-radius: var(--bp-radius);
        box-shadow: var(--bp-shadow-md);
        padding: 14px 18px;
        min-width: 260px;
        display: none;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        color: var(--bp-text-sec);
        animation: slide-up 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    #quick-toast.show { display: flex; }
    @keyframes slide-up {
        from { transform: translateY(16px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    #quick-toast .toast-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
        background: var(--bp-accent-light);
        color: var(--bp-accent);
    }
    #quick-toast .toast-icon.success { background: var(--bp-green-bg);  color: var(--bp-green); }
    #quick-toast .toast-icon.error   { background: var(--bp-red-bg);    color: var(--bp-red); }

    /* ── Spinner ── */
    .bp-spinner {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255,255,255,.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        display: none;
    }
    .bp-spinner.dark-spin {
        border-color: var(--bp-border);
        border-top-color: var(--bp-accent);
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Misc ── */
    .text-sm    { font-size: 12px; }
    .text-muted { color: var(--bp-muted); }
    .monospace  { font-family: 'Courier New', monospace; font-size: 12px; color: var(--bp-muted); }
    .truncate   { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px; }
</style>
@endpush

@section('content')
<div style="padding: 24px; max-width: 1400px; margin: 0 auto;">

    {{-- ── Flash messages ── --}}
    @if(session('success'))
        <div class="bp-alert success" id="flash-alert">
            <span class="bp-alert-icon"><i class="fas fa-check-circle"></i></span>
            <span class="bp-alert-body">{{ session('success') }}</span>
            <button class="bp-alert-close" onclick="this.closest('.bp-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="bp-alert error" id="flash-alert">
            <span class="bp-alert-icon"><i class="fas fa-exclamation-circle"></i></span>
            <span class="bp-alert-body">{{ session('error') }}</span>
            <button class="bp-alert-close" onclick="this.closest('.bp-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- ── Page header ── --}}
    <div class="bp-page-header">
        <div>
            <div class="bp-page-title">
                <span class="title-icon"><i class="fas fa-database"></i></span>
                Database Backups
            </div>
            <div class="bp-page-subtitle">Manage and schedule database backups. Keep your data safe and recoverable.</div>
        </div>
        <div class="bp-header-actions">
            <button class="bp-btn bp-btn-ghost" onclick="openDiagModal()">
                <i class="fas fa-stethoscope"></i>
                Run Diagnostics
            </button>
            <button class="bp-btn bp-btn-secondary" onclick="openCleanupModal()">
                <i class="fas fa-broom"></i>
                Clean Expired
                @if($stats['expired_count'] > 0)
                    <span style="background:var(--bp-red);color:#fff;border-radius:999px;font-size:10px;padding:1px 7px;font-weight:800;">
                        {{ $stats['expired_count'] }}
                    </span>
                @endif
            </button>
            <button class="bp-btn bp-btn-primary" onclick="openCreateModal()">
                <i class="fas fa-plus"></i>
                New Backup
            </button>
        </div>
    </div>

    {{-- ── Stat cards ── --}}
    <div class="bp-stats-grid">
        <div class="bp-stat-card accent">
            <span class="bp-stat-icon"><i class="fas fa-database"></i></span>
            <div class="bp-stat-label">Total Backups</div>
            <div class="bp-stat-value">{{ $stats['total'] }}</div>
            <div class="bp-stat-sub">All time</div>
        </div>
        <div class="bp-stat-card green">
            <span class="bp-stat-icon"><i class="fas fa-check-circle"></i></span>
            <div class="bp-stat-label">Completed</div>
            <div class="bp-stat-value">{{ $stats['completed'] }}</div>
            <div class="bp-stat-sub">Available to download</div>
        </div>
        <div class="bp-stat-card red">
            <span class="bp-stat-icon"><i class="fas fa-times-circle"></i></span>
            <div class="bp-stat-label">Failed</div>
            <div class="bp-stat-value">{{ $stats['failed'] }}</div>
            <div class="bp-stat-sub">Needs attention</div>
        </div>
        <div class="bp-stat-card blue">
            <span class="bp-stat-icon"><i class="fas fa-spinner"></i></span>
            <div class="bp-stat-label">Processing</div>
            <div class="bp-stat-value">{{ $stats['processing'] }}</div>
            <div class="bp-stat-sub">Currently running</div>
        </div>
        <div class="bp-stat-card yellow">
            <span class="bp-stat-icon"><i class="fas fa-clock"></i></span>
            <div class="bp-stat-label">Pending</div>
            <div class="bp-stat-value">{{ $stats['pending'] }}</div>
            <div class="bp-stat-sub">Queued</div>
        </div>
        <div class="bp-stat-card slate">
            <span class="bp-stat-icon"><i class="fas fa-hdd"></i></span>
            <div class="bp-stat-label">Total Size</div>
            <div class="bp-stat-value" style="font-size:18px;">{{ $stats['formatted_total_size'] }}</div>
            <div class="bp-stat-sub">Disk usage</div>
        </div>
    </div>

    {{-- ── Latest backup strip ── --}}
    @if($stats['latest'])
        <div class="bp-latest-strip">
            <span class="strip-icon"><i class="fas fa-shield-alt"></i></span>
            <span>
                Last successful backup:
                <strong>{{ $stats['latest']->backup_name }}</strong>
                &mdash;
                {{ $stats['latest']->completed_at?->diffForHumans() }}
                ({{ $stats['latest']->formatted_size }})
            </span>
        </div>
    @else
        <div class="bp-latest-strip warning">
            <span class="strip-icon"><i class="fas fa-exclamation-triangle"></i></span>
            <span>No completed backups found. Create your first backup to protect your data.</span>
        </div>
    @endif

    {{-- ── Quick actions ── --}}
    <div class="bp-quick-grid" style="margin-bottom: 24px;">
        <div class="bp-quick-card" onclick="triggerQuickBackup()">
            <div class="bp-quick-icon orange"><i class="fas fa-bolt"></i></div>
            <div>
                <div class="bp-quick-title">Quick Backup</div>
                <div class="bp-quick-desc">Database snapshot right now</div>
            </div>
        </div>
        <div class="bp-quick-card" onclick="openCreateModal('full')">
            <div class="bp-quick-icon slate"><i class="fas fa-archive"></i></div>
            <div>
                <div class="bp-quick-title">Full Backup</div>
                <div class="bp-quick-desc">Database + files</div>
            </div>
        </div>
        <div class="bp-quick-card" onclick="openCreateModal('database')">
            <div class="bp-quick-icon blue"><i class="fas fa-table"></i></div>
            <div>
                <div class="bp-quick-title">Database Only</div>
                <div class="bp-quick-desc">SQL dump with options</div>
            </div>
        </div>
        <div class="bp-quick-card" onclick="openDiagModal()">
            <div class="bp-quick-icon green"><i class="fas fa-heartbeat"></i></div>
            <div>
                <div class="bp-quick-title">Health Check</div>
                <div class="bp-quick-desc">Verify system readiness</div>
            </div>
        </div>
    </div>

    {{-- ── Backups table ── --}}
    <div class="bp-card">
        {{-- Filter bar --}}
        <form method="GET" action="{{ route('admin.backups.index') }}" class="bp-filter-bar" id="filter-form">
            <label>Filter:</label>
            <select name="status" class="bp-select" onchange="document.getElementById('filter-form').submit()">
                <option value="">All statuses</option>
                @foreach(['completed','processing','pending','failed'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="type" class="bp-select" onchange="document.getElementById('filter-form').submit()">
                <option value="">All types</option>
                <option value="database" {{ request('type') === 'database' ? 'selected' : '' }}>Database</option>
                <option value="full"     {{ request('type') === 'full'     ? 'selected' : '' }}>Full</option>
            </select>
            @if(request()->hasAny(['status','type']))
                <a href="{{ route('admin.backups.index') }}" class="bp-btn bp-btn-ghost bp-btn-sm">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
            <span style="margin-left:auto; font-size:12px; color:var(--bp-muted);">
                {{ $backups->total() }} {{ Str::plural('backup', $backups->total()) }}
            </span>
        </form>

        {{-- Table --}}
        <div class="bp-table-wrap">
            @if($backups->isEmpty())
                <div class="bp-empty">
                    <div class="bp-empty-icon"><i class="fas fa-database"></i></div>
                    <div class="bp-empty-title">No backups found</div>
                    <div class="bp-empty-desc">
                        @if(request()->hasAny(['status','type']))
                            No backups match your filters. <a href="{{ route('admin.backups.index') }}" style="color:var(--bp-accent);">Clear filters</a>
                        @else
                            Create your first backup using the button above.
                        @endif
                    </div>
                </div>
            @else
                <table class="bp-table">
                    <thead>
                        <tr>
                            <th>Backup Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Size</th>
                            <th>Retention</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                        <tr id="backup-row-{{ $backup->id }}">
                            <td class="primary">
                                <div class="truncate" title="{{ $backup->backup_name }}">
                                    <i class="fas fa-file-archive" style="color:var(--bp-muted);margin-right:6px;"></i>
                                    {{ $backup->backup_name }}
                                </div>
                                @if($backup->error_message)
                                    <div class="text-sm text-muted" style="margin-top:3px;color:var(--bp-red)!important;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ Str::limit($backup->error_message, 60) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="bp-badge {{ $backup->backup_type }}">
                                    @if($backup->backup_type === 'database')
                                        <i class="fas fa-table" style="font-size:9px;"></i> Database
                                    @else
                                        <i class="fas fa-archive" style="font-size:9px;"></i> Full
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="bp-badge {{ $backup->status }}" id="status-{{ $backup->id }}">
                                    <span class="dot"></span>
                                    {{ ucfirst($backup->status) }}
                                </span>
                                @if(in_array($backup->status, ['processing','pending']))
                                    <div class="bp-progress-wrap" style="margin-top:6px;max-width:100px;">
                                        <div class="bp-progress-bar" id="progress-{{ $backup->id }}" style="width:{{ $backup->progress ?? 0 }}%;"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="monospace" id="size-{{ $backup->id }}">
                                {{ $backup->formatted_size ?? '—' }}
                            </td>
                            <td>
                                @if($backup->retention_until)
                                    @if($backup->retention_until->isPast())
                                        <span style="color:var(--bp-red);font-size:12px;">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Expired
                                        </span>
                                    @else
                                        <span style="font-size:12px;color:var(--bp-muted);">
                                            {{ $backup->retention_until->diffForHumans() }}
                                        </span>
                                    @endif
                                @else
                                    <span style="color:var(--bp-muted);">—</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size:12.5px;">{{ $backup->creator?->name ?? 'System' }}</span>
                            </td>
                            <td>
                                <span style="font-size:12.5px;">{{ $backup->created_at->format('M d, Y') }}</span>
                                <div class="text-sm text-muted">{{ $backup->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div class="bp-row-actions" style="justify-content:flex-end;">
                                    {{-- Poll status (for processing/pending) --}}
                                    @if(in_array($backup->status, ['processing','pending']))
                                        <button class="bp-icon-btn status"
                                            title="Refresh status"
                                            onclick="pollStatus({{ $backup->id }})">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    @endif

                                    {{-- Download --}}
                                    @if($backup->canDownload())
                                        <a href="{{ route('admin.backups.download', $backup) }}"
                                           class="bp-icon-btn download"
                                           title="Download backup">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @else
                                        <span class="bp-icon-btn" style="opacity:.3;cursor:default;" title="Not available">
                                            <i class="fas fa-download"></i>
                                        </span>
                                    @endif

                                    {{-- Delete --}}
                                    @if($backup->canDelete())
                                        <button class="bp-icon-btn destroy"
                                            title="Delete backup"
                                            onclick="confirmDelete({{ $backup->id }}, '{{ addslashes($backup->backup_name) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <span class="bp-icon-btn" style="opacity:.3;cursor:default;" title="Cannot delete while processing">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Pagination --}}
        @if($backups->hasPages())
            <div class="bp-pagination">
                <span>Showing {{ $backups->firstItem() }}–{{ $backups->lastItem() }} of {{ $backups->total() }}</span>
                {{ $backups->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ══════════════════════════════════════════
     MODALS
══════════════════════════════════════════ --}}

{{-- Create Backup Modal --}}
<div class="bp-modal-backdrop" id="create-modal">
    <div class="bp-modal">
        <div class="bp-modal-header">
            <div class="bp-modal-title">
                <span class="modal-icon"><i class="fas fa-plus"></i></span>
                Create New Backup
            </div>
            <button class="bp-modal-close" onclick="closeModal('create-modal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.backups.store') }}" id="create-form">
            @csrf
            <div class="bp-modal-body">
                <div class="bp-form-group">
                    <label class="bp-form-label">Backup Type</label>
                    <div class="bp-option-group">
                        <label class="bp-option-card">
                            <input type="radio" name="backup_type" value="database" id="type-database" checked>
                            <div>
                                <div class="bp-option-label"><i class="fas fa-table" style="color:var(--bp-accent);margin-right:5px;"></i> Database Only</div>
                                <div class="bp-option-desc">SQL dump of all tables. Fast and lightweight.</div>
                            </div>
                        </label>
                        <label class="bp-option-card">
                            <input type="radio" name="backup_type" value="full" id="type-full">
                            <div>
                                <div class="bp-option-label"><i class="fas fa-archive" style="color:var(--bp-slate);margin-right:5px;"></i> Full Backup</div>
                                <div class="bp-option-desc">Database + uploaded files. Larger size.</div>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="bp-form-group">
                    <label class="bp-form-label" for="retention_days">Retention Period</label>
                    <select name="retention_days" id="retention_days" class="bp-form-control bp-select">
                        <option value="7">7 days</option>
                        <option value="14">14 days</option>
                        <option value="30" selected>30 days</option>
                        <option value="60">60 days</option>
                        <option value="90">90 days</option>
                        <option value="180">180 days</option>
                        <option value="365">1 year</option>
                    </select>
                    <div class="bp-form-hint">Backup will be automatically marked for cleanup after this period.</div>
                </div>
                <div class="bp-form-group">
                    <label class="bp-checkbox-row">
                        <input type="checkbox" name="async" value="1" id="async-check">
                        <div>
                            <div class="bp-checkbox-label">Run in background (async)</div>
                            <div class="bp-checkbox-desc">Queue the backup job. Requires queue worker to be running.</div>
                        </div>
                    </label>
                </div>
            </div>
            <div class="bp-modal-footer">
                <button type="button" class="bp-btn bp-btn-ghost" onclick="closeModal('create-modal')">Cancel</button>
                <button type="submit" class="bp-btn bp-btn-primary" id="create-submit">
                    <span class="bp-spinner" id="create-spinner"></span>
                    <i class="fas fa-database" id="create-icon"></i>
                    Start Backup
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirm Modal --}}
<div class="bp-modal-backdrop" id="delete-modal">
    <div class="bp-modal" style="max-width:400px;">
        <div class="bp-modal-header">
            <div class="bp-modal-title" style="color:var(--bp-red);">
                <span class="modal-icon" style="background:var(--bp-red-bg);color:var(--bp-red);"><i class="fas fa-trash"></i></span>
                Delete Backup
            </div>
            <button class="bp-modal-close" onclick="closeModal('delete-modal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="bp-modal-body">
            <p style="font-size:13.5px;color:var(--bp-text-sec);line-height:1.6;margin:0 0 10px;">
                Are you sure you want to permanently delete
                <strong id="delete-name" style="color:var(--bp-text);"></strong>?
            </p>
            <p style="font-size:12.5px;color:var(--bp-muted);margin:0;">
                This will remove the backup record and the file from disk. This action cannot be undone.
            </p>
        </div>
        <div class="bp-modal-footer">
            <button type="button" class="bp-btn bp-btn-ghost" onclick="closeModal('delete-modal')">Cancel</button>
            <form method="POST" id="delete-form" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="bp-btn bp-btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Cleanup Confirm Modal --}}
<div class="bp-modal-backdrop" id="cleanup-modal">
    <div class="bp-modal" style="max-width:420px;">
        <div class="bp-modal-header">
            <div class="bp-modal-title">
                <span class="modal-icon" style="background:var(--bp-yellow-bg);color:var(--bp-yellow);"><i class="fas fa-broom"></i></span>
                Clean Expired Backups
            </div>
            <button class="bp-modal-close" onclick="closeModal('cleanup-modal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="bp-modal-body">
            <p style="font-size:13.5px;color:var(--bp-text-sec);line-height:1.6;margin:0 0 10px;">
                This will delete
                <strong style="color:var(--bp-accent);">{{ $stats['expired_count'] }} expired {{ Str::plural('backup', $stats['expired_count']) }}</strong>
                whose retention period has passed.
            </p>
            <p style="font-size:12.5px;color:var(--bp-muted);margin:0;">Files will be removed from disk and records deleted. Non-expired backups are not affected.</p>
        </div>
        <div class="bp-modal-footer">
            <button type="button" class="bp-btn bp-btn-ghost" onclick="closeModal('cleanup-modal')">Cancel</button>
            <form method="POST" action="{{ route('admin.backups.cleanup') }}">
                @csrf
                <button type="submit" class="bp-btn bp-btn-primary">
                    <i class="fas fa-broom"></i> Run Cleanup
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Diagnostics Modal --}}
<div class="bp-modal-backdrop" id="diag-modal">
    <div class="bp-modal" style="max-width:520px;">
        <div class="bp-modal-header">
            <div class="bp-modal-title">
                <span class="modal-icon" style="background:var(--bp-blue-bg);color:var(--bp-blue);"><i class="fas fa-stethoscope"></i></span>
                System Diagnostics
            </div>
            <button class="bp-modal-close" onclick="closeModal('diag-modal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="bp-modal-body">
            <p style="font-size:13px;color:var(--bp-muted);margin:0 0 14px;">
                Runs a quick health check to verify backup system readiness.
            </p>
            <button class="bp-btn bp-btn-secondary" id="diag-run-btn" onclick="runDiagnostics()" style="width:100%;justify-content:center;">
                <span class="bp-spinner dark-spin" id="diag-spinner"></span>
                <i class="fas fa-play" id="diag-play-icon"></i>
                Run Diagnostics
            </button>
            <div class="bp-diag-panel" id="diag-results"></div>
        </div>
        <div class="bp-modal-footer">
            <button type="button" class="bp-btn bp-btn-ghost" onclick="closeModal('diag-modal')">Close</button>
        </div>
    </div>
</div>

{{-- Quick backup toast --}}
<div id="quick-toast">
    <div class="toast-icon" id="toast-icon"><i class="fas fa-bolt" id="toast-fa-icon"></i></div>
    <div>
        <div style="font-weight:600;color:var(--bp-text);font-size:13px;" id="toast-title">Creating backup…</div>
        <div style="font-size:12px;color:var(--bp-muted);" id="toast-msg">Please wait</div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ─── Modal helpers ─────────────────────────────────────────── */
function openModal(id)  { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }

// Close on backdrop click
document.querySelectorAll('.bp-modal-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', e => {
        if (e.target === backdrop) closeModal(backdrop.id);
    });
});

/* ─── Create modal ──────────────────────────────────────────── */
function openCreateModal(preset) {
    if (preset === 'database') document.getElementById('type-database').checked = true;
    if (preset === 'full')     document.getElementById('type-full').checked = true;
    openModal('create-modal');
}

document.getElementById('create-form').addEventListener('submit', function() {
    const btn     = document.getElementById('create-submit');
    const spinner = document.getElementById('create-spinner');
    const icon    = document.getElementById('create-icon');
    btn.disabled  = true;
    spinner.style.display = 'block';
    icon.style.display    = 'none';
});

/* ─── Delete modal ──────────────────────────────────────────── */
function confirmDelete(id, name) {
    document.getElementById('delete-name').textContent = name;
    document.getElementById('delete-form').action = `/admin/backups/${id}`;
    openModal('delete-modal');
}

/* ─── Cleanup modal ─────────────────────────────────────────── */
function openCleanupModal() { openModal('cleanup-modal'); }

/* ─── Diagnostics ───────────────────────────────────────────── */
function openDiagModal() { openModal('diag-modal'); }

function runDiagnostics() {
    const btn     = document.getElementById('diag-run-btn');
    const spinner = document.getElementById('diag-spinner');
    const icon    = document.getElementById('diag-play-icon');
    const panel   = document.getElementById('diag-results');

    btn.disabled  = true;
    spinner.style.display = 'block';
    icon.style.display    = 'none';
    panel.innerHTML = '';

    fetch('{{ route("admin.backups.test") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        const tests = data.tests || {};
        let html = '';

        const statusMap = {
            success: { cls: 'ok',   label: '✓ OK' },
            failed:  { cls: 'error',label: '✗ Failed' },
            available:    { cls: 'ok',   label: '✓ Available' },
            not_available:{ cls: 'warn', label: '⚠ Not found (PDO fallback)' },
            ok:      { cls: 'ok',   label: '✓ OK' },
            missing: { cls: 'error',label: '✗ Missing' },
        };

        const rows = [
            { key: 'Database', value: tests.database?.driver ? `${tests.database.driver} / ${tests.database.database}` : (tests.database?.error ?? ''), status: tests.database?.status },
            { key: 'Backup Dir', value: tests.directory?.writable ? `Writable • ${tests.directory?.free_space} free` : (tests.directory?.error ?? ''), status: tests.directory?.status },
            { key: 'mysqldump', value: tests.mysqldump?.note ?? '', status: tests.mysqldump?.status },
            { key: 'ZIP ext', value: '', status: tests.extensions?.zip },
            { key: 'PDO ext', value: '', status: tests.extensions?.pdo },
            { key: 'Queue', value: `${tests.queue?.driver} (${tests.queue?.mode})`, status: 'ok' },
        ];

        rows.forEach(row => {
            const s = statusMap[row.status] || { cls: 'warn', label: row.status };
            html += `<div class="bp-diag-row">
                <span class="bp-diag-dot ${s.cls}"></span>
                <span class="bp-diag-key">${row.key}</span>
                <span class="bp-diag-value">${row.value}</span>
                <span class="bp-diag-status ${s.cls}">${s.label}</span>
            </div>`;
        });

        const overall = data.overall_status === 'ready'
            ? `<div style="margin-top:12px;padding:10px 12px;background:var(--bp-green-bg);border:1px solid var(--bp-green-border);border-radius:var(--bp-radius-sm);font-size:13px;color:var(--bp-green);font-weight:600;"><i class="fas fa-check-circle" style="margin-right:6px;"></i>${data.message}</div>`
            : `<div style="margin-top:12px;padding:10px 12px;background:var(--bp-yellow-bg);border:1px solid var(--bp-yellow-border);border-radius:var(--bp-radius-sm);font-size:13px;color:var(--bp-yellow);font-weight:600;"><i class="fas fa-exclamation-triangle" style="margin-right:6px;"></i>${data.message}</div>`;

        panel.innerHTML = html + overall;
        panel.classList.add('visible');
    })
    .catch(err => {
        panel.innerHTML = `<div class="bp-diag-row"><span class="bp-diag-dot error"></span><span class="bp-diag-key">Error</span><span class="bp-diag-value">${err.message}</span></div>`;
        panel.classList.add('visible');
    })
    .finally(() => {
        btn.disabled  = false;
        spinner.style.display = 'none';
        icon.style.display    = '';
    });
}

/* ─── Quick backup (AJAX) ───────────────────────────────────── */
function triggerQuickBackup() {
    const toast     = document.getElementById('quick-toast');
    const toastIcon = document.getElementById('toast-icon');
    const toastFa   = document.getElementById('toast-fa-icon');
    const toastTitle= document.getElementById('toast-title');
    const toastMsg  = document.getElementById('toast-msg');

    // Reset
    toastIcon.className  = 'toast-icon';
    toastFa.className    = 'fas fa-bolt';
    toastTitle.textContent = 'Creating backup…';
    toastMsg.textContent   = 'Please wait';
    toast.classList.add('show');

    fetch('{{ route("admin.backups.quick") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toastIcon.classList.add('success');
            toastFa.className    = 'fas fa-check';
            toastTitle.textContent = 'Backup complete!';
            toastMsg.textContent   = data.backup?.size ?? '';
            setTimeout(() => { toast.classList.remove('show'); location.reload(); }, 2500);
        } else {
            toastIcon.classList.add('error');
            toastFa.className    = 'fas fa-times';
            toastTitle.textContent = 'Backup failed';
            toastMsg.textContent   = data.message ?? 'Unknown error';
            setTimeout(() => toast.classList.remove('show'), 4000);
        }
    })
    .catch(err => {
        toastIcon.classList.add('error');
        toastFa.className    = 'fas fa-times';
        toastTitle.textContent = 'Request failed';
        toastMsg.textContent   = err.message;
        setTimeout(() => toast.classList.remove('show'), 4000);
    });
}

/* ─── Poll status for in-progress backups ───────────────────── */
function pollStatus(id) {
    fetch(`/admin/backups/${id}/status`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        // Update badge
        const statusBadge = document.getElementById(`status-${id}`);
        if (statusBadge) {
            statusBadge.className = `bp-badge ${data.status}`;
            statusBadge.innerHTML = `<span class="dot"></span>${data.status.charAt(0).toUpperCase() + data.status.slice(1)}`;
        }
        // Update progress bar
        const bar = document.getElementById(`progress-${id}`);
        if (bar) bar.style.width = (data.progress ?? 0) + '%';
        // Update size
        const sizeEl = document.getElementById(`size-${id}`);
        if (sizeEl && data.formatted_size) sizeEl.textContent = data.formatted_size;
        // If done, reload after brief delay
        if (data.status === 'completed' || data.status === 'failed') {
            setTimeout(() => location.reload(), 1200);
        }
    })
    .catch(console.error);
}

/* ─── Auto-poll any processing rows on page load ────────────── */
document.addEventListener('DOMContentLoaded', () => {
    const processingRows = document.querySelectorAll('[id^="backup-row-"]');
    processingRows.forEach(row => {
        const badge = row.querySelector('[id^="status-"]');
        if (badge && badge.classList.contains('processing')) {
            const id = badge.id.replace('status-', '');
            const interval = setInterval(() => {
                fetch(`/admin/backups/${id}/status`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    const bar = document.getElementById(`progress-${id}`);
                    if (bar) bar.style.width = (data.progress ?? 0) + '%';
                    if (data.status !== 'processing' && data.status !== 'pending') {
                        clearInterval(interval);
                        setTimeout(() => location.reload(), 800);
                    }
                }).catch(() => clearInterval(interval));
            }, 3000);
        }
    });

    // Auto-dismiss flash after 5s
    const flash = document.getElementById('flash-alert');
    if (flash) setTimeout(() => flash.remove(), 5000);
});
</script>
@endpush