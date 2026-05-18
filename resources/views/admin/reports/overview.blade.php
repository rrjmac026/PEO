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
                --rpt-purple:      #7c3aed;
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
                --rpt-purple:      #c084fc;
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

            .rpt-btn-link {
                padding: 6px 12px; font-size: 12px;
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

            .rpt-filter-group select {
                width: 100%; padding: 8px 12px;
                border: 1px solid var(--rpt-border); border-radius: 6px;
                background: var(--rpt-surface2); color: var(--rpt-text);
                font-size: 13px;
            }

            /* Module Sections */
            .rpt-section {
                margin-bottom: 40px;
            }

            .rpt-section-header {
                display: flex; align-items: center; justify-content: space-between;
                margin-bottom: 20px;
            }

            .rpt-section-title {
                font-size: 18px; font-weight: 700; color: var(--rpt-text);
                display: flex; align-items: center; gap: 8px;
            }

            .rpt-section-title i {
                font-size: 16px;
            }

            .rpt-section-link {
                font-size: 12px; font-weight: 600; color: var(--rpt-blue);
                text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
            }

            .rpt-section-link:hover {
                opacity: 0.7;
            }

            /* Summary Cards */
            .rpt-summary-grid {
                display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 14px; margin-bottom: 24px;
            }

            .rpt-card {
                background: var(--rpt-surface);
                border: 1px solid var(--rpt-border);
                border-radius: 12px;
                padding: 18px;
                box-shadow: var(--rpt-shadow);
                transition: box-shadow 0.15s ease;
            }

            .rpt-card:hover {
                box-shadow: var(--rpt-shadow-lg);
            }

            .rpt-card-label {
                font-size: 11px; font-weight: 600; color: var(--rpt-muted);
                text-transform: uppercase; letter-spacing: 0.5px;
                margin-bottom: 6px;
            }

            .rpt-card-value {
                font-size: 28px; font-weight: 900; color: var(--rpt-text);
                margin-bottom: 6px;
            }

            .rpt-card-sub {
                font-size: 11px; color: var(--rpt-muted);
            }

            /* Breakdown Grid */
            .rpt-breakdown-mini {
                background: var(--rpt-surface);
                border: 1px solid var(--rpt-border);
                border-radius: 12px;
                overflow: hidden;
                box-shadow: var(--rpt-shadow);
                margin-bottom: 24px;
            }

            .rpt-breakdown-header {
                padding: 14px 16px; background: var(--rpt-surface2);
                border-bottom: 1px solid var(--rpt-border);
                font-weight: 600; color: var(--rpt-text);
                font-size: 13px;
            }

            .rpt-breakdown-item {
                padding: 10px 16px; border-bottom: 1px solid var(--rpt-border);
                display: flex; justify-content: space-between; align-items: center;
                font-size: 12px;
            }

            .rpt-breakdown-item:last-child {
                border-bottom: none;
            }

            .rpt-breakdown-label {
                color: var(--rpt-text);
            }

            .rpt-breakdown-value {
                font-weight: 700; color: var(--rpt-text);
            }

            /* Timeline / Top performers */
            .rpt-list-item {
                padding: 12px 16px; border-bottom: 1px solid var(--rpt-border);
                display: flex; justify-content: space-between; align-items: center;
                font-size: 12px;
            }

            .rpt-list-item:last-child {
                border-bottom: none;
            }

            .rpt-list-label {
                color: var(--rpt-text);
                flex: 1;
            }

            .rpt-list-value {
                font-weight: 700; color: var(--rpt-text);
                margin-left: 12px;
            }

            /* Badge styles */
            .rpt-badge {
                display: inline-block; padding: 3px 6px;
                border-radius: 3px; font-size: 10px; font-weight: 600;
            }

            .rpt-badge.blue {
                background: rgba(37, 99, 235, 0.15); color: var(--rpt-blue);
            }

            .rpt-badge.green {
                background: rgba(5, 150, 105, 0.15); color: var(--rpt-green);
            }

            .rpt-badge.orange {
                background: rgba(234, 88, 12, 0.15); color: var(--rpt-orange);
            }

            .rpt-badge.purple {
                background: rgba(124, 58, 237, 0.15); color: var(--rpt-purple);
            }

            /* Stat overview grid */
            .rpt-stat-box {
                background: var(--rpt-surface);
                border: 1px solid var(--rpt-border);
                border-radius: 10px;
                padding: 16px;
                text-align: center;
                box-shadow: var(--rpt-shadow);
            }

            .rpt-stat-box-value {
                font-size: 24px; font-weight: 900; color: var(--rpt-text);
                margin-bottom: 4px;
            }

            .rpt-stat-box-label {
                font-size: 11px; font-weight: 600; color: var(--rpt-muted);
                text-transform: uppercase; letter-spacing: 0.5px;
            }
        </style>
        @endpush

        <!-- Header -->
        <div class="rpt-header">
            <h1 class="rpt-title">
                <i class="fas fa-chart-line"></i>
                System Overview Report
            </h1>
            <div class="rpt-actions">
                <a href="{{ route('admin.reports.overview-pdf', request()->query()) }}" class="rpt-btn">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('admin.reports.overview-excel', request()->query()) }}" class="rpt-btn">
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
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="rpt-btn primary">
                        <i class="fas fa-search"></i> Apply Filter
                    </button>
                    <a href="{{ route('admin.reports.overview') }}" class="rpt-btn">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Period Info -->
        <div style="margin-bottom: 24px; padding: 12px 16px; background: var(--rpt-surface2); border-left: 4px solid var(--rpt-orange); border-radius: 6px; color: var(--rpt-text); font-size: 13px;">
            <strong>Report Period:</strong> {{ $range['from']->format('F d, Y') }} — {{ $range['to']->format('F d, Y') }}
        </div>

        <!-- WORK REQUESTS SECTION -->
        <div class="rpt-section">
            <div class="rpt-section-header">
                <h2 class="rpt-section-title">
                    <i class="fas fa-file-contract" style="color: var(--rpt-blue);"></i>
                    Work Requests
                </h2>
                <a href="{{ route('admin.reports.work-requests') }}" class="rpt-section-link">
                    Full Report <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="rpt-summary-grid">
                <div class="rpt-card">
                    <div class="rpt-card-label">Total</div>
                    <div class="rpt-card-value">{{ $workRequestStats['total'] ?? 0 }}</div>
                    <div class="rpt-card-sub">All requests</div>
                </div>
                <div class="rpt-card">
                    <div class="rpt-card-label">Approved</div>
                    <div class="rpt-card-value" style="color: var(--rpt-green);">{{ $workRequestStats['approved'] ?? 0 }}</div>
                    <div class="rpt-card-sub">{{ round(($workRequestStats['approved'] ?? 0) / max(1, $workRequestStats['total'] ?? 1) * 100) }}%</div>
                </div>
                <div class="rpt-card">
                    <div class="rpt-card-label">In Review</div>
                    <div class="rpt-card-value" style="color: var(--rpt-orange);">{{ $workRequestStats['in_review'] ?? 0 }}</div>
                    <div class="rpt-card-sub">{{ round(($workRequestStats['in_review'] ?? 0) / max(1, $workRequestStats['total'] ?? 1) * 100) }}%</div>
                </div>
                <div class="rpt-card">
                    <div class="rpt-card-label">Rejected</div>
                    <div class="rpt-card-value" style="color: #dc2626;">{{ $workRequestStats['rejected'] ?? 0 }}</div>
                    <div class="rpt-card-sub">{{ round(($workRequestStats['rejected'] ?? 0) / max(1, $workRequestStats['total'] ?? 1) * 100) }}%</div>
                </div>
            </div>
        </div>

        <!-- CONCRETE POURINGS SECTION -->
        <div class="rpt-section">
            <div class="rpt-section-header">
                <h2 class="rpt-section-title">
                    <i class="fas fa-water" style="color: var(--rpt-green);"></i>
                    Concrete Pourings
                </h2>
                <a href="{{ route('admin.reports.concrete-pourings') }}" class="rpt-section-link">
                    Full Report <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="rpt-summary-grid">
                <div class="rpt-card">
                    <div class="rpt-card-label">Total Pourings</div>
                    <div class="rpt-card-value">{{ $concretePouringStats['total'] ?? 0 }}</div>
                    <div class="rpt-card-sub">All projects</div>
                </div>
                <div class="rpt-card">
                    <div class="rpt-card-label">Total Volume</div>
                    <div class="rpt-card-value">{{ $concretePouringStats['total_volume'] ?? 0 }}</div>
                    <div class="rpt-card-sub">m³</div>
                </div>
                <div class="rpt-card">
                    <div class="rpt-card-label">Avg. Volume</div>
                    <div class="rpt-card-value" style="font-size: 22px;">{{ $concretePouringStats['avg_volume'] ?? 0 }}</div>
                    <div class="rpt-card-sub">per pouring</div>
                </div>
                <div class="rpt-card">
                    <div class="rpt-card-label">Approved</div>
                    <div class="rpt-card-value" style="color: var(--rpt-green); font-size: 22px;">{{ $concretePouringStats['approved'] ?? 0 }}</div>
                    <div class="rpt-card-sub">{{ round(($concretePouringStats['approved'] ?? 0) / max(1, $concretePouringStats['total'] ?? 1) * 100) }}%</div>
                </div>
            </div>
        </div>

        <!-- MEMOS SECTION -->
        <div class="rpt-section">
            <div class="rpt-section-header">
                <h2 class="rpt-section-title">
                    <i class="fas fa-envelope" style="color: var(--rpt-purple);"></i>
                    Memos
                </h2>
                <a href="{{ route('admin.reports.memos') }}" class="rpt-section-link">
                    Full Report <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="rpt-summary-grid">
                <div class="rpt-card">
                    <div class="rpt-card-label">Total Memos</div>
                    <div class="rpt-card-value">{{ $memoStats['total'] ?? 0 }}</div>
                    <div class="rpt-card-sub">All statuses</div>
                </div>
                <div class="rpt-card">
                    <div class="rpt-card-label">Sent</div>
                    <div class="rpt-card-value" style="color: var(--rpt-green);">{{ $memoStats['sent'] ?? 0 }}</div>
                    <div class="rpt-card-sub">{{ round(($memoStats['sent'] ?? 0) / max(1, $memoStats['total'] ?? 1) * 100) }}%</div>
                </div>
                <div class="rpt-card">
                    <div class="rpt-card-label">Drafts</div>
                    <div class="rpt-card-value" style="color: var(--rpt-muted);">{{ $memoStats['draft'] ?? 0 }}</div>
                    <div class="rpt-card-sub">{{ round(($memoStats['draft'] ?? 0) / max(1, $memoStats['total'] ?? 1) * 100) }}%</div>
                </div>
                <div class="rpt-card">
                    <div class="rpt-card-label">Scheduled</div>
                    <div class="rpt-card-value" style="color: var(--rpt-blue);">{{ $memoStats['scheduled'] ?? 0 }}</div>
                    <div class="rpt-card-sub">{{ round(($memoStats['scheduled'] ?? 0) / max(1, $memoStats['total'] ?? 1) * 100) }}%</div>
                </div>
            </div>
        </div>

        <!-- USERS SECTION -->
        <div class="rpt-section">
            <div class="rpt-section-header">
                <h2 class="rpt-section-title">
                    <i class="fas fa-users" style="color: var(--rpt-orange);"></i>
                    Users & Roles
                </h2>
            </div>

            <div class="rpt-breakdown-mini">
                <div class="rpt-breakdown-header">User Distribution</div>
                <div class="rpt-breakdown-item">
                    <span class="rpt-breakdown-label">Total Users</span>
                    <span class="rpt-breakdown-value">{{ $userStats['total'] ?? 0 }}</span>
                </div>
                @foreach($userStats['by_role'] as $role => $count)
                    <div class="rpt-breakdown-item">
                        <span class="rpt-breakdown-label">
                            <span class="rpt-badge @switch($role)
                                @case('admin') blue @break
                                @case('contractor') green @break
                                @case('engineer') orange @break
                                @case('reviewer') purple @break
                                @default blue
                            @endswitch">
                                {{ ucfirst($role) }}
                            </span>
                        </span>
                        <span class="rpt-breakdown-value">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Stats Summary -->
        <div style="margin-top: 40px; padding: 24px; background: var(--rpt-surface); border: 1px solid var(--rpt-border); border-radius: 12px; box-shadow: var(--rpt-shadow);">
            <h3 style="font-size: 14px; font-weight: 700; color: var(--rpt-text); margin-bottom: 16px;">System Summary</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                <div class="rpt-stat-box">
                    <div class="rpt-stat-box-value">{{ ($workRequestStats['total'] ?? 0) + ($concretePouringStats['total'] ?? 0) + ($memoStats['total'] ?? 0) }}</div>
                    <div class="rpt-stat-box-label">Total Records</div>
                </div>
                <div class="rpt-stat-box">
                    <div class="rpt-stat-box-value" style="color: var(--rpt-green);">{{ ($workRequestStats['approved'] ?? 0) + ($concretePouringStats['approved'] ?? 0) }}</div>
                    <div class="rpt-stat-box-label">Approvals</div>
                </div>
                <div class="rpt-stat-box">
                    <div class="rpt-stat-box-value" style="color: var(--rpt-blue);">{{ $userStats['total'] ?? 0 }}</div>
                    <div class="rpt-stat-box-label">System Users</div>
                </div>
                <div class="rpt-stat-box">
                    <div class="rpt-stat-box-value" style="color: var(--rpt-orange);">{{ $concretePouringStats['total_volume'] ?? 0 }}</div>
                    <div class="rpt-stat-box-label">Concrete Volume</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
