<x-mail::message>
# It's Your Turn to Review

The previous reviewer has completed their step. It is now **your turn** as **{{ $nextStepLabel }}**.

| Field | Details |
|---|---|
| **Project** | {{ $workRequest->name_of_project }} |
| **Location** | {{ $workRequest->project_location }} |
| **Contractor** | {{ $workRequest->contractor_name }} |
| **Previous Reviewer** | {{ $completedByName }} ({{ ucwords(str_replace('_', ' ', $completedStep)) }}) |
| **Your Step** | {{ $nextStepLabel }} |

<x-mail::button :url="route('reviewer.work-requests.show', $workRequest)">
Open Work Request
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>