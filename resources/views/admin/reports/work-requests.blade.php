@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @push('styles')
        <style>
            :root {
                --rpt-surface:     #ffffff;
                --rpt-surface2:    #f8fafc;
                --rpt-border:      #e2e8f0;
                --rpt-text:        #0f172a;
                --rpt-text-sec:    #334155;
                --rpt-muted:       #64748b;
                --rpt-shadow:      0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
                --rpt-shadow-lg:   0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
                --rpt-blue:        #2563eb;
                --rpt-green:       #059669;
                --rpt-orange:      #ea580c;
            }

            .dark {
                --rpt-surface:     #1a1f2e;
                --rpt-surface2:    #1e2335;
                --rpt-border:      #2a3050;
                --rpt-text:        #e8eaf6;
                --rpt-text-sec:    #c5cae9;
                --rpt-muted:       #7c85a8;
                --rpt-shadow:      0 1px 4px rgba(0,0,0,0.35);
                --rpt-shadow-lg:   0 4px 16px rgba(0,0,0,0.45);
                --rpt-blue:        #60a5fa;
                --rpt-green:       #34d399;
                --rpt-orange:      #fb923c;
            }

            .rpt-header {
                display: flex; align-items: center; justify-content: space-between;
                margin-bottom: 32px;
            }

            .rpt-title {
                font-size: 28px; font-weight: 700; color: var(--rpt-text);
                display: flex; align-items: center; gap: 12px;
            }

            .rpt-title i { font-size: 24px; color: var(--rpt-orange); }

            .rpt-actions {
                display: flex; gap: 12px;
            }

            .rpt-btn {
                padding: 8px 16px; border-radius: 8px; border: 1px solid var(--rpt-border);
                background: var(--rpt-surface); color: var(--rpt-text);
                font-size: 13px; font-weight: 600; cursor: pointer;
                transition: all 0.15s; text-decoration: none;
                display: inline-flex; align-items: center; gap: 6px;
            }

            .rpt-btn:hover {
                background: var(--rpt-surface2); box-shadow: var(--rpt-shadow);
            }

            .rpt-btn.primary {
                background: var(--rpt-blue); color: white; border-color: var(--rpt-blue);
            }

            .rpt-btn.primary:hover {
                opacity: 0.9;
            }

            /* Filters Section */
            .rpt-filters {
                background: var(--rpt-surface);
                border: 1px solid var(--rpt-border);
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 24px;
                box-shadow: var(--rpt-shadow);
            }

            .rpt-filters-grid {
                display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 16px; margin-bottom: 16px;
            }

            .rpt-filter-group label {
                display: block; font-size: 12px; font-weight: 600;
                color: var(--rpt-muted); margin-bottom: 6px;
                text-transform: uppercase; letter-spacing: 0.5px;
            }

            .rpt-filter-group input,
            .rpt-filter-group select {
                width: 100%; padding: 8px 12px;
                border: 1px solid var(--rpt-border); border-radius: 6px;
                background: var(--rpt-surface2); color: var(--rpt-text);
                font-size: 13px;
            }

            /* Summary Cards */
            .rpt-summary-grid {
                display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 16px; margin-bottom: 24px;
            }

            .rpt-card {
                background: var(--rpt-surface);
                border: 1px solid var(--rpt-border);
                border-radius: 12px;
                padding: 20px;
                box-shadow: var(--rpt-shadow);
            }

            .rpt-card-label {
                font-size: 12px; font-weight: 600; color: var(--rpt-muted);
                text-transform: uppercase; letter-spacing: 0.5px;
                margin-bottom: 8px;
            }

            .rpt-card-value {
                font-size: 32px; font-weight: 900; color: var(--rpt-text);
                margin-bottom: 8px;
            }

            .rpt-card-sub {
                font-size: 12px; color: var(--rpt-muted);
            }

            /* Data Table */
            .rpt-table-wrapper {
                background: var(--rpt-surface);
                border: 1px solid var(--rpt-border);
                border-radius: 12px;
                overflow: hidden;
                box-shadow: var(--rpt-shadow);
                margin-bottom: 24px;
            }

            .rpt-table {
                width: 100%; border-collapse: collapse;
            }

            .rpt-table thead {
                background: var(--rpt-surface2);
                border-bottom: 2px solid var(--rpt-border);
            }

            .rpt-table th {
                padding: 12px 16px; text-align: left;
                font-size: 12px; font-weight: 600; color: var(--rpt-muted);
                text-transform: uppercase; letter-spacing: 0.5px;
            }

            .rpt-table td {
                padding: 14px 16px; border-bottom: 1px solid var(--rpt-border);
                font-size: 13px; color: var(--rpt-text);
            }

            .rpt-table tbody tr:hover {
                background: var(--rpt-surface2);
            }

            .rpt-badge {
                display: inline-block; padding: 4px 8px;
                border-radius: 4px; font-size: 11px; font-weight: 600;
            }

            .rpt-badge.pending {
                background: rgba(234, 88, 12, 0.15); color: var(--rpt-orange);
            }

            .rpt-badge.approved {
                background: rgba(5, 150, 105, 0.15); color: var(--rpt-green);
            }

            .rpt-badge.rejected {
                background: rgba(220, 38, 38, 0.15); color: #dc2626;
            }

            .rpt-badge.in-review {
                background: rgba(37, 99, 235, 0.15); color: var(--rpt-blue);
            }

            /* Breakdown Cards */
            .rpt-breakdown-grid {
                display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 16px; margin-bottom: 24px;
            }

            .rpt-breakdown-card {
                background: var(--rpt-surface);
                border: 1px solid var(--rpt-border);
                border-radius: 12px;
                overflow: hidden;
                box-shadow: var(--rpt-shadow);
            }

            .rpt-breakdown-header {
                padding: 16px; background: var(--rpt-surface2);
                border-bottom: 1px solid var(--rpt-border);
                font-weight: 600; color: var(--rpt-text);
                font-size: 14px;
            }

            .rpt-breakdown-item {
                padding: 12px 16px; border-bottom: 1px solid var(--rpt-border);
                display: flex; justify-content: space-between; align-items: center;
            }

            .rpt-breakdown-item:last-child {
                border-bottom: none;
            }

            .rpt-breakdown-label {
                font-size: 13px; color: var(--rpt-text);
            }

            .rpt-breakdown-value {
                font-weight: 700; color: var(--rpt-text);
                font-size: 14px;
            }

            .rpt-bar {
                height: 4px; background: var(--rpt-border); border-radius: 2px;
                margin-top: 6px; overflow: hidden;
            }

            .rpt-bar-fill {
                height: 100%; background: var(--rpt-blue); border-radius: 2px;
            }
        </style>
        @endpush

        <!-- Header -->
        <div class="rpt-header">
            <h1 class="rpt-title">
                <i class="fas fa-file-contract"></i>
                Work Requests Report
            </h1>
            <div class="rpt-actions">
                <a href="{{ route('admin.reports.work-requests-pdf', request()->query()) }}" class="rpt-btn">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('admin.reports.work-requests-excel', request()->query()) }}" class="rpt-btn">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="rpt-filters">
            <form method="GET" class="space-y-4">
                <div class="rpt-filters-grid">
                    <div class="rpt-filter-group">
                        <label>Date Range</label>
                        <select name="range">
                            <option value="today" @selected(request('range') === 'today')>Today</option>
                            <option value="week" @selected(request('range') === 'week')>This Week</option>
                            <option value="month" @selected(request('range') === 'month')>This Month</option>
                            <option value="quarter" @selected(request('range') === 'quarter')>This Quarter</option>
                            <option value="year" @selected(request('range') === 'year')>This Year</option>
                            <option value="all" @selected(request('range') === 'all')>All Time</option>
                        </select>
                    </div>

                    <div class="rpt-filter-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                            <option value="in-review" @selected(request('status') === 'in-review')>In Review</option>
                        </select>
                    </div>

                    <div class="rpt-filter-group">
                        <label>Contractor</label>
                        <select name="contractor">
                            <option value="">All Contractors</option>
                            @foreach($contractors as $contractor)
                                <option value="{{ $contractor }}" @selected(request('contractor') === $contractor)>
                                    {{ $contractor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="rpt-btn primary">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.reports.work-requests') }}" class="rpt-btn">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="rpt-summary-grid">
            <div class="rpt-card">
                <div class="rpt-card-label">Total Work Requests</div>
                <div class="rpt-card-value">{{ $summary['total'] ?? 0 }}</div>
                <div class="rpt-card-sub">Period: {{ $range['from']->format('M d, Y') }} - {{ $range['to']->format('M d, Y') }}</div>
            </div>
            <div class="rpt-card">
                <div class="rpt-card-label">Approved</div>
                <div class="rpt-card-value" style="color: var(--rpt-green);">{{ $summary['approved'] ?? 0 }}</div>
                <div class="rpt-card-sub">{{ round(($summary['approved'] ?? 0) / max(1, $summary['total'] ?? 1) * 100) }}% of total</div>
            </div>
            <div class="rpt-card">
                <div class="rpt-card-label">In Review</div>
                <div class="rpt-card-value" style="color: var(--rpt-blue);">{{ $summary['in_review'] ?? 0 }}</div>
                <div class="rpt-card-sub">{{ round(($summary['in_review'] ?? 0) / max(1, $summary['total'] ?? 1) * 100) }}% of total</div>
            </div>
            <div class="rpt-card">
                <div class="rpt-card-label">Rejected</div>
                <div class="rpt-card-value" style="color: #dc2626;">{{ $summary['rejected'] ?? 0 }}</div>
                <div class="rpt-card-sub">{{ round(($summary['rejected'] ?? 0) / max(1, $summary['total'] ?? 1) * 100) }}% of total</div>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="rpt-breakdown-grid">
            <div class="rpt-breakdown-card">
                <div class="rpt-breakdown-header">Status Breakdown</div>
                @forelse($statusBreakdown as $status => $count)
                    <div class="rpt-breakdown-item">
                        <span class="rpt-breakdown-label">
                            <span class="rpt-badge @switch($status)
                                @case('pending') pending @break
                                @case('approved') approved @break
                                @case('rejected') rejected @break
                                @default in-review
                            @endswitch">
                                {{ ucfirst(str_replace('-', ' ', $status)) }}
                            </span>
                        </span>
                        <span class="rpt-breakdown-value">{{ $count }}</span>
                    </div>
                @empty
                    <div class="rpt-breakdown-item">
                        <span class="rpt-breakdown-label">No data available</span>
                    </div>
                @endforelse
            </div>

            @if(is_iterable($contractorBreakdown) && count($contractorBreakdown) > 0)
            <div class="rpt-breakdown-card">
                <div class="rpt-breakdown-header">Top Contractors</div>
                @foreach(is_array($contractorBreakdown) ? array_slice($contractorBreakdown, 0, 10) : $contractorBreakdown->take(10) as $item)
                    <div class="rpt-breakdown-item">
                        <span class="rpt-breakdown-label">{{ is_array($item) ? ($item['contractor_name'] ?? 'Unknown') : ($item->contractor_name ?? 'Unknown') }}</span>
                        <span class="rpt-breakdown-value">{{ is_array($item) ? ($item['count'] ?? 0) : $item->count }}</span>
                    </div>
                @endforeach
            </div>
            @endif

            @if(is_iterable($reviewerBreakdown) && count($reviewerBreakdown) > 0)
            <div class="rpt-breakdown-card">
                <div class="rpt-breakdown-header">Reviewers Activity</div>
                @foreach(is_array($reviewerBreakdown) ? array_slice($reviewerBreakdown, 0, 10) : $reviewerBreakdown->take(10) as $reviewer)
                    <div class="rpt-breakdown-item">
                        <span class="rpt-breakdown-label">{{ is_array($reviewer) ? ($reviewer['name'] ?? 'Unknown') : ($reviewer->name ?? 'Unknown') }}</span>
                        <span class="rpt-breakdown-value">{{ is_array($reviewer) ? ($reviewer['count'] ?? 0) : $reviewer->count }}</span>
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Data Table -->
        <div class="rpt-table-wrapper">
            <table class="rpt-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Project</th>
                        <th>Contractor</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workRequests as $wr)
                        <tr>
                            <td><strong>#{{ $wr->id }}</strong></td>
                            <td>{{ Str::limit($wr->project_name ?? 'N/A', 30) }}</td>
                            <td>{{ $wr->contractor_name ?? 'N/A' }}</td>
                            <td>
                                <span class="rpt-badge @switch($wr->status)
                                    @case('pending') pending @break
                                    @case('approved') approved @break
                                    @case('rejected') rejected @break
                                    @default in-review
                                @endswitch">
                                    {{ ucfirst(str_replace('-', ' ', $wr->status)) }}
                                </span>
                            </td>
                            <td>{{ $wr->assignedProvincialEngineer?->name ?? 'Unassigned' }}</td>
                            <td>{{ $wr->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.work-requests.show', $wr) }}" class="rpt-btn" style="font-size: 11px;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--rpt-muted); padding: 32px 16px;">
                                No work requests found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
