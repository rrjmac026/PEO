@php
    $emailTitle = 'New Concrete Pouring Request Submitted';
    $badgeClass = 'orange';
    $badgeText  = 'New Submission';
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">New Concrete Pouring Request</h2>
<p class="email-intro">
    A contractor has submitted a new concrete pouring request that requires your attention.
    Please review it and assign the appropriate reviewers.
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
        <td class="val">{{ $concretePouring->requestedBy?->name ?? $concretePouring->contractor }}</td>
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
        <td class="lbl">Submitted On</td>
        <td class="val">{{ $concretePouring->created_at->format('F j, Y \a\t g:i A') }}</td>
    </tr>
    <tr>
        <td class="lbl">Status</td>
        <td class="val"><span class="step-pill">Pending Assignment</span></td>
    </tr>
</table>

<div class="cta-wrap">
    <a href="{{ route('admin.concrete-pouring.show', $concretePouring->id) }}" class="cta-btn">
        View &amp; Assign Reviewers
    </a>
</div>
@endsection