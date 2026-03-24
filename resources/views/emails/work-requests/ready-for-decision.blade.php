<x-mail::message>
# Final Decision Required

All reviewers have completed their steps. This work request is now awaiting your **final decision**.

| Field | Details |
|---|---|
| **Project** | {{ $workRequest->name_of_project }} |
| **Location** | {{ $workRequest->project_location }} |
| **Contractor** | {{ $workRequest->contractor_name }} |
| **Work Start** | {{ $workRequest->requested_work_start_date?->format('F j, Y') ?? 'TBD' }} |

<x-mail::button :url="route('admin.work-requests.decision-form', $workRequest)">
Make Final Decision
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>