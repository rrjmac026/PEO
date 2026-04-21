<div class="wr-panel" id="panel-3">
    <div class="wr-panel-tag" style="color:#a78bfa;background:rgba(167,139,250,0.1);border-color:rgba(167,139,250,0.25);">
        👷 Step 3 of 5
    </div>
    <h2 class="wr-panel-title">Assign Resident Engineer</h2>
    <p class="wr-panel-sub">
        Select the Resident Engineer who will review your work request.
    </p>

    @if($residentEngineers->isNotEmpty())

        <div class="wr-fields">
            <div class="wr-field">
                <label class="wr-label" for="assigned_resident_engineer_id">
                    Resident Engineer <span class="wr-req">*</span>
                </label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🧑‍💼</span>
                    <select id="assigned_resident_engineer_id" name="assigned_resident_engineer_id">
                        <option value="">— Select Resident Engineer —</option>
                        @foreach($residentEngineers as $re)
                            <option value="{{ $re->id }}"
                                {{ old('assigned_resident_engineer_id') == $re->id ? 'selected' : '' }}>
                                {{ $re->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <p class="wr-err-msg" id="err-assigned_resident_engineer_id">
                    ⚠ Please select a Resident Engineer before continuing.
                </p>
            </div>

            <div style="
                background: rgba(79,141,255,0.06);
                border: 1px solid rgba(79,141,255,0.2);
                border-radius: 8px;
                padding: 12px 16px;
                font-size: 13px;
                color: var(--wr-muted);
                line-height: 1.6;
            ">
                ℹ️ <strong style="color:var(--wr-text);">Note:</strong>
                The selected Resident Engineer will be notified and their review will begin
                immediately after submission — no further admin assignment is needed for this step.
            </div>
        </div>

    @else

        {{-- No resident engineers registered yet — skip silently --}}
        <div style="
            background: rgba(245,158,11,0.07);
            border: 1px solid rgba(245,158,11,0.25);
            border-radius: 8px;
            padding: 16px 20px;
            font-size: 13px;
            color: var(--wr-muted);
            line-height: 1.7;
        ">
            ⚠️ <strong style="color:var(--wr-text);">No Resident Engineers available.</strong><br>
            There are currently no Resident Engineers registered in the system.
            You can still submit — an Admin will assign one for you.
        </div>

        <input type="hidden" name="assigned_resident_engineer_id" value="">

    @endif

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(3)">← Back</button>
        <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(3)">Continue →</button>
    </div>
</div>