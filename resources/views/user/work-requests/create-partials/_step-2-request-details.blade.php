<div class="wr-panel" id="panel-2">
    <div class="wr-panel-tag green">📋 Step 2 of 4</div>
    <h2 class="wr-panel-title">Request Details</h2>
    <p class="wr-panel-sub">Describe the work being requested and schedule it.</p>

    <div class="wr-fields">
        <div class="wr-field">
            <label class="wr-label" for="contractor_name_display">Requested By</label>
            <div class="wr-input-wrap">
                <span class="wr-icon">👤</span>
                <input type="text" id="contractor_name_display"
                    value="{{ Auth::user()->name }}" readonly>
                <span class="wr-readonly-badge">Auto</span>
            </div>
            <p class="wr-field-hint">Automatically filled with your account name.</p>
        </div>

        <div class="wr-fields wr-two-col">
            <div class="wr-field">
                <label class="wr-label" for="requested_work_start_date">
                    Start Date <span class="wr-req">*</span>
                </label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">📅</span>
                    <input type="date" id="requested_work_start_date" name="requested_work_start_date"
                        value="{{ old('requested_work_start_date') }}"
                        class="{{ $errors->has('requested_work_start_date') ? 'wr-error' : '' }}">
                </div>
                <p class="wr-err-msg {{ $errors->has('requested_work_start_date') ? 'show' : '' }}" id="err-requested_work_start_date">
                    ⚠ {{ $errors->first('requested_work_start_date', 'Start date is required.') }}
                </p>
            </div>
            <div class="wr-field">
                <label class="wr-label" for="requested_work_start_time">Start Time</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🕐</span>
                    <input type="time" id="requested_work_start_time" name="requested_work_start_time"
                        value="{{ old('requested_work_start_time') }}">
                </div>
            </div>
        </div>

        <div class="wr-field">
            <label class="wr-label" for="description_of_work_requested">
                Description of Work <span class="wr-req">*</span>
            </label>
            <div class="wr-input-wrap textarea-wrap">
                <span class="wr-icon">📝</span>
                <textarea id="description_of_work_requested" name="description_of_work_requested" rows="5"
                    placeholder="Provide a detailed description of the work to be performed..."
                    class="{{ $errors->has('description_of_work_requested') ? 'wr-error' : '' }}">{{ old('description_of_work_requested') }}</textarea>
            </div>
            <p class="wr-err-msg {{ $errors->has('description_of_work_requested') ? 'show' : '' }}" id="err-description_of_work_requested">
                ⚠ {{ $errors->first('description_of_work_requested', 'Work description is required.') }}
            </p>
        </div>
    </div>

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(2)">← Back</button>
        <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(2)">Continue →</button>
    </div>
</div>