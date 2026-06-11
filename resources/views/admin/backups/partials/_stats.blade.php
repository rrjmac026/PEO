{{-- resources/views/admin/backups/partials/_stats.blade.php --}}

{{-- ── Stat cards ── --}}
<div class="bp-stats-grid">
    <div class="bp-stat-card accent">
        <span class="bp-stat-icon"><i class="fas fa-database"></i></span>
        <div class="bp-stat-label">Total Backups</div>
        <div class="bp-stat-value">{{ $stats['total'] }}</div>
        <div class="bp-stat-sub">All time</div>
    </div>
    <div class="bp-stat-card green">
        <span class="bp-stat-icon"><i class="fas fa-check-circle"></i></span>
        <div class="bp-stat-label">Completed</div>
        <div class="bp-stat-value">{{ $stats['completed'] }}</div>
        <div class="bp-stat-sub">Available to download</div>
    </div>
    <div class="bp-stat-card red">
        <span class="bp-stat-icon"><i class="fas fa-times-circle"></i></span>
        <div class="bp-stat-label">Failed</div>
        <div class="bp-stat-value">{{ $stats['failed'] }}</div>
        <div class="bp-stat-sub">Needs attention</div>
    </div>
    <div class="bp-stat-card blue">
        <span class="bp-stat-icon"><i class="fas fa-spinner"></i></span>
        <div class="bp-stat-label">Processing</div>
        <div class="bp-stat-value">{{ $stats['processing'] }}</div>
        <div class="bp-stat-sub">Currently running</div>
    </div>
    <div class="bp-stat-card yellow">
        <span class="bp-stat-icon"><i class="fas fa-clock"></i></span>
        <div class="bp-stat-label">Pending</div>
        <div class="bp-stat-value">{{ $stats['pending'] }}</div>
        <div class="bp-stat-sub">Queued</div>
    </div>
    <div class="bp-stat-card slate">
        <span class="bp-stat-icon"><i class="fas fa-hdd"></i></span>
        <div class="bp-stat-label">Total Size</div>
        <div class="bp-stat-value" style="font-size:18px;">{{ $stats['formatted_total_size'] }}</div>
        <div class="bp-stat-sub">Disk usage</div>
    </div>
</div>

{{-- ── Latest backup strip ── --}}
@if($stats['latest'])
    <div class="bp-latest-strip">
        <span class="strip-icon"><i class="fas fa-shield-alt"></i></span>
        <span>
            Last successful backup:
            <strong>{{ $stats['latest']->backup_name }}</strong>
            &mdash;
            {{ $stats['latest']->completed_at?->diffForHumans() }}
            ({{ $stats['latest']->formatted_size }})
        </span>
    </div>
@else
    <div class="bp-latest-strip warning">
        <span class="strip-icon"><i class="fas fa-exclamation-triangle"></i></span>
        <span>No completed backups found. Create your first backup to protect your data.</span>
    </div>
@endif