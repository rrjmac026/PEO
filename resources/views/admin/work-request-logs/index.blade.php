@extends('layouts.app')

@section('title', 'Work Request Logs')

@section('content')
    <!-- Header with Title -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Work Request Logs</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Track and monitor all work request activities and changes</p>
            </div>
        </div>
    </div>

    <!-- Session Messages -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <h3 class="text-red-800 dark:text-red-400 font-semibold mb-2">Errors</h3>
            <ul class="list-disc list-inside text-red-700 dark:text-red-300 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
            <span class="text-green-800 dark:text-green-400">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('admin.work-request-logs.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Employee Filter -->
                <div>
                    <label for="employee_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Filter by Employee
                    </label>
                    <select name="employee_id" 
                            id="employee_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">All Employees</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" 
                                    {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Event Filter -->
                <div>
                    <label for="event" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Filter by Event
                    </label>
                    <select name="event" 
                            id="event"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">All Events</option>
                        @foreach ($events as $event)
                            <option value="{{ $event }}" 
                                    {{ request('event') == $event ? 'selected' : '' }}>
                                {{ $eventLabels[$event] ?? ucfirst(str_replace('_', ' ', $event)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition ease-in-out duration-150 flex items-center justify-center gap-2">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                </div>
            </div>

            <!-- Clear Filters Button -->
            @if (request('employee_id') || request('event'))
                <div class="flex justify-start">
                    <a href="{{ route('admin.work-request-logs.index') }}" 
                       class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition ease-in-out duration-150 text-sm font-semibold">
                        <i class="fas fa-times mr-2"></i> Clear Filters
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Work Request Logs Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        @if ($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Work Request</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Change</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                                <!-- Work Request Link -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($log->workRequest)
                                        <a href="{{ route('admin.work-requests.show', $log->workRequest) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded text-xs font-semibold hover:bg-blue-200 dark:hover:bg-blue-800 transition ease-in-out duration-150">
                                            <span class="font-mono">#{{ $log->workRequest->id }}</span>
                                            <i class="fas fa-external-link-alt ml-2"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm italic">Deleted Request</span>
                                    @endif
                                </td>

                                <!-- Employee -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($log->employee)
                                        <div class="flex items-center gap-2">
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-sm">
                                                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">
                                                    {{ substr($log->employee->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <a href="{{ route('admin.employees.show', $log->employee) }}" 
                                               class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">
                                                {{ $log->employee->user->name }}
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm italic">Unknown User</span>
                                    @endif
                                </td>

                                <!-- Event Type -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $eventColors = [
                                            'created'        => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            'updated'        => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                            'status_changed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                            'submitted'      => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                                            'inspected'      => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                            'reviewed'       => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                                            'approved'       => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            'rejected'       => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            'accepted'       => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300',
                                            'deleted'        => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            'restored'       => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        ];
                                        $colorClass = $eventColors[$log->event] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                                    @endphp
                                    <span class="inline-block px-3 py-1 {{ $colorClass }} text-xs font-semibold rounded-full">
                                        {{ $log->getEventLabelAttribute() }}
                                    </span>
                                </td>

                                <!-- Status Change -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($log->status_from || $log->status_to)
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="font-mono text-gray-600 dark:text-gray-400">
                                                {{ $log->status_from ? ucfirst($log->status_from) : '-' }}
                                            </span>
                                            <i class="fas fa-arrow-right text-gray-400"></i>
                                            <span class="font-mono text-gray-600 dark:text-gray-400">
                                                {{ $log->status_to ? ucfirst($log->status_to) : '-' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>

                                <!-- Date & Time -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col text-sm">
                                        <span class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $log->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="text-gray-500 dark:text-gray-400 text-xs">
                                            {{ $log->created_at->format('h:i A') }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex gap-2">
                                        @if ($log->description || $log->note || $log->changes)
                                            <button onclick="toggleDetails(event, {{ $log->id }})" 
                                                    class="inline-flex items-center px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded text-xs font-semibold hover:bg-indigo-200 dark:hover:bg-indigo-800 transition ease-in-out duration-150"
                                                    title="View Details">
                                                <i class="fas fa-expand-alt"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Expandable Details Row -->
                            @if ($log->description || $log->note || $log->changes)
                                <tr id="details-{{ $log->id }}" class="hidden bg-gray-50 dark:bg-gray-900">
                                    <td colspan="6" class="px-6 py-4">
                                        <div class="space-y-3 text-sm">
                                            @if ($log->description)
                                                <div>
                                                    <span class="font-semibold text-gray-700 dark:text-gray-300">Description:</span>
                                                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $log->description }}</p>
                                                </div>
                                            @endif

                                            @if ($log->note)
                                                <div>
                                                    <span class="font-semibold text-gray-700 dark:text-gray-300">Note:</span>
                                                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $log->note }}</p>
                                                </div>
                                            @endif

                                            @if ($log->changes)
                                                <div>
                                                    <span class="font-semibold text-gray-700 dark:text-gray-300">Changes:</span>
                                                    <div class="mt-1 bg-white dark:bg-gray-800 rounded p-2 font-mono text-xs overflow-x-auto">
                                                        <pre>{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($log->ip_address)
                                                <div class="text-gray-500 dark:text-gray-400">
                                                    <span class="font-semibold">IP Address:</span> {{ $log->ip_address }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $logs->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400 text-lg">No work request logs found</p>
                <p class="text-gray-500 dark:text-gray-500 text-sm mt-2">
                    @if (request('employee_id') || request('event'))
                        Try adjusting your filters
                    @else
                        Work request activities will appear here
                    @endif
                </p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function toggleDetails(event, logId) {
            event.preventDefault();
            const detailsRow = document.getElementById('details-' + logId);
            if (detailsRow) {
                detailsRow.classList.toggle('hidden');
                const button = event.target.closest('button');
                button.querySelector('i').classList.toggle('fa-expand-alt');
                button.querySelector('i').classList.toggle('fa-compress-alt');
            }
        }
    </script>
@endpush
