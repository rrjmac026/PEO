{{-- STEP 5: Inspection & Review --}}
<div class="wr-panel" id="panel-5">
    <div class="wr-panel-tag" style="color:#60a5fa;background:rgba(96,165,250,0.1);border-color:rgba(96,165,250,0.25);">
        🔍 Step 5 of 7
    </div>
    <h2 class="wr-panel-title">Inspection & Review</h2>
    <p class="wr-panel-sub">Manually record findings from the site inspector, surveyor, resident engineer, and MTQA.</p>

    {{-- ── Site Inspector ── --}}
    <div class="wr-section-block" style="border-left:4px solid #60a5fa;">
        <div class="wr-section-block-title" style="color:#60a5fa;">
            <i class="fas fa-hard-hat" style="margin-right:6px;"></i> Site Inspector
        </div>

        <div class="wr-fields">
            <div class="wr-field">
                <label class="wr-label" for="inspected_by_site_inspector">Inspector Name</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">👷</span>
                    <input type="text" name="inspected_by_site_inspector" id="inspected_by_site_inspector"
                           value="{{ old('inspected_by_site_inspector', $workRequest->inspected_by_site_inspector ?? '') }}"
                           placeholder="Full name of site inspector">
                </div>
            </div>

            <div class="wr-fields wr-two-col">
                <div class="wr-field">
                    <label class="wr-label" for="findings_comments">Findings & Comments</label>
                    <div class="wr-input-wrap textarea-wrap">
                        <span class="wr-icon">📋</span>
                        <textarea name="findings_comments" id="findings_comments" rows="3"
                                  placeholder="Enter inspection findings...">{{ old('findings_comments', $workRequest->findings_comments ?? '') }}</textarea>
                    </div>
                </div>

                <div class="wr-field">
                    <label class="wr-label" for="recommendation">Recommendation</label>
                    <div class="wr-input-wrap textarea-wrap">
                        <span class="wr-icon">💡</span>
                        <textarea name="recommendation" id="recommendation" rows="3"
                                  placeholder="Enter recommendation...">{{ old('recommendation', $workRequest->recommendation ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wr-section-gap"></div>

    {{-- ── Surveyor ── --}}
    <div class="wr-section-block" style="border-left:4px solid #c084fc;">
        <div class="wr-section-block-title" style="color:#c084fc;">
            <i class="fas fa-drafting-compass" style="margin-right:6px;"></i> Surveyor
        </div>

        <div class="wr-fields">
            <div class="wr-field">
                <label class="wr-label" for="surveyor_name">Surveyor Name</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">👷</span>
                    <input type="text" name="surveyor_name" id="surveyor_name"
                           value="{{ old('surveyor_name', $workRequest->surveyor_name ?? '') }}"
                           placeholder="Full name of surveyor">
                </div>
            </div>

            <div class="wr-fields wr-two-col">
                <div class="wr-field">
                    <label class="wr-label" for="findings_surveyor">Findings</label>
                    <div class="wr-input-wrap textarea-wrap">
                        <span class="wr-icon">📋</span>
                        <textarea name="findings_surveyor" id="findings_surveyor" rows="3"
                                  placeholder="Enter survey findings...">{{ old('findings_surveyor', $workRequest->findings_surveyor ?? '') }}</textarea>
                    </div>
                </div>

                <div class="wr-field">
                    <label class="wr-label" for="recommendation_surveyor">Recommendation</label>
                    <div class="wr-input-wrap textarea-wrap">
                        <span class="wr-icon">💡</span>
                        <textarea name="recommendation_surveyor" id="recommendation_surveyor" rows="3"
                                  placeholder="Enter recommendation...">{{ old('recommendation_surveyor', $workRequest->recommendation_surveyor ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wr-section-gap"></div>

    {{-- ── Resident Engineer ── --}}
    <div class="wr-section-block" style="border-left:4px solid #34d399;">
        <div class="wr-section-block-title" style="color:#34d399;">
            <i class="fas fa-hard-hat" style="margin-right:6px;"></i> Resident Engineer
        </div>

        <div class="wr-fields">
            <div class="wr-field">
                <label class="wr-label" for="resident_engineer_name">Engineer Name</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">👷</span>
                    <input type="text" name="resident_engineer_name" id="resident_engineer_name"
                           value="{{ old('resident_engineer_name', $workRequest->resident_engineer_name ?? '') }}"
                           placeholder="Full name of resident engineer">
                </div>
            </div>

            <div class="wr-fields wr-two-col">
                <div class="wr-field">
                    <label class="wr-label" for="findings_engineer">Findings</label>
                    <div class="wr-input-wrap textarea-wrap">
                        <span class="wr-icon">📋</span>
                        <textarea name="findings_engineer" id="findings_engineer" rows="3"
                                  placeholder="Enter engineer findings...">{{ old('findings_engineer', $workRequest->findings_engineer ?? '') }}</textarea>
                    </div>
                </div>

                <div class="wr-field">
                    <label class="wr-label" for="recommendation_engineer">Recommendation</label>
                    <div class="wr-input-wrap textarea-wrap">
                        <span class="wr-icon">💡</span>
                        <textarea name="recommendation_engineer" id="recommendation_engineer" rows="3"
                                  placeholder="Enter recommendation...">{{ old('recommendation_engineer', $workRequest->recommendation_engineer ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wr-section-gap"></div>

    {{-- ── MTQA ── --}}
    <div class="wr-section-block" style="border-left:4px solid #f59e0b;">
        <div class="wr-section-block-title" style="color:#f59e0b;">
            <i class="fas fa-clipboard-check" style="margin-right:6px;"></i> MTQA Check
        </div>

        <div class="wr-fields">
            <div class="wr-field">
                <label class="wr-label" for="checked_by_mtqa">Checked By (MTQA)</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">👷</span>
                    <input type="text" name="checked_by_mtqa" id="checked_by_mtqa"
                           value="{{ old('checked_by_mtqa', $workRequest->checked_by_mtqa ?? '') }}"
                           placeholder="Full name of MTQA officer">
                </div>
            </div>

            <div class="wr-field">
                <label class="wr-label" for="recommended_action">Recommended Action</label>
                <div class="wr-input-wrap textarea-wrap">
                    <span class="wr-icon">📋</span>
                    <textarea name="recommended_action" id="recommended_action" rows="3"
                              placeholder="Enter recommended action...">{{ old('recommended_action', $workRequest->recommended_action ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(5)">← Back</button>
        <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(5)">Continue →</button>
    </div>
</div>