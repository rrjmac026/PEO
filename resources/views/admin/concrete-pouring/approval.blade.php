<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Approve/Disapprove - {{ $concretePouring->project_name }}
            </h2>
            <a href="{{ route('admin.concrete-pouring.show', $concretePouring) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white text-xs font-semibold rounded-md hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Error Messages --}}
            @if($errors->any())
                <div class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded relative">
                    <h3 class="font-semibold mb-2">Please fix the following errors:</h3>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Project Information --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Project Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Name</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100 font-semibold">{{ $concretePouring->project_name }}</p>
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estimated Volume</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->estimated_volume }} m³</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pouring Date</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->pouring_datetime?->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Checklist Progress</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->checklist_progress }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Review Summary --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Review Summary</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">ME/MTQA Review</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    @if($concretePouring->me_mtqa_checked_by)
                                        ✓ Completed by {{ $concretePouring->meMtqaChecker?->user?->name }} on {{ $concretePouring->me_mtqa_date?->format('M d, Y') }}
                                    @else
                                        ✗ Not yet completed
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">Resident Engineer Review</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    @if($concretePouring->re_checked_by)
                                        ✓ Completed by {{ $concretePouring->residentEngineer?->user?->name }} on {{ $concretePouring->re_date?->format('M d, Y') }}
                                    @else
                                        ✗ Not yet completed
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Approval Form --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Approve Form --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-green-600 dark:text-green-400 mb-6">
                            <i class="fas fa-check-circle mr-2"></i>Approve Request
                        </h3>
                        
                        <form action="{{ route('admin.concrete-pouring.approve', $concretePouring) }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label for="approval_remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Remarks (Optional)
                                </label>
                                <textarea id="approval_remarks" 
                                          name="approval_remarks" 
                                          rows="6"
                                          placeholder="Enter any remarks or conditions for approval..."
                                          class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 shadow-sm">{{ old('approval_remarks') }}</textarea>
                            </div>

                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-6 py-3 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-check-circle mr-2"></i>
                                Approve
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Disapprove Form --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-6">
                            <i class="fas fa-times-circle mr-2"></i>Disapprove Request
                        </h3>
                        
                        <form action="{{ route('admin.concrete-pouring.disapprove', $concretePouring) }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label for="disapprove_remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Reason for Disapproval <span class="text-red-600">*</span>
                                </label>
                                <textarea id="disapprove_remarks" 
                                          name="approval_remarks" 
                                          rows="6"
                                          placeholder="Enter the reason for disapproving this request..."
                                          class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 dark:focus:border-red-600 focus:ring-red-500 dark:focus:ring-red-600 shadow-sm"
                                          required>{{ old('approval_remarks') }}</textarea>
                                @error('approval_remarks')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-6 py-3 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-times-circle mr-2"></i>
                                Disapprove
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Cancel --}}
            <div class="mt-6 text-center">
                <a href="{{ route('admin.concrete-pouring.show', $concretePouring) }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-600 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Details
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
