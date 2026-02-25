{{-- STEP 1: Project Information --}}
<div class="wr-panel active" id="panel-1">
    <div class="wr-panel-tag">📁 Step 1 of 7</div>
    <h2 class="wr-panel-title">Project Information</h2>
    <p class="wr-panel-sub">Enter the core project details for this work request.</p>

    <div class="wr-fields">
        {{-- Reference Number --}}
        <div class="wr-field">
            <label class="wr-label" for="reference_number">Reference Number</label>
            <div class="wr-input-wrap">
                <span class="wr-icon">🔖</span>
                <input type="text" name="reference_number" id="reference_number"
                       value="{{ old('reference_number', $workRequest->reference_number ?? '') }}"
                       placeholder="e.g.,2024-001">
            </div>
            @error('reference_number')
                <p class="wr-err-msg show" id="err-reference_number">⚠ {{ $message }}</p>
            @enderror
        </div>

        {{-- Project Name --}}
        <div class="wr-field">
            <label class="wr-label" for="name_of_project">
                Project Name <span class="wr-req">*</span>
            </label>
            <div class="wr-input-wrap">
                <span class="wr-icon">🏗</span>
                <input type="text" name="name_of_project" id="name_of_project"
                       value="{{ old('name_of_project', $workRequest->name_of_project ?? '') }}"
                       placeholder="e.g., Highway Expansion Phase 2"
                       class="{{ $errors->has('name_of_project') ? 'wr-error' : '' }}"
                       required>
            </div>
            @error('name_of_project')
                <p class="wr-err-msg show" id="err-name_of_project">⚠ {{ $message }}</p>
            @enderror
        </div>

        {{-- Project Location --}}
        <div class="wr-field">
            <label class="wr-label" for="project_location">
                Project Location <span class="wr-req">*</span>
            </label>
            <div class="wr-input-wrap">
                <span class="wr-icon">📍</span>
                <input type="text" name="project_location" id="project_location"
                       value="{{ old('project_location', $workRequest->project_location ?? '') }}"
                       placeholder="e.g., Davao City, Zone 3"
                       class="{{ $errors->has('project_location') ? 'wr-error' : '' }}"
                       required>
            </div>
            @error('project_location')
                <p class="wr-err-msg show" id="err-project_location">⚠ {{ $message }}</p>
            @enderror
        </div>

        <div class="wr-fields wr-two-col">
            {{-- For Office --}}
            <div class="wr-field">
                <label class="wr-label" for="for_office">For Office</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🏢</span>
                    <input type="text" name="for_office" id="for_office"
                           value="{{ old('for_office', $workRequest->for_office ?? '') }}"
                           placeholder="e.g., Engineering Department">
                </div>
            </div>

            {{-- From Requester --}}
            <div class="wr-field">
                <label class="wr-label" for="from_requester">From / Requester</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">👤</span>
                    <input type="text" name="from_requester" id="from_requester"
                           value="{{ old('from_requester', $workRequest->from_requester ?? '') }}"
                           placeholder="e.g., Project Manager">
                </div>
            </div>
        </div>
    </div>

    <div class="wr-nav">
        <span></span>
        <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(1)">
            Continue →
        </button>
    </div>
</div>