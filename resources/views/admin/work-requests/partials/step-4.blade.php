{{-- STEP 4: Reception & Submission --}}
<div class="wr-panel" id="panel-4">
    <div class="wr-panel-tag purple">📮 Step 4 of 7</div>
    <h2 class="wr-panel-title">Reception & Submission</h2>
    <p class="wr-panel-sub">Record the contractor and reception details.</p>

    <div class="wr-fields">
        {{-- Contractor Name --}}
        <div class="wr-field">
            <label class="wr-label" for="contractor_name">Contractor Name</label>
            <div class="wr-input-wrap">
                <span class="wr-icon">🏛</span>
                <input type="text" name="contractor_name" id="contractor_name"
                       value="{{ old('contractor_name', $workRequest->contractor_name ?? '') }}"
                       placeholder="e.g., XYZ Construction Corp.">
            </div>
        </div>

        <div class="wr-fields wr-two-col">
            {{-- Received By --}}
            <div class="wr-field">
                <label class="wr-label" for="received_by">Received By</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">👤</span>
                    <input type="text" name="received_by" id="received_by"
                           value="{{ old('received_by', $workRequest->received_by ?? '') }}"
                           placeholder="Name of receiver">
                </div>
            </div>

            {{-- Received Date --}}
            <div class="wr-field">
                <label class="wr-label" for="received_date">Received Date</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">📅</span>
                    <input type="date" name="received_date" id="received_date"
                           value="{{ old('received_date', isset($workRequest) ? $workRequest->received_date?->format('Y-m-d') : '') }}">
                </div>
            </div>
        </div>

        {{-- Received Time --}}
        <div class="wr-field">
            <label class="wr-label" for="received_time">Received Time</label>
            <div class="wr-input-wrap">
                <span class="wr-icon">🕐</span>
                <input type="time" name="received_time" id="received_time"
                       value="{{ old('received_time', $workRequest->received_time ?? '') }}">
            </div>
        </div>
    </div>

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(4)">← Back</button>
        <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(4)">Continue →</button>
    </div>
</div>