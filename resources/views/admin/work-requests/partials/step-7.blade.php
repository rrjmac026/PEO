{{-- STEP 7: Provincial Engineer & Status --}}
<div class="wr-panel" id="panel-7">
    <div class="wr-panel-tag green">✅ Step 7 of 7</div>
    <h2 class="wr-panel-title">Provincial Engineer & Status</h2>
    <p class="wr-panel-sub">Select the provincial engineer — their signature will auto-populate. Then set the work request status.</p>

    {{-- ── Provincial Engineer ── --}}
    <div class="wr-section-block" style="border-left:4px solid #14b8a6;">
        <div class="wr-section-block-title" style="color:#14b8a6;">
            <i class="fas fa-user-tie" style="margin-right:6px;"></i> Provincial Engineer — Final Approval
        </div>

        <div class="wr-fields">
            {{-- Employee Search Dropdown --}}
            <div class="wr-field">
                <label class="wr-label" for="pe_employee_search">
                    Search Employee
                    <span style="color:var(--wr-muted);font-weight:400;font-family:'Inter',sans-serif;text-transform:none;letter-spacing:0;"> — type to find</span>
                </label>
                <div class="wr-input-wrap" style="position:relative;">
                    <span class="wr-icon">🔍</span>
                    <input type="text" id="pe_employee_search" autocomplete="off"
                           placeholder="Type name or employee ID..."
                           oninput="wrEmployeeSearch(this,'pe')">
                    <div id="pe_suggestions" class="wr-suggestions" style="display:none;"></div>
                </div>
            </div>

            <div class="wr-fields wr-two-col">
                <div class="wr-field">
                    <label class="wr-label" for="approved_by_name">Provincial Engineer Name</label>
                    <div class="wr-input-wrap">
                        <span class="wr-icon">👤</span>
                        <input type="text" name="approved_by" id="approved_by_name"
                               value="{{ old('approved_by', $workRequest->approved_by ?? '') }}"
                               placeholder="Will auto-fill from selection">
                        <span class="wr-readonly-badge" id="pe_name_badge" style="display:none;">Auto</span>
                    </div>
                </div>

                <div class="wr-field">
                    <label class="wr-label" for="approved_by_designation">Designation</label>
                    <div class="wr-input-wrap">
                        <span class="wr-icon">🏅</span>
                        <input type="text" name="approved_by_designation" id="approved_by_designation"
                               value="{{ old('approved_by_designation', $workRequest->approved_by_designation ?? '') }}"
                               placeholder="e.g., Provincial Engineer">
                    </div>
                </div>
            </div>

            <div class="wr-field">
                <label class="wr-label">Signature Preview</label>
                <div id="pe_sig_preview" class="wr-sig-preview-box">
                    @if(!empty($workRequest->approved_signature ?? ''))
                        <img src="{{ Str::startsWith($workRequest->approved_signature, 'data:') ? $workRequest->approved_signature : asset('storage/' . $workRequest->approved_signature) }}"
                             alt="PE Signature" class="wr-sig-img">
                    @else
                        <span class="wr-sig-empty">No signature yet</span>
                    @endif
                </div>
                <input type="hidden" name="approved_signature" id="approved_signature"
                       value="{{ old('approved_signature', $workRequest->approved_signature ?? '') }}">
            </div>

            <div class="wr-field">
                <label class="wr-label" for="approved_notes">Notes / Approval Notes</label>
                <div class="wr-input-wrap textarea-wrap">
                    <span class="wr-icon">📋</span>
                    <textarea name="approved_notes" id="approved_notes" rows="3"
                              placeholder="Enter approval notes...">{{ old('approved_notes', $workRequest->approved_notes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="wr-section-gap"></div>

    {{-- ── Acceptance ── --}}
    <div class="wr-section-block" style="border-left:4px solid #6b7280;">
        <div class="wr-section-block-title" style="color:var(--wr-muted);">
            <i class="fas fa-handshake" style="margin-right:6px;"></i> Acceptance by Contractor
        </div>

        <div class="wr-fields">
            <div class="wr-field">
                <label class="wr-label" for="accepted_by_contractor">Accepted By</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🤝</span>
                    <input type="text" name="accepted_by_contractor" id="accepted_by_contractor"
                           value="{{ old('accepted_by_contractor', $workRequest->accepted_by_contractor ?? '') }}"
                           placeholder="Contractor representative name">
                </div>
            </div>

            <div class="wr-fields wr-two-col">
                <div class="wr-field">
                    <label class="wr-label" for="accepted_date">Accepted Date</label>
                    <div class="wr-input-wrap">
                        <span class="wr-icon">📅</span>
                        <input type="date" name="accepted_date" id="accepted_date"
                               value="{{ old('accepted_date', isset($workRequest) ? $workRequest->accepted_date?->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div class="wr-field">
                    <label class="wr-label" for="accepted_time">Accepted Time</label>
                    <div class="wr-input-wrap">
                        <span class="wr-icon">🕐</span>
                        <input type="time" name="accepted_time" id="accepted_time"
                               value="{{ old('accepted_time', $workRequest->accepted_time ?? '') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wr-section-gap"></div>

    {{-- ── Status ── --}}
    <div class="wr-fields">
        <div class="wr-field">
            <label class="wr-label" for="status">
                Work Request Status <span class="wr-req">*</span>
            </label>
            <div class="wr-input-wrap no-icon">
                <select name="status" id="status"
                        class="{{ $errors->has('status') ? 'wr-error' : '' }}">
                    <option value="">Select Status</option>
                    @foreach(\App\Models\WorkRequest::getStatuses() as $statusOption)
                        <option value="{{ $statusOption }}"
                            {{ old('status', $workRequest->status ?? 'draft') === $statusOption ? 'selected' : '' }}>
                            {{ ucfirst($statusOption) }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('status')
                <p class="wr-err-msg show" id="err-status">⚠ {{ $message }}</p>
            @enderror
        </div>

        {{-- Notes --}}
        <div class="wr-field">
            <label class="wr-label" for="notes">Additional Notes</label>
            <div class="wr-input-wrap textarea-wrap">
                <span class="wr-icon">📝</span>
                <textarea name="notes" id="notes" rows="3"
                          placeholder="Any additional notes...">{{ old('notes', $workRequest->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="wr-nav">
        <div>
            @isset($workRequest)
            <a href="{{ route('admin.work-requests.show', $workRequest) }}" class="wr-btn wr-btn-ghost">
                ✕ Cancel
            </a>
            @else
            <a href="{{ route('admin.work-requests.index') }}" class="wr-btn wr-btn-ghost">
                ✕ Cancel
            </a>
            @endisset
        </div>
        <div style="display:flex;gap:10px;">
            <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(7)">← Back</button>
            <button type="submit" class="wr-btn wr-btn-success" id="wr-submit-btn">
                <i class="fas fa-save" style="margin-right:6px;"></i>
                {{ isset($workRequest) ? 'Save Changes' : 'Create Work Request' }}
            </button>
        </div>
    </div>
</div>