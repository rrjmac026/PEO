@push('scripts')
<script>
    let wrCurrentStep = 1;
    const wrTotalSteps = 7;

    // Jump back to error step on server-side failures
    @if($errors->any())
    document.addEventListener('DOMContentLoaded', () => {
        const stepFields = {
            1: ['name_of_project','project_location','reference_number'],
            2: ['requested_work_start_date','description_of_work_requested'],
            3: [],
            4: [],
            5: [],
            6: [],
            7: ['status'],
        };
        const errorKeys = @json($errors->keys());
        for (let step = 1; step <= wrTotalSteps; step++) {
            if (stepFields[step].some(f => errorKeys.includes(f))) {
                wrShowStep(step);
                break;
            }
        }
    });
    @endif

    function wrGoToStep(n) {
        // Only allow clicking back to already-visited steps
        if (n < wrCurrentStep) wrShowStep(n);
    }

    function wrNextStep(from) {
        if (!wrValidateStep(from)) return;
        wrShowStep(from + 1);
    }

    function wrPrevStep(from) {
        wrShowStep(from - 1);
    }

    function wrShowStep(n) {
        document.querySelectorAll('.wr-panel').forEach(p => p.classList.remove('active'));
        document.getElementById(`panel-${n}`)?.classList.add('active');
        wrCurrentStep = n;
        wrUpdateIndicators();
        window.scrollTo({ top: 0, behavior: 'smooth' });
        wrAutoSave();
    }

    function wrUpdateIndicators() {
        for (let i = 1; i <= wrTotalSteps; i++) {
            const el = document.getElementById(`step-${i}`);
            if (!el) continue;
            el.classList.remove('active', 'done');
            if (i < wrCurrentStep)      el.classList.add('done');
            else if (i === wrCurrentStep) el.classList.add('active');
            const num = el.querySelector('.wr-step-num');
            num.textContent = i < wrCurrentStep ? '✓' : i;
        }
    }

    function wrValidateStep(step) {
        const required = {
            1: ['name_of_project','project_location'],
            2: ['requested_work_start_date','description_of_work_requested'],
            3: [], 4: [], 5: [], 6: [], 7: [],
        };
        if (!required[step]) return true;
        let ok = true;
        required[step].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.remove('wr-error');
            const err = document.getElementById(`err-${id}`);
            if (err) err.classList.remove('show');
        });
        required[step].forEach(id => {
            const el = document.getElementById(id);
            if (!el || !el.value.trim()) {
                if (el) el.classList.add('wr-error');
                const err = document.getElementById(`err-${id}`);
                if (err) err.classList.add('show');
                ok = false;
            }
        });
        return ok;
    }

    // ── Auto-save indicator ──
    let wrSaveTimer;
    function wrAutoSave() {
        clearTimeout(wrSaveTimer);
        const ind = document.getElementById('wr-float-save');
        if (ind) { ind.classList.add('show'); wrSaveTimer = setTimeout(() => ind.classList.remove('show'), 2000); }
    }
    document.querySelectorAll('#wr-form input, #wr-form textarea, #wr-form select').forEach(el => {
        el.addEventListener('input', () => {
            clearTimeout(el._wrt);
            el._wrt = setTimeout(wrAutoSave, 800);
        });
    });

    // ══════════════════════════════════════════════════════════════════════
    // Employee search & auto-fill signature
    // Context map: which hidden fields each search context fills
    // ══════════════════════════════════════════════════════════════════════
    const wrContextMap = {
        eng4: {
            nameField:       'checked_by_mtqa_name',
            sigHidden:       'mtqa_signature',
            sigPreview:      'eng4_sig_preview',
            badgeEl:         'eng4_name_badge',
        },
        eng3: {
            nameField:       'recommending_approval_by_name',
            sigHidden:       'recommending_approval_signature',
            sigPreview:      'eng3_sig_preview',
            badgeEl:         'eng3_name_badge',
        },
        pe: {
            nameField:       'approved_by_name',
            sigHidden:       'approved_signature',
            sigPreview:      'pe_sig_preview',
            badgeEl:         'pe_name_badge',
        },
    };

    let wrSearchTimers = {};

    function wrEmployeeSearch(inputEl, context) {
        const term = inputEl.value.trim();
        const suggestionsEl = document.getElementById(`${context}_suggestions`);

        clearTimeout(wrSearchTimers[context]);

        if (term.length < 2) {
            suggestionsEl.style.display = 'none';
            return;
        }

        wrSearchTimers[context] = setTimeout(() => {
            fetch(`{{ route('admin.work-requests.search-employee') }}?term=${encodeURIComponent(term)}`)
                .then(r => r.json())
                .then(employees => {
                    suggestionsEl.innerHTML = '';
                    if (!employees.length) {
                        suggestionsEl.innerHTML = '<div class="wr-suggestion-item"><span class="wr-suggestion-meta">No employees found</span></div>';
                        suggestionsEl.style.display = 'block';
                        return;
                    }
                    employees.forEach(emp => {
                        const div = document.createElement('div');
                        div.className = 'wr-suggestion-item';
                        div.innerHTML = `
                            <div class="wr-suggestion-avatar">${emp.name.charAt(0).toUpperCase()}</div>
                            <div>
                                <div class="wr-suggestion-name">${emp.name}</div>
                                <div class="wr-suggestion-meta">${emp.employee_id || ''} · ${emp.position || ''} · ${emp.department || ''}</div>
                            </div>`;
                        div.addEventListener('click', () => wrSelectEmployee(emp, context));
                        suggestionsEl.appendChild(div);
                    });
                    suggestionsEl.style.display = 'block';
                })
                .catch(() => { suggestionsEl.style.display = 'none'; });
        }, 300);
    }

    function wrSelectEmployee(emp, context) {
        const cfg = wrContextMap[context];
        if (!cfg) return;

        // Fill name field
        const nameEl = document.getElementById(cfg.nameField);
        if (nameEl) nameEl.value = emp.name;

        // Show auto badge
        const badge = document.getElementById(cfg.badgeEl);
        if (badge) badge.style.display = '';

        // Hide suggestions & clear search box
        document.getElementById(`${context}_suggestions`).style.display = 'none';
        document.getElementById(`${context}_employee_search`).value = emp.name;

        // Fetch full employee details (to get signature)
        fetch(`/admin/work-requests/employee/${emp.id}`)
            .then(r => r.json())
            .then(data => {
                // The employee may have a signature_path on their user record
                // We'll request it from the employee detail endpoint which should include user info
                wrLoadEmployeeSignature(data, cfg);
            })
            .catch(() => {});
    }

    function wrLoadEmployeeSignature(emp, cfg) {
        // Try to get signature from the employee's user record via a dedicated endpoint
        fetch(`/admin/work-requests/employee/${emp.id}/signature`)
            .then(r => r.json())
            .then(data => {
                if (data.signature_url) {
                    wrSetSignature(cfg, data.signature_url);
                } else {
                    wrClearSignature(cfg);
                }
            })
            .catch(() => wrClearSignature(cfg));
    }

    function wrSetSignature(cfg, url) {
        const hidden = document.getElementById(cfg.sigHidden);
        const preview = document.getElementById(cfg.sigPreview);
        if (hidden) hidden.value = url;
        if (preview) {
            preview.innerHTML = `<img src="${url}" alt="Signature" class="wr-sig-img">`;
            preview.classList.add('has-sig');
        }
    }

    function wrClearSignature(cfg) {
        const hidden = document.getElementById(cfg.sigHidden);
        const preview = document.getElementById(cfg.sigPreview);
        if (hidden) hidden.value = '';
        if (preview) {
            preview.innerHTML = '<span class="wr-sig-empty">No signature on file</span>';
            preview.classList.remove('has-sig');
        }
    }

    // Close suggestion dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        ['eng4','eng3','pe'].forEach(ctx => {
            const sg = document.getElementById(`${ctx}_suggestions`);
            const inp = document.getElementById(`${ctx}_employee_search`);
            if (sg && inp && !inp.contains(e.target) && !sg.contains(e.target)) {
                sg.style.display = 'none';
            }
        });
    });
</script>
@endpush