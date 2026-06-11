{{-- resources/views/admin/backups/partials/_header.blade.php --}}
<div class="bp-page-header">
    <div>
        <div class="bp-page-title">
            <span class="title-icon"><i class="fas fa-database"></i></span>
            Database Backups
        </div>
        <div class="bp-page-subtitle">Manage and schedule database backups. Keep your data safe and recoverable.</div>
    </div>
    <div class="bp-header-actions">
        <button class="bp-btn bp-btn-ghost" onclick="openDiagModal()">
            <i class="fas fa-stethoscope"></i>
            Run Diagnostics
        </button>
        <button class="bp-btn bp-btn-secondary" onclick="openCleanupModal()">
            <i class="fas fa-broom"></i>
            Clean Expired
            @if($stats['expired_count'] > 0)
                <span style="background:var(--bp-red);color:#fff;border-radius:999px;font-size:10px;padding:1px 7px;font-weight:800;">
                    {{ $stats['expired_count'] }}
                </span>
            @endif
        </button>
        <button class="bp-btn bp-btn-primary" onclick="openCreateModal()">
            <i class="fas fa-plus"></i>
            New Backup
        </button>
    </div>
</div>