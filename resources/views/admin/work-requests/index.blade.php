@extends('layouts.app')

@section('title', 'Work Requests')

@push('styles')
@endpush

@section('content')

@include('admin.work-requests.partials._index-styles')
    <!-- ── Page Header ── -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="wr-page-title">Work Requests</h1>
                <p class="wr-page-sub">Manage and track all work requests</p>
            </div>
        </div>
    </div>

    <!-- ── Success Alert ── -->
    @if (session('success'))
        <div class="wr-alert success">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button class="wr-alert-close" onclick="this.closest('.wr-alert').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- ── Error Alert ── -->
    @if (session('error'))
        <div class="wr-alert error">
            <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
            <button class="wr-alert-close" onclick="this.closest('.wr-alert').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- ── Search / Filter Bar ── -->
    <div class="wr-filter-panel">
        <form method="GET" action="{{ route('admin.work-requests.index') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search by project name, location, or requester…"
                   class="wr-input">

            <select name="status" class="wr-select">
                <option value="">All Statuses</option>
                @foreach(\App\Models\WorkRequest::getStatuses() as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="wr-btn wr-btn-dark">
                <i class="fas fa-search"></i> Filter
            </button>

            @if (request('search') || request('status'))
                <a href="{{ route('admin.work-requests.index') }}" class="wr-btn wr-btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- ── Work Requests Table ── -->
    <div class="wr-panel">
        @if ($workRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="wr-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Project Name</th>
                            <th>Location</th>
                            <th>Submitted By</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th class="right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($workRequests as $request)
                            <tr>
                                <!-- ID -->
                                <td>
                                    <span class="wr-id-chip">#{{ $request->id }}</span>
                                </td>

                                <!-- Project Name -->
                                <td style="color: var(--wr-text-sec); font-weight: 600;">
                                    {{ Str::limit($request->name_of_project, 30) }}
                                </td>

                                <!-- Location -->
                                <td class="muted">
                                    {{ Str::limit($request->project_location, 25) }}
                                </td>

                                <!-- Requested By -->
                                <td class="muted">
                                    {{ $request->contractor_name ?? '—' }}
                                </td>

                                <!-- Start Date -->
                                <td class="muted">
                                    {{ $request->requested_work_start_date ? $request->requested_work_start_date->format('M d, Y') : '—' }}
                                </td>

                                <!-- Status -->
                                <td>
                                    @php
                                        $badgeClass = match($request->status) {
                                            'draft'     => 'wr-badge-draft',
                                            'submitted' => 'wr-badge-submitted',
                                            'inspected' => 'wr-badge-inspected',
                                            'reviewed'  => 'wr-badge-reviewed',
                                            'approved'  => 'wr-badge-approved',
                                            'accepted'  => 'wr-badge-accepted',
                                            'rejected'  => 'wr-badge-rejected',
                                            default     => 'wr-badge-draft',
                                        };
                                    @endphp
                                    <span class="wr-badge {{ $badgeClass }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- View --}}
                                        <a href="{{ route('admin.work-requests.show', $request) }}"
                                           class="wr-action-btn view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Edit --}}
                                        @if ($request->canEdit())
                                            <a href="{{ route('admin.work-requests.edit', $request) }}"
                                               class="wr-action-btn edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        {{-- Print --}}
                                        <a href="{{ route('admin.work-requests.print', $request) }}"
                                           class="wr-action-btn print" title="Print PDF" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>

                                        {{-- Download --}}
                                        <a href="{{ route('admin.work-requests.download', $request) }}"
                                           class="wr-action-btn download" title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        {{-- Delete --}}
                                        @if ($request->status === 'draft')
                                            <form action="{{ route('admin.work-requests.destroy', $request) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this work request?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="wr-action-btn delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($workRequests->hasPages())
                <div class="wr-pagination">
                    {{ $workRequests->links() }}
                </div>
            @endif

        @else
            <div class="wr-empty">
                <i class="fas fa-inbox"></i>
                <div class="wr-empty-title">No work requests found</div>
                <div class="wr-empty-sub">Get started by creating your first work request</div>
                <a href="{{ route('admin.work-requests.create') }}" class="wr-btn wr-btn-orange">
                    <i class="fas fa-plus-circle"></i> Create Work Request
                </a>
            </div>
        @endif
    </div>

    <!-- ── Statistics Cards ── -->
    @php
        $stats = [
            ['status' => 'draft',     'label' => 'Draft',     'icon' => 'fa-file',         'color' => 'gray'],
            ['status' => 'submitted', 'label' => 'Submitted', 'icon' => 'fa-paper-plane',  'color' => 'blue'],
            ['status' => 'approved',  'label' => 'Approved',  'icon' => 'fa-check-circle', 'color' => 'green'],
            ['status' => 'rejected',  'label' => 'Rejected',  'icon' => 'fa-times-circle', 'color' => 'red'],
        ];
    @endphp

    <div class="wr-stats-grid">
        @foreach ($stats as $stat)
            <div class="wr-stat-card">
                <div class="wr-stat-icon {{ $stat['color'] }}">
                    <i class="fas {{ $stat['icon'] }}"></i>
                </div>
                <div>
                    <div class="wr-stat-label">{{ $stat['label'] }}</div>
                    <div class="wr-stat-value">
                        {{ \App\Models\WorkRequest::where('status', $stat['status'])->count() }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection