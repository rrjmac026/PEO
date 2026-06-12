{{-- ============================================================
     SECTION 4 — Submission Details
     ============================================================ --}}
<div class="wre-section-divider pb-6">
    <h3 class="wre-section-title text-lg font-semibold mb-4">
        {{ __('Submission Details') }}
    </h3>

    {{-- Contractor Name --}}
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

    {{-- Status --}}
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

    {{-- Additional Notes --}}
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