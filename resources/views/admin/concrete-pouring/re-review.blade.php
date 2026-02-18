<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Resident Engineer Review - {{ $concretePouring->project_name }}
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pouring Date</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $concretePouring->pouring_datetime?->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Review Form --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Resident Engineer Review Form</h3>
                    
                    <form action="{{ route('admin.concrete-pouring.store-re-review', $concretePouring) }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label for="re_remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Review Remarks
                            </label>
                            <textarea id="re_remarks" 
                                      name="re_remarks" 
                                      rows="8"
                                      placeholder="Enter your remarks or findings from the Resident Engineer review..."
                                      class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm">{{ old('re_remarks') }}</textarea>
                            @error('re_remarks')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 bg-orange-600 dark:bg-orange-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-check-circle mr-2"></i>
                                Submit Review
                            </button>
                            <a href="{{ route('admin.concrete-pouring.show', $concretePouring) }}" 
                               class="inline-flex items-center px-6 py-3 bg-gray-600 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
