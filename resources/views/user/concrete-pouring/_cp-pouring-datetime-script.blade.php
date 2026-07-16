<script>
    // ── Restrict pouring_datetime: weekdays only + 3-day processing buffer ──
    document.addEventListener('DOMContentLoaded', function () {
        const dtEl = document.querySelector('input[name="pouring_datetime"]');
        if (!dtEl) return;

        let hintEl = document.getElementById('cp-pouring-hint');
        if (!hintEl) {
            hintEl = document.createElement('p');
            hintEl.id = 'cp-pouring-hint';
            hintEl.style.cssText = 'font-size:11px;margin-top:5px;display:none;';
            dtEl.insertAdjacentElement('afterend', hintEl);
        }

        const isWeekend = (d) => d.getDay() === 0 || d.getDay() === 6; // Sun=0, Sat=6

        dtEl.addEventListener('change', function () {
            if (!dtEl.value) { hintEl.style.display = 'none'; return; }

            const [datePart, timePart] = dtEl.value.split('T');
            const [y, m, d] = datePart.split('-').map(Number);
            const filed = new Date(y, m - 1, d);

            // Reject weekend filing dates outright
            if (isWeekend(filed)) {
                dtEl.value = '';
                hintEl.textContent = '⚠ Weekends are not allowed. Please select a weekday.';
                hintEl.style.color = '#dc2626';
                hintEl.style.display = 'block';
                return;
            }

            // Apply 3-day processing buffer, same as work request
            const earliest = new Date(y, m - 1, d);
            earliest.setDate(earliest.getDate() + 4); // +3 blocked days, lands on 4th

            while (isWeekend(earliest)) {
                earliest.setDate(earliest.getDate() + 1);
            }

            const pad = n => String(n).padStart(2, '0');
            const earliestDateStr = `${earliest.getFullYear()}-${pad(earliest.getMonth()+1)}-${pad(earliest.getDate())}`;
            dtEl.value = `${earliestDateStr}T${timePart || '00:00'}`;

            const fmt = dd => dd.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            hintEl.innerHTML = `📋 Filed <strong>${fmt(filed)}</strong> — 3-day processing period applied. Earliest pouring date: <strong>${fmt(earliest)}</strong>`;
            hintEl.style.color = '';
            hintEl.style.display = 'block';
        });
    });
</script>