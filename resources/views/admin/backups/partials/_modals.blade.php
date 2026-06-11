{{-- resources/views/admin/backups/partials/_modals.blade.php --}}

{{-- ── Create Backup Modal ── --}}
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

{{-- ── Delete Confirm Modal ── --}}
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

{{-- ── Cleanup Confirm Modal ── --}}
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

{{-- ── Diagnostics Modal ── --}}
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