{{-- resources/views/admin/backups/partials/_table.blade.php --}}
<div class="bp-card">

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.backups.index') }}" class="bp-filter-bar" id="filter-form">
        <label>Filter:</label>
        <select name="status" class="bp-select" onchange="document.getElementById('filter-form').submit()">
            <option value="">All statuses</option>
            @foreach(['completed','processing','pending','failed'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="type" class="bp-select" onchange="document.getElementById('filter-form').submit()">
            <option value="">All types</option>
            <option value="database" {{ request('type') === 'database' ? 'selected' : '' }}>Database</option>
            <option value="full"     {{ request('type') === 'full'     ? 'selected' : '' }}>Full</option>
        </select>
        @if(request()->hasAny(['status','type']))
            <a href="{{ route('admin.backups.index') }}" class="bp-btn bp-btn-ghost bp-btn-sm">
                <i class="fas fa-times"></i> Clear
            </a>
        @endif
        <span style="margin-left:auto; font-size:12px; color:var(--bp-muted);">
            {{ $backups->total() }} {{ Str::plural('backup', $backups->total()) }}
        </span>
    </form>

    {{-- Table --}}
    <div class="bp-table-wrap">
        @if($backups->isEmpty())
            <div class="bp-empty">
                <div class="bp-empty-icon"><i class="fas fa-database"></i></div>
                <div class="bp-empty-title">No backups found</div>
                <div class="bp-empty-desc">
                    @if(request()->hasAny(['status','type']))
                        No backups match your filters. <a href="{{ route('admin.backups.index') }}" style="color:var(--bp-accent);">Clear filters</a>
                    @else
                        Create your first backup using the button above.
                    @endif
                </div>
            </div>
        @else
            <table class="bp-table">
                <thead>
                    <tr>
                        <th>Backup Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Size</th>
                        <th>Retention</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($backups as $backup)
                    <tr id="backup-row-{{ $backup->id }}">
                        <td class="primary">
                            <div class="truncate" title="{{ $backup->backup_name }}">
                                <i class="fas fa-file-archive" style="color:var(--bp-muted);margin-right:6px;"></i>
                                {{ $backup->backup_name }}
                            </div>
                            @if($backup->error_message)
                                <div class="text-sm text-muted" style="margin-top:3px;color:var(--bp-red)!important;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ Str::limit($backup->error_message, 60) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="bp-badge {{ $backup->backup_type }}">
                                @if($backup->backup_type === 'database')
                                    <i class="fas fa-table" style="font-size:9px;"></i> Database
                                @else
                                    <i class="fas fa-archive" style="font-size:9px;"></i> Full
                                @endif
                            </span>
                        </td>
                        <td>
                            <span class="bp-badge {{ $backup->status }}" id="status-{{ $backup->id }}">
                                <span class="dot"></span>
                                {{ ucfirst($backup->status) }}
                            </span>
                            @if(in_array($backup->status, ['processing','pending']))
                                <div class="bp-progress-wrap" style="margin-top:6px;max-width:100px;">
                                    <div class="bp-progress-bar" id="progress-{{ $backup->id }}" style="width:{{ $backup->progress ?? 0 }}%;"></div>
                                </div>
                            @endif
                        </td>
                        <td class="monospace" id="size-{{ $backup->id }}">
                            {{ $backup->formatted_size ?? '—' }}
                        </td>
                        <td>
                            @if($backup->retention_until)
                                @if($backup->retention_until->isPast())
                                    <span style="color:var(--bp-red);font-size:12px;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Expired
                                    </span>
                                @else
                                    <span style="font-size:12px;color:var(--bp-muted);">
                                        {{ $backup->retention_until->diffForHumans() }}
                                    </span>
                                @endif
                            @else
                                <span style="color:var(--bp-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:12.5px;">{{ $backup->creator?->name ?? 'System' }}</span>
                        </td>
                        <td>
                            <span style="font-size:12.5px;">{{ $backup->created_at->format('M d, Y') }}</span>
                            <div class="text-sm text-muted">{{ $backup->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <div class="bp-row-actions" style="justify-content:flex-end;">
                                {{-- Poll status (for processing/pending) --}}
                                @if(in_array($backup->status, ['processing','pending']))
                                    <button class="bp-icon-btn status"
                                        title="Refresh status"
                                        onclick="pollStatus({{ $backup->id }})">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                @endif

                                {{-- Download --}}
                                @if($backup->canDownload())
                                    <a href="{{ route('admin.backups.download', $backup) }}"
                                       class="bp-icon-btn download"
                                       title="Download backup">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @else
                                    <span class="bp-icon-btn" style="opacity:.3;cursor:default;" title="Not available">
                                        <i class="fas fa-download"></i>
                                    </span>
                                @endif

                                {{-- Delete --}}
                                @if($backup->canDelete())
                                    <button class="bp-icon-btn destroy"
                                        title="Delete backup"
                                        onclick="confirmDelete({{ $backup->id }}, '{{ addslashes($backup->backup_name) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <span class="bp-icon-btn" style="opacity:.3;cursor:default;" title="Cannot delete while processing">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Pagination --}}
    @if($backups->hasPages())
        <div class="bp-pagination">
            <span>Showing {{ $backups->firstItem() }}–{{ $backups->lastItem() }} of {{ $backups->total() }}</span>
            {{ $backups->links() }}
        </div>
    @endif

</div>