@php
    $emailTitle = $isFirst ? 'Action Required — Work Request Assigned' : 'Heads Up — You Are in the Queue';
    $badgeClass = $isFirst ? 'orange' : 'green';
    $badgeText  = $isFirst ? 'Action Required' : 'In the Queue';
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">
    {{ $isFirst ? 'It\'s Your Turn to Review' : 'You\'ve Been Queued for Review' }}
</h2>
<p class="email-intro">
    @if($isFirst)
        You have been assigned as <strong>{{ $role }}</strong> for the work request below.
        It is currently <strong>your turn</strong> — please log in and submit your review.
    @else
        You have been added to the review queue as <strong>{{ $role }}</strong> for the work request below.
        You will receive another notification when it is your turn to act.
    @endif
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
        <td class="lbl">Your Role</td>
        <td class="val"><span class="step-pill">{{ $role }}</span></td>
    </tr>
</table>

<div class="cta-wrap">
    <a href="{{ route('reviewer.work-requests.show', $workRequest) }}"
       class="cta-btn {{ $isFirst ? '' : 'secondary' }}">
        {{ $isFirst ? 'Start Your Review' : 'Preview Work Request' }}
    </a>
</div>
@endsection