{{-- resources/views/admin/backups/partials/_quick-actions.blade.php --}}
<div class="bp-quick-grid">
    <div class="bp-quick-card" onclick="triggerQuickBackup()">
        <div class="bp-quick-icon orange"><i class="fas fa-bolt"></i></div>
        <div>
            <div class="bp-quick-title">Quick Backup</div>
            <div class="bp-quick-desc">Database snapshot right now</div>
        </div>
    </div>
    <div class="bp-quick-card" onclick="openCreateModal('full')">
        <div class="bp-quick-icon slate"><i class="fas fa-archive"></i></div>
        <div>
            <div class="bp-quick-title">Full Backup</div>
            <div class="bp-quick-desc">Database + files</div>
        </div>
    </div>
    <div class="bp-quick-card" onclick="openCreateModal('database')">
        <div class="bp-quick-icon blue"><i class="fas fa-table"></i></div>
        <div>
            <div class="bp-quick-title">Database Only</div>
            <div class="bp-quick-desc">SQL dump with options</div>
        </div>
    </div>
    <div class="bp-quick-card" onclick="openDiagModal()">
        <div class="bp-quick-icon green"><i class="fas fa-heartbeat"></i></div>
        <div>
            <div class="bp-quick-title">Health Check</div>
            <div class="bp-quick-desc">Verify system readiness</div>
        </div>
    </div>
</div>