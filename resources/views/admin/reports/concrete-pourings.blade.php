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

            .rpt-badge.requested {
                background: rgba(234, 88, 12, 0.15); color: var(--rpt-orange);
            }

            .rpt-badge.approved {
                background: rgba(5, 150, 105, 0.15); color: var(--rpt-green);
            }

            .rpt-badge.disapproved {
                background: rgba(220, 38, 38, 0.15); color: #dc2626;
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
                display: flex; justify-content: space-between; align-items: flex-start;
            }

            .rpt-breakdown-item:last-child {
                border-bottom: none;
            }

            .rpt-breakdown-label {
                font-size: 13px; color: var(--rpt-text);
                flex: 1;
            }

            .rpt-breakdown-value {
                font-weight: 700; color: var(--rpt-text);
                font-size: 14px; text-align: right;
                margin-left: 16px;
            }

            .rpt-bar {
                height: 4px; background: var(--rpt-border); border-radius: 2px;
                margin-top: 6px; overflow: hidden;
            }

            .rpt-bar-fill {
                height: 100%; background: var(--rpt-green); border-radius: 2px;
            }

            .rpt-stats-sub {
                font-size: 11px; color: var(--rpt-muted); margin-top: 3px;
            }
        </style>
        @endpush

        <!-- Header -->
        <div class="rpt-header">
            <h1 class="rpt-title">
                <i class="fas fa-water"></i>
                Concrete Pourings Report
            </h1>
            <div class="rpt-actions">
                <a href="{{ route('admin.reports.concrete-pourings-pdf', request()->query()) }}" class="rpt-btn">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('admin.reports.concrete-pourings-excel', request()->query()) }}" class="rpt-btn">
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
                            <option value="requested" @selected(request('status') === 'requested')>Requested</option>
                            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                            <option value="disapproved" @selected(request('status') === 'disapproved')>Disapproved</option>
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
                    <a href="{{ route('admin.reports.concrete-pourings') }}" class="rpt-btn">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="rpt-summary-grid">
            <div class="rpt-card">
                <div class="rpt-card-label">Total Concrete Pourings</div>
                <div class="rpt-card-value">{{ $summary['total'] ?? 0 }}</div>
                <div class="rpt-card-sub">Period: {{ $range['from']->format('M d, Y') }} - {{ $range['to']->format('M d, Y') }}</div>
            </div>
            <div class="rpt-card">
                <div class="rpt-card-label">Approved</div>
                <div class="rpt-card-value" style="color: var(--rpt-green);">{{ $summary['approved'] ?? 0 }}</div>
                <div class="rpt-card-sub">{{ round(($summary['approved'] ?? 0) / max(1, $summary['total'] ?? 1) * 100) }}% of total</div>
            </div>
            <div class="rpt-card">
                <div class="rpt-card-label">Total Volume</div>
                <div class="rpt-card-value">{{ $summary['total_volume'] ?? 0 }}</div>
                <div class="rpt-card-sub">{{ $summary['total_volume'] ?? 0 }} m³</div>
            </div>
            <div class="rpt-card">
                <div class="rpt-card-label">Avg. Volume/Pour</div>
                <div class="rpt-card-value" style="color: var(--rpt-blue);">{{ $summary['avg_volume'] ?? 0 }}</div>
                <div class="rpt-card-sub">{{ $summary['avg_volume'] ?? 0 }} m³</div>
            </div>
        </div>

        <!-- Status & Contractor Breakdown -->
        <div class="rpt-breakdown-grid">
            <div class="rpt-breakdown-card">
                <div class="rpt-breakdown-header">Status Distribution</div>
                <div class="rpt-breakdown-item">
                    <span class="rpt-breakdown-label">
                        <span class="rpt-badge approved">Approved</span>
                    </span>
                    <span class="rpt-breakdown-value">{{ $summary['approved'] ?? 0 }}</span>
                </div>
                <div class="rpt-breakdown-item">
                    <span class="rpt-breakdown-label">
                        <span class="rpt-badge requested">Requested</span>
                    </span>
                    <span class="rpt-breakdown-value">{{ $summary['pending'] ?? 0 }}</span>
                </div>
                <div class="rpt-breakdown-item">
                    <span class="rpt-breakdown-label">
                        <span class="rpt-badge disapproved">Disapproved</span>
                    </span>
                    <span class="rpt-breakdown-value">{{ $summary['disapproved'] ?? 0 }}</span>
                </div>
            </div>

            @if(is_iterable($contractorBreakdown) && count($contractorBreakdown) > 0)
            <div class="rpt-breakdown-card">
                <div class="rpt-breakdown-header">Top Contractors</div>
                @foreach(is_array($contractorBreakdown) ? array_slice($contractorBreakdown, 0, 8) : $contractorBreakdown->take(8) as $item)
                    <div class="rpt-breakdown-item">
                        <div style="flex: 1;">
                            <span class="rpt-breakdown-label">{{ is_array($item) ? ($item['name'] ?? 'Unknown') : ($item['name'] ?? 'Unknown') }}</span>
                            @php
                                $itemCount = is_array($item) ? ($item['total'] ?? 0) : ($item['total'] ?? 0);
                                $itemVolume = is_array($item) ? ($item['total_volume'] ?? 0) : ($item['total_volume'] ?? 0);
                            @endphp
                            <div class="rpt-stats-sub">{{ $itemCount }} pourings · {{ round($itemVolume, 2) }} m³</div>
                        </div>
                        <span class="rpt-breakdown-value">{{ $itemCount }}</span>
                    </div>
                @endforeach
            </div>
            @endif

            @if(is_iterable($checklistStats) && count($checklistStats) > 0)
            <div class="rpt-breakdown-card">
                <div class="rpt-breakdown-header">Checklist Stats</div>
                @foreach($checklistStats as $key => $stat)
                    <div class="rpt-breakdown-item">
                        <div style="flex: 1;">
                            <span class="rpt-breakdown-label">{{ $stat['label'] }}</span>
                            <div class="rpt-bar">
                                <div class="rpt-bar-fill" style="width: {{ $stat['rate'] }}%;"></div>
                            </div>
                        </div>
                        <span class="rpt-breakdown-value">
                            {{ $stat['checked'] }}/{{ $stat['total'] }}
                            <span style="font-size: 11px; color: var(--rpt-muted);">({{ $stat['rate'] }}%)</span>
                        </span>
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
                        <th>Location</th>
                        <th>Contractor</th>
                        <th>Volume (m³)</th>
                        <th>Status</th>
                        <th>Estimated Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($concretePourings as $cp)
                        <tr>
                            <td><strong>#{{ $cp->id }}</strong></td>
                            <td>{{ $cp->location ?? 'N/A' }}</td>
                            <td>{{ $cp->contractor ?? 'N/A' }}</td>
                            <td>{{ round($cp->estimated_volume, 2) }} m³</td>
                            <td>
                                <span class="rpt-badge @switch($cp->status)
                                    @case('requested') requested @break
                                    @case('approved') approved @break
                                    @case('disapproved') disapproved @break
                                    @default requested
                                @endswitch">
                                    {{ ucfirst($cp->status) }}
                                </span>
                            </td>
                            <td>{{ $cp->estimated_date?->format('M d, Y') ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--rpt-muted); padding: 32px 16px;">
                                No concrete pourings found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
