@php
    $emailTitle = 'Final Decision Required — Concrete Pouring';
    $badgeClass = 'orange';
    $badgeText  = 'Action Required';
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">Final Decision Required</h2>
<p class="email-intro">
    All reviewers have completed their steps for this concrete pouring request. Your
    <strong>final approval or disapproval</strong> is now required. Please log in to review all
    findings and submit your decision.
</p>

<table class="info-table">
    <tr>
        <td class="lbl">Reference No.</td>
        <td class="val">{{ $concretePouring->contract_number }}</td>
    </tr>
    <tr>
        <td class="lbl">Project</td>
        <td class="val">{{ $concretePouring->project_name }}</td>
    </tr>
    <tr>
        <td class="lbl">Location</td>
        <td class="val">{{ $concretePouring->location }}</td>
    </tr>
    <tr>
        <td class="lbl">Contractor</td>
        <td class="val">{{ $concretePouring->contractor }}</td>
    </tr>
    <tr>
        <td class="lbl">Part of Structure</td>
        <td class="val">{{ $concretePouring->part_of_structure }}</td>
    </tr>
    <tr>
        <td class="lbl">Estimated Volume</td>
        <td class="val">{{ $concretePouring->estimated_volume }} cu.m.</td>
    </tr>
    <tr>
        <td class="lbl">Pouring Date & Time</td>
        <td class="val">{{ $concretePouring->pouring_datetime?->format('F j, Y \a\t g:i A') ?? 'TBD' }}</td>
    </tr>
    <tr>
        <td class="lbl">Current Status</td>
        <td class="val"><span class="step-pill">Awaiting Final Decision</span></td>
    </tr>
</table>

{{-- Completed reviews summary --}}
@if($concretePouring->residentEngineer || $concretePouring->meMtqaChecker)
    <p style="font-size:11px; font-weight:700; color:#A07858; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:10px;">
        Completed Reviews
    </p>
    <table style="width:100%; border-collapse:collapse; margin-bottom:28px;">
        @if($concretePouring->residentEngineer && $concretePouring->re_date)
            <tr>
                <td style="padding:8px 12px; font-size:13px; border-bottom:1px solid #F0E0D0; color:#1F7A52; font-weight:600;">
                    <span style="display:inline-block; width:18px; height:18px; border-radius:50%; text-align:center; line-height:18px; font-size:10px; font-weight:700; margin-right:8px; background:rgba(45,158,107,0.15); color:#1F7A52;">✓</span>
                    Resident Engineer
                </td>
                <td style="padding:8px 12px; font-size:13px; border-bottom:1px solid #F0E0D0; color:#3D2B1A; text-align:right;">
                    {{ $concretePouring->residentEngineer->name }}
                    <span style="margin-left:6px; font-size:11px; color:#A07858;">{{ $concretePouring->re_date->format('M d, Y') }}</span>
                </td>
            </tr>
        @endif
        @if($concretePouring->meMtqaChecker && $concretePouring->me_mtqa_date)
            <tr>
                <td style="padding:8px 12px; font-size:13px; color:#1F7A52; font-weight:600;">
                    <span style="display:inline-block; width:18px; height:18px; border-radius:50%; text-align:center; line-height:18px; font-size:10px; font-weight:700; margin-right:8px; background:rgba(45,158,107,0.15); color:#1F7A52;">✓</span>
                    ME/MTQA
                </td>
                <td style="padding:8px 12px; font-size:13px; color:#3D2B1A; text-align:right;">
                    {{ $concretePouring->meMtqaChecker->name }}
                    <span style="margin-left:6px; font-size:11px; color:#A07858;">{{ $concretePouring->me_mtqa_date->format('M d, Y') }}</span>
                </td>
            </tr>
        @endif
    </table>
@endif

<div class="cta-wrap">
    <a href="{{ route('reviewer.concrete-pouring.show', $concretePouring->id) }}" class="cta-btn">
        Submit Final Decision
    </a>
</div>
@endsection