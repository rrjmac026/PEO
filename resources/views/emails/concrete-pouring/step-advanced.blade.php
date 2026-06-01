@php
    $emailTitle = 'Action Required — Your Review Turn';
    $badgeClass = 'orange';
    $badgeText  = 'Action Required';
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">It's Your Turn to Review</h2>
<p class="email-intro">
    The previous reviewer has completed their step and the concrete pouring request has been forwarded to you.
    Please log in and submit your review as <strong>{{ $nextStepLabel }}</strong>.
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
        <td class="lbl">Previous Reviewer</td>
        <td class="val">
            {{ $completedByName }}
            <span class="step-pill" style="margin-left:6px;">
                {{ ucwords(str_replace('_', ' ', $completedStep)) }}
            </span>
        </td>
    </tr>
    <tr>
        <td class="lbl">Your Step</td>
        <td class="val"><span class="step-pill">{{ $nextStepLabel }}</span></td>
    </tr>
</table>

<div class="cta-wrap">
    <a href="{{ route('reviewer.concrete-pouring.show', $concretePouring->id) }}" class="cta-btn">
        Open &amp; Review
    </a>
</div>
@endsection