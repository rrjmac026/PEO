<x-app-layout>

    @push('styles')
        @include('user.concrete-pouring._cp-styles')
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Concrete Pouring Requests
            </h2>
            <a href="{{ route('user.concrete-pouring.create') }}"
               class="inline-flex items-center px-4 py-2 bg-cyan-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> New Request
            </a>
        </div>
    </x-slot>

    <div class="py-12 cp-wrap">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash --}}
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="cp-stat-card">
                    <div class="p-5 flex items-center justify-between">
                        <div>
                            <p class="cp-stat-label">Total</p>
                            <p class="cp-stat-value">{{ $stats['total'] }}</p>
                        </div>
                        <div class="cp-icon-tray cyan"><i class="fas fa-layer-group"></i></div>
                    </div>
                    <div class="cp-stat-foot cyan">
                        <a href="{{ route('user.concrete-pouring.index') }}">All <i class="fas fa-arrow-right text-xs"></i></a>
                    </div>
                </div>
                <div class="cp-stat-card">
                    <div class="p-5 flex items-center justify-between">
                        <div>
                            <p class="cp-stat-label">Pending</p>
                            <p class="cp-stat-value">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="cp-icon-tray blue"><i class="fas fa-hourglass-half"></i></div>
                    </div>
                    <div class="cp-stat-foot blue">
                        <a href="{{ route('user.concrete-pouring.index', ['status' => 'requested']) }}">View <i class="fas fa-arrow-right text-xs"></i></a>
                    </div>
                </div>
                <div class="cp-stat-card">
                    <div class="p-5 flex items-center justify-between">
                        <div>
                            <p class="cp-stat-label">Approved</p>
                            <p class="cp-stat-value">{{ $stats['approved'] }}</p>
                        </div>
                        <div class="cp-icon-tray green"><i class="fas fa-check-circle"></i></div>
                    </div>
                    <div class="cp-stat-foot green">
                        <a href="{{ route('user.concrete-pouring.index', ['status' => 'approved']) }}">View <i class="fas fa-arrow-right text-xs"></i></a>
                    </div>
                </div>
                <div class="cp-stat-card">
                    <div class="p-5 flex items-center justify-between">
                        <div>
                            <p class="cp-stat-label">Disapproved</p>
                            <p class="cp-stat-value">{{ $stats['disapproved'] }}</p>
                        </div>
                        <div class="cp-icon-tray red"><i class="fas fa-times-circle"></i></div>
                    </div>
                    <div class="cp-stat-foot red">
                        <a href="{{ route('user.concrete-pouring.index', ['status' => 'disapproved']) }}">View <i class="fas fa-arrow-right text-xs"></i></a>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="cp-card">
                <div class="cp-card-body">
                    <form method="GET" action="{{ route('user.concrete-pouring.index') }}"
                          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="cp-label">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Project, contractor, location…"
                                   class="cp-input">
                        </div>
                        <div>
                            <label class="cp-label">Status</label>
                            <select name="status" class="cp-select">
                                <option value="">All Statuses</option>
                                <option value="requested"   {{ request('status') === 'requested'   ? 'selected' : '' }}>Pending</option>
                                <option value="approved"    {{ request('status') === 'approved'    ? 'selected' : '' }}>Approved</option>
                                <option value="disapproved" {{ request('status') === 'disapproved' ? 'selected' : '' }}>Disapproved</option>
                            </select>
                        </div>
                        <div>
                            <label class="cp-label">From Date</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="cp-input">
                        </div>
                        <div>
                            <label class="cp-label">To Date</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="cp-input">
                        </div>
                        <div class="lg:col-span-4 flex gap-2 justify-end">
                            <button type="submit"
                                    class="px-5 py-2 bg-cyan-600 text-white text-sm font-semibold rounded-lg hover:bg-cyan-700 transition">
                                <i class="fas fa-search mr-1"></i> Search
                            </button>
                            @if(request()->hasAny(['search','status','date_from','date_to']))
                                <a href="{{ route('user.concrete-pouring.index') }}"
                                   class="px-5 py-2 bg-gray-500 text-white text-sm font-semibold rounded-lg hover:bg-gray-600 transition">
                                    <i class="fas fa-times mr-1"></i> Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="cp-table-container">
                <div class="overflow-x-auto">
                    <table class="cp-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Project</th>
                                <th>Location</th>
                                <th>Part of Structure</th>
                                <th>Pouring Date</th>
                                <th>Status</th>
                                <th>Review Step</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($concretePourings as $cp)
                                <tr>
                                    <td>
                                        <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                            {{ $cp->reference_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <p class="font-medium">{{ $cp->project_name }}</p>
                                        <p class="text-xs" style="color:var(--cp-muted)">{{ $cp->contractor }}</p>
                                    </td>
                                    <td>{{ $cp->location }}</td>
                                    <td>{{ $cp->part_of_structure }}</td>
                                    <td>{{ $cp->pouring_datetime?->format('M d, Y · H:i') ?? '—' }}</td>
                                    <td>
                                        <span class="cp-badge {{ $cp->status }}">
                                            <span class="cp-badge-dot" style="background: currentColor; border-radius: 50%;"></span>
                                            {{ ucfirst($cp->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-xs" style="color:var(--cp-muted)">
                                            {{ $cp->current_step_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex justify-center gap-3">
                                            <a href="{{ route('user.concrete-pouring.show', $cp) }}"
                                               style="color:var(--cp-accent)" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($cp->status === 'requested' && is_null($cp->me_mtqa_user_id))
                                                <a href="{{ route('user.concrete-pouring.edit', $cp) }}"
                                                   style="color:#ea580c" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('user.concrete-pouring.destroy', $cp) }}"
                                                      method="POST" class="inline"
                                                      onsubmit="return confirm('Delete this request?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" style="background:none;border:none;cursor:pointer;color:#dc2626;padding:0" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('user.concrete-pouring.print', $cp) }}" target="_blank"
                                               style="color:var(--cp-muted)" title="Print">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="cp-empty">
                                            <div class="cp-empty-icon">🪣</div>
                                            <p class="cp-empty-msg">No concrete pouring requests found.</p>
                                            <a href="{{ route('user.concrete-pouring.create') }}" class="cp-empty-action">
                                                Create your first one →
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($concretePourings->hasPages())
                    <div class="px-6 py-4" style="background:var(--cp-surface2);border-top:1px solid var(--cp-border)">
                        {{ $concretePourings->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>