<x-mail::message>
# Work Request Resubmitted

The contractor has revised and resubmitted **"{{ $workRequest->name_of_project }}"** following your requested changes.

**Your role:** {{ $stepLabel }}

Please log in and review the updated work request again.

<x-mail::button :url="route('reviewer.work-requests.show', $workRequest)">
Review Work Request
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>