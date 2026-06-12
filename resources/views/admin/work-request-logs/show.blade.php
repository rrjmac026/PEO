{{--
    resources/views/admin/work-request-logs/show.blade.php
    Admin — Full Activity Timeline for a single Work Request
    Styled to match the concrete-pouring logs/show design system.
--}}

@extends('layouts.app')

@section('title', 'Activity Timeline — Work Request #{{ $workRequest->id }}')

@push('styles')
<style>
    /* ═══════════════════════════════════════════════════════
       DESIGN TOKENS
    ═══════════════════════════════════════════════════════ */
    :root {
        --wr-surface:   #ffffff;
        --wr-surface2:  #f8fafc;
        --wr-border:    #e2e8f0;
        --wr-text:      #0f172a;
        --wr-text-sec:  #334155;
        --wr-muted:     #64748b;
        --wr-accent:    #4f46e5;
        --wr-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
    }
    .dark {
        --wr-surface:   #1a1f2e;
        --wr-surface2:  #1e2335;
        --wr-border:    #2a3050;
        --wr-text:      #e8eaf6;
        --wr-text-sec:  #c5cae9;
        --wr-muted:     #7c85a8;
        --wr-accent:    #818cf8;
    }

    /* ── Base ── */
    .wr-page-title { font-size: 28px; font-weight: 800; color: var(--wr-text); line-height: 1.2; }
    .wr-page-sub   { font-size: 14px; color: var(--wr-muted); margin-top: 4px; }

    /* ── Breadcrumb ── */
    .wr-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--wr-muted); margin-bottom: 6px; }
    .wr-breadcrumb a { color: var(--wr-muted); text-decoration: none; transition: color .12s; }
    .wr-breadcrumb a:hover { color: var(--wr-text); }
    .wr-breadcrumb .sep { font-size: 10px; opacity: .6; }
    .wr-breadcrumb .current { color: var(--wr-text); font-weight: 600; }

    /* ── Panel ── */
    .wr-panel { background: var(--wr-surface); border: 1px solid var(--wr-border); border-radius: 12px; overflow: hidden; box-shadow: var(--wr-shadow); }
    .wr-panel-header { padding: 14px 20px; border-bottom: 1px solid var(--wr-border); display: flex; align-items: center; justify-content: space-between; }
    .wr-panel-header-title { font-size: 13px; font-weight: 700; color: var(--wr-text); display: flex; align-items: center; gap: 8px; }
    .wr-panel-body { padding: 20px 24px; }

    /* ── Buttons ── */
    .wr-btn            { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid; cursor: pointer; transition: all .15s; text-decoration: none; white-space: nowrap; }
    .wr-btn-dark       { background: #1e293b; border-color: #1e293b; color: #fff; }
    .wr-btn-dark:hover { background: #334155; }
    .dark .wr-btn-dark { background: #e2e8f0; border-color: #e2e8f0; color: #0f172a; }
    .wr-btn-secondary  { background: var(--wr-surface2); border-color: var(--wr-border); color: var(--wr-text-sec); }
    .wr-btn-secondary:hover { background: var(--wr-border); }

    /* ── Status pill (page header) ── */
    .wr-status { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid; }
    .wr-status.approved   { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
    .wr-status.rejected   { color: #b91c1c; border-color: #fca5a5; background: #fff1f2; }
    .wr-status.accepted   { color: #0f766e; border-color: #5eead4; background: #f0fdfa; }
    .wr-status.in_review,
    .wr-status.assigned   { color: #1d4ed8; border-color: #93c5fd; background: #eff6ff; }
    .wr-status.submitted  { color: #3730a3; border-color: #a5b4fc; background: #eef2ff; }
    .wr-status.default    { color: #475569; border-color: #cbd5e1; background: #f1f5f9; }
    .dark .wr-status.approved  { color: #34d399; border-color: rgba(52,211,153,.3);  background: rgba(52,211,153,.08);  }
    .dark .wr-status.rejected  { color: #f87171; border-color: rgba(248,113,113,.3); background: rgba(248,113,113,.08); }
    .dark .wr-status.accepted  { color: #2dd4bf; border-color: rgba(45,212,191,.3);  background: rgba(45,212,191,.08);  }
    .dark .wr-status.in_review,
    .dark .wr-status.assigned  { color: #60a5fa; border-color: rgba(96,165,250,.3);  background: rgba(96,165,250,.08);  }
    .dark .wr-status.submitted { color: #a5b4fc; border-color: rgba(165,180,252,.3); background: rgba(165,180,252,.08); }
    .dark .wr-status.default   { color: #94a3b8; border-color: rgba(148,163,184,.3); background: rgba(148,163,184,.08); }

    /* ── Event badges ── */
    .wr-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid; white-space: nowrap; }
    .wr-badge-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .ev-created       { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
    .ev-updated       { color: #1d4ed8; border-color: #93c5fd; background: #eff6ff; }
    .ev-status_changed{ color: #6d28d9; border-color: #c4b5fd; background: #f5f3ff; }
    .ev-submitted     { color: #3730a3; border-color: #a5b4fc; background: #eef2ff; }
    .ev-inspected     { color: #92400e; border-color: #fcd34d; background: #fffbeb; }
    .ev-reviewed      { color: #c2410c; border-color: #fdba74; background: #fff7ed; }
    .ev-approved      { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
    .ev-rejected      { color: #b91c1c; border-color: #fca5a5; background: #fff1f2; }
    .ev-accepted      { color: #0f766e; border-color: #5eead4; background: #f0fdfa; }
    .ev-deleted       { color: #991b1b; border-color: #fca5a5; background: #fef2f2; }
    .ev-restored      { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
    .ev-default       { color: #475569; border-color: #cbd5e1; background: #f1f5f9; }
    .dark .ev-created       { color: #34d399; border-color: rgba(52,211,153,.3);   background: rgba(52,211,153,.08); }
    .dark .ev-updated       { color: #60a5fa; border-color: rgba(96,165,250,.3);   background: rgba(96,165,250,.08); }
    .dark .ev-status_changed{ color: #c084fc; border-color: rgba(192,132,252,.3);  background: rgba(192,132,252,.08); }
    .dark .ev-submitted     { color: #a5b4fc; border-color: rgba(165,180,252,.3);  background: rgba(165,180,252,.08); }
    .dark .ev-inspected     { color: #fbbf24; border-color: rgba(251,191,36,.3);   background: rgba(251,191,36,.08); }
    .dark .ev-reviewed      { color: #fb923c; border-color: rgba(251,146,60,.3);   background: rgba(251,146,60,.08); }
    .dark .ev-approved      { color: #34d399; border-color: rgba(52,211,153,.3);   background: rgba(52,211,153,.08); }
    .dark .ev-rejected      { color: #f87171; border-color: rgba(248,113,113,.3);  background: rgba(248,113,113,.08); }
    .dark .ev-accepted      { color: #2dd4bf; border-color: rgba(45,212,191,.3);   background: rgba(45,212,191,.08); }
    .dark .ev-deleted       { color: #f87171; border-color: rgba(248,113,113,.3);  background: rgba(248,113,113,.08); }
    .dark .ev-restored      { color: #34d399; border-color: rgba(52,211,153,.3);   background: rgba(52,211,153,.08); }
    .dark .ev-default       { color: #94a3b8; border-color: rgba(148,163,184,.3);  background: rgba(148,163,184,.08); }
    .ev-created .wr-badge-dot, .ev-approved .wr-badge-dot, .ev-restored .wr-badge-dot { background: #10b981; }
    .ev-updated .wr-badge-dot        { background: #3b82f6; }
    .ev-status_changed .wr-badge-dot { background: #a855f7; }
    .ev-submitted .wr-badge-dot      { background: #6366f1; }
    .ev-inspected .wr-badge-dot      { background: #f59e0b; }
    .ev-reviewed .wr-badge-dot       { background: #f97316; }
    .ev-rejected .wr-badge-dot, .ev-deleted .wr-badge-dot { background: #ef4444; }
    .ev-accepted .wr-badge-dot       { background: #14b8a6; }
    .ev-default .wr-badge-dot        { background: #94a3b8; }

    /* ── Role chip ── */
    .wr-role { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; border: 1px solid; }
    .wr-role.admin               { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }
    .wr-role.contractor          { background: #fffbeb; color: #92400e; border-color: #fde68a; }
    .wr-role.site_inspector      { background: #ecfeff; color: #155e75; border-color: #a5f3fc; }
    .wr-role.surveyor            { background: #f0f9ff; color: #0c4a6e; border-color: #7dd3fc; }
    .wr-role.resident_engineer   { background: #ecfeff; color: #155e75; border-color: #a5f3fc; }
    .wr-role.mtqa                { background: #fff7ed; color: #9a3412; border-color: #fed7aa; }
    .wr-role.engineeriv          { background: #faf5ff; color: #6b21a8; border-color: #e9d5ff; }
    .wr-role.engineeriii         { background: #fdf4ff; color: #701a75; border-color: #f0abfc; }
    .wr-role.provincial_engineer { background: #faf5ff; color: #6b21a8; border-color: #e9d5ff; }
    .dark .wr-role.admin               { background: rgba(71,85,105,.15);    color: #94a3b8; border-color: rgba(148,163,184,.25); }
    .dark .wr-role.contractor          { background: rgba(251,191,36,.08);   color: #fbbf24; border-color: rgba(251,191,36,.25); }
    .dark .wr-role.site_inspector,
    .dark .wr-role.resident_engineer   { background: rgba(6,182,212,.08);    color: #67e8f9; border-color: rgba(6,182,212,.25); }
    .dark .wr-role.surveyor            { background: rgba(56,189,248,.08);   color: #7dd3fc; border-color: rgba(56,189,248,.25); }
    .dark .wr-role.mtqa                { background: rgba(249,115,22,.08);   color: #fdba74; border-color: rgba(249,115,22,.25); }
    .dark .wr-role.engineeriv,
    .dark .wr-role.provincial_engineer { background: rgba(168,85,247,.08);   color: #d8b4fe; border-color: rgba(168,85,247,.25); }
    .dark .wr-role.engineeriii         { background: rgba(217,70,239,.08);   color: #f0abfc; border-color: rgba(217,70,239,.25); }

    /* ── Avatar ── */
    .wr-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--wr-surface2); border: 1px solid var(--wr-border); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: var(--wr-text-sec); flex-shrink: 0; }
    .wr-avatar.sm { width: 26px; height: 26px; font-size: 10px; }

    /* ── Info table ── */
    .wr-info-table { width: 100%; font-size: 13px; }
    .wr-info-table tr { border-bottom: 1px solid var(--wr-border); }
    .wr-info-table tr:last-child { border-bottom: none; }
    .wr-info-table td { padding: 10px 20px; vertical-align: top; }
    .wr-info-table td:first-child { color: var(--wr-muted); white-space: nowrap; width: 45%; }
    .wr-info-table td:last-child  { color: var(--wr-text); font-weight: 600; text-align: right; }

    /* ── Reviewer rows ── */
    .wr-reviewer-row { padding: 14px 20px; border-bottom: 1px solid var(--wr-border); }
    .wr-reviewer-row:last-child { border-bottom: none; }
    .wr-reviewer-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--wr-muted); margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between; }
    .wr-reviewer-name  { font-size: 13px; font-weight: 600; color: var(--wr-text); }
    .wr-reviewer-empty { font-size: 13px; color: var(--wr-muted); font-style: italic; }
    .wr-done-badge    { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f0fdf4; color: #047857; border: 1px solid #6ee7b7; }
    .wr-pending-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #fefce8; color: #854d0e; border: 1px solid #fde047; }
    .dark .wr-done-badge    { background: rgba(52,211,153,.08); color: #34d399; border-color: rgba(52,211,153,.3); }
    .dark .wr-pending-badge { background: rgba(253,224,71,.08); color: #fde047; border-color: rgba(253,224,71,.3); }
    .wr-current-step-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #eff6ff; color: #1d4ed8; border: 1px solid #93c5fd; }
    .dark .wr-current-step-badge { background: rgba(96,165,250,.08); color: #60a5fa; border-color: rgba(96,165,250,.3); }

    /* ── Outcome card ── */
    .wr-outcome { border-radius: 12px; padding: 16px 20px; border: 1px solid; box-shadow: var(--wr-shadow); }
    .wr-outcome.approved  { background: #f0fdf4; border-color: #6ee7b7; color: #047857; }
    .wr-outcome.rejected  { background: #fff1f2; border-color: #fca5a5; color: #b91c1c; }
    .wr-outcome.accepted  { background: #f0fdfa; border-color: #5eead4; color: #0f766e; }
    .dark .wr-outcome.approved { background: rgba(52,211,153,.08); border-color: rgba(52,211,153,.3); color: #34d399; }
    .dark .wr-outcome.rejected { background: rgba(248,113,113,.08); border-color: rgba(248,113,113,.3); color: #f87171; }
    .dark .wr-outcome.accepted { background: rgba(45,212,191,.08); border-color: rgba(45,212,191,.3); color: #2dd4bf; }
    .wr-outcome-title { font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
    .wr-outcome-by    { font-size: 12px; opacity: .85; }
    .wr-outcome-note  { margin-top: 10px; font-size: 12px; font-style: italic; border-radius: 8px; padding: 8px 12px; background: rgba(255,255,255,.55); border: 1px solid rgba(0,0,0,.06); }
    .dark .wr-outcome-note { background: rgba(0,0,0,.2); border-color: rgba(255,255,255,.06); }

    /* ── Layout ── */
    .wr-show-grid { display: grid; grid-template-columns: 320px 1fr; gap: 20px; align-items: start; }
    @media(max-width:1024px) { .wr-show-grid { grid-template-columns: 1fr; } }
    .wr-sidebar { display: flex; flex-direction: column; gap: 16px; }

    /* ── Timeline ── */
    .wr-timeline { position: relative; padding-left: 28px; }
    .wr-timeline::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background: var(--wr-border); border-radius: 2px; }
    .wr-tl-item { position: relative; margin-bottom: 4px; }
    .wr-tl-item:last-child { margin-bottom: 0; }
    .wr-tl-dot { position: absolute; left: -34px; top: 14px; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid var(--wr-surface); box-shadow: 0 0 0 1px var(--wr-border); font-size: 14px; color: #fff; }
    .wr-tl-dot.approved  { background: #10b981; }
    .wr-tl-dot.rejected  { background: #ef4444; }
    .wr-tl-dot.accepted  { background: #14b8a6; }
    .wr-tl-dot.submitted { background: #6366f1; }
    .wr-tl-dot.created   { background: #3b82f6; }
    .wr-tl-dot.default   { background: var(--wr-muted); }

    /* ── Event card ── */
    .wr-event-card { background: var(--wr-surface); border: 1px solid var(--wr-border); border-radius: 12px; padding: 16px 18px; margin-bottom: 16px; box-shadow: var(--wr-shadow); transition: box-shadow .15s, border-color .15s; }
    .wr-event-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-color: var(--wr-accent); }
    .wr-event-top { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 10px; }
    .wr-event-actors { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
    .wr-event-time { font-size: 11px; color: var(--wr-muted); white-space: nowrap; }
    .wr-event-desc { font-size: 13px; color: var(--wr-text); line-height: 1.6; }
    .wr-event-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 10px; }
    .wr-event-step { display: inline-flex; align-items: center; gap: 5px; background: var(--wr-surface2); border: 1px solid var(--wr-border); border-radius: 6px; padding: 3px 10px; font-size: 11px; color: var(--wr-text-sec); }
    .wr-status-flow { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; }
    .wr-sf-from { background: var(--wr-surface2); border: 1px solid var(--wr-border); border-radius: 4px; padding: 2px 8px; color: var(--wr-muted); text-transform: capitalize; }
    .wr-sf-to   { border-radius: 4px; padding: 2px 8px; font-weight: 600; text-transform: capitalize; }
    .wr-sf-to.approved  { background: #f0fdf4; color: #047857; border: 1px solid #6ee7b7; }
    .wr-sf-to.rejected  { background: #fff1f2; color: #b91c1c; border: 1px solid #fca5a5; }
    .wr-sf-to.accepted  { background: #f0fdfa; color: #0f766e; border: 1px solid #5eead4; }
    .wr-sf-to.default   { background: #eff6ff; color: #1e40af; border: 1px solid #93c5fd; }
    .dark .wr-sf-to.approved { background: rgba(52,211,153,.08);  color: #34d399; border-color: rgba(52,211,153,.3); }
    .dark .wr-sf-to.rejected { background: rgba(248,113,113,.08); color: #f87171; border-color: rgba(248,113,113,.3); }
    .dark .wr-sf-to.accepted { background: rgba(45,212,191,.08);  color: #2dd4bf; border-color: rgba(45,212,191,.3); }
    .dark .wr-sf-to.default  { background: rgba(96,165,250,.08);  color: #60a5fa; border-color: rgba(96,165,250,.3); }

    /* ── Note block ── */
    .wr-note { margin-top: 12px; background: var(--wr-surface2); border: 1px solid var(--wr-border); border-radius: 8px; padding: 10px 14px; display: flex; gap: 10px; }
    .wr-note-icon { color: var(--wr-muted); flex-shrink: 0; margin-top: 1px; }
    .wr-note-text { font-size: 12px; color: var(--wr-text-sec); font-style: italic; line-height: 1.6; }

    /* ── Changes diff ── */
    .wr-diff-toggle { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--wr-accent); cursor: pointer; margin-top: 10px; background: none; border: none; padding: 0; }
    .wr-diff-toggle:hover { text-decoration: underline; }
    .wr-diff-table { width: 100%; border-collapse: collapse; margin-top: 8px; border-radius: 8px; overflow: hidden; border: 1px solid var(--wr-border); font-size: 12px; }
    .wr-diff-table thead tr { background: var(--wr-surface2); }
    .wr-diff-table thead th { padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 700; color: var(--wr-muted); text-transform: uppercase; letter-spacing: .5px; }
    .wr-diff-table tbody tr { border-top: 1px solid var(--wr-border); }
    .wr-diff-table tbody tr:hover { background: var(--wr-surface2); }
    .wr-diff-table td { padding: 7px 12px; vertical-align: top; }
    .wr-diff-table td.field { font-family: monospace; color: var(--wr-text-sec); }
    .wr-diff-table td.old   { color: #dc2626; text-decoration: line-through; }
    .wr-diff-table td.new   { color: #059669; font-weight: 600; }
    .dark .wr-diff-table td.old { color: #f87171; }
    .dark .wr-diff-table td.new { color: #34d399; }

    /* ── Tech details ── */
    .wr-tech-toggle { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; color: var(--wr-muted); cursor: pointer; margin-top: 6px; background: none; border: none; padding: 0; }
    .wr-tech-toggle:hover { color: var(--wr-text-sec); }
    .wr-tech-val { font-size: 11px; font-family: monospace; color: var(--wr-muted); margin-top: 4px; }

    /* ── Timeline end marker ── */
    .wr-tl-end-dot { width: 28px; height: 28px; border-radius: 50%; background: var(--wr-surface2); border: 2px solid var(--wr-border); display: flex; align-items: center; justify-content: center; position: absolute; left: -32px; top: 4px; }
    .wr-tl-end-label { font-size: 12px; color: var(--wr-muted); font-style: italic; }

    /* ── Empty ── */
    .wr-empty { padding: 60px 24px; text-align: center; }
    .wr-empty i { font-size: 36px; color: var(--wr-muted); opacity: .35; display: block; margin-bottom: 14px; }
    .wr-empty-title { font-size: 15px; font-weight: 600; color: var(--wr-text-sec); }
    .wr-empty-sub   { font-size: 13px; color: var(--wr-muted); margin-top: 4px; }
</style>
@endpush

@section('content')

@php
    $logs = $workRequest->logs ?? collect();

    $knownEvents = ['created','updated','status_changed','submitted','inspected','reviewed','approved','rejected','accepted','deleted','restored'];

    $evClass = [];
    foreach ($knownEvents as $ev) { $evClass[$ev] = 'ev-' . $ev; }

    $dotClass = [
        'approved'  => 'approved',
        'rejected'  => 'rejected',
        'accepted'  => 'accepted',
        'submitted' => 'submitted',
        'created'   => 'created',
    ];

    $statusCls = match($workRequest->status) {
        'approved'  => 'approved',
        'rejected'  => 'rejected',
        'accepted'  => 'accepted',
        'in_review' => 'in_review',
        'assigned'  => 'assigned',
        'submitted' => 'submitted',
        default     => 'default',
    };

    $reviewerList = [
        ['label' => 'Site Inspector',       'user' => $workRequest->assignedSiteInspector,     'col' => 'site_inspector'],
        ['label' => 'Surveyor',             'user' => $workRequest->assignedSurveyor,           'col' => 'surveyor'],
        ['label' => 'Resident Engineer',    'user' => $workRequest->assignedResidentEngineer,   'col' => 'resident_engineer'],
        ['label' => 'ME / MTQA',            'user' => $workRequest->assignedMtqa,               'col' => 'mtqa'],
        ['label' => 'Engineer IV',          'user' => $workRequest->assignedEngineerIv,         'col' => 'engineer_iv'],
        ['label' => 'Engineer III',         'user' => $workRequest->assignedEngineerIii,        'col' => 'engineer_iii'],
        ['label' => 'Provincial Engineer',  'user' => $workRequest->assignedProvincialEngineer, 'col' => 'provincial_engineer'],
    ];
    $assignedReviewers = collect($reviewerList)->filter(fn($r) => $r['user']);
@endphp

{{-- Page Header --}}
<div class="mb-8">
    <div class="wr-breadcrumb">
        <a href="{{ route('admin.work-requests.index') }}">Work Requests</a>
        <i class="fas fa-chevron-right sep"></i>
        <a href="{{ route('admin.work-request-logs.index') }}">Activity Logs</a>
        <i class="fas fa-chevron-right sep"></i>
        <span class="current">WR #{{ $workRequest->id }}</span>
    </div>

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="wr-page-title flex flex-wrap items-center gap-3">
                Activity Timeline
                <span class="wr-status {{ $statusCls }}">
                    <span style="width:7px;height:7px;border-radius:50%;background:currentColor;display:inline-block;opacity:.8;"></span>
                    {{ ucfirst(str_replace('_', ' ', $workRequest->status)) }}
                </span>
            </h1>
            <p class="wr-page-sub">
                {{ $workRequest->name_of_project ?? $workRequest->project_name ?? 'Work Request #' . $workRequest->id }}
                @if($workRequest->project_location)
                    &bull; {{ $workRequest->project_location }}
                @endif
            </p>
        </div>

        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.work-requests.show', $workRequest) }}" class="wr-btn wr-btn-dark">
                <i class="fas fa-eye"></i> View Request
            </a>
            <a href="{{ route('admin.work-request-logs.index') }}" class="wr-btn wr-btn-secondary">
                <i class="fas fa-arrow-left"></i> All Logs
            </a>
        </div>
    </div>
</div>

<div class="wr-show-grid">

    {{-- ── SIDEBAR ─────────────────────────────────────────────── --}}
    <div class="wr-sidebar">

        {{-- Request Info --}}
        <div class="wr-panel">
            <div class="wr-panel-header">
                <span class="wr-panel-header-title">
                    <i class="fas fa-file-alt" style="color:var(--wr-muted);font-size:13px;"></i>
                    Request Info
                </span>
                <span style="font-size:12px;color:var(--wr-muted);">{{ $logs->count() }} event(s)</span>
            </div>
            @php
                $infoRows = [
                    ['label' => 'WR #',             'value' => $workRequest->id],
                    ['label' => 'Contract #',        'value' => $workRequest->contract_number ?? '—'],
                    ['label' => 'Project',           'value' => $workRequest->name_of_project ?? '—'],
                    ['label' => 'Location',          'value' => $workRequest->project_location ?? '—'],
                    ['label' => 'Contractor',        'value' => $workRequest->contractor_name ?? '—'],
                    ['label' => 'Current Step',      'value' => $workRequest->current_step_label ?? '—'],
                    ['label' => 'Work Start Date',   'value' => optional($workRequest->requested_work_start_date)->format('M d, Y') ?? '—'],
                    ['label' => 'Submitted',         'value' => $workRequest->created_at->format('M d, Y g:i A')],
                ];
            @endphp
            <table class="wr-info-table">
                @foreach($infoRows as $row)
                    <tr>
                        <td>{{ $row['label'] }}</td>
                        <td>{{ $row['value'] }}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        {{-- Assigned Reviewers --}}
        <div class="wr-panel">
            <div class="wr-panel-header">
                <span class="wr-panel-header-title">
                    <i class="fas fa-users" style="color:var(--wr-muted);font-size:13px;"></i>
                    Assigned Reviewers
                </span>
            </div>
            @forelse($assignedReviewers as $reviewer)
                <div class="wr-reviewer-row">
                    <div class="wr-reviewer-label">
                        <span>{{ $reviewer['label'] }}</span>
                        @if($workRequest->current_review_step === $reviewer['col'])
                            <span class="wr-current-step-badge">
                                <i class="fas fa-circle-dot" style="font-size:8px;"></i> Active
                            </span>
                        @elseif($workRequest->current_review_step !== null)
                            @php
                                $stepKeys = array_keys(\App\Models\WorkRequest::REVIEW_STEPS);
                                $currentIdx = array_search($workRequest->current_review_step, $stepKeys);
                                $thisIdx    = array_search($reviewer['col'], $stepKeys);
                            @endphp
                            @if($thisIdx !== false && $currentIdx !== false && $thisIdx < $currentIdx)
                                <span class="wr-done-badge"><i class="fas fa-check" style="font-size:9px;"></i> Done</span>
                            @elseif($thisIdx !== false && $currentIdx !== false && $thisIdx > $currentIdx)
                                <span class="wr-pending-badge">Pending</span>
                            @endif
                        @elseif(in_array($workRequest->status, ['approved','rejected','accepted']))
                            <span class="wr-done-badge"><i class="fas fa-check" style="font-size:9px;"></i> Done</span>
                        @endif
                    </div>
                    <div class="wr-reviewer-name">{{ $reviewer['user']->name }}</div>
                </div>
            @empty
                <div style="padding:16px 20px;">
                    <span class="wr-reviewer-empty">No reviewers assigned yet</span>
                </div>
            @endforelse
        </div>

        {{-- Outcome Card --}}
        @if(in_array($workRequest->status, ['approved', 'rejected', 'accepted']))
            @php
                $outcomeCls  = $workRequest->status;
                $outcomeIcon = match($workRequest->status) {
                    'approved' => 'fa-check-circle',
                    'rejected' => 'fa-times-circle',
                    'accepted' => 'fa-handshake',
                };
                $outcomeLabel = match($workRequest->status) {
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'accepted' => 'Accepted',
                };
            @endphp
            <div class="wr-outcome {{ $outcomeCls }}">
                <div class="wr-outcome-title">
                    <i class="fas {{ $outcomeIcon }}"></i>
                    {{ $outcomeLabel }}
                </div>
                @if($workRequest->admin_decision_remarks)
                    <div class="wr-outcome-note">"{{ $workRequest->admin_decision_remarks }}"</div>
                @endif
            </div>
        @endif

    </div>

    {{-- ── TIMELINE ────────────────────────────────────────────── --}}
    <div class="wr-panel">
        <div class="wr-panel-header">
            <span class="wr-panel-header-title">
                <i class="fas fa-history" style="color:var(--wr-muted);font-size:13px;"></i>
                Full Activity Timeline
            </span>
            <span style="font-size:12px;color:var(--wr-muted);">
                {{ $logs->count() }} event(s) &bull; Newest first
            </span>
        </div>

        <div class="wr-panel-body">
            @if($logs->isEmpty())
                <div class="wr-empty">
                    <i class="fas fa-clipboard-list"></i>
                    <div class="wr-empty-title">No activity recorded yet</div>
                    <div class="wr-empty-sub">Events will appear here as actions are taken on this request.</div>
                </div>
            @else
                <div class="wr-timeline">
                    @foreach($logs as $log)
                        @php
                            $evKey    = in_array($log->event, $knownEvents) ? $log->event : 'default';
                            $badgeCls = 'ev-' . $evKey;
                            $dot      = $dotClass[$evKey] ?? 'default';

                            $actorUser = $log->user ?? $log->employee?->user ?? null;
                            $actorName = $actorUser?->name ?? 'System';
                            $actorRole = $actorUser?->role ?? 'admin';

                            $sfToCls = match($log->status_to) {
                                'approved' => 'approved',
                                'rejected' => 'rejected',
                                'accepted' => 'accepted',
                                default    => 'default',
                            };
                        @endphp

                        <div class="wr-tl-item">
                            <div class="wr-tl-dot {{ $dot }}">
                                @if($log->event === 'approved')
                                    <i class="fas fa-check" style="font-size:12px;"></i>
                                @elseif($log->event === 'rejected')
                                    <i class="fas fa-times" style="font-size:12px;"></i>
                                @elseif($log->event === 'accepted')
                                    <i class="fas fa-handshake" style="font-size:11px;"></i>
                                @elseif($log->event === 'submitted')
                                    <i class="fas fa-paper-plane" style="font-size:11px;"></i>
                                @elseif($log->event === 'created')
                                    <i class="fas fa-plus" style="font-size:11px;"></i>
                                @else
                                    <i class="fas fa-circle" style="font-size:8px;"></i>
                                @endif
                            </div>

                            <div class="wr-event-card">

                                {{-- Top row --}}
                                <div class="wr-event-top">
                                    <div class="wr-event-actors">
                                        <span class="wr-badge {{ $badgeCls }}">
                                            <span class="wr-badge-dot"></span>
                                            {{ $log->event_label }}
                                        </span>
                                        <div style="display:flex;align-items:center;gap:6px;">
                                            <div class="wr-avatar sm">{{ strtoupper(substr($actorName, 0, 1)) }}</div>
                                            <span style="font-size:13px;font-weight:600;color:var(--wr-text);">{{ $actorName }}</span>
                                            <span class="wr-role {{ $actorRole }}">
                                                {{ ucwords(str_replace('_', ' ', $actorRole)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="wr-event-time">
                                        <span style="font-weight:600;color:var(--wr-text-sec);">{{ $log->created_at->format('M d, Y') }}</span>
                                        &bull; {{ $log->created_at->format('g:i A') }}
                                        &bull; {{ $log->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                {{-- Description --}}
                                @if($log->description)
                                    <p class="wr-event-desc">{{ $log->description }}</p>
                                @endif

                                {{-- Status transition --}}
                                @if($log->status_from || $log->status_to)
                                    <div class="wr-event-meta">
                                        <div class="wr-status-flow">
                                            @if($log->status_from)
                                                <span class="wr-sf-from">{{ $log->status_from }}</span>
                                                <i class="fas fa-arrow-right" style="font-size:9px;color:var(--wr-muted);"></i>
                                            @endif
                                            @if($log->status_to)
                                                <span class="wr-sf-to {{ $sfToCls }}">{{ $log->status_to }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- Note --}}
                                @if($log->note)
                                    <div class="wr-note">
                                        <i class="fas fa-comment-dots wr-note-icon" style="font-size:13px;"></i>
                                        <p class="wr-note-text">{{ $log->note }}</p>
                                    </div>
                                @endif

                                {{-- Changes diff --}}
                                @if(!empty($log->changes))
                                    <details>
                                        <summary class="wr-diff-toggle">
                                            <i class="fas fa-chevron-down" style="font-size:10px;"></i>
                                            {{ count($log->changes) }} field(s) changed — click to expand
                                        </summary>
                                        <table class="wr-diff-table">
                                            <thead>
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Before</th>
                                                    <th>After</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($log->changes as $field => $change)
                                                    @php [$old, $new] = is_array($change) ? $change : [null, $change]; @endphp
                                                    <tr>
                                                        <td class="field">{{ str_replace('_', ' ', $field) }}</td>
                                                        <td class="old">{{ is_bool($old) ? ($old ? 'Yes' : 'No') : ($old ?? '—') }}</td>
                                                        <td class="new">{{ is_bool($new) ? ($new ? 'Yes' : 'No') : ($new ?? '—') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </details>
                                @endif

                                {{-- Tech details --}}
                                @if($log->ip_address)
                                    <details>
                                        <summary class="wr-tech-toggle">
                                            <i class="fas fa-info-circle" style="font-size:10px;"></i>
                                            Technical details
                                        </summary>
                                        <p class="wr-tech-val">IP: {{ $log->ip_address }}</p>
                                    </details>
                                @endif

                            </div>
                        </div>
                    @endforeach

                    {{-- End marker --}}
                    <div class="wr-tl-item" style="position:relative;">
                        <div class="wr-tl-end-dot">
                            <i class="fas fa-clock" style="font-size:11px;color:var(--wr-muted);"></i>
                        </div>
                        <div style="padding-left:12px;padding-top:6px;padding-bottom:4px;">
                            <span class="wr-tl-end-label">
                                Request created {{ $workRequest->created_at->format('M d, Y \a\t g:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection