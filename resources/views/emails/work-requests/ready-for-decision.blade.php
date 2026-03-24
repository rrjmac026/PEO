@php
    $emailTitle = 'Final Decision Required';
    $badgeClass = 'orange';
    $badgeText  = 'Action Required';
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">Final Decision Required</h2>
<p class="email-intro">
    All reviewers have completed their steps. This work request is now awaiting your
    <strong>final approval or rejection</strong>. Please log in to make your decision.
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
        <td class="lbl">Work Start Date</td>
        <td class="val">{{ $workRequest->requested_work_start_date?->format('F j, Y') ?? 'TBD' }}</td>
    </tr>
    <tr>
        <td class="lbl">Current Status</td>
        <td class="val"><span class="step-pill">Awaiting Final Decision</span></td>
    </tr>
</table>

<div class="cta-wrap">
    <a href="{{ route('admin.work-requests.decision-form', $workRequest) }}" class="cta-btn">
        Make Final Decision
    </a>
</div>
@endsection