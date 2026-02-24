{{-- STEP 4: Submission & Reception --}}
<div class="wr-panel" id="panel-4">
    <div class="wr-panel-tag purple">📮 Step 4 of 5</div>
    <h2 class="wr-panel-title">Submission & Reception</h2>
    <p class="wr-panel-sub">Record submission and reception details.</p>

    <div class="wr-fields">
        <div class="wr-fields wr-two-col">
            <div class="wr-field">
                <label class="wr-label" for="received_by">Received By</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">👤</span>
                    <input type="text" id="received_by" name="received_by"
                        value="{{ old('received_by') }}" placeholder="Name">
                </div>
            </div>
            <div class="wr-field">
                <label class="wr-label" for="received_date">Received Date</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">📅</span>
                    <input type="date" id="received_date" name="received_date"
                        value="{{ old('received_date') }}">
                </div>
            </div>
        </div>

        <div class="wr-field">
            <label class="wr-label" for="received_time">Received Time</label>
            <div class="wr-input-wrap">
                <span class="wr-icon">🕐</span>
                <input type="time" id="received_time" name="received_time"
                    value="{{ old('received_time') }}">
            </div>
        </div>
    </div>

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(4)">← Back</button>
        <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(4)">Continue →</button>
    </div>
</div>
