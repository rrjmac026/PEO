<script>
    let wrCurrentStep = 1;
    const wrTotalSteps = 4;

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            const errFields = {
                1: ['name_of_project', 'project_location'],
                2: ['requested_work_start_date', 'description_of_work_requested'],
                3: [],
            };
            const errorKeys = @json($errors->keys());
            for (let step = 1; step <= 3; step++) {
                if (errFields[step].some(f => errorKeys.includes(f))) {
                    wrShowStep(step);
                    break;
                }
            }
        });
    @endif

    function wrGoToStep(n) {
        if (n >= wrCurrentStep) return;
        wrShowStep(n);
    }

    function wrNextStep(from) {
        if (!wrValidateStep(from)) return;
        if (from === wrTotalSteps - 1) wrBuildSummary();
        wrShowStep(from + 1);
    }

    function wrPrevStep(from) {
        wrShowStep(from - 1);
    }

    function wrShowStep(n) {
        document.querySelectorAll('.wr-panel').forEach(p => p.classList.remove('active'));
        document.getElementById(`panel-${n}`).classList.add('active');
        wrCurrentStep = n;
        wrUpdateIndicators();
        window.scrollTo({ top: 0, behavior: 'smooth' });
        wrAutoSave();
    }

    function wrUpdateIndicators() {
        for (let i = 1; i <= wrTotalSteps; i++) {
            const el = document.getElementById(`step-${i}`);
            el.classList.remove('active', 'done');
            if (i < wrCurrentStep) el.classList.add('done');
            else if (i === wrCurrentStep) el.classList.add('active');
            const num = el.querySelector('.wr-step-num');
            num.textContent = i < wrCurrentStep ? '✓' : i;
        }
    }

    function wrValidateStep(step) {
        const required = {
            1: ['name_of_project', 'project_location'],
            2: ['requested_work_start_date', 'description_of_work_requested'],
            3: [],
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

    function wrGetVal(id) {
        const el = document.getElementById(id);
        return el ? el.value.trim() : '';
    }

    function wrBuildSummary() {
        const sections = [
            {
                icon: '📁', title: 'Project Information',
                rows: [
                    { k: 'Project Name',    v: wrGetVal('name_of_project') },
                    { k: 'Location',        v: wrGetVal('project_location') },
                    { k: 'For Office',      v: wrGetVal('for_office') || '—' },
                    { k: 'From Requester',  v: wrGetVal('from_requester') || '—' },
                ]
            },
            {
                icon: '📋', title: 'Request Details',
                rows: [
                    { k: 'Requested By',  v: wrGetVal('contractor_name_display') },
                    { k: 'Start Date',    v: wrGetVal('requested_work_start_date') || '—' },
                    { k: 'Start Time',    v: wrGetVal('requested_work_start_time') || '—' },
                    { k: 'Description',   v: wrGetVal('description_of_work_requested') },
                ]
            },
            {
                icon: '⚙️', title: 'Pay Item & Submission',
                rows: [
                    { k: 'Item No.',      v: wrGetVal('item_no') || '—' },
                    { k: 'Description',   v: wrGetVal('description') || '—' },
                    { k: 'Equipment',     v: wrGetVal('equipment_to_be_used') || '—' },
                    { k: 'Quantity',      v: wrGetVal('estimated_quantity') ? `${wrGetVal('estimated_quantity')} ${wrGetVal('unit')}` : '—' },
                    { k: 'Notes',         v: wrGetVal('notes') || '—' },
                ]
            }
        ];

        document.getElementById('wr-summary-grid').innerHTML = sections.map(sec => `
            <div class="wr-summary-section">
                <div class="wr-summary-head">${sec.icon} ${sec.title}</div>
                <div class="wr-summary-rows">
                    ${sec.rows.map(r => `
                        <div class="wr-summary-row">
                            <span class="wr-summary-key">${r.k}</span>
                            <span class="wr-summary-val ${r.v === '—' ? 'empty' : ''}">${r.v}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `).join('');
    }

    let wrSaveTimer;
    function wrAutoSave() {
        clearTimeout(wrSaveTimer);
        const ind = document.getElementById('wr-float-save');
        ind.classList.add('show');
        wrSaveTimer = setTimeout(() => ind.classList.remove('show'), 2000);
    }
    document.querySelectorAll('#wr-form input, #wr-form textarea, #wr-form select').forEach(el => {
        el.addEventListener('input', () => {
            clearTimeout(el._wrt);
            el._wrt = setTimeout(wrAutoSave, 800);
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const dateEl = document.getElementById('requested_work_start_date');
        if (dateEl) dateEl.min = new Date().toISOString().split('T')[0];
    });
</script>