{{-- ============================================================
     SECTION 3 — Pay Item Details
     ============================================================ --}}
<div class="wre-section-divider pb-6">
    <h3 class="wre-section-title text-lg font-semibold mb-4">
        {{ __('Pay Item Details') }}
    </h3>

    {{-- Item Number --}}
    <div class="mb-4">
        <label for="item_no" class="wre-label block text-sm mb-2">
            {{ __('Item Number') }}
        </label>
        <input type="text" name="item_no" id="item_no"
            value="{{ old('item_no', $workRequest->item_no) }}"
            class="wre-input block w-full px-3 py-2 shadow-sm">
        @error('item_no')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Item Description --}}
    <div class="mb-4">
        <label for="description" class="wre-label block text-sm mb-2">
            {{ __('Description') }}
        </label>
        <textarea name="description" id="description" rows="3"
            class="wre-textarea block w-full px-3 py-2 shadow-sm">{{ old('description', $workRequest->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Equipment to be Used --}}
    <div class="mb-4">
        <label for="equipment_to_be_used" class="wre-label block text-sm mb-2">
            {{ __('Equipment to be Used') }}
        </label>
        <input type="text" name="equipment_to_be_used" id="equipment_to_be_used"
            value="{{ old('equipment_to_be_used', $workRequest->equipment_to_be_used) }}"
            class="wre-input block w-full px-3 py-2 shadow-sm">
        @error('equipment_to_be_used')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Estimated Quantity + Unit --}}
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label for="estimated_quantity" class="wre-label block text-sm mb-2">
                {{ __('Estimated Quantity') }}
            </label>
            <input type="number" name="estimated_quantity" id="estimated_quantity" step="0.01"
                value="{{ old('estimated_quantity', $workRequest->estimated_quantity) }}"
                class="wre-input block w-full px-3 py-2 shadow-sm">
            @error('estimated_quantity')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="unit" class="wre-label block text-sm mb-2">
                {{ __('Unit') }}
            </label>
            <input type="text" name="unit" id="unit"
                value="{{ old('unit', $workRequest->unit) }}"
                placeholder="e.g., m, kg, hours"
                class="wre-input block w-full px-3 py-2 shadow-sm">
            @error('unit')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- NEW: Quantity --}}
    <div class="mb-4">
        <label for="quantity" class="wre-label block text-sm mb-2">
            {{ __('Quantity') }}
        </label>
        <input type="number" name="quantity" id="quantity" step="0.01"
            value="{{ old('quantity', $workRequest->quantity) }}"
            class="wre-input block w-full px-3 py-2 shadow-sm">
        @error('quantity')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>