{{-- STEP 6: Engineer IV (MTQA Head) & Engineer III (Recommending Approval) --}}
<div class="wr-panel" id="panel-6">
    <div class="wr-panel-tag" style="color:#818cf8;background:rgba(129,140,248,0.1);border-color:rgba(129,140,248,0.25);">
        ✍️ Step 6 of 7
    </div>
    <h2 class="wr-panel-title">Engineer IV & Engineer III</h2>
    <p class="wr-panel-sub">Select the engineers — their details will be saved to the work request.</p>

    {{-- ── Engineer IV (MTQA Head) ── --}}
    <div class="wr-section-block" style="border-left:4px solid #f59e0b;">
        <div class="wr-section-block-title" style="color:#f59e0b;">
            <i class="fas fa-clipboard-list" style="margin-right:6px;"></i> Engineer IV — MTQA Check
        </div>
        <div class="wr-fields">
            <div class="wr-field">
                <label class="wr-label" for="checked_by_mtqa_name">Engineer IV</label>
                <div class="wr-input-wrap no-icon">
                    <select name="checked_by_mtqa" id="checked_by_mtqa_name">
                        <option value="">— Select Engineer IV —</option>
                        @foreach($engineers_iv as $user)
                            <option value="{{ $user->name }}"
                                {{ old('checked_by_mtqa', $workRequest->checked_by_mtqa ?? '') === $user->name ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
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
            <div class="wr-field">
                <label class="wr-label" for="recommending_approval_by_name">Engineer III</label>
                <div class="wr-input-wrap no-icon">
                    <select name="recommending_approval_by" id="recommending_approval_by_name">
                        <option value="">— Select Engineer III —</option>
                        @foreach($engineers_iii as $user)
                            <option value="{{ $user->name }}"
                                {{ old('recommending_approval_by', $workRequest->recommending_approval_by ?? '') === $user->name ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
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