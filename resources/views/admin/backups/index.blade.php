{{-- resources/views/admin/backups/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Database Backups')

@push('styles')
    @include('admin.backups.partials._styles')
@endpush

@section('content')
<div style="padding: 24px; max-width: 1400px; margin: 0 auto;">

    @include('admin.backups.partials._flash')
    @include('admin.backups.partials._header')
    @include('admin.backups.partials._stats')
    @include('admin.backups.partials._quick-actions')
    @include('admin.backups.partials._table')

</div>

@include('admin.backups.partials._modals')

{{-- Quick backup toast --}}
<div id="quick-toast">
    <div class="toast-icon" id="toast-icon"><i class="fas fa-bolt" id="toast-fa-icon"></i></div>
    <div>
        <div style="font-weight:600;color:var(--bp-text);font-size:13px;" id="toast-title">Creating backup…</div>
        <div style="font-size:12px;color:var(--bp-muted);" id="toast-msg">Please wait</div>
    </div>
</div>
@endsection

@push('scripts')
    @include('admin.backups.partials._scripts')
@endpush