@php
    $emailTitle  = 'New Work Request Submitted';
    $badgeClass  = 'orange';
    $badgeText   = 'New Submission';
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">New Work Request Submitted</h2>
<p class="email-intro">
    A contractor has submitted a new work request that requires your attention.
    Please review it and assign the appropriate reviewers.
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
        <td class="lbl">Submitted On</td>
        <td class="val">{{ $workRequest->created_at->format('F j, Y \a\t g:i A') }}</td>
    </tr>
</table>

<div class="cta-wrap">
    <a href="{{ route('admin.work-requests.show', $workRequest) }}" class="cta-btn">
        View Work Request
    </a>
</div>
@endsection