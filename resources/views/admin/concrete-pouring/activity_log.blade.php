{{-- ============================================================
     resources/views/admin/concrete-pouring/_activity_log.blade.php
     Drop this partial inside your show.blade.php:
       @include('admin.concrete-pouring._activity_log', ['concretePouring' => $concretePouring])
     ============================================================ --}}

@php
    $logs = $concretePouring->logs ?? collect();

    $colorMap = [
        'submitted'      => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'ring' => 'ring-blue-400',   'dot' => 'bg-blue-500'],
        'updated'        => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'ring' => 'ring-yellow-400', 'dot' => 'bg-yellow-500'],
        'deleted'        => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'ring' => 'ring-red-400',    'dot' => 'bg-red-500'],
        'assigned'       => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'ring' => 'ring-indigo-400', 'dot' => 'bg-indigo-500'],
        're_reviewed'    => ['bg' => 'bg-cyan-100',   'text' => 'text-cyan-700',   'ring' => 'ring-cyan-400',   'dot' => 'bg-cyan-500'],
        'pe_noted'       => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'ring' => 'ring-purple-400', 'dot' => 'bg-purple-500'],
        'mtqa_decided'   => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'ring' => 'ring-orange-400', 'dot' => 'bg-orange-500'],
        'approved'       => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'ring' => 'ring-green-400',  'dot' => 'bg-green-500'],
        'disapproved'    => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'ring' => 'ring-red-400',    'dot' => 'bg-red-500'],
        'status_changed' => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'ring' => 'ring-gray-300',   'dot' => 'bg-gray-400'],
    ];

    $iconMap = [
        'submitted'      => 'M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5',
        'updated'        => 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z',
        'deleted'        => 'M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0',
        'assigned'       => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
        're_reviewed'    => 'M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75',
        'pe_noted'       => 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z',
        'mtqa_decided'   => 'M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.97zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.97z',
        'approved'       => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'disapproved'    => 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'status_changed' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99',
    ];
@endphp

<div class="mt-8">
    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Activity Log
        </h3>
        <span class="text-xs text-gray-400">{{ $logs->count() }} event(s)</span>
    </div>

    @if ($logs->isEmpty())
        <div class="rounded-lg border border-dashed border-gray-200 bg-gray-50 py-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-gray-300" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <p class="mt-2 text-sm text-gray-400">No activity recorded yet.</p>
        </div>
    @else
        <ol class="relative border-l border-gray-200 ml-3 space-y-0">
            @foreach ($logs as $log)
                @php
                    $colors  = $colorMap[$log->event]  ?? $colorMap['status_changed'];
                    $iconPath = $iconMap[$log->event]  ?? $iconMap['status_changed'];
                @endphp

                <li class="mb-0 ml-6 group">
                    {{-- ── Timeline dot ────────────────────────────────── --}}
                    <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full
                                 ring-4 ring-white {{ $colors['dot'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-3.5 w-3.5 text-white"
                             fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/>
                        </svg>
                    </span>

                    {{-- ── Card ────────────────────────────────────────── --}}
                    <div class="ml-2 mb-4 rounded-lg border border-gray-100 bg-white p-4 shadow-sm
                                transition-shadow group-hover:shadow-md">

                        {{-- Top row: badge + timestamp --}}
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5
                                         text-xs font-medium {{ $colors['bg'] }} {{ $colors['text'] }}">
                                {{ $log->event_label }}
                            </span>
                            <time class="text-xs text-gray-400"
                                  title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                {{ $log->created_at->diffForHumans() }}
                                &bull;
                                {{ $log->created_at->format('M d, Y g:i A') }}
                            </time>
                        </div>

                        {{-- Description --}}
                        @if ($log->description)
                            <p class="text-sm text-gray-700 leading-relaxed">
                                {{ $log->description }}
                            </p>
                        @endif

                        {{-- Actor info --}}
                        <div class="mt-2 flex items-center gap-2">
                            <div class="h-5 w-5 rounded-full bg-gray-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-500"
                                     viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-500">
                                <span class="font-medium text-gray-700">{{ $log->actor_name }}</span>
                                &bull;
                                <span class="italic">{{ $log->actor_role }}</span>
                            </span>
                        </div>

                        {{-- Review step badge --}}
                        @if ($log->review_step)
                            <div class="mt-2">
                                <span class="inline-flex items-center gap-1 rounded bg-gray-100
                                             px-1.5 py-0.5 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                                    </svg>
                                    Step: {{ ucwords(str_replace('_', ' ', $log->review_step)) }}
                                </span>
                            </div>
                        @endif

                        {{-- Status transition --}}
                        @if ($log->status_from || $log->status_to)
                            <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-500">
                                @if ($log->status_from)
                                    <span class="rounded bg-gray-100 px-1.5 py-0.5 capitalize">
                                        {{ $log->status_from }}
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                @endif
                                @if ($log->status_to)
                                    <span class="rounded px-1.5 py-0.5 capitalize font-medium
                                        {{ $log->status_to === 'approved'    ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $log->status_to === 'disapproved' ? 'bg-red-100 text-red-700'     : '' }}
                                        {{ !in_array($log->status_to, ['approved','disapproved']) ? 'bg-gray-100 text-gray-600' : '' }}">
                                        {{ $log->status_to }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Optional freeform note --}}
                        @if ($log->note)
                            <div class="mt-2 rounded bg-gray-50 border border-gray-100 px-3 py-2 text-xs text-gray-600 italic">
                                "{{ $log->note }}"
                            </div>
                        @endif

                        {{-- Changes diff (collapsible) --}}
                        @if (!empty($log->changes))
                            <details class="mt-2">
                                <summary class="cursor-pointer text-xs text-gray-400 hover:text-gray-600 select-none">
                                    {{ count($log->changes) }} field(s) changed — click to expand
                                </summary>
                                <div class="mt-2 overflow-x-auto rounded border border-gray-100">
                                    <table class="min-w-full text-xs">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-1.5 text-left font-medium text-gray-500">Field</th>
                                                <th class="px-3 py-1.5 text-left font-medium text-gray-500">Before</th>
                                                <th class="px-3 py-1.5 text-left font-medium text-gray-500">After</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 bg-white">
                                            @foreach ($log->changes as $field => [$old, $new])
                                                <tr>
                                                    <td class="px-3 py-1.5 font-mono text-gray-500">
                                                        {{ str_replace('_', ' ', $field) }}
                                                    </td>
                                                    <td class="px-3 py-1.5 text-red-600 line-through">
                                                        {{ is_bool($old) ? ($old ? 'Yes' : 'No') : ($old ?? '—') }}
                                                    </td>
                                                    <td class="px-3 py-1.5 text-green-600 font-medium">
                                                        {{ is_bool($new) ? ($new ? 'Yes' : 'No') : ($new ?? '—') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        @endif

                        {{-- IP / Agent (admin-only detail, collapsed) --}}
                        @if ($log->ip_address)
                            <details class="mt-1">
                                <summary class="cursor-pointer text-xs text-gray-300 hover:text-gray-400 select-none">
                                    Technical details
                                </summary>
                                <p class="mt-1 text-xs text-gray-400 font-mono">
                                    IP: {{ $log->ip_address }}
                                </p>
                            </details>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    @endif
</div>
