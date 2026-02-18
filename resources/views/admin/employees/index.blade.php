@extends('layouts.app')

@section('title', 'Employees')

@push('styles')
<style>
    /* ══════════════════════════════════════════
       LIGHT MODE TOKENS (primary / default)
    ══════════════════════════════════════════ */
    :root {
        --em-surface:   #ffffff;
        --em-surface2:  #f8fafc;
        --em-border:    #e2e8f0;
        --em-text:      #0f172a;
        --em-text-sec:  #334155;
        --em-muted:     #64748b;
        --em-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --em-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
    }

    /* ══════════════════════════════════════════
       DARK MODE TOKENS (override on .dark)
    ══════════════════════════════════════════ */
    .dark {
        --em-surface:   #1a1f2e;
        --em-surface2:  #1e2335;
        --em-border:    #2a3050;
        --em-text:      #e8eaf6;
        --em-text-sec:  #c5cae9;
        --em-muted:     #7c85a8;
        --em-shadow:    0 1px 4px rgba(0,0,0,0.35);
        --em-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
    }

    /* ── Page heading ── */
    .em-page-title { font-size: 28px; font-weight: 800; color: var(--em-text); line-height: 1.2; }
    .em-page-sub   { font-size: 14px; color: var(--em-muted); margin-top: 4px; }

    /* ── Alert banners ── */
    .em-alert {
        display: flex; align-items: flex-start; justify-content: space-between;
        padding: 12px 16px; border-radius: 10px; border: 1px solid;
        margin-bottom: 16px; font-size: 14px;
    }
    .em-alert.success { background: #f0fdf4; border-color: #86efac; color: #166534; }
    .em-alert.error   { background: #fff1f2; border-color: #fca5a5; color: #991b1b; }
    .dark .em-alert.success { background: rgba(5,150,105,.12);  border-color: rgba(52,211,153,.3);  color: #6ee7b7; }
    .dark .em-alert.error   { background: rgba(220,38,38,.10);  border-color: rgba(248,113,113,.3); color: #fca5a5; }
    .em-alert ul { list-style: disc; padding-left: 20px; margin-top: 4px; }
    .em-alert-close {
        background: none; border: none; cursor: pointer; font-size: 14px;
        opacity: .6; color: inherit; padding: 0; margin-left: 12px; flex-shrink: 0;
    }
    .em-alert-close:hover { opacity: 1; }

    /* ── Search bar ── */
    .em-search-wrap {
        display: flex; gap: 8px; margin-bottom: 20px;
    }
    .em-input {
        flex: 1;
        background: var(--em-surface);
        border: 1px solid var(--em-border);
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 14px;
        color: var(--em-text);
        box-shadow: var(--em-shadow);
        transition: border-color .15s, box-shadow .15s;
        outline: none;
    }
    .em-input::placeholder { color: var(--em-muted); }
    .em-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    }
    .dark .em-input:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129,140,248,.12);
    }

    /* ── Buttons ── */
    .em-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px;
        font-size: 13px; font-weight: 600;
        border: 1px solid; cursor: pointer;
        transition: all .15s; text-decoration: none; white-space: nowrap;
    }
    .em-btn-indigo {
        background: #4f46e5; border-color: #4f46e5; color: #fff;
    }
    .em-btn-indigo:hover { background: #4338ca; border-color: #4338ca; }
    .dark .em-btn-indigo { background: #6366f1; border-color: #6366f1; }
    .dark .em-btn-indigo:hover { background: #818cf8; border-color: #818cf8; }

    .em-btn-dark {
        background: #1e293b; border-color: #1e293b; color: #fff;
    }
    .em-btn-dark:hover { background: #334155; border-color: #334155; }
    .dark .em-btn-dark { background: #e2e8f0; border-color: #e2e8f0; color: #0f172a; }
    .dark .em-btn-dark:hover { background: #fff; border-color: #fff; }

    .em-btn-secondary {
        background: var(--em-surface2); border-color: var(--em-border); color: var(--em-text-sec);
    }
    .em-btn-secondary:hover { border-color: var(--em-muted); }

    /* ── Panel ── */
    .em-panel {
        background: var(--em-surface);
        border: 1px solid var(--em-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--em-shadow);
    }

    /* ── Table ── */
    .em-table { width: 100%; border-collapse: collapse; }
    .em-table thead tr {
        background: var(--em-surface2);
        border-bottom: 1px solid var(--em-border);
    }
    .em-table thead th {
        padding: 11px 20px; text-align: left;
        font-size: 11px; font-weight: 700;
        color: var(--em-muted);
        text-transform: uppercase; letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .em-table tbody tr {
        border-bottom: 1px solid var(--em-border);
        transition: background .12s;
    }
    .em-table tbody tr:last-child { border-bottom: none; }
    .em-table tbody tr:hover { background: var(--em-surface2); }
    .em-table td { padding: 14px 20px; font-size: 14px; color: var(--em-text); white-space: nowrap; }
    .em-table td.muted { color: var(--em-muted); }

    /* ── Avatar ── */
    .em-avatar {
        width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 700; flex-shrink: 0;
        background: #e0e7ff; color: #4338ca;
    }
    .dark .em-avatar { background: rgba(129,140,248,.2); color: #a5b4fc; }

    .em-emp-name  { font-size: 14px; font-weight: 600; color: var(--em-text); }
    .em-emp-email { font-size: 12px; color: var(--em-muted); margin-top: 2px; }

    /* ── Employee ID chip ── */
    .em-id-chip {
        font-family: monospace; font-size: 12px; font-weight: 700;
        color: var(--em-muted);
        background: var(--em-surface2);
        border: 1px solid var(--em-border);
        padding: 2px 8px; border-radius: 6px;
    }

    /* ── Department badge ── */
    .em-dept-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 600; border: 1px solid;
        /* light */
        color: #1d4ed8; border-color: #93c5fd; background: #eff6ff;
    }
    .dark .em-dept-badge {
        color: #60a5fa; border-color: rgba(96,165,250,.3); background: rgba(96,165,250,.1);
    }

    /* ── Action icon buttons ── */
    .em-action-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 7px;
        font-size: 13px; border: 1px solid; cursor: pointer;
        transition: all .15s; text-decoration: none; background: none;
    }
    /* light */
    .em-action-btn.view   { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }
    .em-action-btn.edit   { color: #b45309; border-color: #fde68a; background: #fffbeb; }
    .em-action-btn.delete { color: #dc2626; border-color: #fca5a5; background: #fff1f2; }
    /* light hover */
    .em-action-btn.view:hover   { background: #dbeafe; border-color: #93c5fd; }
    .em-action-btn.edit:hover   { background: #fef3c7; border-color: #fcd34d; }
    .em-action-btn.delete:hover { background: #fee2e2; border-color: #f87171; }
    /* dark */
    .dark .em-action-btn.view   { color: #60a5fa; border-color: rgba(96,165,250,.3);   background: rgba(96,165,250,.1); }
    .dark .em-action-btn.edit   { color: #fbbf24; border-color: rgba(251,191,36,.3);   background: rgba(251,191,36,.1); }
    .dark .em-action-btn.delete { color: #f87171; border-color: rgba(248,113,113,.3);  background: rgba(248,113,113,.1); }
    /* dark hover */
    .dark .em-action-btn.view:hover   { background: rgba(96,165,250,.2);  border-color: rgba(96,165,250,.5); }
    .dark .em-action-btn.edit:hover   { background: rgba(251,191,36,.2);  border-color: rgba(251,191,36,.5); }
    .dark .em-action-btn.delete:hover { background: rgba(248,113,113,.2); border-color: rgba(248,113,113,.5); }

    /* ── Empty state ── */
    .em-empty { padding: 56px 24px; text-align: center; }
    .em-empty i { font-size: 36px; color: var(--em-muted); opacity: .4; display: block; margin-bottom: 14px; }
    .em-empty-title { font-size: 16px; font-weight: 600; color: var(--em-text-sec); margin-bottom: 8px; }
    .em-empty-link  { font-size: 14px; color: #4f46e5; text-decoration: none; }
    .em-empty-link:hover { text-decoration: underline; }
    .dark .em-empty-link { color: #818cf8; }

    /* ── Pagination ── */
    .em-pagination { padding: 16px 24px; border-top: 1px solid var(--em-border); }
</style>
@endpush

@section('content')

    <!-- ── Page Header ── -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="em-page-title">Employees</h1>
                <p class="em-page-sub">Manage and view all employees</p>
            </div>
            <a href="{{ route('admin.employees.create') }}" class="em-btn em-btn-indigo">
                <i class="fas fa-plus"></i> Add Employee
            </a>
        </div>
    </div>

    <!-- ── Error Alert ── -->
    @if ($errors->any())
        <div class="em-alert error">
            <div>
                <div style="font-weight:700; margin-bottom:6px;">Please fix the following errors:</div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button class="em-alert-close" onclick="this.closest('.em-alert').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- ── Success Alert ── -->
    @if (session('success'))
        <div class="em-alert success">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button class="em-alert-close" onclick="this.closest('.em-alert').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- ── Search Bar ── -->
    <div class="em-search-wrap">
        <form method="GET" action="{{ route('admin.employees.index') }}" class="flex gap-2 flex-1">
            <input type="text"
                   name="search"
                   placeholder="Search by name, ID, position, or department…"
                   value="{{ request('search') }}"
                   class="em-input">
            <button type="submit" class="em-btn em-btn-dark">
                <i class="fas fa-search"></i> Search
            </button>
            @if (request('search'))
                <a href="{{ route('admin.employees.index') }}" class="em-btn em-btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- ── Employees Table ── -->
    <div class="em-panel">
        @if ($employees->count() > 0)
            <div class="overflow-x-auto">
                <table class="em-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>ID</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <!-- Name + avatar -->
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="em-avatar">
                                            {{ strtoupper(substr($employee->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="em-emp-name">{{ $employee->user->name }}</div>
                                            <div class="em-emp-email">{{ $employee->user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Employee ID -->
                                <td>
                                    <span class="em-id-chip">{{ $employee->employee_id }}</span>
                                </td>

                                <!-- Position -->
                                <td style="color: var(--em-text-sec);">{{ $employee->position }}</td>

                                <!-- Department -->
                                <td>
                                    <span class="em-dept-badge">{{ $employee->department }}</span>
                                </td>

                                <!-- Phone -->
                                <td class="muted">
                                    {{ $employee->phone ?? '—' }}
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.employees.show', $employee) }}"
                                           class="em-action-btn view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.employees.edit', $employee) }}"
                                           class="em-action-btn edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.employees.destroy', $employee) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="em-action-btn delete" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="em-pagination">
                {{ $employees->links() }}
            </div>

        @else
            <div class="em-empty">
                <i class="fas fa-inbox"></i>
                <div class="em-empty-title">No employees found</div>
                <a href="{{ route('admin.employees.create') }}" class="em-empty-link">
                    Create the first employee
                </a>
            </div>
        @endif
    </div>

@endsection