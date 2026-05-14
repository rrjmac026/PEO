<div class="wr-panel active" id="panel-1">
    <div class="wr-panel-tag">📁 Step 1 of 4</div>
    <h2 class="wr-panel-title">Project Information</h2>
    <p class="wr-panel-sub">Tell us about the project where the work will take place.</p>

    <div class="wr-fields">

        {{-- Project Name --}}
        <div class="wr-field">
            <label class="wr-label" for="name_of_project">
                Project Name <span class="wr-req">*</span>
            </label>
            <div class="wr-input-wrap">
                <span class="wr-icon">🏗</span>
                <input type="text" id="name_of_project" name="name_of_project"
                    value="{{ old('name_of_project') }}"
                    placeholder="e.g., Highway Expansion Phase 2"
                    class="{{ $errors->has('name_of_project') ? 'wr-error' : '' }}">
            </div>
            <p class="wr-err-msg {{ $errors->has('name_of_project') ? 'show' : '' }}" id="err-name_of_project">
                ⚠ {{ $errors->first('name_of_project', 'Project name is required.') }}
            </p>
        </div>

        {{-- Project Location --}}
        <div class="wr-field">
            <label class="wr-label" for="project_location">
                Project Location <span class="wr-req">*</span>
            </label>
            <div class="wr-input-wrap">
                <span class="wr-icon">📍</span>
                <input type="text" id="project_location" name="project_location"
                    value="{{ old('project_location') }}"
                    placeholder="e.g., Davao City, Zone 3"
                    class="{{ $errors->has('project_location') ? 'wr-error' : '' }}">
            </div>
            <p class="wr-err-msg {{ $errors->has('project_location') ? 'show' : '' }}" id="err-project_location">
                ⚠ {{ $errors->first('project_location', 'Project location is required.') }}
            </p>
        </div>

        {{-- Reference Number --}}
        <div class="wr-field" id="wrRefField">
            <label class="wr-label">
                Reference Number
                <span class="wr-label-optional">(optional)</span>
            </label>

            {{-- Main trigger button — looks like a real input --}}
            <div class="wr-ref-trigger" id="wrRefTrigger" onclick="wrRefToggle()" role="combobox"
                 aria-haspopup="listbox" aria-expanded="false" aria-label="Reference number" tabindex="0"
                 onkeydown="if(event.key==='Enter'||event.key===' ')wrRefToggle()">
                <span class="wr-ref-trigger-icon">#</span>
                <span id="wrRefDisplay" class="wr-ref-display wr-ref-placeholder">
                    Select or search a reference number
                </span>
                <span class="wr-ref-trigger-actions">
                    <button type="button" id="wrRefClearBtn" class="wr-ref-clear-btn"
                        onclick="wrRefClear(event)" title="Clear selection" style="display:none;">
                        ✕
                    </button>
                    <svg id="wrRefChevron" class="wr-ref-chevron" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </span>
            </div>

            {{-- Dropdown panel --}}
            <div class="wr-ref-dropdown" id="wrRefDropdown" role="dialog" aria-label="Reference number options">
                {{-- Search --}}
                <div class="wr-ref-search-wrap">
                    <svg class="wr-ref-search-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                    </svg>
                    <input
                        type="text"
                        class="wr-ref-search-input"
                        id="wrRefSearchInput"
                        placeholder="Search reference numbers…"
                        autocomplete="off"
                        oninput="wrRefFilter(this.value)"
                        onkeydown="wrRefKey(event)"
                        aria-label="Search reference numbers">
                </div>

                {{-- List of options --}}
                <div class="wr-ref-list" id="wrRefList" role="listbox"></div>

                {{-- Custom entry footer --}}
                <div class="wr-ref-footer" onclick="wrRefEnableCustom()">
                    <span class="wr-ref-footer-icon">+</span>
                    <span id="wrRefAddLabel">Enter a custom reference number</span>
                </div>
            </div>

            {{-- Custom free-text input (shown on demand) --}}
            <div class="wr-ref-custom-wrap" id="wrRefCustomWrap">
                <div class="wr-input-wrap">
                    <span class="wr-icon">#️⃣</span>
                    <input type="text" id="wrRefCustomInput"
                        placeholder="e.g., REF-2026-001"
                        maxlength="100"
                        oninput="wrRefOnCustomInput(this.value)">
                    <span class="wr-ref-custom-badge">Custom</span>
                </div>
            </div>

            {{-- Hidden field submitted with the form --}}
            <input type="hidden" name="reference_number" id="wrRefHidden" value="{{ old('reference_number') }}">

            <p class="wr-field-hint">Pick from existing reference numbers, or type a custom one.</p>
        </div>

        {{-- For Office --}}
        <div class="wr-field">
            <label class="wr-label" for="for_office">For Office</label>
            <div class="wr-input-wrap">
                <span class="wr-icon">🏢</span>
                <input type="text" id="for_office" name="for_office"
                    value="PROVINCIAL ENGINEERS OFFICE" readonly>
                <span class="wr-readonly-badge">Fixed</span>
            </div>
            <p class="wr-field-hint">This field is fixed and cannot be changed.</p>
        </div>

        {{-- From / Requester --}}
        <div class="wr-field">
            <label class="wr-label" for="from_requester">
                From / Requester
                <span class="wr-label-optional">(optional)</span>
            </label>
            <div class="wr-input-wrap">
                <span class="wr-icon">👤</span>
                <input type="text" id="from_requester" name="from_requester"
                    value="{{ old('from_requester') }}"
                    placeholder="e.g., Project Manager">
            </div>
        </div>

    </div>{{-- /.wr-fields --}}

    <div class="wr-nav">
        <span></span>
        <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(1)">
            Continue →
        </button>
    </div>
</div>{{-- /#panel-1 --}}


{{-- ============================================================
     REFERENCE NUMBER COMBOBOX — Styles
     ============================================================ --}}
<style>
/* ── Label optional tag ─────────────────────────────────────── */
.wr-label-optional {
    color: var(--wr-muted, #9ca3af);
    font-weight: 400;
    font-size: 0.8em;
}

/* ── Trigger (looks like a proper input) ────────────────────── */
.wr-ref-trigger {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 0 14px;
    height: 44px;
    /* Explicitly match .wr-input-wrap so no dark/global style bleeds in */
    background: #ffffff;
    color: #111827;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    cursor: pointer;
    transition: border-color 0.15s, box-shadow 0.15s;
    position: relative;
    box-sizing: border-box;
    user-select: none;
}
.wr-ref-trigger:hover {
    border-color: var(--wr-primary, #3b82f6);
}
.wr-ref-trigger:focus {
    outline: none;
    border-color: var(--wr-primary, #3b82f6);
    box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
}
.wr-ref-trigger.open {
    border-color: var(--wr-primary, #3b82f6);
    box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}

/* Trigger's "#" icon */
.wr-ref-trigger-icon {
    font-size: 14px;
    font-weight: 700;
    color: var(--wr-muted, #9ca3af);
    flex-shrink: 0;
    line-height: 1;
}

/* The display text */
.wr-ref-display {
    flex: 1;
    font-size: 0.925rem;
    color: var(--wr-text, #111827);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1;
}
.wr-ref-placeholder {
    color: var(--wr-placeholder, #9ca3af) !important;
}

/* Actions cluster (clear + chevron) */
.wr-ref-trigger-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

/* Clear button */
.wr-ref-clear-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--wr-muted, #9ca3af);
    font-size: 13px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    transition: background 0.12s, color 0.12s;
    line-height: 1;
}
.wr-ref-clear-btn:hover {
    background: var(--wr-muted-bg, #f3f4f6);
    color: var(--wr-danger, #ef4444);
}

/* Chevron arrow */
.wr-ref-chevron {
    width: 16px;
    height: 16px;
    color: var(--wr-muted, #9ca3af);
    transition: transform 0.2s;
    flex-shrink: 0;
}
.wr-ref-trigger.open .wr-ref-chevron {
    transform: rotate(180deg);
}

/* ── Dropdown panel ─────────────────────────────────────────── */
.wr-ref-dropdown {
    display: none;
    position: absolute;
    left: 0;
    right: 0;
    top: 100%;
    z-index: 200;
    background: var(--wr-input-bg, #fff);
    border: 1.5px solid var(--wr-primary, #3b82f6);
    border-top: none;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    overflow: hidden;
}
.wr-ref-dropdown.open {
    display: block;
}

/* Need the trigger's parent to be relative for absolute positioning */
#wrRefField {
    position: relative;
}

/* ── Search row inside dropdown ─────────────────────────────── */
.wr-ref-search-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border-bottom: 1px solid var(--wr-border, #e5e7eb);
    background: var(--wr-muted-bg, #f9fafb);
}
.wr-ref-search-icon {
    width: 16px;
    height: 16px;
    color: var(--wr-muted, #9ca3af);
    flex-shrink: 0;
}
.wr-ref-search-input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-size: 0.9rem;
    color: var(--wr-text, #111827);
    padding: 0;
}
.wr-ref-search-input::placeholder {
    color: var(--wr-placeholder, #9ca3af);
}

/* ── Options list ───────────────────────────────────────────── */
.wr-ref-list {
    max-height: 220px;
    overflow-y: auto;
    padding: 6px 0;
}
.wr-ref-list::-webkit-scrollbar { width: 5px; }
.wr-ref-list::-webkit-scrollbar-track { background: transparent; }
.wr-ref-list::-webkit-scrollbar-thumb { background: var(--wr-border, #d1d5db); border-radius: 99px; }

.wr-ref-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    font-size: 0.9rem;
    color: var(--wr-text, #111827);
    cursor: pointer;
    transition: background 0.1s;
}
.wr-ref-item:hover,
.wr-ref-item.wr-ref-focused {
    background: var(--wr-hover-bg, #eff6ff);
    color: var(--wr-primary, #3b82f6);
}
.wr-ref-item.wr-ref-selected {
    background: var(--wr-selected-bg, #dbeafe);
    color: var(--wr-primary-dark, #1d4ed8);
    font-weight: 600;
}
.wr-ref-item-hash {
    font-size: 11px;
    font-weight: 700;
    color: var(--wr-muted, #9ca3af);
    flex-shrink: 0;
}
.wr-ref-item.wr-ref-selected .wr-ref-item-hash,
.wr-ref-item:hover .wr-ref-item-hash {
    color: inherit;
    opacity: 0.6;
}

.wr-ref-empty {
    padding: 18px 16px;
    text-align: center;
    font-size: 0.875rem;
    color: var(--wr-muted, #9ca3af);
}

/* ── Footer "add custom" ────────────────────────────────────── */
.wr-ref-footer {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    font-size: 0.875rem;
    color: var(--wr-primary, #3b82f6);
    cursor: pointer;
    border-top: 1px solid var(--wr-border, #e5e7eb);
    transition: background 0.1s;
    font-weight: 500;
}
.wr-ref-footer:hover {
    background: var(--wr-hover-bg, #eff6ff);
}
.wr-ref-footer-icon {
    font-size: 16px;
    font-weight: 700;
    flex-shrink: 0;
}

/* ── Custom input (shown below trigger when selected) ───────── */
.wr-ref-custom-wrap {
    display: none;
    margin-top: 8px;
}
.wr-ref-custom-wrap.visible {
    display: block;
}
.wr-ref-custom-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.03em;
    background: #fff7e6;
    color: #92600a;
    white-space: nowrap;
    flex-shrink: 0;
}

/* Dark mode — Tailwind class-based (.dark on <html>, used by Laravel Breeze) */
.dark .wr-ref-trigger { background: #1f2937; color: #f9fafb; border-color: #374151; }
.dark .wr-ref-trigger:hover,
.dark .wr-ref-trigger:focus { border-color: #3b82f6; }
.dark .wr-ref-display { color: #f9fafb; }
.dark .wr-ref-placeholder { color: #6b7280 !important; }
.dark .wr-ref-trigger-icon { color: #6b7280; }
.dark .wr-ref-dropdown { background: #1f2937; border-color: #3b82f6; }
.dark .wr-ref-search-wrap { background: #111827; border-color: #374151; }
.dark .wr-ref-search-input { color: #f9fafb; }
.dark .wr-ref-item { color: #e5e7eb; }
.dark .wr-ref-item:hover,
.dark .wr-ref-item.wr-ref-focused { background: rgba(59,130,246,0.15); color: #93c5fd; }
.dark .wr-ref-item.wr-ref-selected { background: rgba(59,130,246,0.25); color: #bfdbfe; }
.dark .wr-ref-empty { color: #6b7280; }
.dark .wr-ref-footer { border-color: #374151; color: #60a5fa; }
.dark .wr-ref-footer:hover { background: rgba(59,130,246,0.1); }
.dark .wr-ref-list::-webkit-scrollbar-thumb { background: #374151; }
</style>


{{-- ============================================================
     REFERENCE NUMBER COMBOBOX — JavaScript
     ============================================================ --}}
<script>
(function () {
    'use strict';

    const ALL_REFS = @json($referenceNumbers ?? []);

    let _selected  = null;
    let _isCustom  = false;
    let _filtered  = [];
    let _focusIdx  = -1;

    const $ = id => document.getElementById(id);

    // ── Render list ──────────────────────────────────────────────────────────
    function render(query) {
        const q = (query || '').trim().toLowerCase();
        _filtered = q
            ? ALL_REFS.filter(r => r.toLowerCase().includes(q))
            : [...ALL_REFS];
        _focusIdx = -1;

        const list = $('wrRefList');

        if (_filtered.length === 0) {
            list.innerHTML = '<div class="wr-ref-empty">No matches found</div>';
            $('wrRefAddLabel').textContent = q
                ? `Use "${query.trim()}" as custom`
                : 'Enter a custom reference number';
            return;
        }

        list.innerHTML = _filtered.map((r, i) => {
            const sel     = r === _selected ? ' wr-ref-selected' : '';
            const escaped = r.replace(/&/g,'&amp;').replace(/"/g,'&quot;');
            return `<div class="wr-ref-item${sel}" data-idx="${i}" data-val="${escaped}" role="option">
                        <span class="wr-ref-item-hash">#</span>${r}
                    </div>`;
        }).join('');

        list.querySelectorAll('.wr-ref-item').forEach(el => {
            el.addEventListener('click', () => wrRefSelect(el.dataset.val));
        });

        $('wrRefAddLabel').textContent = 'Enter a custom reference number';
    }

    // ── Open / close ─────────────────────────────────────────────────────────
    window.wrRefToggle = function () {
        const dd   = $('wrRefDropdown');
        const trig = $('wrRefTrigger');
        if (dd.classList.contains('open')) {
            wrRefClose();
        } else {
            dd.classList.add('open');
            trig.classList.add('open');
            trig.setAttribute('aria-expanded', 'true');
            $('wrRefSearchInput').value = '';
            render('');
            setTimeout(() => $('wrRefSearchInput').focus(), 40);
        }
    };

    function wrRefClose() {
        $('wrRefDropdown').classList.remove('open');
        $('wrRefTrigger').classList.remove('open');
        $('wrRefTrigger').setAttribute('aria-expanded', 'false');
    }

    document.addEventListener('click', function (e) {
        if ($('wrRefField') && !$('wrRefField').contains(e.target)) wrRefClose();
    });

    // ── Select existing ───────────────────────────────────────────────────────
    window.wrRefSelect = function (val) {
        _selected = val;
        _isCustom = false;

        $('wrRefHidden').value = val;

        const disp = $('wrRefDisplay');
        disp.textContent = val;
        disp.classList.remove('wr-ref-placeholder');

        $('wrRefClearBtn').style.display = 'inline-flex';
        $('wrRefCustomWrap').classList.remove('visible');
        $('wrRefCustomInput').value = '';

        wrRefClose();
    };

    // ── Clear ─────────────────────────────────────────────────────────────────
    window.wrRefClear = function (e) {
        e.stopPropagation();
        _selected = null;
        _isCustom = false;

        $('wrRefHidden').value = '';

        const disp = $('wrRefDisplay');
        disp.textContent = 'Select or search a reference number';
        disp.classList.add('wr-ref-placeholder');

        $('wrRefClearBtn').style.display = 'none';
        $('wrRefCustomWrap').classList.remove('visible');
        $('wrRefCustomInput').value = '';
    };

    // ── Filter ────────────────────────────────────────────────────────────────
    window.wrRefFilter = function (query) {
        render(query);
    };

    // ── Enable custom input ───────────────────────────────────────────────────
    window.wrRefEnableCustom = function () {
        const query = ($('wrRefSearchInput').value || '').trim();
        _isCustom = true;
        _selected = null;

        const disp = $('wrRefDisplay');
        disp.classList.remove('wr-ref-placeholder');

        $('wrRefClearBtn').style.display = 'inline-flex';
        $('wrRefCustomWrap').classList.add('visible');

        const inp = $('wrRefCustomInput');
        if (query) {
            inp.value = query;
            $('wrRefHidden').value = query;
            disp.textContent = query;
        } else {
            disp.textContent = 'Custom reference number';
        }

        wrRefClose();
        setTimeout(() => inp.focus(), 40);
    };

    // ── Custom input live sync ────────────────────────────────────────────────
    window.wrRefOnCustomInput = function (val) {
        $('wrRefHidden').value = val;
        const disp = $('wrRefDisplay');
        disp.textContent = val || 'Custom reference number';
        disp.classList.toggle('wr-ref-placeholder', !val);
    };

    // ── Keyboard navigation ───────────────────────────────────────────────────
    window.wrRefKey = function (e) {
        const items = document.querySelectorAll('#wrRefList .wr-ref-item');

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                _focusIdx = Math.min(_focusIdx + 1, items.length - 1);
                highlightFocused(items);
                break;
            case 'ArrowUp':
                e.preventDefault();
                _focusIdx = Math.max(_focusIdx - 1, 0);
                highlightFocused(items);
                break;
            case 'Enter':
                e.preventDefault();
                if (_focusIdx >= 0 && _filtered[_focusIdx]) {
                    wrRefSelect(_filtered[_focusIdx]);
                } else {
                    wrRefEnableCustom();
                }
                break;
            case 'Escape':
                wrRefClose();
                break;
        }
    };

    function highlightFocused(items) {
        items.forEach((el, i) =>
            el.classList.toggle('wr-ref-focused', i === _focusIdx)
        );
        if (items[_focusIdx]) {
            items[_focusIdx].scrollIntoView({ block: 'nearest' });
        }
    }

    // ── DOMContentLoaded: restore old() value + submit guard ─────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const oldVal = @json(old('reference_number') ?? '');
        if (oldVal) {
            if (ALL_REFS.includes(oldVal)) {
                wrRefSelect(oldVal);
            } else {
                _isCustom = true;
                $('wrRefHidden').value = oldVal;

                const disp = $('wrRefDisplay');
                disp.textContent = oldVal;
                disp.classList.remove('wr-ref-placeholder');

                $('wrRefClearBtn').style.display = 'inline-flex';
                $('wrRefCustomWrap').classList.add('visible');
                $('wrRefCustomInput').value = oldVal;
            }
        }

        const form = document.getElementById('wr-form');
        if (form) {
            form.addEventListener('submit', function () {
                const customWrap  = $('wrRefCustomWrap');
                const customInput = $('wrRefCustomInput');
                const hidden      = $('wrRefHidden');
                if (customWrap?.classList.contains('visible') && customInput?.value.trim()) {
                    hidden.value = customInput.value.trim();
                }
            });
        }
    });

})();
</script>