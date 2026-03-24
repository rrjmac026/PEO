@php
    $isApproved  = $workRequest->admin_decision === 'approved';
    $emailTitle  = 'Work Request ' . ucfirst($workRequest->admin_decision);
    $badgeClass  = $isApproved ? 'green' : 'red';
    $badgeText   = $isApproved ? 'Approved' : 'Rejected';
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">
    Your Work Request Has Been {{ ucfirst($workRequest->admin_decision) }}
</h2>

@if($isApproved)
    <div class="decision-approved">
        <div class="big-label">&#10003; Approved</div>
        <p style="font-size:13px; color:#1F7A52; margin-top:4px;">
            Congratulations! Your work request has been approved.
        </p>
    </div>
@else
    <div class="decision-rejected">
        <div class="big-label">&#10007; Rejected</div>
        <p style="font-size:13px; color:#B91C1C; margin-top:4px;">
            Unfortunately, your work request has been rejected.
        </p>
    </div>
@endif

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
        <td class="lbl">Decision</td>
        <td class="val"><span class="step-pill">{{ ucfirst($workRequest->admin_decision) }}</span></td>
    </tr>
    <tr>
        <td class="lbl">Decided On</td>
        <td class="val">
            {{ $workRequest->admin_decision_at?->format('F j, Y \a\t g:i A') ?? now()->format('F j, Y') }}
        </td>
    </tr>
</table>

@if($workRequest->admin_decision_remarks)
    <div class="remarks-box">
        <div class="remarks-label">Remarks from Admin</div>
        <p>{{ $workRequest->admin_decision_remarks }}</p>
    </div>
@endif

<div class="cta-wrap">
    <a href="{{ route('user.work-requests.show', $workRequest) }}" class="cta-btn">
        View Your Work Request
    </a>
</div>
@endsection