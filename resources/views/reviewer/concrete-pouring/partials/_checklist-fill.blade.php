{{-- resources/views/reviewer/concrete-pouring/partials/_checklist-fill.blade.php --}}
{{-- Variables expected: $concretePouring, $canFillChecklist --}}

@php
    $checklistItems = [
        'concrete_vibrator'               => 'Concrete Vibrator',
        'field_density_test'              => 'Field Density Test',
        'protective_covering_materials'   => 'Protective Covering Materials',
        'beam_cylinder_molds'             => 'Beam / Cylinder Molds',
        'warning_signs_barricades'        => 'Warning Signs & Barricades',
        'curing_materials'                => 'Curing Materials',
        'concrete_saw'                    => 'Concrete Saw',
        'slump_cones'                     => 'Slump Cones',
        'concrete_block_spacer'           => 'Concrete Block Spacer',
        'plumbness'                       => 'Plumbness',
        'finishing_tools_equipment'       => 'Finishing Tools & Equipment',
        'quality_of_materials'            => 'Quality of Materials',
        'line_grade_alignment'            => 'Line, Grade & Alignment',
        'lighting_system'                 => 'Lighting System',
        'required_construction_equipment' => 'Required Construction Equipment',
        'electrical_layout'               => 'Electrical Layout',
        'rebar_sizes_spacing'             => 'Rebar Sizes & Spacing',
        'plumbing_layout'                 => 'Plumbing Layout',
        'rebars_installation'             => 'Rebars Installation',
        'falseworks_formworks'            => 'Falseworks / Formworks',
    ];
@endphp

<div class="cp-card">
    <div class="cp-card-head">
        <div class="cp-card-head-icon green"><i class="fas fa-tasks"></i></div>
        <span class="cp-card-title">Pre-Pouring Checklist</span>
        <span class="ml-auto text-sm" style="color:var(--cp-muted)">
            {{ $concretePouring->checklist_progress }}% complete
        </span>
    </div>
    <div class="cp-card-body">

        {{-- Progress bar --}}
        <div class="mb-4">
            <div style="height:6px;background:var(--cp-border);border-radius:99px;overflow:hidden">
                <div style="height:100%;width:{{ $concretePouring->checklist_progress }}%;background:#059669;border-radius:99px"></div>
            </div>
        </div>

        @if($canFillChecklist)
            {{-- Editable checklist form --}}
            <div class="rv-form-box" style="margin-bottom: 20px;">
                <div class="rv-form-title">
                    <i class="fas fa-clipboard-check text-green-500"></i>
                    Fill in the checklist items
                </div>
                <form action="{{ route('reviewer.concrete-pouring.store-checklist', $concretePouring) }}"
                      method="POST">
                    @csrf
                    <div class="cp-checklist-fill-grid">
                        @foreach($checklistItems as $field => $label)
                            <label class="cp-fill-check-label">
                                <input type="checkbox"
                                       name="{{ $field }}"
                                       value="1"
                                       {{ $concretePouring->$field ? 'checked' : '' }}>
                                <span class="cp-fill-check-box"><i class="fas fa-check"></i></span>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div style="margin-top: 16px;">
                        <button type="submit"
                                class="px-6 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition inline-flex items-center gap-2">
                            <i class="fas fa-save"></i> Save Checklist
                        </button>
                    </div>
                </form>
            </div>
        @else
            {{-- Read-only view --}}
            <div class="cp-checklist-grid">
                @foreach($checklistItems as $field => $label)
                    <div class="cp-check-item {{ $concretePouring->$field ? 'checked' : 'unchecked' }}">
                        <span class="cp-check-icon">{{ $concretePouring->$field ? '✅' : '⬜' }}</span>
                        <span>{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>

<style>
    .cp-checklist-fill-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 10px;
    }
    .cp-fill-check-label {
        display: flex; align-items: center; gap: 10px;
        padding: 11px 14px;
        background: var(--cp-surface2);
        border: 1.5px solid var(--cp-border);
        border-radius: 8px;
        cursor: pointer;
        transition: border-color .18s, background .18s;
        font-size: 13px; color: var(--cp-text);
        user-select: none;
    }
    .cp-fill-check-label input[type=checkbox] { display: none; }
    .cp-fill-check-box {
        width: 18px; height: 18px; border-radius: 4px;
        border: 2px solid var(--cp-border);
        background: var(--cp-surface);
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        transition: all .18s; font-size: 11px;
    }
    .cp-fill-check-label:has(input:checked) {
        border-color: rgba(5,150,105,0.5);
        background: rgba(5,150,105,0.06);
    }
    .cp-fill-check-label:has(input:checked) .cp-fill-check-box {
        background: #059669; border-color: #059669; color: white;
    }
</style>