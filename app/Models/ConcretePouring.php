<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConcretePouring extends Model
{
    use HasFactory;

    protected $table = 'concrete_pourings';

    protected $fillable = [
        // Project Information
        'project_name',
        'location',
        'contractor',
        'part_of_structure',
        'estimated_volume',
        'station_limits_section',
        'pouring_datetime',
        
        // Requested by
        'requested_by_employee_id',
        
        // Checklist Items (stored as boolean or JSON)
        'checklist_data',
        
        // Individual checklist items
        'concrete_vibrator',
        'field_density_test',
        'protective_covering_materials',
        'beam_cylinder_molds',
        'warning_signs_barricades',
        'curing_materials',
        'concrete_saw',
        'slump_cones',
        'concrete_block_spacer',
        'plumbness',
        'finishing_tools_equipment',
        'quality_of_materials',
        'line_grade_alignment',
        'lighting_system',
        'required_construction_equipment',
        'electrical_layout',
        'rebar_sizes_spacing',
        'plumbing_layout',
        'rebars_installation',
        'falseworks_formworks',
        
        // ME/MTQA Review
        'me_mtqa_remarks',
        'me_mtqa_checked_by',
        'me_mtqa_date',
        
        // Resident Engineer Review
        're_remarks',
        're_checked_by',
        're_date',
        
        // Final Approval
        'status', // 'requested', 'approved', 'disapproved'
        'approval_remarks',
        'approved_by',
        'approved_date',
        'disapproved_by',
        'disapproved_date',
        
        // Provincial Engineer Note
        'noted_by',
        'noted_date',
    ];

    protected $casts = [
        'pouring_datetime' => 'datetime',
        'me_mtqa_date' => 'date',
        're_date' => 'date',
        'approved_date' => 'date',
        'disapproved_date' => 'date',
        'noted_date' => 'date',
        'estimated_volume' => 'decimal:2',
        'checklist_data' => 'array',
        
        // Checklist boolean fields
        'concrete_vibrator' => 'boolean',
        'field_density_test' => 'boolean',
        'protective_covering_materials' => 'boolean',
        'beam_cylinder_molds' => 'boolean',
        'warning_signs_barricades' => 'boolean',
        'curing_materials' => 'boolean',
        'concrete_saw' => 'boolean',
        'slump_cones' => 'boolean',
        'concrete_block_spacer' => 'boolean',
        'plumbness' => 'boolean',
        'finishing_tools_equipment' => 'boolean',
        'quality_of_materials' => 'boolean',
        'line_grade_alignment' => 'boolean',
        'lighting_system' => 'boolean',
        'required_construction_equipment' => 'boolean',
        'electrical_layout' => 'boolean',
        'rebar_sizes_spacing' => 'boolean',
        'plumbing_layout' => 'boolean',
        'rebars_installation' => 'boolean',
        'falseworks_formworks' => 'boolean',
    ];

    /**
     * Get the employee who requested this concrete pouring
     */
    public function requestedBy()
    {
        return $this->belongsTo(Employee::class, 'requested_by_employee_id');
    }

    /**
     * Get the ME/MTQA who checked this form
     */
    public function meMtqaChecker()
    {
        return $this->belongsTo(Employee::class, 'me_mtqa_checked_by');
    }

    /**
     * Get the Resident Engineer who checked this form
     */
    public function residentEngineer()
    {
        return $this->belongsTo(Employee::class, 're_checked_by');
    }

    /**
     * Get the approver
     */
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    /**
     * Get the person who disapproved
     */
    public function disapprover()
    {
        return $this->belongsTo(Employee::class, 'disapproved_by');
    }

    /**
     * Get the Provincial Engineer who noted the form
     */
    public function notedByEngineer()
    {
        return $this->belongsTo(Employee::class, 'noted_by');
    }

    /**
     * Scope for approved pourings
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending pourings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'requested');
    }

    /**
     * Scope for disapproved pourings
     */
    public function scopeDisapproved($query)
    {
        return $query->where('status', 'disapproved');
    }

    /**
     * Search concrete pourings by project, location, or contractor
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('project_name', 'LIKE', "%{$term}%")
                ->orWhere('location', 'LIKE', "%{$term}%")
                ->orWhere('contractor', 'LIKE', "%{$term}%")
                ->orWhere('part_of_structure', 'LIKE', "%{$term}%")
                ->orWhereHas('requestedBy.user', function ($q2) use ($term) {
                    $q2->where('name', 'LIKE', "%{$term}%");
                });
        });
    }

    /**
     * Get formatted project title
     */
    public function getProjectTitleAttribute()
    {
        return "{$this->project_name} - {$this->location}";
    }

    /**
     * Check if all checklist items are completed
     */
    public function getChecklistCompleteAttribute()
    {
        $checklistFields = [
            'concrete_vibrator',
            'field_density_test',
            'protective_covering_materials',
            'beam_cylinder_molds',
            'warning_signs_barricades',
            'curing_materials',
            'concrete_saw',
            'slump_cones',
            'concrete_block_spacer',
            'plumbness',
            'finishing_tools_equipment',
            'quality_of_materials',
            'line_grade_alignment',
            'lighting_system',
            'required_construction_equipment',
            'electrical_layout',
            'rebar_sizes_spacing',
            'plumbing_layout',
            'rebars_installation',
            'falseworks_formworks',
        ];

        foreach ($checklistFields as $field) {
            if (!$this->$field) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get checklist completion percentage
     */
    public function getChecklistProgressAttribute()
    {
        $checklistFields = [
            'concrete_vibrator',
            'field_density_test',
            'protective_covering_materials',
            'beam_cylinder_molds',
            'warning_signs_barricades',
            'curing_materials',
            'concrete_saw',
            'slump_cones',
            'concrete_block_spacer',
            'plumbness',
            'finishing_tools_equipment',
            'quality_of_materials',
            'line_grade_alignment',
            'lighting_system',
            'required_construction_equipment',
            'electrical_layout',
            'rebar_sizes_spacing',
            'plumbing_layout',
            'rebars_installation',
            'falseworks_formworks',
        ];

        $completed = 0;
        $total = count($checklistFields);

        foreach ($checklistFields as $field) {
            if ($this->$field) {
                $completed++;
            }
        }

        return round(($completed / $total) * 100, 2);
    }

    /**
     * Approve the concrete pouring request
     */
    public function approve(Employee $approver, $remarks = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_date' => now(),
            'approval_remarks' => $remarks,
        ]);
    }

    /**
     * Disapprove the concrete pouring request
     */
    public function disapprove(Employee $disapprover, $remarks = null)
    {
        $this->update([
            'status' => 'disapproved',
            'disapproved_by' => $disapprover->id,
            'disapproved_date' => now(),
            'approval_remarks' => $remarks,
        ]);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'approved' => 'success',
            'disapproved' => 'danger',
            'requested' => 'warning',
            default => 'secondary',
        };
    }
}