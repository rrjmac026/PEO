{{-- STEP 5: Acceptance & Final Status --}}
<div class="wr-panel" id="panel-5">
    <div class="wr-panel-tag green">🎉 Step 5 of 5</div>
    <h2 class="wr-panel-title">Acceptance & Final Status</h2>
    <p class="wr-panel-sub">Record contractor acceptance and final work request status.</p>

    <div class="wr-fields">
        <div class="wr-fields wr-two-col">
            <div class="wr-field">
                <label class="wr-label" for="accepted_by_contractor">Accepted By (Contractor)</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">👤</span>
                    <input type="text" id="accepted_by_contractor" name="accepted_by_contractor"
                        value="{{ old('accepted_by_contractor') }}" placeholder="Contractor name">
                </div>
            </div>
            <div class="wr-field">
                <label class="wr-label" for="accepted_date">Accepted Date</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">📅</span>
                    <input type="date" id="accepted_date" name="accepted_date"
                        value="{{ old('accepted_date') }}">
                </div>
            </div>
        </div>

        <div class="wr-field">
            <label class="wr-label" for="accepted_time">Accepted Time</label>
            <div class="wr-input-wrap">
                <span class="wr-icon">🕐</span>
                <input type="time" id="accepted_time" name="accepted_time"
                    value="{{ old('accepted_time') }}">
            </div>
        </div>

        <div class="wr-field">
            <label class="wr-label">Work Request Status <span class="wr-req">*</span></label>
            <div class="wr-status-options" id="wr-status-options">
                @foreach(\App\Models\WorkRequest::getStatuses() as $statusOption)
                    @php
                        $colors = [
                            'draft'     => '#7c85a8',
                            'submitted' => '#4f8dff',
                            'inspected' => '#00d4aa',
                            'reviewed'  => '#f59e0b',
                            'approved'  => '#00d4aa',
                            'accepted'  => '#00d4aa',
                            'rejected'  => '#ff6b6b',
                        ];
                        $dotColor = $colors[$statusOption] ?? '#7c85a8';
                        $isSelected = old('status', 'draft') === $statusOption;
                    @endphp
                    <label class="wr-status-opt {{ $isSelected ? 'selected' : '' }}" data-val="{{ $statusOption }}">
                        <input type="radio" name="status" value="{{ $statusOption }}" {{ $isSelected ? 'checked' : '' }}>
                        <span class="wr-status-dot" style="background:{{ $dotColor }};"></span>
                        <span class="wr-status-opt-label">{{ ucfirst($statusOption) }}</span>
                    </label>
                @endforeach
            </div>
            <p class="wr-err-msg {{ $errors->has('status') ? 'show' : '' }}" id="err-status">
                ⚠ {{ $errors->first('status', 'Please select a status.') }}
            </p>
        </div>
    </div>

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(5)">← Edit</button>
        <button type="submit" class="wr-btn wr-btn-success" id="wr-submit-btn">
            🚀 Create Work Request
        </button>
    </div>
</div>
