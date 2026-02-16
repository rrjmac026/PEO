<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Work Request Details') }}
            </h2>
            <div class="flex gap-2">
                @if($workRequest->canEdit())
                    <a href="{{ route('user.work-requests.edit', $workRequest) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i>
                        {{ __('Edit') }}
                    </a>
                @endif
                <a href="{{ route('user.work-requests.print', $workRequest) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-print mr-2"></i>
                    {{ __('Print') }}
                </a>
                <a href="{{ route('user.work-requests.download', $workRequest) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-download mr-2"></i>
                    {{ __('PDF') }}
                </a>
                <a href="{{ route('user.work-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Badge -->
            <div class="mb-6">
                @php
                    $statusColors = [
                        'draft' => 'gray',
                        'submitted' => 'blue',
                        'inspected' => 'purple',
                        'reviewed' => 'indigo',
                        'approved' => 'green',
                        'accepted' => 'green',
                        'rejected' => 'red',
                    ];
                    $color = $statusColors[$workRequest->status] ?? 'gray';
                @endphp
                <div class="inline-flex px-4 py-2 text-sm font-semibold leading-6 rounded-full bg-{{ $color }}-100 text-{{ $color }}-800 dark:bg-{{ $color }}-900 dark:text-{{ $color }}-200">
                    <i class="fas fa-circle mr-2 text-{{ $color }}-500"></i>
                    {{ ucfirst($workRequest->status) }}
                </div>
            </div>

            <!-- Work Request Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Project Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
                            {{ __('Project Information') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Project Name') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->name_of_project }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Project Location') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->project_location }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('For Office') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->for_office ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('From Requester') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->from_requester ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Request Details -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-8 mb-8">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
                            {{ __('Request Details') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Requested By') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->requested_by }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Start Date') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->requested_work_start_date?->format('M d, Y') ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Start Time') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->requested_work_start_time ?? '-' }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Description of Work Requested') }}</p>
                            <p class="text-base font-medium mt-2 whitespace-pre-wrap">{{ $workRequest->description_of_work_requested }}</p>
                        </div>
                    </div>

                    <!-- Pay Item Details -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-8 mb-8">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
                            {{ __('Pay Item Details') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Item Number') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->item_no ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Equipment to be Used') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->equipment_to_be_used ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Estimated Quantity') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->estimated_quantity ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Unit') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->unit ?? '-' }}</p>
                            </div>
                            @if($workRequest->description)
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Description') }}</p>
                                    <p class="text-base font-medium mt-2 whitespace-pre-wrap">{{ $workRequest->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submission Details -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-8 mb-8">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
                            {{ __('Submission Details') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Contractor Name') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->contractor_name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Submitted Date') }}</p>
                                <p class="text-base font-medium">{{ $workRequest->submitted_date?->format('M d, Y') ?? '-' }}</p>
                            </div>
                            @if($workRequest->notes)
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Additional Notes') }}</p>
                                    <p class="text-base font-medium mt-2 whitespace-pre-wrap">{{ $workRequest->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('Created') }}</p>
                                <p class="text-sm font-medium">{{ $workRequest->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('Last Updated') }}</p>
                                <p class="text-sm font-medium">{{ $workRequest->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('Request ID') }}</p>
                                <p class="text-sm font-medium">#{{ $workRequest->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Logs -->
            @if($workRequest->logs->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
                            {{ __('Activity Log') }}
                        </h3>
                        <div class="space-y-4">
                            @foreach($workRequest->logs as $log)
                                <div class="pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0 last:pb-0">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                {{ ucfirst(str_replace('_', ' ', $log->event)) }}
                                            </p>
                                            @if($log->description)
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $log->description }}</p>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Delete Button (if editable) -->
            @if($workRequest->canEdit())
                <div class="mt-6 flex justify-end">
                    <form action="{{ route('user.work-requests.destroy', $workRequest) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this work request? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <i class="fas fa-trash mr-2"></i>
                            {{ __('Delete Request') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
