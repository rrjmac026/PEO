{{-- ============================================================
     SECTION 2 — Request Details
     ============================================================ --}}
<div class="wre-section-divider pb-6">
    <h3 class="wre-section-title text-lg font-semibold mb-4">
        {{ __('Request Details') }}
    </h3>

    {{-- Requested By (Read-only) --}}
    <div class="mb-4">
        <label for="requested_by" class="wre-label block text-sm mb-2">
            {{ __('Requested By') }} <span class="text-red-500">*</span>
        </label>
        <input type="text" name="requested_by" id="requested_by"
            value="{{ old('requested_by', $workRequest->requested_by) }}" readonly
            class="wre-input wre-readonly block w-full px-3 py-2 shadow-sm">
    </div>

    {{-- Requested Work Start Date --}}
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

    {{-- Requested Work Start Time --}}
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

    {{-- Description of Work Requested --}}
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