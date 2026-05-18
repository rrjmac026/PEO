{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="rpt-wrap">

    {{-- ── PAGE HEADER ──────────────────────────────────────────────────── --}}
    <div class="rpt-page-header">
        <div>
            <h1 class="rpt-page-title">
                <svg class="rpt-title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 17v-2m3 2v-4m3 4v-6M3 21h18M3 10l9-7 9 7"/>
                </svg>
                Reports &amp; Analytics
            </h1>
            <p class="rpt-page-sub">System-wide performance overview for the selected period</p>
        </div>

        {{-- Date range quick filters --}}
        <form method="GET" action="{{ route('admin.reports.index') }}" class="rpt-range-form" id="rangeForm">
            <div class="rpt-range-pills">
                @foreach([
                    'last_7_days'  => 'Last 7 Days',
                    'last_30_days' => 'Last 30 Days',
                    'this_month'   => 'This Month',
                    'last_month'   => 'Last Month',
                    'last_quarter' => 'Last Quarter',
                    'this_year'    => 'This Year',
                ] as $key => $label)
                    <button type="submit" name="preset" value="{{ $key }}"
                        class="rpt-pill {{ ($range['preset'] ?? '') === $key ? 'active' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="rpt-custom-range">
                <input type="date" name="date_from" value="{{ $range['from']->format('Y-m-d') }}" class="rpt-date-input">
                <span class="rpt-range-sep">—</span>
                <input type="date" name="date_to" value="{{ $range['to']->format('Y-m-d') }}" class="rpt-date-input">
                <button type="submit" class="rpt-btn rpt-btn-sm">Apply</button>
            </div>
        </form>
    </div>

    <div class="rpt-period-badge">
        <svg viewBox="0 0 16 16" fill="currentColor"><path d="M4 1a1 1 0 0 1 2 0v1h4V1a1 1 0 1 1 2 0v1h1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h1V1zm0 4a1 1 0 0 0 0 2h8a1 1 0 0 0 0-2H4z"/></svg>
        {{ $range['label'] }}
    </div>

    {{-- ── MODULE CARDS ─────────────────────────────────────────────────── --}}
    <div class="rpt-modules-grid">

        {{-- Work Requests --}}
        <div class="rpt-module-card rpt-mod-blue">
            <div class="rpt-mod-header">
                <div class="rpt-mod-icon-wrap rpt-mod-icon-blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="rpt-mod-title">Work Requests</h3>
                    <p class="rpt-mod-sub">Contractor submission pipeline</p>
                </div>
            </div>

            <div class="rpt-stats-row">
                <div class="rpt-stat">
                    <span class="rpt-stat-val">{{ $workRequestStats['total'] }}</span>
                    <span class="rpt-stat-lbl">Total</span>
                </div>
                <div class="rpt-stat rpt-stat-green">
                    <span class="rpt-stat-val">{{ $workRequestStats['approved'] }}</span>
                    <span class="rpt-stat-lbl">Approved</span>
                </div>
                <div class="rpt-stat rpt-stat-red">
                    <span class="rpt-stat-val">{{ $workRequestStats['rejected'] }}</span>
                    <span class="rpt-stat-lbl">Rejected</span>
                </div>
                <div class="rpt-stat rpt-stat-yellow">
                    <span class="rpt-stat-val">{{ $workRequestStats['pending'] }}</span>
                    <span class="rpt-stat-lbl">Pending</span>
                </div>
            </div>

            @if($workRequestStats['total'] > 0)
            <div class="rpt-approval-bar-wrap">
                <div class="rpt-approval-bar-label">
                    <span>Approval Rate</span>
                    <strong>{{ $workRequestStats['total'] > 0 ? round(($workRequestStats['approved'] / $workRequestStats['total']) * 100, 1) : 0 }}%</strong>
                </div>
                <div class="rpt-bar-track">
                    <div class="rpt-bar-fill rpt-bar-green" style="width:{{ $workRequestStats['total'] > 0 ? round(($workRequestStats['approved'] / $workRequestStats['total']) * 100) : 0 }}%"></div>
                </div>
            </div>
            @endif

            <div class="rpt-mod-actions">
                <a href="{{ route('admin.reports.work-requests', request()->only(['preset','date_from','date_to'])) }}" class="rpt-btn rpt-btn-primary">
                    View Full Report
                </a>
                <a href="{{ route('admin.reports.work-requests.pdf', request()->only(['preset','date_from','date_to'])) }}" target="_blank" class="rpt-btn rpt-btn-outline">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="rpt-btn-icon"><path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586L7.707 10.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"/></svg>
                    PDF
                </a>
                <a href="{{ route('admin.reports.work-requests.excel', request()->only(['preset','date_from','date_to'])) }}" class="rpt-btn rpt-btn-outline">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="rpt-btn-icon"><path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 9a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm0 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"/></svg>
                    Excel
                </a>
            </div>
        </div>

        {{-- Concrete Pourings --}}
        <div class="rpt-module-card rpt-mod-orange">
            <div class="rpt-mod-header">
                <div class="rpt-mod-icon-wrap rpt-mod-icon-orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="rpt-mod-title">Concrete Pourings</h3>
                    <p class="rpt-mod-sub">Volume &amp; checklist tracking</p>
                </div>
            </div>

            <div class="rpt-stats-row">
                <div class="rpt-stat">
                    <span class="rpt-stat-val">{{ $concretePouringStats['total'] }}</span>
                    <span class="rpt-stat-lbl">Total</span>
                </div>
                <div class="rpt-stat rpt-stat-green">
                    <span class="rpt-stat-val">{{ $concretePouringStats['approved'] }}</span>
                    <span class="rpt-stat-lbl">Approved</span>
                </div>
                <div class="rpt-stat rpt-stat-red">
                    <span class="rpt-stat-val">{{ $concretePouringStats['disapproved'] }}</span>
                    <span class="rpt-stat-lbl">Disapproved</span>
                </div>
                <div class="rpt-stat rpt-stat-yellow">
                    <span class="rpt-stat-val">{{ $concretePouringStats['pending'] }}</span>
                    <span class="rpt-stat-lbl">Pending</span>
                </div>
            </div>

            <div class="rpt-volume-chips">
                <div class="rpt-chip">
                    <span class="rpt-chip-label">Total Volume</span>
                    <span class="rpt-chip-val">{{ number_format($concretePouringStats['total_volume'], 2) }} m³</span>
                </div>
                <div class="rpt-chip">
                    <span class="rpt-chip-label">Avg / Request</span>
                    <span class="rpt-chip-val">{{ number_format($concretePouringStats['avg_volume'], 2) }} m³</span>
                </div>
            </div>

            <div class="rpt-mod-actions">
                <a href="{{ route('admin.reports.concrete-pourings', request()->only(['preset','date_from','date_to'])) }}" class="rpt-btn rpt-btn-primary rpt-btn-orange">
                    View Full Report
                </a>
                <a href="{{ route('admin.reports.concrete-pourings.pdf', request()->only(['preset','date_from','date_to'])) }}" target="_blank" class="rpt-btn rpt-btn-outline">PDF</a>
                <a href="{{ route('admin.reports.concrete-pourings.excel', request()->only(['preset','date_from','date_to'])) }}" class="rpt-btn rpt-btn-outline">Excel</a>
            </div>
        </div>

        {{-- Memos --}}
        <div class="rpt-module-card rpt-mod-violet">
            <div class="rpt-mod-header">
                <div class="rpt-mod-icon-wrap rpt-mod-icon-violet">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="rpt-mod-title">Memos</h3>
                    <p class="rpt-mod-sub">Delivery &amp; read-rate analytics</p>
                </div>
            </div>

            <div class="rpt-stats-row">
                <div class="rpt-stat">
                    <span class="rpt-stat-val">{{ $memoStats['total'] }}</span>
                    <span class="rpt-stat-lbl">Total</span>
                </div>
                <div class="rpt-stat rpt-stat-green">
                    <span class="rpt-stat-val">{{ $memoStats['sent'] }}</span>
                    <span class="rpt-stat-lbl">Sent</span>
                </div>
                <div class="rpt-stat rpt-stat-gray">
                    <span class="rpt-stat-val">{{ $memoStats['draft'] }}</span>
                    <span class="rpt-stat-lbl">Draft</span>
                </div>
                <div class="rpt-stat rpt-stat-blue">
                    <span class="rpt-stat-val">{{ $memoStats['scheduled'] }}</span>
                    <span class="rpt-stat-lbl">Scheduled</span>
                </div>
            </div>

            @if($memoStats['sent'] > 0)
            <div class="rpt-type-dist">
                @foreach($memoStats['by_type']->take(4) as $type => $count)
                <div class="rpt-type-row">
                    <span class="rpt-type-name">{{ $type }}</span>
                    <div class="rpt-bar-track rpt-bar-track-sm">
                        <div class="rpt-bar-fill rpt-bar-violet"
                             style="width:{{ $memoStats['total'] > 0 ? round(($count / $memoStats['total']) * 100) : 0 }}%"></div>
                    </div>
                    <span class="rpt-type-count">{{ $count }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div class="rpt-mod-actions">
                <a href="{{ route('admin.reports.memos', request()->only(['preset','date_from','date_to'])) }}" class="rpt-btn rpt-btn-primary rpt-btn-violet">
                    View Full Report
                </a>
                <a href="{{ route('admin.reports.memos.pdf', request()->only(['preset','date_from','date_to'])) }}" target="_blank" class="rpt-btn rpt-btn-outline">PDF</a>
                <a href="{{ route('admin.reports.memos.excel', request()->only(['preset','date_from','date_to'])) }}" class="rpt-btn rpt-btn-outline">Excel</a>
            </div>
        </div>

        {{-- Users --}}
        <div class="rpt-module-card rpt-mod-teal">
            <div class="rpt-mod-header">
                <div class="rpt-mod-icon-wrap rpt-mod-icon-teal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="rpt-mod-title">System Users</h3>
                    <p class="rpt-mod-sub">Role distribution snapshot</p>
                </div>
            </div>

            <div class="rpt-user-total">
                <span class="rpt-user-num">{{ $userStats['total'] }}</span>
                <span class="rpt-user-lbl">Registered Users</span>
            </div>

            <div class="rpt-role-list">
                @foreach($userStats['by_role'] as $role => $count)
                <div class="rpt-role-row">
                    <span class="rpt-role-badge">{{ str_replace('_', ' ', ucfirst($role)) }}</span>
                    <span class="rpt-role-count">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>{{-- /.rpt-modules-grid --}}

    {{-- ── OVERVIEW EXPORT ─────────────────────────────────────────────── --}}
    <div class="rpt-overview-export">
        <div class="rpt-export-info">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="rpt-export-icon">
                <path d="M9 17v-2m3 2v-4m3 4v-6M3 21h18M3 10l9-7 9 7"/>
            </svg>
            <div>
                <strong>Combined Overview Report</strong>
                <span>All three modules in a single exportable document</span>
            </div>
        </div>
        <div class="rpt-export-actions">
            <a href="{{ route('admin.reports.overview', request()->only(['preset','date_from','date_to'])) }}" class="rpt-btn rpt-btn-primary">
                View Overview
            </a>
            <a href="{{ route('admin.reports.overview.pdf', request()->only(['preset','date_from','date_to'])) }}" target="_blank" class="rpt-btn rpt-btn-outline">
                Export PDF
            </a>
            <a href="{{ route('admin.reports.overview.excel', request()->only(['preset','date_from','date_to'])) }}" class="rpt-btn rpt-btn-outline">
                Export Excel
            </a>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
/* ── Variables ─────────────────────────────────────────────────────────────── */
:root {
    --rpt-blue:   #1e40af;
    --rpt-blue-light: #dbeafe;
    --rpt-orange: #c2410c;
    --rpt-orange-light: #ffedd5;
    --rpt-violet: #6d28d9;
    --rpt-violet-light: #ede9fe;
    --rpt-teal:   #0f766e;
    --rpt-teal-light: #ccfbf1;
    --rpt-green:  #15803d;
    --rpt-red:    #dc2626;
    --rpt-yellow: #b45309;
    --rpt-gray:   #475569;
    --rpt-border: #e2e8f0;
    --rpt-bg:     #f8fafc;
    --rpt-text:   #0f172a;
    --rpt-muted:  #64748b;
    --rpt-radius: 12px;
    --rpt-shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 12px rgba(0,0,0,.05);
}

/* ── Layout ────────────────────────────────────────────────────────────────── */
.rpt-wrap { padding: 1.5rem; max-width: 1280px; margin: 0 auto; }

.rpt-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}
.rpt-page-title {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--rpt-text);
    margin: 0;
}
.rpt-title-icon { width: 1.4rem; height: 1.4rem; color: var(--rpt-blue); }
.rpt-page-sub { margin: .25rem 0 0; color: var(--rpt-muted); font-size: .875rem; }

/* ── Range form ────────────────────────────────────────────────────────────── */
.rpt-range-form { display: flex; flex-direction: column; gap: .5rem; align-items: flex-end; }
.rpt-range-pills { display: flex; flex-wrap: wrap; gap: .35rem; justify-content: flex-end; }
.rpt-pill {
    padding: .3rem .75rem;
    border: 1px solid var(--rpt-border);
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 500;
    background: #fff;
    color: var(--rpt-muted);
    cursor: pointer;
    transition: all .15s;
}
.rpt-pill:hover, .rpt-pill.active {
    background: var(--rpt-blue);
    border-color: var(--rpt-blue);
    color: #fff;
}
.rpt-custom-range {
    display: flex;
    align-items: center;
    gap: .4rem;
}
.rpt-date-input {
    border: 1px solid var(--rpt-border);
    border-radius: 6px;
    padding: .3rem .5rem;
    font-size: .8rem;
    color: var(--rpt-text);
    background: #fff;
}
.rpt-range-sep { color: var(--rpt-muted); font-size: .8rem; }

/* ── Period badge ──────────────────────────────────────────────────────────── */
.rpt-period-badge {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: var(--rpt-blue-light);
    color: var(--rpt-blue);
    border-radius: 6px;
    padding: .35rem .75rem;
    font-size: .8rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}
.rpt-period-badge svg { width: .85rem; height: .85rem; }

/* ── Modules grid ──────────────────────────────────────────────────────────── */
.rpt-modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

/* ── Module card ───────────────────────────────────────────────────────────── */
.rpt-module-card {
    background: #fff;
    border: 1px solid var(--rpt-border);
    border-radius: var(--rpt-radius);
    padding: 1.25rem;
    box-shadow: var(--rpt-shadow);
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.rpt-mod-header { display: flex; align-items: flex-start; gap: .75rem; }
.rpt-mod-icon-wrap {
    flex-shrink: 0;
    width: 2.5rem; height: 2.5rem;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
}
.rpt-mod-icon-wrap svg { width: 1.2rem; height: 1.2rem; }
.rpt-mod-icon-blue   { background: var(--rpt-blue-light);   color: var(--rpt-blue); }
.rpt-mod-icon-orange { background: var(--rpt-orange-light); color: var(--rpt-orange); }
.rpt-mod-icon-violet { background: var(--rpt-violet-light); color: var(--rpt-violet); }
.rpt-mod-icon-teal   { background: var(--rpt-teal-light);   color: var(--rpt-teal); }

.rpt-mod-title { font-size: .95rem; font-weight: 700; color: var(--rpt-text); margin: 0; }
.rpt-mod-sub   { font-size: .75rem; color: var(--rpt-muted); margin: .15rem 0 0; }

/* ── Stats row ─────────────────────────────────────────────────────────────── */
.rpt-stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: .5rem;
    background: var(--rpt-bg);
    border-radius: 8px;
    padding: .75rem .5rem;
}
.rpt-stat { text-align: center; }
.rpt-stat-val {
    display: block;
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--rpt-text);
    line-height: 1;
}
.rpt-stat-lbl { display: block; font-size: .68rem; color: var(--rpt-muted); margin-top: .2rem; }
.rpt-stat-green .rpt-stat-val { color: var(--rpt-green); }
.rpt-stat-red   .rpt-stat-val { color: var(--rpt-red); }
.rpt-stat-yellow .rpt-stat-val { color: var(--rpt-yellow); }
.rpt-stat-blue  .rpt-stat-val { color: var(--rpt-blue); }
.rpt-stat-gray  .rpt-stat-val { color: var(--rpt-gray); }

/* ── Progress bar ──────────────────────────────────────────────────────────── */
.rpt-approval-bar-wrap { font-size: .75rem; }
.rpt-approval-bar-label {
    display: flex; justify-content: space-between;
    color: var(--rpt-muted); margin-bottom: .3rem;
}
.rpt-bar-track {
    height: 6px;
    background: var(--rpt-border);
    border-radius: 999px;
    overflow: hidden;
}
.rpt-bar-fill { height: 100%; border-radius: 999px; transition: width .4s ease; }
.rpt-bar-green  { background: #22c55e; }
.rpt-bar-violet { background: #8b5cf6; }
.rpt-bar-track-sm { height: 4px; flex: 1; }

/* ── Volume chips ──────────────────────────────────────────────────────────── */
.rpt-volume-chips { display: flex; gap: .5rem; }
.rpt-chip {
    flex: 1;
    background: var(--rpt-bg);
    border-radius: 8px;
    padding: .6rem .75rem;
    display: flex; flex-direction: column; gap: .15rem;
}
.rpt-chip-label { font-size: .68rem; color: var(--rpt-muted); }
.rpt-chip-val   { font-size: .9rem; font-weight: 700; color: var(--rpt-text); }

/* ── Memo type dist ────────────────────────────────────────────────────────── */
.rpt-type-dist { display: flex; flex-direction: column; gap: .4rem; }
.rpt-type-row  { display: flex; align-items: center; gap: .5rem; font-size: .75rem; }
.rpt-type-name { width: 90px; color: var(--rpt-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex-shrink: 0; }
.rpt-type-count { width: 28px; text-align: right; font-weight: 600; color: var(--rpt-text); flex-shrink: 0; }

/* ── User panel ────────────────────────────────────────────────────────────── */
.rpt-user-total { text-align: center; padding: .75rem; background: var(--rpt-teal-light); border-radius: 8px; }
.rpt-user-num  { display: block; font-size: 2rem; font-weight: 800; color: var(--rpt-teal); }
.rpt-user-lbl  { font-size: .75rem; color: var(--rpt-teal); }
.rpt-role-list { display: flex; flex-direction: column; gap: .35rem; }
.rpt-role-row  { display: flex; justify-content: space-between; align-items: center; }
.rpt-role-badge {
    font-size: .72rem; font-weight: 500; color: var(--rpt-gray);
    background: var(--rpt-bg); border-radius: 4px; padding: .2rem .5rem;
    text-transform: capitalize;
}
.rpt-role-count { font-size: .8rem; font-weight: 700; color: var(--rpt-teal); }

/* ── Buttons ───────────────────────────────────────────────────────────────── */
.rpt-mod-actions { display: flex; gap: .5rem; flex-wrap: wrap; margin-top: auto; }
.rpt-btn {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .45rem .9rem;
    border-radius: 7px;
    font-size: .78rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}
.rpt-btn-sm { padding: .3rem .7rem; font-size: .75rem; }
.rpt-btn-icon { width: .9rem; height: .9rem; }
.rpt-btn-primary { background: var(--rpt-blue); color: #fff; }
.rpt-btn-primary:hover { background: #1d3a9e; color: #fff; }
.rpt-btn-orange { background: var(--rpt-orange) !important; }
.rpt-btn-orange:hover { background: #9a3412 !important; }
.rpt-btn-violet { background: var(--rpt-violet) !important; }
.rpt-btn-violet:hover { background: #5b21b6 !important; }
.rpt-btn-outline {
    background: #fff; color: var(--rpt-muted);
    border: 1px solid var(--rpt-border);
}
.rpt-btn-outline:hover { background: var(--rpt-bg); color: var(--rpt-text); }

/* ── Overview export bar ───────────────────────────────────────────────────── */
.rpt-overview-export {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    border-radius: var(--rpt-radius);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    color: #fff;
}
.rpt-export-info { display: flex; align-items: center; gap: .85rem; }
.rpt-export-icon { width: 2rem; height: 2rem; opacity: .8; }
.rpt-export-info strong { display: block; font-size: .95rem; }
.rpt-export-info span { font-size: .8rem; opacity: .8; }
.rpt-export-actions { display: flex; gap: .5rem; flex-wrap: wrap; }
.rpt-export-actions .rpt-btn-primary { background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3); }
.rpt-export-actions .rpt-btn-primary:hover { background: rgba(255,255,255,.25); }
.rpt-export-actions .rpt-btn-outline { background: rgba(255,255,255,.95); color: var(--rpt-blue); border-color: transparent; }
.rpt-export-actions .rpt-btn-outline:hover { background: #fff; }
</style>
@endpush