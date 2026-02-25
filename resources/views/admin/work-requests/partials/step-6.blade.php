{{-- STEP 6: Engineer IV (MTQA Head) & Engineer III (Recommending Approval) --}}
<div class="wr-panel" id="panel-6">
    <div class="wr-panel-tag" style="color:#818cf8;background:rgba(129,140,248,0.1);border-color:rgba(129,140,248,0.25);">
        ✍️ Step 6 of 7
    </div>
    <h2 class="wr-panel-title">Engineer IV & Engineer III</h2>
    <p class="wr-panel-sub">Select the engineers from the system — their signature will auto-populate.</p>

    {{-- ── Engineer IV (MTQA Head) ── --}}
    <div class="wr-section-block" style="border-left:4px solid #f59e0b;">
        <div class="wr-section-block-title" style="color:#f59e0b;">
            <i class="fas fa-clipboard-list" style="margin-right:6px;"></i> Engineer IV — MTQA Check
        </div>

        <div class="wr-fields">
            {{-- Employee Search Dropdown --}}
            <div class="wr-field">
                <label class="wr-label" for="eng4_employee_search">
                    Search Employee
                    <span style="color:var(--wr-muted);font-weight:400;font-family:'Inter',sans-serif;text-transform:none;letter-spacing:0;"> — type to find</span>
                </label>
                <div class="wr-input-wrap" style="position:relative;">
                    <span class="wr-icon">🔍</span>
                    <input type="text" id="eng4_employee_search" autocomplete="off"
                           placeholder="Type name or employee ID..."
                           oninput="wrEmployeeSearch(this,'eng4')">
                    <div id="eng4_suggestions" class="wr-suggestions" style="display:none;"></div>
                </div>
            </div>

            <div class="wr-fields wr-two-col">
                <div class="wr-field">
                    <label class="wr-label" for="checked_by_mtqa_name">Engineer IV Name</label>
                    <div class="wr-input-wrap">
                        <span class="wr-icon">👤</span>
                        <input type="text" name="checked_by_mtqa" id="checked_by_mtqa_name"
                               value="{{ old('checked_by_mtqa', $workRequest->checked_by_mtqa ?? '') }}"
                               placeholder="Will auto-fill from selection">
                        <span class="wr-readonly-badge" id="eng4_name_badge" style="display:none;">Auto</span>
                    </div>
                </div>

                <div class="wr-field">
                    <label class="wr-label">Signature Preview</label>
                    <div id="eng4_sig_preview" class="wr-sig-preview-box">
                        @if(!empty($workRequest->mtqa_signature ?? ''))
                            <img src="{{ Str::startsWith($workRequest->mtqa_signature, 'data:') ? $workRequest->mtqa_signature : asset('storage/' . $workRequest->mtqa_signature) }}"
                                 alt="MTQA Signature" class="wr-sig-img">
                        @else
                            <span class="wr-sig-empty">No signature yet</span>
                        @endif
                    </div>
                    <input type="hidden" name="mtqa_signature" id="mtqa_signature"
                           value="{{ old('mtqa_signature', $workRequest->mtqa_signature ?? '') }}">
                </div>
            </div>

            <div class="wr-field">
                <label class="wr-label" for="recommended_action_eng4">Recommended Action</label>
                <div class="wr-input-wrap textarea-wrap">
                    <span class="wr-icon">📋</span>
                    <textarea name="recommended_action" id="recommended_action_eng4" rows="3"
                              placeholder="Enter recommended action...">{{ old('recommended_action', $workRequest->recommended_action ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="wr-section-gap"></div>

    {{-- ── Engineer III (Recommending Approval) ── --}}
    <div class="wr-section-block" style="border-left:4px solid #f97316;">
        <div class="wr-section-block-title" style="color:#f97316;">
            <i class="fas fa-thumbs-up" style="margin-right:6px;"></i> Engineer III — Recommending Approval
        </div>

        <div class="wr-fields">
            {{-- Employee Search Dropdown --}}
            <div class="wr-field">
                <label class="wr-label" for="eng3_employee_search">
                    Search Employee
                    <span style="color:var(--wr-muted);font-weight:400;font-family:'Inter',sans-serif;text-transform:none;letter-spacing:0;"> — type to find</span>
                </label>
                <div class="wr-input-wrap" style="position:relative;">
                    <span class="wr-icon">🔍</span>
                    <input type="text" id="eng3_employee_search" autocomplete="off"
                           placeholder="Type name or employee ID..."
                           oninput="wrEmployeeSearch(this,'eng3')">
                    <div id="eng3_suggestions" class="wr-suggestions" style="display:none;"></div>
                </div>
            </div>

            <div class="wr-fields wr-two-col">
                <div class="wr-field">
                    <label class="wr-label" for="recommending_approval_by_name">Engineer III Name</label>
                    <div class="wr-input-wrap">
                        <span class="wr-icon">👤</span>
                        <input type="text" name="recommending_approval_by" id="recommending_approval_by_name"
                               value="{{ old('recommending_approval_by', $workRequest->recommending_approval_by ?? '') }}"
                               placeholder="Will auto-fill from selection">
                        <span class="wr-readonly-badge" id="eng3_name_badge" style="display:none;">Auto</span>
                    </div>
                </div>

                <div class="wr-field">
                    <label class="wr-label" for="recommending_approval_designation">Designation</label>
                    <div class="wr-input-wrap">
                        <span class="wr-icon">🏅</span>
                        <input type="text" name="recommending_approval_designation" id="recommending_approval_designation"
                               value="{{ old('recommending_approval_designation', $workRequest->recommending_approval_designation ?? '') }}"
                               placeholder="e.g., Engineer III">
                    </div>
                </div>
            </div>

            <div class="wr-field">
                <label class="wr-label">Signature Preview</label>
                <div id="eng3_sig_preview" class="wr-sig-preview-box">
                    @if(!empty($workRequest->recommending_approval_signature ?? ''))
                        <img src="{{ Str::startsWith($workRequest->recommending_approval_signature, 'data:') ? $workRequest->recommending_approval_signature : asset('storage/' . $workRequest->recommending_approval_signature) }}"
                             alt="Eng III Signature" class="wr-sig-img">
                    @else
                        <span class="wr-sig-empty">No signature yet</span>
                    @endif
                </div>
                <input type="hidden" name="recommending_approval_signature" id="recommending_approval_signature"
                       value="{{ old('recommending_approval_signature', $workRequest->recommending_approval_signature ?? '') }}">
            </div>

            <div class="wr-field">
                <label class="wr-label" for="recommending_approval_notes">Notes</label>
                <div class="wr-input-wrap textarea-wrap">
                    <span class="wr-icon">📋</span>
                    <textarea name="recommending_approval_notes" id="recommending_approval_notes" rows="3"
                              placeholder="Enter recommending approval notes...">{{ old('recommending_approval_notes', $workRequest->recommending_approval_notes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(6)">← Back</button>
        <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(6)">Continue →</button>
    </div>
</div>