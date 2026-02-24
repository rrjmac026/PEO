{{-- resources/views/reviewer/work-requests/show.blade.php --}}

{{-- Everyone sees the full request details --}}
@include('partials.work-request-details', ['workRequest' => $workRequest])

{{-- Site Inspector Section --}}
@if($role === 'site_inspector')
    <form method="POST" action="{{ route('reviewer.work-requests.store-inspection', $workRequest) }}">
        @csrf
        <input name="inspected_by_site_inspector" value="{{ $workRequest->inspected_by_site_inspector }}">
        <textarea name="findings_comments">{{ $workRequest->findings_comments }}</textarea>
        <textarea name="recommendation">{{ $workRequest->recommendation }}</textarea>
        <button type="submit">Submit Inspection</button>
    </form>
@endif

{{-- Surveyor Section --}}
@if($role === 'surveyor')
    <form method="POST" action="{{ route('reviewer.work-requests.store-survey', $workRequest) }}">
        @csrf
        {{-- surveyor fields --}}
    </form>
@endif

{{-- Resident Engineer Section --}}
@if($role === 'resident_engineer')
    <form method="POST" action="{{ route('reviewer.work-requests.store-engineer-review', $workRequest) }}">
        @csrf
        {{-- engineer fields --}}
    </form>
@endif

{{-- Provincial Engineer Section --}}
@if($role === 'provincial_engineer')
    <form method="POST" action="{{ route('reviewer.work-requests.store-provincial-note', $workRequest) }}">
        @csrf
        <textarea name="approved_notes">{{ $workRequest->approved_notes }}</textarea>
        <button type="submit">Add Note</button>
    </form>
@endif