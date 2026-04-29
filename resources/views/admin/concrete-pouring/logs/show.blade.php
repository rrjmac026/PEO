{{--
    resources/views/admin/concrete-pouring/logs/show.blade.php
    Admin — Full Activity Timeline for a single Concrete Pouring request
--}}

@extends('layouts.app')

@section('title', 'Activity Log — ' . $concretePouring->reference_number)

@section('content')

@php
    $logs = $concretePouring->logs ?? collect();
@endphp

{{-- ── Breadcrumb + Header ──────────────────────────────────────────────────── --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
        <div class="flex flex-wrap items-center gap-1.5 text-sm text-text-secondary mb-2">
            <a href="{{ route('admin.concrete-pouring.index') }}" class="hover:text-text-primary transition-colors">Concrete Pouring</a>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
            <a href="{{ route('admin.concrete-pouring.logs') }}" class="hover:text-text-primary transition-colors">Activity Logs</a>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
            <span class="font-medium text-text-primary">{{ $concretePouring->reference_number }}</span>
        </div>

        <h1 class="text-2xl font-bold text-text-primary tracking-tight flex flex-wrap items-center gap-2">
            Activity Timeline
            @php
                $statusColor = match($concretePouring->status) {
                    'approved'    => 'tag-event-approved',
                    'disapproved' => 'tag-event-disapproved',
                    default       => 'tag-event-status-changed',
                };
            @endphp
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium badge {{ $statusColor }} capitalize">
                {{ $concretePouring->status }}
            </span>
        </h1>
        <p class="mt-1 text-sm text-text-secondary">
            {{ $concretePouring->project_name }}
            @if ($concretePouring->location)
                &bull; {{ $concretePouring->location }}
            @endif
        </p>
    </div>

    <div class="flex gap-2 shrink-0">
        <a href="{{ route('admin.concrete-pouring.show', $concretePouring) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-border-primary bg-surface-primary px-4 py-2 text-sm font-medium text-text-secondary shadow-token-sm hover:bg-surface-secondary hover:text-text-primary transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            View Request
        </a>
        <a href="{{ route('admin.concrete-pouring.logs') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-border-primary bg-surface-primary px-4 py-2 text-sm font-medium text-text-secondary shadow-token-sm hover:bg-surface-secondary hover:text-text-primary transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            All Logs
        </a>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ── LEFT: Request Summary Card ──────────────────────────────────────── --}}
    <div class="xl:col-span-1 space-y-4">

        {{-- Request Info --}}
        <div class="rounded-xl bg-surface-primary ring-1 ring-border-primary shadow-token-sm overflow-hidden">
            <div class="border-b border-border-primary px-4 py-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-text-primary">Request Info</h2>
                <span class="text-xs text-text-tertiary">{{ $logs->count() }} event(s)</span>
            </div>
            <div class="divide-y divide-border-primary text-sm">
                @php
                    $infoRows = [
                        ['label' => 'Reference #',   'value' => $concretePouring->reference_number ?? '—'],
                        ['label' => 'Project',        'value' => $concretePouring->project_name],
                        ['label' => 'Location',       'value' => $concretePouring->location],
                        ['label' => 'Contractor',     'value' => $concretePouring->contractor],
                        ['label' => 'Structure Part', 'value' => $concretePouring->part_of_structure],
                        ['label' => 'Volume (m³)',    'value' => $concretePouring->estimated_volume],
                        ['label' => 'Pouring Date',   'value' => optional($concretePouring->pouring_datetime)->format('M d, Y g:i A')],
                        ['label' => 'Submitted',      'value' => $concretePouring->created_at->format('M d, Y g:i A')],
                    ];
                @endphp
                @foreach ($infoRows as $row)
                    <div class="px-4 py-2.5 flex justify-between gap-3">
                        <span class="text-text-tertiary shrink-0">{{ $row['label'] }}</span>
                        <span class="text-text-primary font-medium text-right">{{ $row['value'] ?? '—' }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Assigned Reviewers --}}
        <div class="rounded-xl bg-surface-primary ring-1 ring-border-primary shadow-token-sm overflow-hidden">
            <div class="border-b border-border-primary px-4 py-3">
                <h2 class="text-sm font-semibold text-text-primary">Assigned Reviewers</h2>
            </div>
            <div class="divide-y divide-border-primary text-sm">
                @php
                    $reviewers = [
                        ['label' => 'Resident Engineer',   'user' => $concretePouring->residentEngineer,  'date' => $concretePouring->re_date,         'done' => !is_null($concretePouring->re_date)],
                        ['label' => 'Provincial Engineer', 'user' => $concretePouring->notedByEngineer,   'date' => $concretePouring->noted_date,       'done' => !is_null($concretePouring->noted_date)],
                        ['label' => 'ME/MTQA',             'user' => $concretePouring->meMtqaChecker,     'date' => $concretePouring->me_mtqa_date,     'done' => !is_null($concretePouring->me_mtqa_date)],
                    ];
                @endphp
                @foreach ($reviewers as $reviewer)
                    <div class="px-4 py-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-text-secondary">{{ $reviewer['label'] }}</span>
                            @if ($reviewer['done'])
                                <span class="inline-flex items-center gap-1 rounded-full tag-event-approved px-2 py-0.5 text-xs font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                    Done
                                </span>
                            @elseif ($reviewer['user'])
                                <span class="inline-flex items-center rounded-full tag-event-updated px-2 py-0.5 text-xs font-medium">
                                    Pending
                                </span>
                            @else
                                <span class="text-xs text-text-muted">Unassigned</span>
                            @endif
                        </div>
                        @if ($reviewer['user'])
                            <p class="text-sm font-medium text-text-primary">{{ $reviewer['user']->name }}</p>
                            @if ($reviewer['date'])
                                <p class="text-xs text-text-tertiary mt-0.5">
                                    Reviewed {{ \Carbon\Carbon::parse($reviewer['date'])->format('M d, Y') }}
                                </p>
                            @endif
                        @else
                            <p class="text-sm text-text-muted italic">Not assigned</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Outcome Card --}}
        @if (in_array($concretePouring->status, ['approved', 'disapproved']))
            @php
                $outcomeTag = $concretePouring->status === 'approved' 
                    ? 'tag-event-approved' 
                    : 'tag-event-disapproved';
            @endphp
            <div class="rounded-xl ring-1 shadow-token-sm overflow-hidden {{ $outcomeTag }} px-4 py-4">
                <div class="flex items-center gap-2 mb-2">
                    @if ($concretePouring->status === 'approved')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-semibold">Approved</span>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-semibold">Disapproved</span>
                    @endif
                </div>
                @php $finalUser = $concretePouring->status === 'approved' ? $concretePouring->approver : $concretePouring->disapprover; @endphp
                @if ($finalUser)
                    <p class="text-xs">By <span class="font-medium">{{ $finalUser->name }}</span></p>
                @endif
                @if ($concretePouring->approval_remarks)
                    <p class="mt-2 text-xs italic rounded bg-surface-primary/60 px-2 py-1.5">
                        "{{ $concretePouring->approval_remarks }}"
                    </p>
                @endif
            </div>
        @endif

    </div>

    {{-- ── RIGHT: Timeline ─────────────────────────────────────────────────── --}}
    <div class="xl:col-span-2">
        <div class="rounded-xl bg-surface-primary ring-1 ring-border-primary shadow-token-sm overflow-hidden">

            {{-- Timeline Header --}}
            <div class="border-b border-border-primary px-5 py-4 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-text-tertiary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-text-primary">Full Activity Timeline</h2>
                </div>
                <span class="text-xs text-text-tertiary">
                    {{ $logs->count() }} event(s) &bull;
                    Newest first
                </span>
            </div>

            <div class="px-5 py-5">
                @if ($logs->isEmpty())
                    <div class="py-16 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-text-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        <p class="mt-3 text-sm text-text-muted">No activity recorded yet.</p>
                    </div>
                @else
                    <ol class="relative border-l-2 border-border-primary ml-3 space-y-0">
                        @foreach ($logs as $log)
                            <li class="mb-1 ml-7 group">
                                {{-- Timeline icon dot --}}
                                @php
                                    $iconBg = match($log->event) {
                                        'approved'       => 'bg-status-success-dot',
                                        'disapproved'    => 'bg-status-error-dot',
                                        'assigned'       => 'bg-status-info-dot',
                                        'submitted'      => 'bg-status-info-dot',
                                        default          => 'bg-text-tertiary',
                                    };
                                @endphp
                                <span class="absolute -left-[18px] flex h-9 w-9 items-center justify-center rounded-full ring-4 ring-surface-primary shadow {{ $iconBg }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4 text-white"
                                         fill="none" viewBox="0 0 24 24"
                                         stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </span>

                                {{-- Event card --}}
                                <div class="mb-5 ml-2 rounded-xl border border-border-primary bg-surface-primary p-4 shadow-token-sm transition-all group-hover:shadow-token-md group-hover:border-action-primary">

                                    {{-- Top row: badge + actor + time --}}
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <x-badge-event :event="$log->event" />
                                            <div class="flex items-center gap-1.5">
                                                <x-avatar :name="$log->actor_name" size="sm" />
                                                <span class="text-xs font-medium text-text-primary">{{ $log->actor_name }}</span>
                                                <x-badge-role :role="$log->actor_role" />
                                            </div>
                                        </div>
                                        <time class="text-xs text-text-tertiary shrink-0"
                                              title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                            {{ $log->created_at->format('M d, Y') }}
                                            &bull;
                                            {{ $log->created_at->format('g:i A') }}
                                            <span class="hidden sm:inline">&bull; {{ $log->created_at->diffForHumans() }}</span>
                                        </time>
                                    </div>

                                    {{-- Description --}}
                                    @if ($log->description)
                                        <p class="text-sm text-text-primary leading-relaxed">
                                            {{ $log->description }}
                                        </p>
                                    @endif

                                    {{-- Review Step + Status Transition row --}}
                                    @if ($log->review_step || $log->status_from || $log->status_to)
                                        <div class="mt-2 flex flex-wrap items-center gap-2">
                                            @if ($log->review_step)
                                                <span class="inline-flex items-center gap-1 rounded-md bg-surface-secondary px-2 py-0.5 text-xs text-text-secondary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                                                    </svg>
                                                    Step: {{ ucwords(str_replace('_', ' ', $log->review_step)) }}
                                                </span>
                                            @endif

                                            @if ($log->status_from || $log->status_to)
                                                <div class="flex items-center gap-1.5 text-xs">
                                                    @if ($log->status_from)
                                                        <span class="rounded bg-surface-secondary px-1.5 py-0.5 text-text-secondary capitalize">
                                                            {{ $log->status_from }}
                                                        </span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-text-tertiary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                                        </svg>
                                                    @endif
                                                    @if ($log->status_to)
                                                        @php
                                                            $statusBadgeClass = match($log->status_to) {
                                                                'approved'    => 'tag-event-approved',
                                                                'disapproved' => 'tag-event-disapproved',
                                                                default       => 'tag-event-status-changed',
                                                            };
                                                        @endphp
                                                        <span class="rounded px-1.5 py-0.5 capitalize font-medium badge {{ $statusBadgeClass }}">
                                                            {{ $log->status_to }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Freeform note --}}
                                    @if ($log->note)
                                        <div class="mt-3 rounded-lg bg-surface-secondary border border-border-primary px-3 py-2.5 flex gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-text-tertiary shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                                            </svg>
                                            <p class="text-xs text-text-secondary italic leading-relaxed">{{ $log->note }}</p>
                                        </div>
                                    @endif

                                    {{-- Changes diff (collapsible) --}}
                                    @if (!empty($log->changes))
                                        <details class="mt-3">
                                            <summary class="cursor-pointer select-none inline-flex items-center gap-1.5 text-xs text-action-primary hover:text-action-primary-hover font-medium transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                                </svg>
                                                {{ count($log->changes) }} field(s) changed — click to expand
                                            </summary>
                                            <div class="mt-2 overflow-x-auto rounded-lg border border-border-primary">
                                                <table class="min-w-full text-xs">
                                                    <thead class="bg-surface-secondary">
                                                        <tr>
                                                            <th class="px-3 py-2 text-left font-semibold text-text-secondary">Field</th>
                                                            <th class="px-3 py-2 text-left font-semibold text-text-secondary">Before</th>
                                                            <th class="px-3 py-2 text-left font-semibold text-text-secondary">After</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-border-primary bg-surface-primary">
                                                        @foreach ($log->changes as $field => [$old, $new])
                                                            <tr class="hover:bg-surface-secondary">
                                                                <td class="px-3 py-1.5 font-mono text-text-secondary capitalize">
                                                                    {{ str_replace('_', ' ', $field) }}
                                                                </td>
                                                                <td class="px-3 py-1.5 text-status-error-text line-through">
                                                                    {{ is_bool($old) ? ($old ? 'Yes' : 'No') : ($old ?? '—') }}
                                                                </td>
                                                                <td class="px-3 py-1.5 text-status-success-text font-semibold">
                                                                    {{ is_bool($new) ? ($new ? 'Yes' : 'No') : ($new ?? '—') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </details>
                                    @endif

                                    {{-- Technical details --}}
                                    @if ($log->ip_address)
                                        <details class="mt-2">
                                            <summary class="cursor-pointer select-none text-[11px] text-text-tertiary hover:text-text-secondary transition-colors">
                                                Technical details
                                            </summary>
                                            <p class="mt-1 text-[11px] font-mono text-text-tertiary">
                                                IP: {{ $log->ip_address }}
                                            </p>
                                        </details>
                                    @endif
                                </div>
                            </li>
                        @endforeach

                        {{-- Timeline end marker --}}
                        <li class="ml-7">
                            <span class="absolute -left-[14px] flex h-7 w-7 items-center justify-center rounded-full bg-surface-secondary ring-4 ring-surface-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-text-tertiary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <div class="ml-2 pb-2 text-xs text-text-tertiary italic pt-1">
                                Request created {{ $concretePouring->created_at->format('M d, Y \a\t g:i A') }}
                            </div>
                        </li>
                    </ol>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
