{{-- resources/views/admin/backups/partials/_scripts.blade.php --}}
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
            success:       { cls: 'ok',   label: '✓ OK' },
            failed:        { cls: 'error', label: '✗ Failed' },
            available:     { cls: 'ok',   label: '✓ Available' },
            not_available: { cls: 'warn', label: '⚠ Not found (PDO fallback)' },
            ok:            { cls: 'ok',   label: '✓ OK' },
            missing:       { cls: 'error', label: '✗ Missing' },
        };

        const rows = [
            { key: 'Database',  value: tests.database?.driver ? `${tests.database.driver} / ${tests.database.database}` : (tests.database?.error ?? ''), status: tests.database?.status },
            { key: 'Backup Dir',value: tests.directory?.writable ? `Writable • ${tests.directory?.free_space} free` : (tests.directory?.error ?? ''), status: tests.directory?.status },
            { key: 'mysqldump', value: tests.mysqldump?.note ?? '', status: tests.mysqldump?.status },
            { key: 'ZIP ext',   value: '', status: tests.extensions?.zip },
            { key: 'PDO ext',   value: '', status: tests.extensions?.pdo },
            { key: 'Queue',     value: `${tests.queue?.driver} (${tests.queue?.mode})`, status: 'ok' },
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
    const toast      = document.getElementById('quick-toast');
    const toastIcon  = document.getElementById('toast-icon');
    const toastFa    = document.getElementById('toast-fa-icon');
    const toastTitle = document.getElementById('toast-title');
    const toastMsg   = document.getElementById('toast-msg');

    toastIcon.className    = 'toast-icon';
    toastFa.className      = 'fas fa-bolt';
    toastTitle.textContent = 'Creating backup…';
    toastMsg.textContent   = 'Please wait';
    toast.classList.add('show');

    fetch('{{ route("admin.backups.quick") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN':      document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'X-Requested-With':  'XMLHttpRequest',
            'Content-Type':      'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toastIcon.classList.add('success');
            toastFa.className      = 'fas fa-check';
            toastTitle.textContent = 'Backup complete!';
            toastMsg.textContent   = data.backup?.size ?? '';
            setTimeout(() => { toast.classList.remove('show'); location.reload(); }, 2500);
        } else {
            toastIcon.classList.add('error');
            toastFa.className      = 'fas fa-times';
            toastTitle.textContent = 'Backup failed';
            toastMsg.textContent   = data.message ?? 'Unknown error';
            setTimeout(() => toast.classList.remove('show'), 4000);
        }
    })
    .catch(err => {
        toastIcon.classList.add('error');
        toastFa.className      = 'fas fa-times';
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
        const statusBadge = document.getElementById(`status-${id}`);
        if (statusBadge) {
            statusBadge.className = `bp-badge ${data.status}`;
            statusBadge.innerHTML = `<span class="dot"></span>${data.status.charAt(0).toUpperCase() + data.status.slice(1)}`;
        }
        const bar = document.getElementById(`progress-${id}`);
        if (bar) bar.style.width = (data.progress ?? 0) + '%';
        const sizeEl = document.getElementById(`size-${id}`);
        if (sizeEl && data.formatted_size) sizeEl.textContent = data.formatted_size;
        if (data.status === 'completed' || data.status === 'failed') {
            setTimeout(() => location.reload(), 1200);
        }
    })
    .catch(console.error);
}

/* ─── Auto-poll processing rows on page load ────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[id^="backup-row-"]').forEach(row => {
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