<div class="wr-panel active" id="panel-1">
    <div class="wr-panel-tag">📁 Step 1 of 4</div>
    <h2 class="wr-panel-title">Project Information</h2>
    <p class="wr-panel-sub">Tell us about the project where the work will take place.</p>

    <div class="wr-fields">
        <div class="wr-field">
            <label class="wr-label" for="name_of_project">
                Project Name <span class="wr-req">*</span>
            </label>
            <div class="wr-input-wrap">
                <span class="wr-icon">🏗</span>
                <input type="text" id="name_of_project" name="name_of_project"
                    value="{{ old('name_of_project') }}"
                    placeholder="e.g., Highway Expansion Phase 2"
                    class="{{ $errors->has('name_of_project') ? 'wr-error' : '' }}">
            </div>
            <p class="wr-err-msg {{ $errors->has('name_of_project') ? 'show' : '' }}" id="err-name_of_project">
                ⚠ {{ $errors->first('name_of_project', 'Project name is required.') }}
            </p>
        </div>

        <div class="wr-field">
            <label class="wr-label" for="project_location">
                Project Location <span class="wr-req">*</span>
            </label>
            <div class="wr-input-wrap">
                <span class="wr-icon">📍</span>
                <input type="text" id="project_location" name="project_location"
                    value="{{ old('project_location') }}"
                    placeholder="e.g., Davao City, Zone 3"
                    class="{{ $errors->has('project_location') ? 'wr-error' : '' }}">
            </div>
            <p class="wr-err-msg {{ $errors->has('project_location') ? 'show' : '' }}" id="err-project_location">
                ⚠ {{ $errors->first('project_location', 'Project location is required.') }}
            </p>
        </div>

        <div class="wr-field">
            <label class="wr-label" for="for_office">For Office</label>
            <div class="wr-input-wrap">
                <span class="wr-icon">🏢</span>
                <input type="text" id="for_office" name="for_office"
                    value="PROVINCIAL ENGINEERS OFFICE" readonly>
                <span class="wr-readonly-badge">Fixed</span>
            </div>
            <p class="wr-field-hint">This field is fixed and cannot be changed.</p>
        </div>

        <div class="wr-field">
            <label class="wr-label" for="from_requester">
                From / Requester
                <span style="color:var(--wr-muted);font-weight:400;">(optional)</span>
            </label>
            <div class="wr-input-wrap">
                <span class="wr-icon">👤</span>
                <input type="text" id="from_requester" name="from_requester"
                    value="{{ old('from_requester') }}"
                    placeholder="e.g., Project Manager">
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