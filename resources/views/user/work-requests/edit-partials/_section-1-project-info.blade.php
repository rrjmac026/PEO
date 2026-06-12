{{-- ============================================================
     SECTION 1 — Project Information
     ============================================================ --}}
<div class="wre-section-divider pb-6">
    <h3 class="wre-section-title text-lg font-semibold mb-4">
        {{ __('Project Information') }}
    </h3>

    {{-- Contract Number --}}
    <div class="mb-4">
        <label for="contract_number" class="wre-label block text-sm mb-2">
            {{ __('Contract Number') }}
        </label>
        <input type="text" name="contract_number" id="contract_number"
            value="{{ old('contract_number', $workRequest->contract_number) }}"
            class="wre-input block w-full px-3 py-2 shadow-sm @error('contract_number') border-red-500 @enderror">
        @error('contract_number')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Project Name --}}
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

    {{-- Project Location --}}
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

    {{-- For Office --}}
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

    {{-- From Requester --}}
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