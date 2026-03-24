<x-mail::message>
# Your Work Request Has Been {{ ucfirst($workRequest->admin_decision) }}

@if($workRequest->admin_decision === 'approved')
🎉 Congratulations! Your work request has been **approved**.
@else
😔 Unfortunately, your work request has been **rejected**.
@endif

| Field | Details |
|---|---|
| **Project** | {{ $workRequest->name_of_project }} |
| **Location** | {{ $workRequest->project_location }} |
| **Decision** | {{ ucfirst($workRequest->admin_decision) }} |
| **Decided On** | {{ $workRequest->admin_decision_at?->format('F j, Y \a\t g:i A') ?? now()->format('F j, Y') }} |

@if($workRequest->admin_decision_remarks)
**Remarks from Admin:**

{{ $workRequest->admin_decision_remarks }}
@endif

<x-mail::button :url="route('user.work-requests.show', $workRequest)">
View Your Work Request
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>