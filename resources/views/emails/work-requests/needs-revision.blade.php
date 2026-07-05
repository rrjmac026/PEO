<x-mail::message>
# Revision Requested

Your work request **"{{ $workRequest->name_of_project }}"** requires changes before it can proceed.

**Requested by:** {{ $stepLabel }}

Please review the recommendation left on your work request and resubmit once updated.

<x-mail::button :url="route('user.work-requests.edit', $workRequest)">
View & Edit Work Request
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>