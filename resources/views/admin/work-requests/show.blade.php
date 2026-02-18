<x-app-layout>
    @error('error')
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ $message }}</span>
        </div>
    @enderror
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Work Request #' . $workRequest->id) }}
            </h2>
            <div class="flex gap-2">
                @if($workRequest->canEdit())
                    <a href="{{ route('admin.work-requests.edit', $workRequest) }}" 
                       class="inline-flex items-center px-4 py-2 bg-orange-600 dark:bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-orange-600 focus:bg-orange-700 dark:focus:bg-orange-600 active:bg-orange-900 dark:active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i>
                        {{ __('Edit') }}
                    </a>
                @endif
                <a href="{{ route('admin.work-requests.print', $workRequest) }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 dark:bg-purple-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 dark:hover:bg-purple-600 focus:bg-purple-700 dark:focus:bg-purple-600 active:bg-purple-900 dark:active:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" 
                   target="_blank">
                    <i class="fas fa-print mr-2"></i>
                    {{ __('Print') }}
                </a>
                <a href="{{ route('admin.work-requests.download', $workRequest) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:bg-green-700 dark:focus:bg-green-600 active:bg-green-900 dark:active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-download mr-2"></i>
                    {{ __('Download') }}
                </a>
                <a href="{{ route('admin.work-requests.export-excel', $workRequest) }}"
                class="btn btn-success">
                    Download Excel
                </a>
                @if($workRequest->canEdit())
                    <form action="{{ route('admin.work-requests.destroy', $workRequest) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this work request?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-900 dark:active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <i class="fas fa-trash mr-2"></i>
                            {{ __('Delete') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @php
            $statusColors = [
                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                'submitted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                'inspected' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                'reviewed' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'accepted' => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300',
                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            ];
            $colorClass = $statusColors[$workRequest->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
        @endphp

        <div class="mb-6">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                {{ ucfirst($workRequest->status) }}
            </span>
        </div>

        <!-- Project Information -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Project Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Name</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->name_of_project ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Location</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->project_location ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">For Office</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->for_office ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Requester</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->from_requester ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Details -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Request Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Requested By</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->requested_by ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Work Start Date</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->requested_work_start_date?->format('Y-m-d') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Work Start Time</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->requested_work_start_time ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pay Item Details -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Pay Item Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Item No.</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->item_no ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Equipment to be Used</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->equipment_to_be_used ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estimated Quantity</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->estimated_quantity ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->unit ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->description ?? 'N/A' }}</p>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description of Work Requested</label>
                    <p class="text-gray-900 dark:text-gray-100 mt-1 whitespace-pre-wrap">{{ $workRequest->description_of_work_requested ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Submission -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Submission</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Submitted By</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->submitted_by ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Submitted Date</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->submitted_date?->format('Y-m-d') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contractor Name</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->contractor_name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inspection -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Inspection</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Inspected By (Site Inspector)</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->inspected_by_site_inspector ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site Inspector Signature</label>
                        @if($workRequest->site_inspector_signature)
                            <img src="{{ $workRequest->site_inspector_signature }}" alt="Signature" class="h-16 mt-1">
                        @else
                            <p class="text-gray-900 dark:text-gray-100 mt-1">N/A</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Surveyor Name</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->surveyor_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Surveyor Signature</label>
                        @if($workRequest->surveyor_signature)
                            <img src="{{ $workRequest->surveyor_signature }}" alt="Signature" class="h-16 mt-1">
                        @else
                            <p class="text-gray-900 dark:text-gray-100 mt-1">N/A</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Resident Engineer Name</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->resident_engineer_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Resident Engineer Signature</label>
                        @if($workRequest->resident_engineer_signature)
                            <img src="{{ $workRequest->resident_engineer_signature }}" alt="Signature" class="h-16 mt-1">
                        @else
                            <p class="text-gray-900 dark:text-gray-100 mt-1">N/A</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Findings and Recommendations -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Findings and Recommendations</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Findings/Comments</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1 whitespace-pre-wrap">{{ $workRequest->findings_comments ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recommendation</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1 whitespace-pre-wrap">{{ $workRequest->recommendation ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recommended Action</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1 whitespace-pre-wrap">{{ $workRequest->recommended_action ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review and Approval -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Review and Approval</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Checked By MTQA</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->checked_by_mtqa ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">MTQA Signature</label>
                        @if($workRequest->mtqa_signature)
                            <img src="{{ $workRequest->mtqa_signature }}" alt="Signature" class="h-16 mt-1">
                        @else
                            <p class="text-gray-900 dark:text-gray-100 mt-1">N/A</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reviewed By</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->reviewed_by ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reviewer Designation</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->reviewer_designation ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recommending Approval By</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->recommending_approval_by ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recommending Approval Designation</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->recommending_approval_designation ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recommending Approval Signature</label>
                        @if($workRequest->recommending_approval_signature)
                            <img src="{{ $workRequest->recommending_approval_signature }}" alt="Signature" class="h-16 mt-1">
                        @else
                            <p class="text-gray-900 dark:text-gray-100 mt-1">N/A</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved By</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->approved_by ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved By Designation</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->approved_by_designation ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approval Signature</label>
                        @if($workRequest->approved_signature)
                            <img src="{{ $workRequest->approved_signature }}" alt="Signature" class="h-16 mt-1">
                        @else
                            <p class="text-gray-900 dark:text-gray-100 mt-1">N/A</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Acceptance -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Acceptance</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Accepted By Contractor</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->accepted_by_contractor ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Accepted Date</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->accepted_date?->format('Y-m-d') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Accepted Time</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->accepted_time ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Received By</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->received_by ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Received Date</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->received_date?->format('Y-m-d') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Received Time</label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">{{ $workRequest->received_time ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>


        <!-- Additional Information -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Additional Information</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <p class="text-gray-900 dark:text-gray-100 mt-1 whitespace-pre-wrap">{{ $workRequest->notes ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="bg-gray-50 dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Created: {{ $workRequest->created_at?->format('Y-m-d H:i:s') }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400">Last Updated: {{ $workRequest->updated_at?->format('Y-m-d H:i:s') }}</p>
        </div>

        <!-- Back Button -->
        <div class="mt-6">
            <a href="{{ route('admin.work-requests.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Back to Work Requests') }}
            </a>
        </div>
        </div>
    </div>
</x-app-layout>
