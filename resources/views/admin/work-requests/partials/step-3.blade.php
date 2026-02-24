{{-- STEP 3: Work Details & Pay Items --}}
<div class="wr-panel" id="panel-3">
    <div class="wr-panel-tag orange">⚙️ Step 3 of 5</div>
    <h2 class="wr-panel-title">Work Details & Pay Items</h2>
    <p class="wr-panel-sub">Specify the pay items, equipment, and quantities.</p>

    <div class="wr-fields">
        <div class="wr-fields wr-two-col">
            <div class="wr-field">
                <label class="wr-label" for="item_no">Item Number</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">#</span>
                    <input type="text" id="item_no" name="item_no"
                        value="{{ old('item_no') }}" placeholder="e.g., A-101">
                </div>
            </div>
            <div class="wr-field">
                <label class="wr-label" for="equipment_to_be_used">Equipment to be Used</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🚧</span>
                    <input type="text" id="equipment_to_be_used" name="equipment_to_be_used"
                        value="{{ old('equipment_to_be_used') }}" placeholder="e.g., Excavator, Roller">
                </div>
            </div>
        </div>

        <div class="wr-field">
            <label class="wr-label" for="description">Pay Item Description</label>
            <div class="wr-input-wrap textarea-wrap">
                <span class="wr-icon">📄</span>
                <textarea id="description" name="description" rows="3"
                    placeholder="Brief description of the pay item...">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="wr-fields wr-three-col">
            <div class="wr-field">
                <label class="wr-label" for="estimated_quantity">Estimated Quantity</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🔢</span>
                    <input type="number" id="estimated_quantity" name="estimated_quantity"
                        step="0.01" min="0" placeholder="0.00"
                        value="{{ old('estimated_quantity') }}">
                </div>
            </div>
            <div class="wr-field">
                <label class="wr-label" for="quantity">Final Quantity</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🔢</span>
                    <input type="number" id="quantity" name="quantity"
                        step="0.01" min="0" placeholder="0.00"
                        value="{{ old('quantity') }}">
                </div>
            </div>
            <div class="wr-field">
                <label class="wr-label" for="unit">Unit</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">📐</span>
                    <input type="text" id="unit" name="unit"
                        value="{{ old('unit') }}" placeholder="m, kg, hrs, cu.m">
                </div>
            </div>
        </div>

        <div class="wr-field">
            <label class="wr-label" for="contractor_name">Contractor Name</label>
            <div class="wr-input-wrap">
                <span class="wr-icon">🏛</span>
                <input type="text" id="contractor_name" name="contractor_name"
                    value="{{ old('contractor_name') }}" placeholder="e.g., XYZ Construction Corp.">
            </div>
        </div>
    </div>

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(3)">← Back</button>
        <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(3)">Continue →</button>
    </div>
</div>
