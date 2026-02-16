<?php

namespace App\Imports;

use App\Models\WorkRequest;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    public array $failures = [];

    /**
     * Map CSV row to WorkRequest model.
     *
     * Expected CSV column headers match the WorkRequest $fillable field names exactly,
     * e.g.: name_of_project, project_location, for_office, from_requester, ...
     */
    public function model(array $row): WorkRequest
    {
        return new WorkRequest([
            // Project Information
            'name_of_project'                    => $row['name_of_project']                    ?? null,
            'project_location'                   => $row['project_location']                   ?? null,

            // For / From
            'for_office'                         => $row['for_office']                         ?? null,
            'from_requester'                     => $row['from_requester']                     ?? null,

            // Request Details
            'requested_by'                       => $row['requested_by']                       ?? null,
            'requested_work_start_date'          => $row['requested_work_start_date']          ?? null,
            'requested_work_start_time'          => $row['requested_work_start_time']          ?? null,

            // Pay Item Details
            'item_no'                            => $row['item_no']                            ?? null,
            'description'                        => $row['description']                        ?? null,
            'equipment_to_be_used'               => $row['equipment_to_be_used']               ?? null,
            'estimated_quantity'                 => $row['estimated_quantity']                 ?? null,
            'unit'                               => $row['unit']                               ?? null,
            'description_of_work_requested'      => $row['description_of_work_requested']      ?? null,

            // Submission
            'submitted_by'                       => $row['submitted_by']                       ?? null,
            'submitted_date'                     => $row['submitted_date']                     ?? null,
            'contractor_name'                    => $row['contractor_name']                    ?? null,

            // Inspection
            'inspected_by_site_inspector'        => $row['inspected_by_site_inspector']        ?? null,
            'site_inspector_signature'           => $row['site_inspector_signature']           ?? null,
            'surveyor_name'                      => $row['surveyor_name']                      ?? null,
            'surveyor_signature'                 => $row['surveyor_signature']                 ?? null,
            'resident_engineer_name'             => $row['resident_engineer_name']             ?? null,
            'resident_engineer_signature'        => $row['resident_engineer_signature']        ?? null,

            // Findings and Recommendations
            'findings_comments'                  => $row['findings_comments']                  ?? null,
            'recommendation'                     => $row['recommendation']                     ?? null,
            'recommended_action'                 => $row['recommended_action']                 ?? null,

            // Review and Approval
            'checked_by_mtqa'                    => $row['checked_by_mtqa']                    ?? null,
            'mtqa_signature'                     => $row['mtqa_signature']                     ?? null,
            'reviewed_by'                        => $row['reviewed_by']                        ?? null,
            'reviewer_designation'               => $row['reviewer_designation']               ?? null,
            'recommending_approval_by'           => $row['recommending_approval_by']           ?? null,
            'recommending_approval_designation'  => $row['recommending_approval_designation']  ?? null,
            'recommending_approval_signature'    => $row['recommending_approval_signature']    ?? null,
            'approved_by'                        => $row['approved_by']                        ?? null,
            'approved_by_designation'            => $row['approved_by_designation']            ?? null,
            'approved_signature'                 => $row['approved_signature']                 ?? null,

            // Acceptance
            'accepted_by_contractor'             => $row['accepted_by_contractor']             ?? null,
            'accepted_date'                      => $row['accepted_date']                      ?? null,
            'accepted_time'                      => $row['accepted_time']                      ?? null,
            'received_by'                        => $row['received_by']                        ?? null,
            'received_date'                      => $row['received_date']                      ?? null,
            'received_time'                      => $row['received_time']                      ?? null,

            // Status
            'status'                             => $row['status'] ?? WorkRequest::STATUS_DRAFT,
            'notes'                              => $row['notes']  ?? null,
        ]);
    }

    /**
     * Validation rules applied per row before model() is called.
     */
    public function rules(): array
    {
        return [
            'name_of_project'               => 'required|string|max:255',
            'project_location'              => 'required|string|max:255',
            'requested_by'                  => 'required|string|max:255',
            'requested_work_start_date'     => 'required|date',
            'description_of_work_requested' => 'required|string',
            'estimated_quantity'            => 'nullable|numeric|min:0',
            'unit'                          => 'nullable|string|max:50',
            'status'                        => 'nullable|in:' . implode(',', WorkRequest::getStatuses()),
        ];
    }

    /**
     * Custom human-readable column names for validation error messages.
     */
    public function customValidationAttributes(): array
    {
        return [
            'name_of_project'               => 'project name',
            'project_location'              => 'project location',
            'requested_by'                  => 'requested by',
            'requested_work_start_date'     => 'work start date',
            'description_of_work_requested' => 'description of work',
        ];
    }

    /**
     * Collect row-level failures instead of throwing, so the rest of the import continues.
     */
    public function onFailure(Failure ...$failures): void
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    public function batchSize(): int  { return 100; }
    public function chunkSize(): int  { return 100; }
}