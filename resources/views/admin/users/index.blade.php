<x-app-layout>
    <!-- ── Page Header ── -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="um-page-title">User Management</h1>
                <p class="um-page-sub">Manage and view all users</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="um-btn um-btn-indigo">
                <i class="fas fa-plus"></i> Add New User
            </a>
        </div>
    </div>

    @push('styles')
    <style>
        /* ══════════════════════════════════════════
           LIGHT MODE TOKENS (primary / default)
        ══════════════════════════════════════════ */
        :root {
            --um-surface:   #ffffff;
            --um-surface2:  #f8fafc;
            --um-border:    #e2e8f0;
            --um-text:      #0f172a;
            --um-text-sec:  #334155;
            --um-muted:     #64748b;
            --um-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            --um-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
        }

        /* ══════════════════════════════════════════
           DARK MODE TOKENS (override on .dark)
        ══════════════════════════════════════════ */
        .dark {
            --um-surface:   #1a1f2e;
            --um-surface2:  #1e2335;
            --um-border:    #2a3050;
            --um-text:      #e8eaf6;
            --um-text-sec:  #c5cae9;
            --um-muted:     #7c85a8;
            --um-shadow:    0 1px 4px rgba(0,0,0,0.35);
            --um-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
        }

        /* ── Page heading ── */
        .um-page-title { font-size: 28px; font-weight: 800; color: var(--um-text); line-height: 1.2; }
        .um-page-sub   { font-size: 14px; color: var(--um-muted); margin-top: 4px; }

        /* ── Alert banners ── */
        .um-alert {
            display: flex; align-items: flex-start; justify-content: space-between;
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .um-alert.success {
            background: #f0fdf4; border-color: #86efac; color: #166534;
        }
        .um-alert.error {
            background: #fff1f2; border-color: #fca5a5; color: #991b1b;
        }
        .dark .um-alert.success {
            background: rgba(5,150,105,.12); border-color: rgba(52,211,153,.3); color: #6ee7b7;
        }
        .dark .um-alert.error {
            background: rgba(220,38,38,.10); border-color: rgba(248,113,113,.3); color: #fca5a5;
        }
        .um-alert-close {
            background: none; border: none; cursor: pointer;
            font-size: 14px; opacity: .6; line-height: 1;
            color: inherit; padding: 0; margin-left: 12px; flex-shrink: 0;
        }
        .um-alert-close:hover { opacity: 1; }

        /* ── Panel (card wrapper) ── */
        .um-panel {
            background: var(--um-surface);
            border: 1px solid var(--um-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--um-shadow);
        }
        .um-panel-body { padding: 20px 24px; }

        /* ── Filter bar inputs ── */
        .um-input {
            width: 100%;
            background: var(--um-surface2);
            border: 1px solid var(--um-border);
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 14px;
            color: var(--um-text);
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .um-input::placeholder { color: var(--um-muted); }
        .um-input:focus {
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234,88,12,.12);
        }
        .dark .um-input:focus {
            border-color: #fb923c;
            box-shadow: 0 0 0 3px rgba(251,146,60,.12);
        }

        /* ── Buttons ── */
        .um-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px;
            font-size: 13px; font-weight: 600;
            border: 1px solid; cursor: pointer;
            transition: all .15s; text-decoration: none;
            white-space: nowrap;
        }
        .um-btn-primary {
            background: #1e293b; border-color: #1e293b; color: #fff;
        }
        .um-btn-primary:hover { background: #334155; border-color: #334155; }
        .dark .um-btn-primary { background: #e2e8f0; border-color: #e2e8f0; color: #0f172a; }
        .dark .um-btn-primary:hover { background: #fff; border-color: #fff; }

        .um-btn-secondary {
            background: var(--um-surface2); border-color: var(--um-border);
            color: var(--um-text-sec);
        }
        .um-btn-secondary:hover { border-color: var(--um-muted); }

        .um-btn-indigo {
            background: #4f46e5; border-color: #4f46e5; color: #fff;
        }
        .um-btn-indigo:hover { background: #4338ca; border-color: #4338ca; }
        .dark .um-btn-indigo { background: #6366f1; border-color: #6366f1; }
        .dark .um-btn-indigo:hover { background: #818cf8; border-color: #818cf8; }

        /* ── Table ── */
        .um-table { width: 100%; border-collapse: collapse; }
        .um-table thead tr {
            background: var(--um-surface2);
            border-bottom: 1px solid var(--um-border);
        }
        .um-table thead th {
            padding: 11px 20px;
            text-align: left;
            font-size: 11px; font-weight: 700;
            color: var(--um-muted);
            text-transform: uppercase; letter-spacing: 0.5px;
            white-space: nowrap;
        }
        .um-table thead th.right { text-align: right; }
        .um-table tbody tr {
            border-bottom: 1px solid var(--um-border);
            transition: background .12s;
        }
        .um-table tbody tr:last-child { border-bottom: none; }
        .um-table tbody tr:hover { background: var(--um-surface2); }
        .um-table td {
            padding: 14px 20px;
            font-size: 14px;
            color: var(--um-text);
            white-space: nowrap;
        }
        .um-table td.muted { color: var(--um-muted); }
        .um-table td.right { text-align: right; }

        /* ── ID chip ── */
        .um-id-chip {
            font-size: 12px; font-weight: 700;
            color: var(--um-muted);
            background: var(--um-surface2);
            border: 1px solid var(--um-border);
            padding: 2px 8px; border-radius: 6px;
            font-family: monospace;
        }

        /* ── Role badges ── */
        .um-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
            border: 1px solid;
        }
        .um-badge-dot { width: 6px; height: 6px; border-radius: 50%; }

        /* light */
        .um-badge.admin  { color: #b91c1c; border-color: #fca5a5; background: #fff1f2; }
        .um-badge.user   { color: #1d4ed8; border-color: #93c5fd; background: #eff6ff; }
        .um-badge.other  { color: #475569; border-color: #cbd5e1; background: #f1f5f9; }
        /* dark */
        .dark .um-badge.admin { color: #f87171; border-color: rgba(248,113,113,.35); background: rgba(248,113,113,.1); }
        .dark .um-badge.user  { color: #60a5fa; border-color: rgba(96,165,250,.35);  background: rgba(96,165,250,.1); }
        .dark .um-badge.other { color: #94a3b8; border-color: rgba(148,163,184,.3);  background: rgba(148,163,184,.08); }

        /* dot colors */
        .um-badge.admin .um-badge-dot  { background: #ef4444; }
        .um-badge.user  .um-badge-dot  { background: #3b82f6; }
        .um-badge.other .um-badge-dot  { background: #94a3b8; }
        .dark .um-badge.admin .um-badge-dot { background: #f87171; }
        .dark .um-badge.user  .um-badge-dot { background: #60a5fa; }

        /* ── Action icon buttons ── */
        .um-action-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 30px; height: 30px; border-radius: 7px;
            font-size: 13px; border: 1px solid; cursor: pointer;
            transition: all .15s; text-decoration: none; background: none;
        }
        .um-action-btn.view   { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }
        .um-action-btn.edit   { color: #ea580c; border-color: #fed7aa; background: #fff7ed; }
        .um-action-btn.delete { color: #dc2626; border-color: #fca5a5; background: #fff1f2; }

        .um-action-btn.view:hover   { background: #dbeafe; border-color: #93c5fd; }
        .um-action-btn.edit:hover   { background: #ffedd5; border-color: #fdba74; }
        .um-action-btn.delete:hover { background: #fee2e2; border-color: #f87171; }

        .dark .um-action-btn.view   { color: #60a5fa; border-color: rgba(96,165,250,.3);  background: rgba(96,165,250,.1); }
        .dark .um-action-btn.edit   { color: #fb923c; border-color: rgba(251,146,60,.3);  background: rgba(251,146,60,.1); }
        .dark .um-action-btn.delete { color: #f87171; border-color: rgba(248,113,113,.3); background: rgba(248,113,113,.1); }

        .dark .um-action-btn.view:hover   { background: rgba(96,165,250,.2);  border-color: rgba(96,165,250,.5); }
        .dark .um-action-btn.edit:hover   { background: rgba(251,146,60,.2);  border-color: rgba(251,146,60,.5); }
        .dark .um-action-btn.delete:hover { background: rgba(248,113,113,.2); border-color: rgba(248,113,113,.5); }

        /* ── Empty state ── */
        .um-empty {
            padding: 48px 24px; text-align: center;
            color: var(--um-muted); font-size: 14px;
        }
        .um-empty i { font-size: 32px; margin-bottom: 12px; opacity: .4; display: block; }

        /* ── Pagination wrapper ── */
        .um-pagination { margin-top: 20px; }
    </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ── Success Message ── --}}
            @if(session('success'))
                <div class="um-alert success" role="alert">
                    <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                    <button class="um-alert-close" onclick="this.closest('.um-alert').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- ── Error Message ── --}}
            @if(session('error'))
                <div class="um-alert error" role="alert">
                    <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
                    <button class="um-alert-close" onclick="this.closest('.um-alert').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- ── Filters ── --}}
            <div class="um-panel mb-5">
                <div class="um-panel-body">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3 items-end">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search by name or email…"
                                   class="um-input">
                        </div>
                        <div class="min-w-[150px]">
                            <select name="role" class="um-input">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user"  {{ request('role') == 'user'  ? 'selected' : '' }}>User</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            @if(request('search') || request('role'))
                                <a href="{{ route('admin.users.index') }}" class="um-btn um-btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Users Table ── --}}
            <div class="um-panel">
                <div class="overflow-x-auto">
                    <table class="um-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th class="right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <span class="um-id-chip">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td style="font-weight: 600;">{{ $user->name }}</td>
                                    <td class="muted">{{ $user->email }}</td>
                                    <td>
                                        @php
                                            $roleClass = match($user->role) {
                                                'admin' => 'admin',
                                                'user'  => 'user',
                                                default => 'other',
                                            };
                                        @endphp
                                        <span class="um-badge {{ $roleClass }}">
                                            <span class="um-badge-dot"></span>
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="muted">{{ $user->created_at->format('M d, Y') }}</td>
                                    <td class="right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.users.show', $user) }}"
                                               class="um-action-btn view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                               class="um-action-btn edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="um-action-btn delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 0;">
                                        <div class="um-empty">
                                            <i class="fas fa-users-slash"></i>
                                            No users found.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── Pagination ── --}}
            <div class="um-pagination">
                {{ $users->links() }}
            </div>

        </div>
    </div>
</x-app-layout>