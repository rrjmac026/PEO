{{-- resources/views/reviewer/concrete-pouring/partials/_pipeline.blade.php --}}
{{-- Variables expected: $concretePouring, $isMyTurn --}}

<div class="cp-card">
    <div class="cp-card-head">
        <div class="cp-card-head-icon blue"><i class="fas fa-project-diagram"></i></div>
        <span class="cp-card-title">Review Pipeline</span>
    </div>
    <div class="cp-card-body">
        <div class="cp-timeline">
            @include('reviewer.concrete-pouring.partials._step-resident-engineer')
            @include('reviewer.concrete-pouring.partials._step-provincial-engineer')
            @include('reviewer.concrete-pouring.partials._step-mtqa')
        </div>
    </div>
</div>
