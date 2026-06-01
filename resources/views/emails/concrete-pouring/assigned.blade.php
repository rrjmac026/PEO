@php
    $emailTitle = $isFirst ? 'Action Required — Concrete Pouring Review' : 'Heads Up — You Are in the Queue';
    $badgeClass = $isFirst ? 'orange' : 'green';
    $badgeText  = $isFirst ? 'Action Required' : 'In the Queue';

    $steps = [
        'resident_engineer'   => 'Resident Engineer',
        'mtqa'                => 'ME/MTQA',
        'provincial_engineer' => 'Provincial Engineer (Final Decision)',
    ];
    $roleMap = [
        'Resident Engineer'                    => 'resident_engineer',
        'ME/MTQA'                              => 'mtqa',
        'Provincial Engineer'                  => 'provincial_engineer',
        'Provincial Engineer (Final Decision)' => 'provincial_engineer',
    ];
    $stepKeys       = array_keys($steps);
    $currentStepKey = $roleMap[$role] ?? null;
    $currentIdx     = array_search($currentStepKey, $stepKeys);
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">
    {{ $isFirst ? "It's Your Turn to Review" : "You've Been Queued for Review" }}
</h2>
<p class="email-intro">
    @if($isFirst)
        You have been assigned as <strong>{{ $role }}</strong> for the concrete pouring request below.
        It is currently <strong>your turn</strong> — please log in and submit your review.
    @else
        You have been added to the review queue as <strong>{{ $role }}</strong> for the concrete pouring request below.
        You will receive another notification when it is your turn to act.
    @endif
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
        <td class="lbl">Your Role</td>
        <td class="val"><span class="step-pill">{{ $role }}</span></td>
    </tr>
</table>

{{-- Review pipeline --}}
<p style="font-size:11px; font-weight:700; color:#A07858; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:10px;">
    Review Pipeline
</p>
<table style="width:100%; border-collapse:collapse; margin-bottom:28px;">
    @foreach($steps as $key => $label)
        @php
            $idx = array_search($key, $stepKeys);
            $isDone   = $idx < $currentIdx;
            $isActive = $key === $currentStepKey;
        @endphp
        <tr>
            <td style="padding:8px 12px; font-size:13px; border-bottom:1px solid #F0E0D0;
                       color: {{ $isDone ? '#1F7A52' : ($isActive ? '#B84A00' : '#A07858') }};
                       font-weight: {{ $isActive ? '700' : '400' }};">
                <span style="display:inline-block; width:18px; height:18px; border-radius:50%; text-align:center; line-height:18px; font-size:10px; font-weight:700; margin-right:8px;
                             background: {{ $isDone ? 'rgba(45,158,107,0.15)' : ($isActive ? 'rgba(224,90,0,0.12)' : 'rgba(160,120,88,0.10)') }};
                             color: {{ $isDone ? '#1F7A52' : ($isActive ? '#B84A00' : '#A07858') }};">
                    {{ $isDone ? '✓' : ($isActive ? '●' : '○') }}
                </span>
                {{ $label }}
                @if($isActive)
                    <span style="margin-left:8px; font-size:11px; background:rgba(224,90,0,0.10); color:#B84A00; border:1px solid rgba(224,90,0,0.20); border-radius:4px; padding:2px 8px;">You</span>
                @endif
            </td>
        </tr>
    @endforeach
</table>

<div class="cta-wrap">
    <a href="{{ route('reviewer.concrete-pouring.show', $concretePouring->id) }}"
       class="cta-btn {{ $isFirst ? '' : 'secondary' }}">
        {{ $isFirst ? 'Start Your Review' : 'Preview Request' }}
    </a>
</div>
@endsection