@php
    $reviewSteps = ['resident_engineer', 'mtqa', 'engineer_iv', 'engineer_iii', 'provincial_engineer'];
@endphp

<div class="all-recommendations mt-4">
    <h5 class="text-base font-semibold text-gray-800 mb-3">Reviewer Feedback</h5>
    @foreach($reviewSteps as $step)
        @include('work-requests._recommendation-history', ['step' => $step])
    @endforeach
</div>