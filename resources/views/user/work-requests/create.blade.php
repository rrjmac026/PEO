<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create New Work Request') }}
            </h2>
            <a href="{{ route('user.work-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('user.work-requests.store') }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            <!-- Project Information Section -->
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
                                    {{ __('Project Information') }}
                                </h3>

                                <!-- Project Name -->
                                <div class="mb-4">
                                    <label for="name_of_project" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Project Name') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name_of_project" id="name_of_project" 
                                        value="{{ old('name_of_project') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('name_of_project') border-red-500 @enderror">
                                    @error('name_of_project')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Project Location -->
                                <div class="mb-4">
                                    <label for="project_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Project Location') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="project_location" id="project_location" 
                                        value="{{ old('project_location') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('project_location') border-red-500 @enderror">
                                    @error('project_location')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- For Office -->
                                <div class="mb-4">
                                    <label for="for_office" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('For Office') }}
                                    </label>
                                    <input type="text" name="for_office" id="for_office" 
                                        value="{{ old('for_office') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    @error('for_office')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- From Requester -->
                                <div class="mb-4">
                                    <label for="from_requester" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('From Requester') }}
                                    </label>
                                    <input type="text" name="from_requester" id="from_requester" 
                                        value="{{ old('from_requester') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    @error('from_requester')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Request Details Section -->
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
                                    {{ __('Request Details') }}
                                </h3>

                                <!-- Requested By (Auto-filled) -->
                                <div class="mb-4">
                                    <label for="requested_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Requested By') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="requested_by" id="requested_by" 
                                        value="{{ old('requested_by', Auth::user()->name) }}" readonly
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-gray-100 dark:bg-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:text-gray-300">
                                    @error('requested_by')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Requested Work Start Date -->
                                <div class="mb-4">
                                    <label for="requested_work_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Requested Work Start Date') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="requested_work_start_date" id="requested_work_start_date" 
                                        value="{{ old('requested_work_start_date') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('requested_work_start_date') border-red-500 @enderror">
                                    @error('requested_work_start_date')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Requested Work Start Time -->
                                <div class="mb-4">
                                    <label for="requested_work_start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Requested Work Start Time') }}
                                    </label>
                                    <input type="time" name="requested_work_start_time" id="requested_work_start_time" 
                                        value="{{ old('requested_work_start_time') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    @error('requested_work_start_time')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description of Work Requested -->
                                <div class="mb-4">
                                    <label for="description_of_work_requested" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Description of Work Requested') }} <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="description_of_work_requested" id="description_of_work_requested" rows="4"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('description_of_work_requested') border-red-500 @enderror">{{ old('description_of_work_requested') }}</textarea>
                                    @error('description_of_work_requested')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Pay Item Details Section -->
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
                                    {{ __('Pay Item Details') }}
                                </h3>

                                <!-- Item Number -->
                                <div class="mb-4">
                                    <label for="item_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Item Number') }}
                                    </label>
                                    <input type="text" name="item_no" id="item_no" 
                                        value="{{ old('item_no') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    @error('item_no')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Description') }}
                                    </label>
                                    <textarea name="description" id="description" rows="3"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Equipment to be Used -->
                                <div class="mb-4">
                                    <label for="equipment_to_be_used" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Equipment to be Used') }}
                                    </label>
                                    <input type="text" name="equipment_to_be_used" id="equipment_to_be_used" 
                                        value="{{ old('equipment_to_be_used') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    @error('equipment_to_be_used')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Estimated Quantity -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="estimated_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            {{ __('Estimated Quantity') }}
                                        </label>
                                        <input type="number" name="estimated_quantity" id="estimated_quantity" step="0.01"
                                            value="{{ old('estimated_quantity') }}"
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                        @error('estimated_quantity')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            {{ __('Unit') }}
                                        </label>
                                        <input type="text" name="unit" id="unit" 
                                            value="{{ old('unit') }}"
                                            placeholder="e.g., m, kg, hours"
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                        @error('unit')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submission Details Section -->
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
                                    {{ __('Submission Details') }}
                                </h3>

                                <!-- Contractor Name -->
                                <div class="mb-4">
                                    <label for="contractor_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Contractor Name') }}
                                    </label>
                                    <input type="text" name="contractor_name" id="contractor_name" 
                                        value="{{ old('contractor_name') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    @error('contractor_name')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Status') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" id="status" 
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('status') border-red-500 @enderror">
                                        <option value="">{{ __('Select Status') }}</option>
                                        @foreach(\App\Models\WorkRequest::getStatuses() as $status)
                                            <option value="{{ $status }}" {{ old('status') === $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Notes -->
                                <div class="mb-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Additional Notes') }}
                                    </label>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end gap-4">
                                <a href="{{ route('user.work-requests.index') }}" 
                                    class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Create Request') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
