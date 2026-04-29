{{--
    resources/views/admin/concrete-pouring/logs/index.blade.php
    Admin — All Concrete Pouring Activity Logs
--}}

@extends('layouts.app')

@section('title', 'Concrete Pouring — Activity Logs')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────────── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <div class="flex items-center gap-2 text-sm text-text-secondary mb-1">
            <a href="{{ route('admin.concrete-pouring.index') }}" class="hover:text-text-primary transition-colors">
                Concrete Pouring
            </a>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
            <span class="text-text-primary font-medium">Activity Logs</span>
        </div>
        <h1 class="text-2xl font-bold text-text-primary tracking-tight">Activity Logs</h1>
        <p class="mt-1 text-sm text-text-secondary">
            A complete audit trail of all actions across every concrete pouring request.
        </p>
    </div>
    <a href="{{ route('admin.concrete-pouring.index') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-border-primary bg-surface-primary px-4 py-2 text-sm font-medium text-text-secondary shadow-token-sm hover:bg-surface-secondary hover:text-text-primary transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Requests
    </a>
</div>

{{-- ── Summary Stats ────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @php
        $statCards = [
            ['label' => 'Total Events',  'value' => $logs->total(),                               'type' => 'default'],
            ['label' => 'Today',         'value' => $todayCount ?? 0,                              'type' => 'info'],
            ['label' => 'Approvals',     'value' => $approvedCount ?? 0,                           'type' => 'success'],
            ['label' => 'Disapprovals',  'value' => $disapprovedCount ?? 0,                        'type' => 'error'],
        ];
    @endphp
    @foreach ($statCards as $stat)
        <x-stat-card 
            label="{{ $stat['label'] }}" 
            value="{{ number_format($stat['value']) }}"
            type="{{ $stat['type'] }}" 
        />
    @endforeach
</div>

{{-- ── Filter Bar ───────────────────────────────────────────────────────────── --}}
<div class="mb-5 rounded-xl bg-surface-primary ring-1 ring-border-primary shadow-token-sm">
    <form method="GET" action="{{ route('admin.concrete-pouring.logs') }}"
          class="flex flex-wrap items-end gap-3 p-4">

        {{-- Search --}}
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-text-secondary mb-1">Search</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-text-tertiary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Reference #, project, user…"
                       class="w-full rounded-lg border border-border-primary bg-surface-secondary py-2 pl-9 pr-3 text-sm text-text-primary placeholder-text-muted focus:border-action-primary focus:bg-surface-primary focus:outline-none focus:ring-2 focus:ring-token transition">
            </div>
        </div>

        {{-- Event Type --}}
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-text-secondary mb-1">Event</label>
            <select name="event"
                    class="w-full rounded-lg border border-border-primary bg-surface-secondary py-2 pl-3 pr-8 text-sm text-text-primary focus:border-action-primary focus:bg-surface-primary focus:outline-none focus:ring-2 focus:ring-token transition appearance-none">
                <option value="">All Events</option>
                <option value="submitted"    @selected(request('event') === 'submitted')>Submitted</option>
                <option value="updated"      @selected(request('event') === 'updated')>Updated</option>
                <option value="deleted"      @selected(request('event') === 'deleted')>Deleted</option>
                <option value="assigned"     @selected(request('event') === 'assigned')>Assigned</option>
                <option value="re_reviewed"  @selected(request('event') === 're_reviewed')>RE Reviewed</option>
                <option value="pe_noted"     @selected(request('event') === 'pe_noted')>PE Noted</option>
                <option value="mtqa_decided" @selected(request('event') === 'mtqa_decided')>MTQA Decided</option>
                <option value="approved"     @selected(request('event') === 'approved')>Approved</option>
                <option value="disapproved"  @selected(request('event') === 'disapproved')>Disapproved</option>
            </select>
        </div>

        {{-- Actor Role --}}
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-text-secondary mb-1">Actor Role</label>
            <select name="role"
                    class="w-full rounded-lg border border-border-primary bg-surface-secondary py-2 pl-3 pr-8 text-sm text-text-primary focus:border-action-primary focus:bg-surface-primary focus:outline-none focus:ring-2 focus:ring-token transition appearance-none">
                <option value="">All Roles</option>
                <option value="admin"               @selected(request('role') === 'admin')>Admin</option>
                <option value="contractor"          @selected(request('role') === 'contractor')>Contractor</option>
                <option value="resident_engineer"   @selected(request('role') === 'resident_engineer')>Resident Engineer</option>
                <option value="provincial_engineer" @selected(request('role') === 'provincial_engineer')>Provincial Engineer</option>
                <option value="mtqa"                @selected(request('role') === 'mtqa')>ME/MTQA</option>
            </select>
        </div>

        {{-- Date From --}}
        <div class="min-w-[140px]">
            <label class="block text-xs font-medium text-text-secondary mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="w-full rounded-lg border border-border-primary bg-surface-secondary py-2 px-3 text-sm text-text-primary focus:border-action-primary focus:bg-surface-primary focus:outline-none focus:ring-2 focus:ring-token transition">
        </div>

        {{-- Date To --}}
        <div class="min-w-[140px]">
            <label class="block text-xs font-medium text-text-secondary mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="w-full rounded-lg border border-border-primary bg-surface-secondary py-2 px-3 text-sm text-text-primary focus:border-action-primary focus:bg-surface-primary focus:outline-none focus:ring-2 focus:ring-token transition">
        </div>

        <div class="flex items-end gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-action-primary px-4 py-2 text-sm font-medium text-white shadow-token-sm hover:bg-action-primary-hover focus:outline-none focus:ring-2 focus:ring-action-primary focus:ring-offset-1 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/>
                </svg>
                Filter
            </button>
            @if (request()->hasAny(['search','event','role','date_from','date_to']))
                <a href="{{ route('admin.concrete-pouring.logs') }}"
                   class="inline-flex items-center gap-1 rounded-lg border border-border-primary bg-surface-primary px-3 py-2 text-sm font-medium text-text-secondary hover:bg-surface-secondary hover:text-text-primary transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

{{-- ── Logs Table ───────────────────────────────────────────────────────────── --}}
<div class="rounded-xl bg-surface-primary ring-1 ring-border-primary shadow-token-sm overflow-hidden">
    @if ($logs->isEmpty())
        <div class="py-20 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-text-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <p class="mt-3 text-sm font-medium text-text-muted">No activity logs found.</p>
            <p class="text-xs text-text-tertiary mt-1">Try adjusting your filters.</p>
        </div>
    @else
        <table class="min-w-full divide-y divide-border-primary text-sm">
            <thead>
                <tr class="bg-surface-secondary text-left">
                    <th class="px-4 py-3 font-semibold text-xs text-text-secondary uppercase tracking-wider w-[170px]">Timestamp</th>
                    <th class="px-4 py-3 font-semibold text-xs text-text-secondary uppercase tracking-wider">Request</th>
                    <th class="px-4 py-3 font-semibold text-xs text-text-secondary uppercase tracking-wider w-[140px]">Event</th>
                    <th class="px-4 py-3 font-semibold text-xs text-text-secondary uppercase tracking-wider">Description</th>
                    <th class="px-4 py-3 font-semibold text-xs text-text-secondary uppercase tracking-wider w-[160px]">Actor</th>
                    <th class="px-4 py-3 font-semibold text-xs text-text-secondary uppercase tracking-wider w-[80px] text-center">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-primary">
                @foreach ($logs as $log)
                    <tr class="hover:bg-surface-secondary transition-colors group cursor-pointer"
                        onclick="window.location='{{ route('admin.concrete-pouring.logs.show', $log->concretePouring) }}'">

                        {{-- Timestamp --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="text-xs font-medium text-text-primary">
                                {{ $log->created_at->format('M d, Y') }}
                            </p>
                            <p class="text-xs text-text-tertiary mt-0.5">
                                {{ $log->created_at->format('g:i A') }}
                                &bull;
                                {{ $log->created_at->diffForHumans() }}
                            </p>
                        </td>

                        {{-- Request --}}
                        <td class="px-4 py-3">
                            @if ($log->concretePouring)
                                <a href="{{ route('admin.concrete-pouring.logs.show', $log->concretePouring) }}"
                                   class="font-medium text-action-primary hover:underline transition-colors"
                                   onclick="event.stopPropagation()">
                                    {{ $log->concretePouring->reference_number ?? 'N/A' }}
                                </a>
                                <p class="text-xs text-text-tertiary mt-0.5 truncate max-w-[200px]">
                                    {{ $log->concretePouring->project_name }}
                                </p>
                            @else
                                <span class="text-text-tertiary italic text-xs">[Deleted]</span>
                            @endif
                        </td>

                        {{-- Event Badge --}}
                        <td class="px-4 py-3">
                            <x-badge-event :event="$log->event" />
                        </td>

                        {{-- Description --}}
                        <td class="px-4 py-3">
                            <p class="text-sm text-text-primary line-clamp-2 max-w-sm">
                                {{ $log->description ?? '—' }}
                            </p>
                            @if ($log->note)
                                <p class="mt-0.5 text-xs text-text-tertiary italic line-clamp-1">
                                    "{{ $log->note }}"
                                </p>
                            @endif
                        </td>

                        {{-- Actor --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <x-avatar :name="$log->actor_name" size="md" />
                                <div>
                                    <p class="text-xs font-medium text-text-primary leading-tight">{{ $log->actor_name }}</p>
                                    <x-badge-role :role="$log->actor_role" />
                                </div>
                            </div>
                        </td>

                        {{-- View Link --}}
                        <td class="px-4 py-3 text-center" onclick="event.stopPropagation()">
                            @if ($log->concretePouring)
                                <a href="{{ route('admin.concrete-pouring.logs.show', $log->concretePouring) }}"
                                   title="View all logs for this request"
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-border-primary bg-surface-primary text-text-tertiary hover:text-action-primary hover:border-action-primary hover:bg-surface-secondary transition-all shadow-token-sm group-hover:border-action-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($logs->hasPages())
            <div class="border-t border-border-primary px-4 py-3 flex items-center justify-between">
                <p class="text-xs text-text-tertiary">
                    Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ number_format($logs->total()) }} events
                </p>
                {{ $logs->withQueryString()->links() }}
            </div>
        @else
            <div class="border-t border-border-primary px-4 py-3">
                <p class="text-xs text-text-tertiary">Showing {{ $logs->count() }} event(s)</p>
            </div>
        @endif
    @endif
</div>

@endsection
