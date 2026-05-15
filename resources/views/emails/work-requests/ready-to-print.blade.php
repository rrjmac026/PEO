@php
    $emailTitle = 'Work Request Ready to Print';
    $badgeClass = 'green';
    $badgeText  = 'Ready to Print';
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">Work Request Ready to Print</h2>
<p class="email-intro">
    The following work request has been <strong>approved</strong> by the Provincial Engineer
    and is now ready for printing. Please log in to download or print the approved form.
</p>

<div class="decision-approved">
    <div class="big-label">&#10003; Approved</div>
    <p style="font-size:13px; color:#1F7A52; margin-top:4px;">
        All reviews complete — cleared for printing.
    </p>
</div>

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
        <td class="lbl">Approved By</td>
        <td class="val">{{ $workRequest->approved_by ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Approved On</td>
        <td class="val">
            {{ $workRequest->accepted_date
                ? \Carbon\Carbon::parse($workRequest->accepted_date)->format('F j, Y')
                : now()->format('F j, Y') }}
        </td>
    </tr>
</table>

@if($workRequest->approved_recommendation_action)
    <div class="remarks-box">
        <div class="remarks-label">Remarks from Provincial Engineer</div>
        <p>{{ $workRequest->approved_recommendation_action }}</p>
    </div>
@endif

<div class="cta-wrap">
    <a href="{{ route('reviewer.work-requests.show', $workRequest) }}" class="cta-btn">
        Open &amp; Print
    </a>
</div>
@endsection
