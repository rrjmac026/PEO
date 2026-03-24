<x-mail::message>
# {{ $isFirst ? 'Action Required — Your Review Turn' : 'Heads Up — You Are in the Queue' }}

@if($isFirst)
You have been assigned as **{{ $role }}** for a work request and it is **your turn to review**.
@else
You have been assigned as **{{ $role }}** for a work request. You will be notified again when it is your turn.
@endif

| Field | Details |
|---|---|
| **Project** | {{ $workRequest->name_of_project }} |
| **Location** | {{ $workRequest->project_location }} |
| **Contractor** | {{ $workRequest->contractor_name }} |
| **Work Start** | {{ $workRequest->requested_work_start_date?->format('F j, Y') ?? 'TBD' }} |
| **Your Role** | {{ $role }} |

@if($isFirst)
<x-mail::button :url="route('reviewer.work-requests.show', $workRequest)">
Start Your Review
</x-mail::button>
@else
<x-mail::button :url="route('reviewer.work-requests.show', $workRequest)" color="secondary">
Preview Work Request
</x-mail::button>
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>