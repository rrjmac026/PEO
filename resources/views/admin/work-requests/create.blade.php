<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create Work Request') }}
            </h2>
            <a href="{{ route('admin.work-requests.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">{{ __('Validation Error!') }}</strong>
                    <ul class="mt-2 ml-4 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- Create Form --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.work-requests.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Project Information Section --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Project Information') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Project Name --}}
                                <div>
                                    <label for="name_of_project" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Project Name') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name_of_project" 
                                           id="name_of_project"
                                           value="{{ old('name_of_project') }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm @error('name_of_project') border-red-500 @enderror"
                                           required>
                                    @error('name_of_project')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Project Location --}}
                                <div>
                                    <label for="project_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Project Location') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="project_location" 
                                           id="project_location"
                                           value="{{ old('project_location') }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm @error('project_location') border-red-500 @enderror"
                                           required>
                                    @error('project_location')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Request Details Section --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Request Details') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Requested By --}}
                                <div>
                                    <label for="requested_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Requested By') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="requested_by" 
                                           id="requested_by"
                                           value="{{ old('requested_by') }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm @error('requested_by') border-red-500 @enderror"
                                           required>
                                    @error('requested_by')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Requested Work Start Date --}}
                                <div>
                                    <label for="requested_work_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Requested Work Start Date') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" 
                                           name="requested_work_start_date" 
                                           id="requested_work_start_date"
                                           value="{{ old('requested_work_start_date') }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm @error('requested_work_start_date') border-red-500 @enderror"
                                           required>
                                    @error('requested_work_start_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                {{-- For Office --}}
                                <div>
                                    <label for="for_office" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('For Office') }}
                                    </label>
                                    <input type="text" 
                                           name="for_office" 
                                           id="for_office"
                                           value="{{ old('for_office') }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm"
                                           placeholder="e.g., PROVINCIAL ENGINEERS OFFICE">
                                    @error('for_office')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- From Requester --}}
                                <div>
                                    <label for="from_requester" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('From Requester') }}
                                    </label>
                                    <input type="text" 
                                           name="from_requester" 
                                           id="from_requester"
                                           value="{{ old('from_requester') }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm">
                                    @error('from_requester')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Work Details Section --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Work Details') }}
                            </h3>

                            {{-- Description of Work Requested --}}
                            <div class="mb-6">
                                <label for="description_of_work_requested" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Description of Work Requested') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea name="description_of_work_requested" 
                                          id="description_of_work_requested"
                                          rows="4"
                                          class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm @error('description_of_work_requested') border-red-500 @enderror"
                                          required>{{ old('description_of_work_requested') }}</textarea>
                                @error('description_of_work_requested')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Equipment to be Used --}}
                                <div>
                                    <label for="equipment_to_be_used" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Equipment to be Used') }}
                                    </label>
                                    <input type="text" 
                                           name="equipment_to_be_used" 
                                           id="equipment_to_be_used"
                                           value="{{ old('equipment_to_be_used') }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm">
                                    @error('equipment_to_be_used')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Contractor Name --}}
                                <div>
                                    <label for="contractor_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Contractor Name') }}
                                    </label>
                                    <input type="text" 
                                           name="contractor_name" 
                                           id="contractor_name"
                                           value="{{ old('contractor_name') }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm">
                                    @error('contractor_name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                                {{-- Estimated Quantity --}}
                                <div>
                                    <label for="estimated_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Estimated Quantity') }}
                                    </label>
                                    <input type="number" 
                                           name="estimated_quantity" 
                                           id="estimated_quantity"
                                           step="0.01"
                                           value="{{ old('estimated_quantity') }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm">
                                    @error('estimated_quantity')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Unit --}}
                                <div>
                                    <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Unit') }}
                                    </label>
                                    <input type="text" 
                                           name="unit" 
                                           id="unit"
                                           value="{{ old('unit') }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm"
                                           placeholder="e.g., meters, hours">
                                    @error('unit')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Item Number --}}
                                <div>
                                    <label for="item_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Item Number') }}
                                    </label>
                                    <input type="text" 
                                           name="item_no" 
                                           id="item_no"
                                           value="{{ old('item_no') }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm">
                                    @error('item_no')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Status Section --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Status') }}
                            </h3>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Work Request Status') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="status" 
                                        id="status"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm @error('status') border-red-500 @enderror"
                                        required>
                                    <option value="">{{ __('Select Status') }}</option>
                                    @foreach(\App\Models\WorkRequest::getStatuses() as $status)
                                        <option value="{{ $status }}" {{ old('status') === $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.work-requests.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-times mr-2"></i>
                                {{ __('Cancel') }}
                            </a>

                            <div class="flex gap-3">
                                {{-- Reset Button --}}
                                <button type="reset" 
                                        class="inline-flex items-center px-4 py-2 bg-yellow-600 dark:bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:bg-yellow-700 dark:focus:bg-yellow-600 active:bg-yellow-900 dark:active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <i class="fas fa-redo mr-2"></i>
                                    {{ __('Clear') }}
                                </button>

                                {{-- Submit Button --}}
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-orange-600 dark:bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-orange-600 focus:bg-orange-700 dark:focus:bg-orange-600 active:bg-orange-900 dark:active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    {{ __('Create Work Request') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="mt-6 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            <strong>{{ __('Note:') }}</strong> {{ __('Fill in the required fields marked with an asterisk (*) to create a new work request.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
