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

        {{-- Reference Number — searchable combobox --}}
        <div class="wr-field">
            <label class="wr-label">
                Reference Number
                <span style="color:var(--wr-muted);font-weight:400;">(optional)</span>
            </label>

            {{-- Trigger button --}}
            <div class="wr-ref-combobox" id="wrRefField">
                <div class="wr-input-wrap wr-ref-trigger" id="wrRefTrigger" onclick="wrRefToggle()">
                    
                    <span id="wrRefDisplay" class="wr-ref-display wr-ref-placeholder">
                        Select or search reference number
                    </span>
                    <button type="button" id="wrRefClearBtn"
                        class="wr-ref-clear-btn" style="display:none;"
                        onclick="wrRefClear(event)" title="Clear selection">✕</button>
                    <span class="wr-ref-chevron" id="wrRefChevron">▼</span>
                </div>

                {{-- Dropdown panel --}}
                <div class="wr-ref-dropdown" id="wrRefDropdown">
                    <div class="wr-ref-search-row">
                        <span class="wr-ref-search-icon">⌕</span>
                        <input
                            type="text"
                            class="wr-ref-search-input"
                            id="wrRefSearchInput"
                            placeholder="Search reference numbers…"
                            autocomplete="off"
                            oninput="wrRefFilter(this.value)"
                            onkeydown="wrRefKey(event)">
                    </div>
                    <div class="wr-ref-list" id="wrRefList"></div>
                    <div class="wr-ref-divider"></div>
                    <div class="wr-ref-add-custom" onclick="wrRefEnableCustom()">
                        <span class="wr-ref-add-icon">+</span>
                        <span id="wrRefAddLabel">Enter custom reference number</span>
                    </div>
                </div>
            </div>

            {{-- Custom free-text row (shown on demand) --}}
            <div class="wr-ref-custom-wrap" id="wrRefCustomWrap">
                <div class="wr-input-wrap">
                    <span class="wr-icon">#️⃣</span>
                    <input type="text" id="wrRefCustomInput"
                        placeholder="e.g., REF-2026-001"
                        maxlength="100"
                        oninput="wrRefOnCustomInput(this.value)">
                    <span class="wr-readonly-badge" style="background:var(--wr-warn-bg,#fff7e6);color:#92600a;">Custom</span>
                </div>
            </div>

            {{-- Hidden field submitted with the form --}}
            <input type="hidden" name="reference_number" id="wrRefHidden"
                value="{{ old('reference_number') }}">

            <p class="wr-field-hint">Select from existing reference numbers or create a new one.</p>
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
                <span style="color:var(--wr-muted);font-weight:400;">(optional)</span>
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

</style>


{{-- ============================================================
     REFERENCE NUMBER COMBOBOX — JavaScript
     ============================================================ --}}
<script>
(function () {
    'use strict';

    /* ----------------------------------------------------------
       Data — fed directly from Laravel
    ---------------------------------------------------------- */
    const ALL_REFS = @json($referenceNumbers ?? []);

    /* ----------------------------------------------------------
       State
    ---------------------------------------------------------- */
    let _selected  = null;   // currently selected value (from list)
    let _isCustom  = false;  // true when user chose "enter custom"
    let _filtered  = [];     // currently visible items
    let _focusIdx  = -1;     // keyboard-focused row index

    /* ----------------------------------------------------------
       DOM helpers
    ---------------------------------------------------------- */
    const $ = id => document.getElementById(id);

    /* ----------------------------------------------------------
       Render list
    ---------------------------------------------------------- */
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
                : 'Enter custom reference number';
            return;
        }

        list.innerHTML = _filtered.map((r, i) => {
            const sel = r === _selected ? ' wr-ref-selected' : '';
            return `<div class="wr-ref-item${sel}" data-idx="${i}" onclick="wrRefSelect('${r}')">
                        <span class="wr-ref-item-hash">#</span>${r}
                    </div>`;
        }).join('');

        $('wrRefAddLabel').textContent = 'Enter custom reference number';
    }

    /* ----------------------------------------------------------
       Open / close
    ---------------------------------------------------------- */
    window.wrRefToggle = function () {
        const dd   = $('wrRefDropdown');
        const trig = $('wrRefTrigger');
        if (dd.classList.contains('open')) {
            wrRefClose();
        } else {
            dd.classList.add('open');
            trig.classList.add('open');
            $('wrRefSearchInput').value = '';
            render('');
            setTimeout(() => $('wrRefSearchInput').focus(), 40);
        }
    };

    function wrRefClose() {
        $('wrRefDropdown').classList.remove('open');
        $('wrRefTrigger').classList.remove('open');
    }

    /* Close on outside click */
    document.addEventListener('click', function (e) {
        if (!$('wrRefField').contains(e.target)) wrRefClose();
    });

    /* ----------------------------------------------------------
       Select an existing item
    ---------------------------------------------------------- */
    window.wrRefSelect = function (val) {
        _selected = val;
        _isCustom = false;

        $('wrRefHidden').value = val;

        const disp = $('wrRefDisplay');
        disp.textContent = val;
        disp.classList.remove('wr-ref-placeholder');

        $('wrRefClearBtn').style.display = 'inline';
        $('wrRefCustomWrap').classList.remove('visible');
        $('wrRefCustomInput').value = '';

        wrRefClose();
    };

    /* ----------------------------------------------------------
       Clear selection
    ---------------------------------------------------------- */
    window.wrRefClear = function (e) {
        e.stopPropagation();
        _selected = null;
        _isCustom = false;

        $('wrRefHidden').value = '';

        const disp = $('wrRefDisplay');
        disp.textContent = 'Select or search reference number';
        disp.classList.add('wr-ref-placeholder');

        $('wrRefClearBtn').style.display = 'none';
        $('wrRefCustomWrap').classList.remove('visible');
        $('wrRefCustomInput').value = '';

        wrRefClose();
    };

    /* ----------------------------------------------------------
       Filter as user types
    ---------------------------------------------------------- */
    window.wrRefFilter = function (query) {
        render(query);
    };

    /* ----------------------------------------------------------
       Enable custom input
    ---------------------------------------------------------- */
    window.wrRefEnableCustom = function () {
        const query = ($('wrRefSearchInput').value || '').trim();
        _isCustom = true;
        _selected = null;

        const disp = $('wrRefDisplay');
        disp.textContent = 'Custom reference number';
        disp.classList.remove('wr-ref-placeholder');

        $('wrRefClearBtn').style.display = 'inline';
        $('wrRefCustomWrap').classList.add('visible');

        const inp = $('wrRefCustomInput');
        if (query) {
            inp.value = query;
            $('wrRefHidden').value = query;
            disp.textContent = query;
        }

        wrRefClose();
        setTimeout(() => inp.focus(), 40);
    };

    /* ----------------------------------------------------------
       Custom input — live sync to hidden field
    ---------------------------------------------------------- */
    window.wrRefOnCustomInput = function (val) {
        $('wrRefHidden').value = val;
        $('wrRefDisplay').textContent = val || 'Custom reference number';
    };

    /* ----------------------------------------------------------
       Keyboard navigation
    ---------------------------------------------------------- */
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

    /* ----------------------------------------------------------
       Restore old() value after a validation redirect
    ---------------------------------------------------------- */
    document.addEventListener('DOMContentLoaded', function () {
        const oldVal = @json(old('reference_number') ?? '');
        if (!oldVal) return;

        if (ALL_REFS.includes(oldVal)) {
            wrRefSelect(oldVal);
        } else {
            /* It's a custom value — show the custom input pre-filled */
            _isCustom = true;
            $('wrRefHidden').value = oldVal;

            const disp = $('wrRefDisplay');
            disp.textContent = oldVal;
            disp.classList.remove('wr-ref-placeholder');

            $('wrRefClearBtn').style.display = 'inline';
            $('wrRefCustomWrap').classList.add('visible');
            $('wrRefCustomInput').value = oldVal;
        }
    });

})();
</script>