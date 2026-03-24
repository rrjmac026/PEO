@php
    $emailTitle = 'Action Required — Your Review Turn';
    $badgeClass = 'orange';
    $badgeText  = 'Action Required';
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">It's Your Turn to Review</h2>
<p class="email-intro">
    The previous reviewer has completed their step and the work request has been forwarded to you.
    Please log in and submit your review as <strong>{{ $nextStepLabel }}</strong>.
</p>

<table class="info-table">
    <tr>
        <td class="lbl">Project</td>
        <td class="val">{{ $workRequest->name_of_project }}</td>
    </tr>
    <tr>
        <td class="lbl">Location</td>
        <td class="val">{{ $workRequest->project_location }}</td>
    </tr>
    <tr>
        <td class="lbl">Contractor</td>
        <td class="val">{{ $workRequest->contractor_name ?? '—' }}</td>
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
    <a href="{{ route('reviewer.work-requests.show', $workRequest) }}" class="cta-btn">
        Open &amp; Review
    </a>
</div>
@endsection