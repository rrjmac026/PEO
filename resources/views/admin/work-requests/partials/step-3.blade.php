{{-- STEP 3: Work Details & Pay Items --}}
<div class="wr-panel" id="panel-3">
    <div class="wr-panel-tag orange">⚙️ Step 3 of 7</div>
    <h2 class="wr-panel-title">Work Details & Pay Items</h2>
    <p class="wr-panel-sub">Enter the pay item specifications, equipment, and quantities.</p>

    <div class="wr-fields">
        <div class="wr-fields wr-two-col">
            {{-- Item Number --}}
            <div class="wr-field">
                <label class="wr-label" for="item_no">Item Number</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">#</span>
                    <input type="text" name="item_no" id="item_no"
                           value="{{ old('item_no', $workRequest->item_no ?? '') }}"
                           placeholder="e.g., A-101">
                </div>
            </div>

            {{-- Equipment --}}
            <div class="wr-field">
                <label class="wr-label" for="equipment_to_be_used">Equipment to be Used</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🚧</span>
                    <input type="text" name="equipment_to_be_used" id="equipment_to_be_used"
                           value="{{ old('equipment_to_be_used', $workRequest->equipment_to_be_used ?? '') }}"
                           placeholder="e.g., Excavator, Roller">
                </div>
            </div>
        </div>

        {{-- Pay Item Description --}}
        <div class="wr-field">
            <label class="wr-label" for="description">Pay Item Description</label>
            <div class="wr-input-wrap textarea-wrap">
                <span class="wr-icon">📄</span>
                <textarea name="description" id="description" rows="3"
                          placeholder="Brief description of the pay item...">{{ old('description', $workRequest->description ?? '') }}</textarea>
            </div>
        </div>

        <div class="wr-fields wr-three-col">
            {{-- Estimated Quantity --}}
            <div class="wr-field">
                <label class="wr-label" for="estimated_quantity">Estimated Quantity</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🔢</span>
                    <input type="number" name="estimated_quantity" id="estimated_quantity"
                           step="0.01" min="0" placeholder="0.00"
                           value="{{ old('estimated_quantity', $workRequest->estimated_quantity ?? '') }}">
                </div>
            </div>

            {{-- Unit --}}
            <div class="wr-field">
                <label class="wr-label" for="unit">Unit</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">📐</span>
                    <input type="text" name="unit" id="unit"
                           value="{{ old('unit', $workRequest->unit ?? '') }}"
                           placeholder="m, kg, hrs, cu.m">
                </div>
            </div>

            {{-- Final Quantity --}}
            <div class="wr-field">
                <label class="wr-label" for="quantity">Final Quantity</label>
                <div class="wr-input-wrap">
                    <span class="wr-icon">🔢</span>
                    <input type="number" name="quantity" id="quantity"
                           step="0.01" min="0" placeholder="0.00"
                           value="{{ old('quantity', $workRequest->quantity ?? '') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="wr-nav">
        <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(3)">← Back</button>
        <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(3)">Continue →</button>
    </div>
</div>