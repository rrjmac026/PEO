<x-app-layout>
    @push('styles')
    <style>
        /* ══════════════════════════════════════════
           LIGHT MODE TOKENS (primary / default)
        ══════════════════════════════════════════ */
        :root {
            --wr-surface:   #ffffff;
            --wr-surface2:  #f8fafc;
            --wr-border:    #e2e8f0;
            --wr-text:      #0f172a;
            --wr-text-sec:  #334155;
            --wr-muted:     #64748b;
            --wr-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            --wr-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
        }

        /* ══════════════════════════════════════════
           DARK MODE TOKENS (override on .dark)
        ══════════════════════════════════════════ */
        .dark {
            --wr-surface:   #1a1f2e;
            --wr-surface2:  #1e2335;
            --wr-border:    #2a3050;
            --wr-text:      #e8eaf6;
            --wr-text-sec:  #c5cae9;
            --wr-muted:     #7c85a8;
            --wr-shadow:    0 1px 4px rgba(0,0,0,0.35);
            --wr-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
        }

        .wre-wrap { font-family: 'Inter', sans-serif; }

        /* ── Form container ── */
        .wre-card {
            background: var(--wr-surface);
            border: 1px solid var(--wr-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--wr-shadow);
            transition: box-shadow 0.25s ease;
        }
        .wre-card:hover { box-shadow: var(--wr-shadow-lg); }

        /* ── Form inputs ── */
        .wre-input, .wre-textarea, .wre-select {
            background: var(--wr-surface);
            border: 1px solid var(--wr-border);
            color: var(--wr-text);
            border-radius: 6px;
        }

        .wre-input:focus, .wre-textarea:focus, .wre-select:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .wre-label {
            color: var(--wr-text);
            font-weight: 500;
        }

        .wre-section-title {
            color: var(--wr-text);
        }

        .wre-section-divider {
            border-bottom: 1px solid var(--wr-border);
        }

        .wre-readonly {
            background: var(--wr-surface2);
            color: var(--wr-muted);
        }
    </style>
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Work Request') }}
            </h2>
            <a href="{{ route('user.work-requests.show', $workRequest) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 wre-wrap">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="wre-card">
                <div class="p-6" style="color: var(--wr-text);">
                    <form action="{{ route('user.work-requests.update', $workRequest) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-6">
                            <!-- Project Information Section -->
                            <div class="wre-section-divider pb-6">
                                <h3 class="wre-section-title text-lg font-semibold mb-4">
                                    {{ __('Project Information') }}
                                </h3>

                                <!-- Project Name -->
                                <div class="mb-4">
                                    <label for="name_of_project" class="wre-label block text-sm mb-2">
                                        {{ __('Project Name') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name_of_project" id="name_of_project" 
                                        value="{{ old('name_of_project', $workRequest->name_of_project) }}"
                                        class="wre-input block w-full px-3 py-2 shadow-sm @error('name_of_project') border-red-500 @enderror">
                                    @error('name_of_project')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Project Location -->
                                <div class="mb-4">
                                    <label for="project_location" class="wre-label block text-sm mb-2">
                                        {{ __('Project Location') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="project_location" id="project_location" 
                                        value="{{ old('project_location', $workRequest->project_location) }}"
                                        class="wre-input block w-full px-3 py-2 shadow-sm @error('project_location') border-red-500 @enderror">
                                    @error('project_location')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- For Office -->
                                <div class="mb-4">
                                    <label for="for_office" class="wre-label block text-sm mb-2">
                                        {{ __('For Office') }}
                                    </label>
                                    <input type="text" name="for_office" id="for_office" 
                                        value="{{ old('for_office', $workRequest->for_office) }}"
                                        class="wre-input block w-full px-3 py-2 shadow-sm">
                                    @error('for_office')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- From Requester -->
                                <div class="mb-4">
                                    <label for="from_requester" class="wre-label block text-sm mb-2">
                                        {{ __('From Requester') }}
                                    </label>
                                    <input type="text" name="from_requester" id="from_requester" 
                                        value="{{ old('from_requester', $workRequest->from_requester) }}"
                                        class="wre-input block w-full px-3 py-2 shadow-sm">
                                    @error('from_requester')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Request Details Section -->
                            <div class="wre-section-divider pb-6">
                                <h3 class="wre-section-title text-lg font-semibold mb-4">
                                    {{ __('Request Details') }}
                                </h3>

                                <!-- Requested By (Read-only) -->
                                <div class="mb-4">
                                    <label for="requested_by" class="wre-label block text-sm mb-2">
                                        {{ __('Requested By') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="requested_by" id="requested_by" 
                                        value="{{ old('requested_by', $workRequest->requested_by) }}" readonly
                                        class="wre-input wre-readonly block w-full px-3 py-2 shadow-sm">
                                </div>

                                <!-- Requested Work Start Date -->
                                <div class="mb-4">
                                    <label for="requested_work_start_date" class="wre-label block text-sm mb-2">
                                        {{ __('Requested Work Start Date') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="requested_work_start_date" id="requested_work_start_date" 
                                        value="{{ old('requested_work_start_date', $workRequest->requested_work_start_date?->format('Y-m-d')) }}"
                                        class="wre-input block w-full px-3 py-2 shadow-sm @error('requested_work_start_date') border-red-500 @enderror">
                                    @error('requested_work_start_date')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Requested Work Start Time -->
                                <div class="mb-4">
                                    <label for="requested_work_start_time" class="wre-label block text-sm mb-2">
                                        {{ __('Requested Work Start Time') }}
                                    </label>
                                    <input type="time" name="requested_work_start_time" id="requested_work_start_time" 
                                        value="{{ old('requested_work_start_time', $workRequest->requested_work_start_time) }}"
                                        class="wre-input block w-full px-3 py-2 shadow-sm">
                                    @error('requested_work_start_time')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description of Work Requested -->
                                <div class="mb-4">
                                    <label for="description_of_work_requested" class="wre-label block text-sm mb-2">
                                        {{ __('Description of Work Requested') }} <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="description_of_work_requested" id="description_of_work_requested" rows="4"
                                        class="wre-textarea block w-full px-3 py-2 shadow-sm @error('description_of_work_requested') border-red-500 @enderror">{{ old('description_of_work_requested', $workRequest->description_of_work_requested) }}</textarea>
                                    @error('description_of_work_requested')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Pay Item Details Section -->
                            <div class="wre-section-divider pb-6">
                                <h3 class="wre-section-title text-lg font-semibold mb-4">
                                    {{ __('Pay Item Details') }}
                                </h3>

                                <!-- Item Number -->
                                <div class="mb-4">
                                    <label for="item_no" class="wre-label block text-sm mb-2">
                                        {{ __('Item Number') }}
                                    </label>
                                    <input type="text" name="item_no" id="item_no" 
                                        value="{{ old('item_no', $workRequest->item_no) }}"
                                        class="wre-input block w-full px-3 py-2 shadow-sm">
                                    @error('item_no')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label for="description" class="wre-label block text-sm mb-2">
                                        {{ __('Description') }}
                                    </label>
                                    <textarea name="description" id="description" rows="3"
                                        class="wre-textarea block w-full px-3 py-2 shadow-sm">{{ old('description', $workRequest->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Equipment to be Used -->
                                <div class="mb-4">
                                    <label for="equipment_to_be_used" class="wre-label block text-sm mb-2">
                                        {{ __('Equipment to be Used') }}
                                    </label>
                                    <input type="text" name="equipment_to_be_used" id="equipment_to_be_used" 
                                        value="{{ old('equipment_to_be_used', $workRequest->equipment_to_be_used) }}"
                                        class="wre-input block w-full px-3 py-2 shadow-sm">
                                    @error('equipment_to_be_used')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Estimated Quantity -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="estimated_quantity" class="wre-label block text-sm mb-2">
                                            {{ __('Estimated Quantity') }}
                                        </label>
                                        <input type="number" name="estimated_quantity" id="estimated_quantity" step="0.01"
                                            value="{{ old('estimated_quantity', $workRequest->estimated_quantity) }}"
                                            class="wre-input block w-full px-3 py-2 shadow-sm">
                                        @error('estimated_quantity')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="unit" class="wre-label block text-sm mb-2">
                                            {{ __('Unit') }}
                                        </label>
                                        <input type="text" name="unit" id="unit" 
                                            value="{{ old('unit', $workRequest->unit) }}"
                                            placeholder="e.g., m, kg, hours"
                                            class="wre-input block w-full px-3 py-2 shadow-sm">
                                        @error('unit')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submission Details Section -->
                            <div class="wre-section-divider pb-6">
                                <h3 class="wre-section-title text-lg font-semibold mb-4">
                                    {{ __('Submission Details') }}
                                </h3>

                                <!-- Contractor Name -->
                                <div class="mb-4">
                                    <label for="contractor_name" class="wre-label block text-sm mb-2">
                                        {{ __('Contractor Name') }}
                                    </label>
                                    <input type="text" name="contractor_name" id="contractor_name" 
                                        value="{{ old('contractor_name', $workRequest->contractor_name) }}"
                                        class="wre-input block w-full px-3 py-2 shadow-sm">
                                    @error('contractor_name')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="mb-4">
                                    <label for="status" class="wre-label block text-sm mb-2">
                                        {{ __('Status') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" id="status" 
                                        class="wre-select block w-full px-3 py-2 shadow-sm @error('status') border-red-500 @enderror">
                                        <option value="">{{ __('Select Status') }}</option>
                                        @foreach(\App\Models\WorkRequest::getStatuses() as $status)
                                            <option value="{{ $status }}" {{ old('status', $workRequest->status) === $status ? 'selected' : '' }}>
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
                                    <label for="notes" class="wre-label block text-sm mb-2">
                                        {{ __('Additional Notes') }}
                                    </label>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="wre-textarea block w-full px-3 py-2 shadow-sm">{{ old('notes', $workRequest->notes) }}</textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end gap-4">
                                <a href="{{ route('user.work-requests.show', $workRequest) }}" 
                                    class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Update Request') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
