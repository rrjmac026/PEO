<x-app-layout>
    @push('styles')
    <style>
        /* ══════════════════════════════════════════
           LIGHT MODE TOKENS (primary / default)
        ══════════════════════════════════════════ */
        :root {
            --wr-surface:   #ffffff;
            --wr-surface2:  #f8fafc;
            --wr-border:    #e2e8f0;
            --wr-text:      #0f172a;
            --wr-text-sec:  #334155;
            --wr-muted:     #64748b;
            --wr-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            --wr-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
        }

        /* ══════════════════════════════════════════
           DARK MODE TOKENS (override on .dark)
        ══════════════════════════════════════════ */
        .dark {
            --wr-surface:   #1a1f2e;
            --wr-surface2:  #1e2335;
            --wr-border:    #2a3050;
            --wr-text:      #e8eaf6;
            --wr-text-sec:  #c5cae9;
            --wr-muted:     #7c85a8;
            --wr-shadow:    0 1px 4px rgba(0,0,0,0.35);
            --wr-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
        }

        .wri-wrap { font-family: 'Inter', sans-serif; }

        /* ── Card containers ── */
        .wri-card {
            background: var(--wr-surface);
            border: 1px solid var(--wr-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--wr-shadow);
            transition: box-shadow 0.25s ease;
        }
        .wri-card:hover { box-shadow: var(--wr-shadow-lg); }

        /* ── Table styling ── */
        .wri-table-container {
            background: var(--wr-surface);
            border: 1px solid var(--wr-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--wr-shadow);
        }

        .wri-table {
            width: 100%;
            border-collapse: collapse;
        }

        .wri-table thead {
            background: var(--wr-surface2);
        }

        .wri-table th {
            padding: 12px 24px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--wr-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--wr-border);
        }

        .wri-table td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--wr-border);
            color: var(--wr-text);
            font-size: 14px;
        }

        .wri-table tbody tr {
            transition: background 0.15s;
        }

        .wri-table tbody tr:hover {
            background: var(--wr-surface2);
        }

        .wri-table tr:last-child td {
            border-bottom: none;
        }

        .wri-status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .wri-status-badge.draft {
            background: #f1f5f9;
            color: #475569;
        }
        .dark .wri-status-badge.draft {
            background: rgba(100,116,139,0.12);
            color: #cbd5e1;
        }

        .wri-status-badge.submitted {
            background: #dbeafe;
            color: #1e40af;
        }
        .dark .wri-status-badge.submitted {
            background: rgba(37,99,235,0.15);
            color: #60a5fa;
        }

        .wri-status-badge.inspected {
            background: #f3e8ff;
            color: #6b21a8;
        }
        .dark .wri-status-badge.inspected {
            background: rgba(124,58,237,0.15);
            color: #c084fc;
        }

        .wri-status-badge.reviewed {
            background: #e0e7ff;
            color: #3730a3;
        }
        .dark .wri-status-badge.reviewed {
            background: rgba(129,140,248,0.15);
            color: #818cf8;
        }

        .wri-status-badge.approved,
        .wri-status-badge.accepted {
            background: #dcfce7;
            color: #166534;
        }
        .dark .wri-status-badge.approved,
        .dark .wri-status-badge.accepted {
            background: rgba(5,150,105,0.15);
            color: #34d399;
        }

        .wri-status-badge.rejected {
            background: #fee2e2;
            color: #991b1b;
        }
        .dark .wri-status-badge.rejected {
            background: rgba(220,38,38,0.15);
            color: #f87171;
        }

        .wri-link-action {
            color: #2563eb;
            text-decoration: none;
            transition: opacity 0.15s;
        }
        .wri-link-action:hover { opacity: 0.7; }
        .dark .wri-link-action { color: #60a5fa; }

        .wri-link-danger {
            color: #dc2626;
            text-decoration: none;
            transition: opacity 0.15s;
        }
        .wri-link-danger:hover { opacity: 0.7; }
        .dark .wri-link-danger { color: #f87171; }
    </style>
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Work Requests') }}
            </h2>
            <a href="{{ route('user.work-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                {{ __('New Request') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 wri-wrap">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filter Section -->
            <div class="wri-card mb-6">
                <div class="p-6" style="color: var(--wr-text);">
                    <form method="GET" action="{{ route('user.work-requests.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium mb-2" style="color: var(--wr-text);">
                                {{ __('Search') }}
                            </label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Project name, location, contractor..." 
                                style="background: var(--wr-surface); border: 1px solid var(--wr-border); color: var(--wr-text);" class="block w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium mb-2" style="color: var(--wr-text);">
                                {{ __('Status') }}
                            </label>
                            <select name="status" id="status" style="background: var(--wr-surface); border: 1px solid var(--wr-border); color: var(--wr-text);" class="block w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('All Statuses') }}</option>
                                @foreach(\App\Models\WorkRequest::getStatuses() as $status)
                                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-search mr-2"></i>
                                {{ __('Search') }}
                            </button>
                            @if(request('search') || request('status'))
                                <a href="{{ route('user.work-requests.index') }}" class="flex-1 px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 text-center">
                                    <i class="fas fa-times mr-2"></i>
                                    {{ __('Clear') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Work Requests Table -->
            <div class="wri-table-container">
                <div class="overflow-x-auto">
                    <table class="wri-table text-sm text-left">
                        <thead style="background: var(--wr-surface2);">
                            <tr>
                                <th class="px-6 py-3 font-semibold">{{ __('Project Name') }}</th>
                                <th class="px-6 py-3 font-semibold">{{ __('Location') }}</th>
                                <th class="px-6 py-3 font-semibold">{{ __('Contractor') }}</th>
                                <th class="px-6 py-3 font-semibold">{{ __('Status') }}</th>
                                <th class="px-6 py-3 font-semibold">{{ __('Date') }}</th>
                                <th class="px-6 py-3 font-semibold text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody style="border: none;">
                            @forelse($workRequests as $workRequest)
                                <tr>
                                    <td class="px-6 py-4 font-medium">
                                        {{ $workRequest->name_of_project }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $workRequest->project_location }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $workRequest->contractor_name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="wri-status-badge {{ $workRequest->status }}">
                                            {{ ucfirst($workRequest->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $workRequest->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-3">
                                            <a href="{{ route('user.work-requests.show', $workRequest) }}" class="wri-link-action" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($workRequest->canEdit())
                                                <a href="{{ route('user.work-requests.edit', $workRequest) }}" style="color: #ea580c; text-decoration: none; transition: opacity 0.15s;" class="hover:opacity-70" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('user.work-requests.destroy', $workRequest) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="wri-link-danger" style="background: none; border: none; cursor: pointer; padding: 0;" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center" style="color: var(--wr-muted);">
                                        {{ __('No work requests found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($workRequests->hasPages())
                    <div class="px-6 py-4" style="background: var(--wr-surface2); border-top: 1px solid var(--wr-border);">
                        {{ $workRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
