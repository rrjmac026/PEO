<script>
    let wrCurrentStep = 1;
    const wrTotalSteps = 5;

    // If there are server-side validation errors, jump to the right panel
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            const errFields = {
                1: ['name_of_project','project_location'],
                2: ['requested_work_start_date','description_of_work_requested'],
                3: [],
                4: [],
                5: [],
                6: [],
                7: ['status'],
            };
            const errorKeys = @json($errors->keys());
            for (let step = 1; step <= wrTotalSteps; step++) {
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
            4: [],
            5: [],
            6: [],
            7: [],
        };
        if (!required[step]) return true;
        let ok = true;
        required[step].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.classList.remove('wr-error'); }
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

    // Status toggle
    document.querySelectorAll('.wr-status-opt').forEach(opt => {
        opt.addEventListener('click', () => {
            document.querySelectorAll('.wr-status-opt').forEach(o => o.classList.remove('selected'));
            opt.classList.add('selected');
        });
    });

    // Toggle collapsible sections
    function wrToggleSection(header) {
        header.classList.toggle('collapsed');
        const content = header.nextElementSibling;
        if (content && content.classList.contains('wr-section-content')) {
            content.classList.toggle('show');
        }
    }

    // Auto-save indicator
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

    // Min date
    document.addEventListener('DOMContentLoaded', () => {
        const dateEl = document.getElementById('requested_work_start_date');
        if (dateEl) dateEl.min = new Date().toISOString().split('T')[0];
    });
</script>
