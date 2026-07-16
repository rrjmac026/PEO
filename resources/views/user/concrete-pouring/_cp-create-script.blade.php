<script>
(function () {
    'use strict';

    const ALL_CP_REFS = @json($contractNumbers ?? []);
    let _sel = null, _custom = false, _filtered = [], _focusIdx = -1;
    const $ = id => document.getElementById(id);

    // ── Render list ───────────────────────────────────────────────────────
    function render(query) {
        const q = (query || '').trim().toLowerCase();
        _filtered = q
            ? ALL_CP_REFS.filter(r => r.toLowerCase().includes(q))
            : [...ALL_CP_REFS];
        _focusIdx = -1;

        const list = $('cpRefList');

        if (!_filtered.length) {
            list.innerHTML = '<div class="cp-ref-empty">No matches found</div>';
            $('cpRefAddLabel').textContent = q
                ? `Use "${query.trim()}" as custom`
                : 'Enter a custom contract number';
            return;
        }

        list.innerHTML = _filtered.map((r, i) => {
            const sel = r === _sel ? ' cp-ref-selected' : '';
            const esc = r.replace(/&/g, '&amp;').replace(/"/g, '&quot;');
            return `<div class="cp-ref-item${sel}" data-val="${esc}">
                        <span class="cp-ref-item-hash">#</span>${r}
                    </div>`;
        }).join('');

        list.querySelectorAll('.cp-ref-item').forEach(el =>
            el.addEventListener('click', () => cpRefSelect(el.dataset.val))
        );
        $('cpRefAddLabel').textContent = 'Enter a custom contract number';
    }

    // ── Open / close ──────────────────────────────────────────────────────
    window.cpRefToggle = function () {
        const dd = $('cpRefDropdown'), tr = $('cpRefTrigger');
        if (dd.classList.contains('open')) { cpRefClose(); return; }
        dd.classList.add('open');
        tr.classList.add('open');
        tr.setAttribute('aria-expanded', 'true');
        $('cpRefChevron').style.transform = 'rotate(180deg)';
        $('cpRefSearchInput').value = '';
        render('');
        setTimeout(() => $('cpRefSearchInput').focus(), 40);
    };

    function cpRefClose() {
        $('cpRefDropdown').classList.remove('open');
        $('cpRefTrigger').classList.remove('open');
        $('cpRefTrigger').setAttribute('aria-expanded', 'false');
        $('cpRefChevron').style.transform = '';
    }

    document.addEventListener('click', e => {
        if ($('cpRefField') && !$('cpRefField').contains(e.target)) cpRefClose();
    });

    // ── Select existing ───────────────────────────────────────────────────
    window.cpRefSelect = function (val) {
        _sel = val; _custom = false;
        $('cpRefHidden').value = val;
        const d = $('cpRefDisplay');
        d.textContent = val;
        d.classList.remove('cp-ref-placeholder');
        $('cpRefClearBtn').style.display = 'inline-flex';
        $('cpRefCustomWrap').classList.remove('visible');
        $('cpRefCustomInput').value = '';
        cpRefClose();
    };

    // ── Clear ─────────────────────────────────────────────────────────────
    window.cpRefClear = function (e) {
        e.stopPropagation();
        _sel = null; _custom = false;
        $('cpRefHidden').value = '';
        const d = $('cpRefDisplay');
        d.textContent = 'Select or search a contract number';
        d.classList.add('cp-ref-placeholder');
        $('cpRefClearBtn').style.display = 'none';
        $('cpRefCustomWrap').classList.remove('visible');
        $('cpRefCustomInput').value = '';
    };

    // ── Filter ────────────────────────────────────────────────────────────
    window.cpRefFilter = function (q) { render(q); };

    // ── Enable custom input ───────────────────────────────────────────────
    window.cpRefEnableCustom = function () {
        const q = ($('cpRefSearchInput').value || '').trim();
        _custom = true; _sel = null;
        const d = $('cpRefDisplay');
        $('cpRefClearBtn').style.display = 'inline-flex';
        $('cpRefCustomWrap').classList.add('visible');
        const inp = $('cpRefCustomInput');
        if (q) {
            inp.value = q;
            $('cpRefHidden').value = q;
            d.textContent = q;
        } else {
            d.textContent = 'Custom contract number';
        }
        d.classList.remove('cp-ref-placeholder');
        cpRefClose();
        setTimeout(() => inp.focus(), 40);
    };

    // ── Custom input live sync ────────────────────────────────────────────
    window.cpRefOnCustomInput = function (val) {
        $('cpRefHidden').value = val;
        const d = $('cpRefDisplay');
        d.textContent = val || 'Custom contract number';
        d.classList.toggle('cp-ref-placeholder', !val);
    };

    // ── Keyboard navigation ───────────────────────────────────────────────
    window.cpRefKey = function (e) {
        const items = document.querySelectorAll('#cpRefList .cp-ref-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            _focusIdx = Math.min(_focusIdx + 1, items.length - 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            _focusIdx = Math.max(_focusIdx - 1, 0);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            _focusIdx >= 0 && _filtered[_focusIdx]
                ? cpRefSelect(_filtered[_focusIdx])
                : cpRefEnableCustom();
            return;
        } else if (e.key === 'Escape') {
            cpRefClose(); return;
        }
        items.forEach((el, i) => el.classList.toggle('cp-ref-focused', i === _focusIdx));
        if (items[_focusIdx]) items[_focusIdx].scrollIntoView({ block: 'nearest' });
    };

    // ── Restore old() value on validation failure ─────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const oldVal = @json(old('contract_number') ?? '');
        if (!oldVal) return;

        if (ALL_CP_REFS.includes(oldVal)) {
            cpRefSelect(oldVal);
        } else {
            _custom = true;
            $('cpRefHidden').value = oldVal;
            const d = $('cpRefDisplay');
            d.textContent = oldVal;
            d.classList.remove('cp-ref-placeholder');
            $('cpRefClearBtn').style.display = 'inline-flex';
            $('cpRefCustomWrap').classList.add('visible');
            $('cpRefCustomInput').value = oldVal;
        }
    });
})();
</script>
@include('user.concrete-pouring._cp-pouring-datetime-script')