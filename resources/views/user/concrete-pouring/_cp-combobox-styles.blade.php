<style>
    .cp-checklist-check {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px,1fr));
        gap: 10px;
    }
    .cp-check-label {
        display: flex; align-items: center; gap: 10px;
        padding: 11px 14px;
        background: var(--cp-surface2);
        border: 1.5px solid var(--cp-border);
        border-radius: 8px;
        cursor: pointer;
        transition: border-color .18s, background .18s;
        font-size: 13px; color: var(--cp-text);
        user-select: none;
    }
    .cp-check-label input[type=checkbox] { display: none; }
    .cp-check-box {
        width: 18px; height: 18px; border-radius: 4px;
        border: 2px solid var(--cp-border);
        background: var(--cp-surface);
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        transition: all .18s;
        font-size: 11px;
    }
    .cp-check-label:has(input:checked) {
        border-color: rgba(5,150,105,0.5);
        background: rgba(5,150,105,0.06);
    }
    .cp-check-label:has(input:checked) .cp-check-box {
        background: #059669; border-color: #059669; color: white;
    }
    .cp-section-title {
        font-size: 16px; font-weight: 700; color: var(--cp-text);
        margin-bottom: 4px;
    }
    .cp-section-sub { font-size: 13px; color: var(--cp-muted); margin-bottom: 20px; }
    .cp-form-grid { display: grid; gap: 16px; }
    .cp-form-two { grid-template-columns: 1fr 1fr; }
    @media(max-width:600px){ .cp-form-two { grid-template-columns: 1fr; } }
    .cp-form-section {
        border-bottom: 1px solid var(--cp-border);
        padding-bottom: 28px; margin-bottom: 28px;
    }
    .cp-form-section:last-child { border-bottom: none; }

    /* ── Contract Number Combobox ───────────────────────────────── */
    #cpRefField { position: relative; }

    .cp-ref-trigger {
        display: flex; align-items: center; gap: 10px;
        width: 100%; height: 44px; padding: 0 14px;
        background: var(--cp-surface2);
        border: 1.5px solid var(--cp-border);
        border-radius: 8px;
        cursor: pointer; user-select: none;
        transition: border-color .18s, box-shadow .18s;
        box-sizing: border-box;
        color: var(--cp-text);
    }
    .cp-ref-trigger:hover { border-color: #06b6d4; }
    .cp-ref-trigger.open {
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6,182,212,0.15);
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
    .cp-ref-trigger-icon {
        font-size: 14px; font-weight: 700;
        color: var(--cp-muted); flex-shrink: 0; line-height: 1;
    }
    .cp-ref-display {
        flex: 1; font-size: 14px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        color: var(--cp-text);
    }
    .cp-ref-placeholder { color: var(--cp-muted) !important; }

    .cp-ref-clear-btn {
        background: none; border: none; cursor: pointer;
        color: var(--cp-muted); font-size: 13px;
        width: 22px; height: 22px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0; transition: background .12s, color .12s; line-height: 1;
    }
    .cp-ref-clear-btn:hover { background: rgba(239,68,68,.1); color: #ef4444; }

    /* ── Dropdown panel ── */
    .cp-ref-dropdown {
        display: none;
        position: absolute; left: 0; right: 0; top: 100%; z-index: 300;
        background: var(--cp-surface2);
        border: 1.5px solid #06b6d4; border-top: none;
        border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;
        box-shadow: 0 8px 28px rgba(0,0,0,.2);
        overflow: hidden;
    }
    .cp-ref-dropdown.open { display: block; }

    /* ── Search row ── */
    .cp-ref-search-wrap {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 14px;
        background: var(--cp-surface);
        border-bottom: 1px solid var(--cp-border);
    }
    .cp-ref-search-input {
        flex: 1; border: none; outline: none;
        background: transparent;
        font-size: 14px; color: var(--cp-text);
        font-family: inherit; padding: 0;
    }
    .cp-ref-search-input::placeholder { color: var(--cp-muted); }

    /* ── Options list ── */
    .cp-ref-list { max-height: 220px; overflow-y: auto; padding: 4px 0; }
    .cp-ref-list::-webkit-scrollbar { width: 4px; }
    .cp-ref-list::-webkit-scrollbar-thumb { background: var(--cp-border); border-radius: 99px; }

    .cp-ref-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 16px; font-size: 14px;
        color: var(--cp-text); cursor: pointer; transition: background .1s;
    }
    .cp-ref-item:hover, .cp-ref-item.cp-ref-focused {
        background: rgba(6,182,212,.1); color: #06b6d4;
    }
    .cp-ref-item.cp-ref-selected {
        background: rgba(6,182,212,.18); color: #0891b2; font-weight: 600;
    }
    .cp-ref-item-hash {
        font-size: 11px; font-weight: 700;
        color: var(--cp-muted); flex-shrink: 0;
    }
    .cp-ref-item:hover .cp-ref-item-hash,
    .cp-ref-item.cp-ref-focused .cp-ref-item-hash,
    .cp-ref-item.cp-ref-selected .cp-ref-item-hash { color: inherit; opacity: .6; }

    .cp-ref-empty {
        padding: 18px 16px; text-align: center;
        font-size: 13px; color: var(--cp-muted);
    }

    /* ── Footer "add custom" ── */
    .cp-ref-footer {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 16px; font-size: 13px; font-weight: 500;
        color: #06b6d4; cursor: pointer;
        border-top: 1px solid var(--cp-border);
        transition: background .1s;
    }
    .cp-ref-footer:hover { background: rgba(6,182,212,.08); }

    /* ── Custom free-text input ── */
    .cp-ref-custom-wrap { display: none; margin-top: 8px; }
    .cp-ref-custom-wrap.visible { display: block; }
</style>