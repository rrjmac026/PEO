{{-- resources/views/reviewer/concrete-pouring/partials/_checklist.blade.php --}}
{{-- Variables expected: $concretePouring --}}

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
        <div class="mb-4">
            <div style="height:6px;background:var(--cp-border);border-radius:99px;overflow:hidden">
                <div style="height:100%;width:{{ $concretePouring->checklist_progress }}%;background:#059669;border-radius:99px"></div>
            </div>
        </div>
        <div class="cp-checklist-grid">
            @foreach($checklistItems as $field => $label)
                <div class="cp-check-item {{ $concretePouring->$field ? 'checked' : 'unchecked' }}">
                    <span class="cp-check-icon">{{ $concretePouring->$field ? '✅' : '⬜' }}</span>
                    <span>{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
