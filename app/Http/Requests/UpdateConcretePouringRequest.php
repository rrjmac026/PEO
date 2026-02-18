<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConcretePouringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $concretePouring = $this->route('concretePouring');
        
        // Check if user has an employee record
        if (!$this->user() || !$this->user()->employee) {
            return false;
        }
        
        // Check if user owns this request or is admin
        return $concretePouring->requested_by_employee_id === $this->user()->employee->id
            || $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $concretePouring = $this->route('concretePouring');
        
        // If already approved, don't allow datetime changes to past
        $datetimeRule = $concretePouring->status === 'approved' 
            ? 'required|date' 
            : 'required|date|after:now';
        
        return [
            'project_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contractor' => 'required|string|max:255',
            'part_of_structure' => 'required|string|max:255',
            'estimated_volume' => 'required|numeric|min:0|max:9999.99',
            'station_limits_section' => 'nullable|string|max:255',
            'pouring_datetime' => $datetimeRule,
            
            // Checklist items
            'concrete_vibrator' => 'nullable|boolean',
            'field_density_test' => 'nullable|boolean',
            'protective_covering_materials' => 'nullable|boolean',
            'beam_cylinder_molds' => 'nullable|boolean',
            'warning_signs_barricades' => 'nullable|boolean',
            'curing_materials' => 'nullable|boolean',
            'concrete_saw' => 'nullable|boolean',
            'slump_cones' => 'nullable|boolean',
            'concrete_block_spacer' => 'nullable|boolean',
            'plumbness' => 'nullable|boolean',
            'finishing_tools_equipment' => 'nullable|boolean',
            'quality_of_materials' => 'nullable|boolean',
            'line_grade_alignment' => 'nullable|boolean',
            'lighting_system' => 'nullable|boolean',
            'required_construction_equipment' => 'nullable|boolean',
            'electrical_layout' => 'nullable|boolean',
            'rebar_sizes_spacing' => 'nullable|boolean',
            'plumbing_layout' => 'nullable|boolean',
            'rebars_installation' => 'nullable|boolean',
            'falseworks_formworks' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'project_name' => 'project name',
            'location' => 'location',
            'contractor' => 'contractor',
            'part_of_structure' => 'part of structure',
            'estimated_volume' => 'estimated volume',
            'station_limits_section' => 'station limits/section',
            'pouring_datetime' => 'pouring date and time',
            
            'concrete_vibrator' => 'concrete vibrator',
            'field_density_test' => 'field density test (FDT)',
            'protective_covering_materials' => 'protective covering materials',
            'beam_cylinder_molds' => 'BEAM/Cylinder molds',
            'warning_signs_barricades' => 'warning signs/barricades/flagmen',
            'curing_materials' => 'curing materials',
            'concrete_saw' => 'concrete saw',
            'slump_cones' => 'slump cones',
            'concrete_block_spacer' => 'concrete block spacer',
            'plumbness' => 'plumbness',
            'finishing_tools_equipment' => 'finishing tools/equipment',
            'quality_of_materials' => 'quality of materials',
            'line_grade_alignment' => 'line and grade alignment',
            'lighting_system' => 'lighting system',
            'required_construction_equipment' => 'required construction equipment',
            'electrical_layout' => 'electrical layout',
            'rebar_sizes_spacing' => 'rebar sizes, spacing and number',
            'plumbing_layout' => 'plumbing layout',
            'rebars_installation' => 'rebars installation requirement',
            'falseworks_formworks' => 'falseworks/formworks adequacy',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox values to boolean
        $checklistFields = [
            'concrete_vibrator', 'field_density_test', 'protective_covering_materials',
            'beam_cylinder_molds', 'warning_signs_barricades', 'curing_materials',
            'concrete_saw', 'slump_cones', 'concrete_block_spacer', 'plumbness',
            'finishing_tools_equipment', 'quality_of_materials', 'line_grade_alignment',
            'lighting_system', 'required_construction_equipment', 'electrical_layout',
            'rebar_sizes_spacing', 'plumbing_layout', 'rebars_installation', 'falseworks_formworks'
        ];

        $data = [];
        foreach ($checklistFields as $field) {
            $data[$field] = $this->has($field) ? true : false;
        }

        $this->merge($data);
    }
}