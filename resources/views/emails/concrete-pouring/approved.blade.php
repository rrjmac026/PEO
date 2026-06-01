@php
    $emailTitle = 'Concrete Pouring Request Approved';
    $badgeClass = 'green';
    $badgeText  = 'Approved';
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">Concrete Pouring Request Approved</h2>

<div class="decision-approved">
    <div class="big-label">&#10003; Approved</div>
    <p style="font-size:13px; color:#1F7A52; margin-top:4px;">
        Your concrete pouring request has been officially approved by the Provincial Engineer.
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
        <td class="lbl">Approved By</td>
        <td class="val">
            {{ $concretePouring->approver?->name ?? $concretePouring->notedByEngineer?->name ?? '—' }}
            <span class="step-pill" style="margin-left:6px;">Provincial Engineer</span>
        </td>
    </tr>
    <tr>
        <td class="lbl">Approved On</td>
        <td class="val">
            {{ $concretePouring->approved_date?->format('F j, Y') ?? $concretePouring->noted_date?->format('F j, Y') ?? now()->format('F j, Y') }}
        </td>
    </tr>
</table>

@if($concretePouring->approval_remarks)
    <div class="remarks-box">
        <div class="remarks-label">Remarks from Provincial Engineer</div>
        <p>{{ $concretePouring->approval_remarks }}</p>
    </div>
@endif

<p style="font-size:14px; color:#6B4F3A; line-height:1.7;">
    You may now proceed with the concrete pouring as scheduled.
</p>

<div class="cta-wrap">
    <a href="{{ route('user.concrete-pouring.show', $concretePouring->id) }}" class="cta-btn">
        View Approved Request
    </a>
</div>
@endsection