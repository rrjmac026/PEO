<div class="wr-panel" id="panel-4">
    <div class="wr-panel-tag orange">⚙️ Step 4 of 5</div>
    <h2 class="wr-panel-title">Pay Item & Submission</h2>
    <p class="wr-panel-sub">Specify the pay items and equipment details.</p>

    <div class="wr-fields">
        <div class="wr-fields wr-two-col">
            <div class="wr-field">
                <label class="wr-label" for="item_no">Item Number <span class="wr-required">*</span></label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">#</span>
                    <input type="text" id="item_no" name="item_no" required
                        value="{{ old('item_no') }}" placeholder="e.g., A-101">
                </div>
            </div>
            <div class="wr-field">
                <label class="wr-label" for="equipment_to_be_used">Equipment to be Used <span class="wr-required">*</span></label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🚧</span>
                    <input type="text" id="equipment_to_be_used" name="equipment_to_be_used" required
                        value="{{ old('equipment_to_be_used') }}" placeholder="e.g., Excavator, Roller">
                </div>
            </div>
        </div>

        <div class="wr-field">
            <label class="wr-label" for="description">Item Description <span class="wr-required">*</span></label>
            <div class="wr-input-wrap textarea-wrap">
                <span class="wr-icon">📄</span>
                <textarea id="description" name="description" rows="3" required
                    placeholder="Brief description of the pay item...">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="wr-fields wr-two-col">
            <div class="wr-field">
                <label class="wr-label" for="estimated_quantity">Estimated Quantity <span class="wr-required">*</span></label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🔢</span>
                    <input type="number" id="estimated_quantity" name="estimated_quantity" required
                        step="0.01" min="0.01" placeholder="0.00"
                        value="{{ old('estimated_quantity') }}">
                </div>
            </div>
            <div class="wr-field">
                <label class="wr-label" for="unit">Unit <span class="wr-required">*</span></label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">📐</span>
                    <input type="text" id="unit" name="unit" required
                        value="{{ old('unit') }}" placeholder="m, kg, hrs, cu.m">
                </div>
            </div>
        </div>

        {{-- NEW: Quantity field --}}
        <div class="wr-field">
            <label class="wr-label" for="quantity">Quantity <span class="wr-required">*</span></label>
            <div class="wr-input-wrap">
                <span class="wr-icon">📦</span>
                <input type="number" id="quantity" name="quantity" required
                    step="0.01" min="0.01" placeholder="0.00"
                    value="{{ old('quantity') }}">
            </div>
        </div>

        <div class="wr-field">
            <label class="wr-label" for="notes">Additional Notes</label>
            <div class="wr-input-wrap textarea-wrap">
                <span class="wr-icon">💬</span>
                <textarea id="notes" name="notes" rows="3"
                    placeholder="Any additional information or special instructions...">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(4)">← Back</button>
        <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(4)">Review →</button>
    </div>
</div>