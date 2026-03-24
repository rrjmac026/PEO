<x-mail::message>
# New Work Request Submitted

A contractor has submitted a new work request requiring your attention.

| Field | Details |
|---|---|
| **Project** | {{ $workRequest->name_of_project }} |
| **Location** | {{ $workRequest->project_location }} |
| **Contractor** | {{ $workRequest->contractor_name }} |
| **Work Start** | {{ $workRequest->requested_work_start_date?->format('F j, Y') ?? 'TBD' }} |

<x-mail::button :url="route('admin.work-requests.show', $workRequest)">
View Work Request
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>