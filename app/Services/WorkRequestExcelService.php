<?php

namespace App\Services;

use App\Models\WorkRequest;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WorkRequestExcelService
{
    /**
     * Map of WorkRequest type values to xlsx sheet names.
     */
    private const SHEET_MAP = [
        'construction' => 'CONSTRUCTION',
        'maintenance'  => 'MAINTENANCE',
        'waterworks'   => 'WATERWORKS',
    ];

    /**
     * Path to the blank template (store in storage/app or resources).
     */
    private string $templatePath;

    public function __construct()
    {
        // Place WORK-REQUEST-2025.xlsx in storage/app/templates/
        $this->templatePath = storage_path('app/templates/WORK-REQUEST-2025.xlsx');
    }

    /**
     * Generate a filled xlsx for the given WorkRequest.
     * Returns the path to the temporary file.
     */
    public function generate(WorkRequest $workRequest): string
    {
        $spreadsheet = IOFactory::load($this->templatePath);

        // Determine which sheet to fill based on the "for" field (type of work)
        $sheetName = $this->resolveSheet($workRequest);
        $sheet = $spreadsheet->getSheetByName($sheetName)
            ?? $spreadsheet->getActiveSheet();

        $this->fillSheet($sheet, $workRequest);

        // Save to a temp file
        $tmpPath = tempnam(sys_get_temp_dir(), 'work_request_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpPath);

        return $tmpPath;
    }

    /**
     * Fill the target sheet with work request data.
     */
    private function fillSheet($sheet, WorkRequest $workRequest): void
    {
        // Row 7 – Name of Project (value goes in D7, after "Name of Project :")
        $sheet->setCellValue('D7', $workRequest->name_of_project);

        // Row 8 – Project Location
        $sheet->setCellValue('D8', $workRequest->project_location);

        // Row 12 – For (type / category of work)
        $sheet->setCellValue('C12', $workRequest->for ?? $workRequest->category);

        // Row 13 – Requested Work to Start: Date & Time
        if ($workRequest->start_date) {
            $sheet->setCellValue('G13', \Carbon\Carbon::parse($workRequest->start_date)->format('m/d/Y'));
        }
        if ($workRequest->start_time) {
            $sheet->setCellValue('H13', $workRequest->start_time);
        }

        // Row 16 – From (requesting department/office)
        $sheet->setCellValue('C16', $workRequest->requested_by);

        // Row 20 – Pay Item No.
        $sheet->setCellValue('C20', $workRequest->item_no ?? '');

        // Row 21 – Description of pay item + equipment + quantity + unit
        $sheet->setCellValue('C21', $workRequest->item_description ?? '');
        $sheet->setCellValue('G21', $workRequest->quantity ?? '');
        $sheet->setCellValue('H21', $workRequest->unit ?? '');

        // Row 20 column F – Equipment
        $sheet->setCellValue('G20', $workRequest->equipment ?? '');

        // Row 23 – Description of Work Requested (value below label, row 24)
        $sheet->setCellValue('A24', $workRequest->description_of_work);

        // Row 25 / 27 – Submitted by (contractor name on row 28)
        $sheet->setCellValue('A27', $workRequest->contractor_name ?? '');

        // Row 27 col F – Received By signature line (date/time filled on receipt)
        // These stay blank; they are filled manually after printing.

        // Additional fields – adapt column names to your actual migration
        if ($workRequest->submitted_by) {
            $sheet->setCellValue('C25', $workRequest->submitted_by);
        }
    }

    /**
     * Resolve which sheet to use based on the work request type.
     */
    private function resolveSheet(WorkRequest $workRequest): string
    {
        $type = strtolower($workRequest->type ?? $workRequest->for ?? '');

        foreach (self::SHEET_MAP as $key => $sheetName) {
            if (str_contains($type, $key)) {
                return $sheetName;
            }
        }

        return 'CONSTRUCTION'; // default
    }
}