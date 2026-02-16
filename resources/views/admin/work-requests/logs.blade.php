{{-- resources/views/work-requests/logs.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">
                Activity Log — {{ $workRequest->project_name }}
            </h2>
            <a href="{{ route('admin.work-requests.show', $workRequest) }}"
               class="text-sm text-indigo-600 hover:text-indigo-800">
                ← Back to Request
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Summary bar --}}
            <div class="mb-6 p-4 bg-white rounded-lg shadow-sm border border-gray-200 flex flex-wrap gap-4 text-sm text-gray-600">
                <span><strong>Project:</strong> {{ $workRequest->name_of_project }}</span>
                <span><strong>Location:</strong> {{ $workRequest->project_location }}</span>
                <span>
                    <strong>Status:</strong>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        {{ ucfirst($workRequest->status) }}
                    </span>
                </span>
                <span><strong>Total Events:</strong> {{ $logs->total() }}</span>
            </div>

            {{-- Timeline --}}
            @if ($logs->isEmpty())
                <p class="text-center text-gray-500 py-12">No activity recorded yet.</p>
            @else
                <ol class="relative border-l border-gray-200 ml-4 space-y-0">
                    @foreach ($logs as $log)
                        @php
                            $colors = [
                                'blue'   => ['dot' => 'bg-blue-500',   'badge' => 'bg-blue-100 text-blue-800'],
                                'yellow' => ['dot' => 'bg-yellow-400', 'badge' => 'bg-yellow-100 text-yellow-800'],
                                'indigo' => ['dot' => 'bg-indigo-500', 'badge' => 'bg-indigo-100 text-indigo-800'],
                                'cyan'   => ['dot' => 'bg-cyan-500',   'badge' => 'bg-cyan-100 text-cyan-800'],
                                'purple' => ['dot' => 'bg-purple-500', 'badge' => 'bg-purple-100 text-purple-800'],
                                'green'  => ['dot' => 'bg-green-500',  'badge' => 'bg-green-100 text-green-800'],
                                'red'    => ['dot' => 'bg-red-500',    'badge' => 'bg-red-100 text-red-800'],
                                'gray'   => ['dot' => 'bg-gray-400',   'badge' => 'bg-gray-100 text-gray-700'],
                                'teal'   => ['dot' => 'bg-teal-500',   'badge' => 'bg-teal-100 text-teal-800'],
                            ];
                            $color  = $colors[$log->event_color] ?? $colors['gray'];
                        @endphp

                        <li class="mb-6 ml-6">
                            {{-- Dot --}}
                            <span class="absolute -left-[9px] flex h-4 w-4 items-center justify-center rounded-full ring-4 ring-white {{ $color['dot'] }}"></span>

                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                                {{-- Header row --}}
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $color['badge'] }}">
                                        {{ $log->event_label }}
                                    </span>
                                    <time class="text-xs text-gray-400" datetime="{{ $log->created_at->toIso8601String() }}">
                                        {{ $log->created_at->format('M d, Y  H:i') }}
                                    </time>
                                </div>

                                {{-- Actor --}}
                                @if ($log->employee)
                                    <p class="text-xs text-gray-500 mb-1">
                                        By <span class="font-medium text-gray-700">{{ $log->employee->full_name ?? $log->employee->name }}</span>
                                    </p>
                                @endif

                                {{-- Description --}}
                                @if ($log->description)
                                    <p class="text-sm text-gray-700">{{ $log->description }}</p>
                                @endif

                                {{-- Status change pill --}}
                                @if ($log->status_from && $log->status_to && $log->status_from !== $log->status_to)
                                    <div class="mt-2 flex items-center gap-2 text-xs">
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600">{{ ucfirst($log->status_from) }}</span>
                                        <span class="text-gray-400">→</span>
                                        <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-700">{{ ucfirst($log->status_to) }}</span>
                                    </div>
                                @endif

                                {{-- Note --}}
                                @if ($log->note)
                                    <p class="mt-2 text-xs italic text-gray-500 border-l-2 border-gray-200 pl-2">{{ $log->note }}</p>
                                @endif

                                {{-- Field-level changes --}}
                                @if (!empty($log->changes))
                                    <details class="mt-3">
                                        <summary class="cursor-pointer text-xs text-indigo-600 hover:text-indigo-800 select-none">
                                            {{ count($log->changes) }} field(s) changed
                                        </summary>
                                        <div class="mt-2 overflow-x-auto">
                                            <table class="min-w-full text-xs border border-gray-200 rounded">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-3 py-1.5 text-left font-semibold text-gray-600">Field</th>
                                                        <th class="px-3 py-1.5 text-left font-semibold text-gray-600">Before</th>
                                                        <th class="px-3 py-1.5 text-left font-semibold text-gray-600">After</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @foreach ($log->changes as $field => [$old, $new])
                                                        <tr>
                                                            <td class="px-3 py-1.5 font-mono text-gray-700">{{ $field }}</td>
                                                            <td class="px-3 py-1.5 text-red-600 line-through">{{ $old ?? '—' }}</td>
                                                            <td class="px-3 py-1.5 text-green-700">{{ $new ?? '—' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </details>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $logs->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>