{{-- Component: Card wrapper --}}
@props(['hoverable' => false])

<div class="card @if($hoverable) card-hover @endif" {{ $attributes }}>
    {{ $slot }}
</div>
