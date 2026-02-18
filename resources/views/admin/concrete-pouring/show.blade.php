<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Concrete Pouring Request #{{ $concretePouring->id }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.concrete-pouring.print', $concretePouring) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white text-xs font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </a>
                <a href="{{ route('admin.concrete-pouring.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white text-xs font-semibold rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Status Badge --}}
            <div class="mb-6">
                @switch($concretePouring->status)
                    @case('approved')
                        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">
                            ✓ Approved
                        </span>
                        @break
                    @case('disapproved')
                        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300">
                            ✗ Disapproved
                        </span>
                        @break
                    @default
                        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300">
                            ⏱ Pending
                        </span>
                @endswitch
            </div>

            {{-- Project Information --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Project Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Name</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->project_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->location }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contractor</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->contractor }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Part of Structure</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->part_of_structure }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Station Limits/Section</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->station_limits_section }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estimated Volume (m³)</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->estimated_volume }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pouring Date & Time</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->pouring_datetime?->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Requested By</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $concretePouring->requestedBy?->user?->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Checklist Progress --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Checklist Progress</h3>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $concretePouring->checklist_progress }}%"></div>
                            </div>
                        </div>
                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $concretePouring->checklist_progress }}%</span>
                    </div>
                </div>
            </div>

            {{-- ME/MTQA Section --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">ME/MTQA Review</h3>
                        @if(!$concretePouring->me_mtqa_checked_by)
                            <a href="{{ route('admin.concrete-pouring.me-mtqa-review-form', $concretePouring) }}" 
                               class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-xs font-semibold rounded-md hover:bg-orange-700">
                                <i class="fas fa-edit mr-2"></i>
                                Add Review
                            </a>
                        @endif
                    </div>
                    @if($concretePouring->me_mtqa_checked_by)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reviewed By</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->meMtqaChecker?->user?->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Review Date</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->me_mtqa_date?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remarks</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->me_mtqa_remarks ?? 'No remarks' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">Not yet reviewed</p>
                    @endif
                </div>
            </div>

            {{-- Resident Engineer Section --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Resident Engineer Review</h3>
                        @if(!$concretePouring->re_checked_by)
                            <a href="{{ route('admin.concrete-pouring.re-review-form', $concretePouring) }}" 
                               class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-xs font-semibold rounded-md hover:bg-orange-700">
                                <i class="fas fa-edit mr-2"></i>
                                Add Review
                            </a>
                        @endif
                    </div>
                    @if($concretePouring->re_checked_by)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reviewed By</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->residentEngineer?->user?->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Review Date</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->re_date?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remarks</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->re_remarks ?? 'No remarks' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">Not yet reviewed</p>
                    @endif
                </div>
            </div>

            {{-- Approval Section --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Approval</h3>
                        @if($concretePouring->status === 'requested')
                            <a href="{{ route('admin.concrete-pouring.approval-form', $concretePouring) }}" 
                               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-xs font-semibold rounded-md hover:bg-purple-700">
                                <i class="fas fa-check-circle mr-2"></i>
                                Review for Approval
                            </a>
                        @endif
                    </div>

                    @if($concretePouring->status === 'approved')
                        <div class="border-l-4 border-green-500 p-4 bg-green-50 dark:bg-green-900/20">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved By</label>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100 font-semibold">{{ $concretePouring->approver?->user?->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approval Date</label>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->approved_date?->format('M d, Y') ?? 'N/A' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remarks</label>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->approval_remarks ?? 'No remarks' }}</p>
                                </div>
                            </div>
                        </div>
                    @elseif($concretePouring->status === 'disapproved')
                        <div class="border-l-4 border-red-500 p-4 bg-red-50 dark:bg-red-900/20">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Disapproved By</label>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100 font-semibold">{{ $concretePouring->disapprover?->user?->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Disapproval Date</label>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->disapproved_date?->format('M d, Y') ?? 'N/A' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason</label>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->approval_remarks ?? 'No remarks' }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">Pending approval</p>
                    @endif
                </div>
            </div>

            {{-- Provincial Engineer Note Section --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Provincial Engineer Note</h3>
                        @if(!$concretePouring->noted_by)
                            <form action="{{ route('admin.concrete-pouring.add-note', $concretePouring) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Add Note
                                </button>
                            </form>
                        @endif
                    </div>

                    @if($concretePouring->noted_by)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Noted By</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->notedByEngineer?->user?->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Note Date</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->noted_date?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">Not yet noted</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
