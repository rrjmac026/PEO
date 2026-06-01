@php
    $emailTitle = 'Concrete Pouring Request Disapproved';
    $badgeClass = 'red';
    $badgeText  = 'Disapproved';
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">Concrete Pouring Request Disapproved</h2>

<div class="decision-rejected">
    <div class="big-label">&#10007; Disapproved</div>
    <p style="font-size:13px; color:#B91C1C; margin-top:4px;">
        Unfortunately, your concrete pouring request has been disapproved. Please review the remarks below.
    </p>
</div>

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
        <td class="val">{{ $concretePouring->pouring_datetime?->format('F j, Y \a\t g:i A') }}</td>
    </tr>
    <tr>
        <td class="lbl">Disapproved By</td>
        <td class="val">
            {{ $concretePouring->disapprover?->name ?? $concretePouring->notedByEngineer?->name ?? '—' }}
            <span class="step-pill" style="margin-left:6px;">Provincial Engineer</span>
        </td>
    </tr>
    <tr>
        <td class="lbl">Disapproved On</td>
        <td class="val">
            {{ $concretePouring->disapproved_date?->format('F j, Y') ?? $concretePouring->noted_date?->format('F j, Y') ?? now()->format('F j, Y') }}
        </td>
    </tr>
</table>

@if($concretePouring->approval_remarks)
    <div class="remarks-box">
        <div class="remarks-label">Reason / Remarks from Provincial Engineer</div>
        <p>{{ $concretePouring->approval_remarks }}</p>
    </div>
@else
    <div class="remarks-box">
        <div class="remarks-label">Remarks</div>
        <p style="color:#A07858; font-style:italic;">No remarks provided.</p>
    </div>
@endif

<p style="font-size:14px; color:#6B4F3A; line-height:1.7;">
    If you have questions or would like to resubmit, please contact the Provincial Engineers Office
    or submit a new concrete pouring request.
</p>

<div class="cta-wrap">
    <a href="{{ route('user.concrete-pouring.show', $concretePouring->id) }}" class="cta-btn"
       style="background:#B91C1C; box-shadow:0 4px 16px rgba(185,28,28,0.30);">
        View Request
    </a>
</div>
@endsection