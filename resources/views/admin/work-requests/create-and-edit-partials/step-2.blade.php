{{-- STEP 2: Request Details --}}
<div class="wr-panel" id="panel-2">
    <div class="wr-panel-tag green">📋 Step 2 of 7</div>
    <h2 class="wr-panel-title">Request Details</h2>
    <p class="wr-panel-sub">Specify when the work should start and describe the work to be done.</p>

    <div class="wr-fields">
        <div class="wr-fields wr-two-col">
            {{-- Requested Work Start Date --}}
            <div class="wr-field">
                <label class="wr-label" for="requested_work_start_date">
                    Work Start Date <span class="wr-req">*</span>
                </label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">📅</span>
                    <input type="date" name="requested_work_start_date" id="requested_work_start_date"
                           value="{{ old('requested_work_start_date', isset($workRequest) ? $workRequest->requested_work_start_date?->format('Y-m-d') : '') }}"
                           class="{{ $errors->has('requested_work_start_date') ? 'wr-error' : '' }}"
                           required>
                </div>
                @error('requested_work_start_date')
                    <p class="wr-err-msg show" id="err-requested_work_start_date">⚠ {{ $message }}</p>
                @enderror
            </div>

            {{-- Requested Work Start Time --}}
            <div class="wr-field">
                <label class="wr-label" for="requested_work_start_time">Start Time</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🕐</span>
                    <input type="time" name="requested_work_start_time" id="requested_work_start_time"
                           value="{{ old('requested_work_start_time', $workRequest->requested_work_start_time ?? '') }}">
                </div>
            </div>
        </div>

        {{-- Description of Work Requested --}}
        <div class="wr-field">
            <label class="wr-label" for="description_of_work_requested">
                Description of Work Requested <span class="wr-req">*</span>
            </label>
            <div class="wr-input-wrap textarea-wrap">
                <span class="wr-icon">📝</span>
                <textarea name="description_of_work_requested" id="description_of_work_requested"
                          rows="4"
                          placeholder="Provide a detailed description of the work to be performed..."
                          class="{{ $errors->has('description_of_work_requested') ? 'wr-error' : '' }}"
                          required>{{ old('description_of_work_requested', $workRequest->description_of_work_requested ?? '') }}</textarea>
            </div>
            @error('description_of_work_requested')
                <p class="wr-err-msg show" id="err-description_of_work_requested">⚠ {{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(2)">← Back</button>
        <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(2)">Continue →</button>
    </div>
</div>