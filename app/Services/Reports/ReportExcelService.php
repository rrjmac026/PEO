<?php

namespace App\Services\Reports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * ReportExcelService
 *
 * Generates .xlsx report files using PhpOffice\PhpSpreadsheet.
 * Install via: composer require phpoffice/phpspreadsheet
 *
 * All public methods return a full filesystem path to the generated .xlsx file
 * (written to storage/app/reports/). The caller is responsible for cleanup
 * (response()->download($path)->deleteFileAfterSend(true)).
 */
class ReportExcelService
{
    // ── Brand palette (ARGB hex, no #) ───────────────────────────────────────
    private const CLR_HEADER_BG   = 'FF1E40AF';  // Indigo-700
    private const CLR_HEADER_FG   = 'FFFFFFFF';
    private const CLR_ALT_ROW     = 'FFF1F5F9';  // Slate-100
    private const CLR_SECTION_BG  = 'FFE2E8F0';  // Slate-200
    private const CLR_SECTION_FG  = 'FF1E40AF';
    private const CLR_DARK        = 'FF0F172A';  // Slate-900
    private const CLR_MUTED       = 'FF64748B';  // Slate-500
    private const CLR_GREEN       = 'FF16A34A';
    private const CLR_RED         = 'FFDC2626';
    private const CLR_YELLOW      = 'FFCA8A04';
    private const CLR_BLUE        = 'FF2563EB';
    private const CLR_WHITE       = 'FFFFFFFF';
    private const CLR_CARD_BG     = 'FFF8FAFC';  // Slate-50

    // ── Output directory ─────────────────────────────────────────────────────
    private const OUTPUT_DIR = 'reports';

    // =========================================================================
    // PUBLIC  —  REPORT GENERATORS
    // =========================================================================

    /**
     * Work Requests Excel Report
     * Returns path to generated .xlsx file.
     */
    public function workRequestsReport(
        Collection $workRequests,
        array      $summary,
        array      $range,
        array      $filters,
    ): string {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Work Requests Report')
            ->setCreator('DPWH Reporting System')
            ->setDescription('Work Requests Report – ' . $range['label']);

        // ── Sheet 1: Summary ─────────────────────────────────────────────────
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Summary');

        $this->writeAgencyHeader($sheet, 'Work Requests Report', $range);

        $row = 6;
        $this->writeSectionHeading($sheet, $row++, 'SUMMARY STATISTICS', 'A', 'F');

        $summaryItems = [
            ['Total Requests',        $summary['total'],                          null],
            ['Approved',              $summary['approved'],                       self::CLR_GREEN],
            ['Rejected',              $summary['rejected'],                       self::CLR_RED],
            ['Pending',               $summary['pending'],                        self::CLR_YELLOW],
            ['In Review',             $summary['in_review'] ?? 0,                self::CLR_BLUE],
            ['Approval Rate',         $summary['approval_rate'] . '%',            null],
        ];

        foreach ($summaryItems as [$label, $value, $color]) {
            $this->writeSummaryRow($sheet, $row++, $label, $value, $color);
        }

        $row++;
        $this->writeSectionHeading($sheet, $row++, 'FILTERS APPLIED', 'A', 'F');
        $this->writeKeyValue($sheet, $row++, 'Date Range', $range['label']);
        $this->writeKeyValue($sheet, $row++, 'Status Filter', $filters['status'] ?? 'All');
        $this->writeKeyValue($sheet, $row++, 'Contractor Filter', $filters['contractor'] ?? 'All');
        $this->writeKeyValue($sheet, $row++, 'Step Filter', $filters['step'] ?? 'All');

        // ── Sheet 2: By Contractor ───────────────────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('By Contractor');
        $this->writeAgencyHeader($sheet2, 'Work Requests by Contractor', $range);

        $row = 6;
        $headers = ['Contractor', 'Total', 'Approved', 'Rejected', 'Pending', 'Approval Rate'];
        $colWidths = [40, 12, 12, 12, 12, 16];
        $this->writeTableHeader($sheet2, $row++, $headers, $colWidths);

        $byContractor = $workRequests->groupBy('contractor_name');
        $dataRow = 0;
        foreach ($byContractor as $name => $group) {
            $total    = $group->count();
            $approved = $group->where('status', 'approved')->count();
            $rejected = $group->where('status', 'rejected')->count();
            $pending  = $group->whereNotIn('status', ['approved', 'rejected'])->count();
            $rate     = $total > 0 ? round(($approved / $total) * 100, 1) . '%' : '0%';

            $this->writeTableRow($sheet2, $row++, $dataRow++, [
                $name ?: '(Unknown)', $total, $approved, $rejected, $pending, $rate,
            ], $colWidths);
        }
        $this->writeTableFooter($sheet2, $row, count($headers), $colWidths);

        // ── Sheet 3: By Review Step ──────────────────────────────────────────
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('By Review Step');
        $this->writeAgencyHeader($sheet3, 'Work Requests by Review Step', $range);

        $row = 6;
        $this->writeTableHeader($sheet3, $row++, ['Review Step', 'Count', '% of Total'], [40, 15, 15]);
        $byStep  = $workRequests->groupBy('current_review_step');
        $total   = $workRequests->count();
        $dataRow = 0;
        foreach ($byStep as $step => $group) {
            $cnt  = $group->count();
            $pct  = $total > 0 ? round(($cnt / $total) * 100, 1) . '%' : '0%';
            $this->writeTableRow($sheet3, $row++, $dataRow++, [
                $step ?: 'Complete', $cnt, $pct,
            ], [40, 15, 15]);
        }

        // ── Sheet 4: Detailed Records ────────────────────────────────────────
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Detailed Records');
        $this->writeAgencyHeader($sheet4, 'Work Requests – Detailed Records', $range);

        $row = 6;
        $headers = [
            'Ref #', 'Project Name', 'Contractor', 'Status',
            'Current Step', 'Submitted Date', 'Assigned PE', 'Assigned By',
        ];
        $colWidths = [18, 40, 30, 14, 20, 16, 28, 24];
        $this->writeTableHeader($sheet4, $row++, $headers, $colWidths);

        $dataRow = 0;
        foreach ($workRequests as $wr) {
            $this->writeTableRow($sheet4, $row++, $dataRow++, [
                $wr->reference_number ?? 'N/A',
                $wr->name_of_project ?? '—',
                $wr->contractor_name ?? '—',
                strtoupper($wr->status),
                $wr->current_review_step ?? 'Complete',
                $wr->created_at->format('m/d/Y'),
                optional($wr->assignedProvincialEngineer)->name ?? '—',
                optional($wr->assignedByAdmin)->name ?? '—',
            ], $colWidths);

            // Colour-code status cell (col D = 4th column)
            $statusCol = 'D';
            $statusColor = match (strtolower($wr->status)) {
                'approved'  => self::CLR_GREEN,
                'rejected'  => self::CLR_RED,
                'in_review' => self::CLR_BLUE,
                default     => self::CLR_YELLOW,
            };
            $sheet4->getStyle($statusCol . $row - 1)->getFont()->getColor()->setARGB($statusColor);
            $sheet4->getStyle($statusCol . $row - 1)->getFont()->setBold(true);
        }

        $this->writeTableFooter($sheet4, $row, count($headers), $colWidths);
        $this->writeRecordCount($sheet4, $row + 1, $workRequests->count());

        $spreadsheet->setActiveSheetIndex(0);
        return $this->save($spreadsheet);
    }

    // -------------------------------------------------------------------------

    /**
     * Concrete Pourings Excel Report
     */
    public function concretePouringsReport(
        Collection $concretePourings,
        array      $summary,
        array      $range,
        array      $filters,
    ): string {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Concrete Pourings Report')
            ->setCreator('DPWH Reporting System')
            ->setDescription('Concrete Pourings Report – ' . $range['label']);

        // ── Sheet 1: Summary ─────────────────────────────────────────────────
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Summary');
        $this->writeAgencyHeader($sheet, 'Concrete Pourings Report', $range);

        $row = 6;
        $this->writeSectionHeading($sheet, $row++, 'SUMMARY STATISTICS', 'A', 'F');

        $summaryItems = [
            ['Total Requests',            $summary['total'],                       null],
            ['Approved',                  $summary['approved'],                    self::CLR_GREEN],
            ['Disapproved',               $summary['disapproved'],                 self::CLR_RED],
            ['Pending (Requested)',        $summary['pending'],                     self::CLR_YELLOW],
            ['Total Estimated Volume',    number_format($summary['total_volume'], 2) . ' m³', null],
            ['Avg Volume / Request',      number_format($summary['avg_volume'], 2) . ' m³', null],
            ['Avg Checklist Completion',  $summary['avg_checklist_completion'] . '%',        null],
            ['Approval Rate',             $summary['approval_rate'] . '%',                   null],
        ];

        foreach ($summaryItems as [$label, $value, $color]) {
            $this->writeSummaryRow($sheet, $row++, $label, $value, $color);
        }

        $row++;
        $this->writeSectionHeading($sheet, $row++, 'FILTERS APPLIED', 'A', 'F');
        $this->writeKeyValue($sheet, $row++, 'Date Range', $range['label']);
        $this->writeKeyValue($sheet, $row++, 'Status Filter', $filters['status'] ?? 'All');
        $this->writeKeyValue($sheet, $row++, 'Contractor Filter', $filters['contractor'] ?? 'All');

        // ── Sheet 2: By Contractor ───────────────────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('By Contractor');
        $this->writeAgencyHeader($sheet2, 'Concrete Pourings by Contractor', $range);

        $row = 6;
        $headers   = ['Contractor', 'Total', 'Approved', 'Disapproved', 'Pending', 'Total Volume (m³)', 'Avg Volume (m³)'];
        $colWidths  = [40, 10, 12, 14, 10, 18, 16];
        $this->writeTableHeader($sheet2, $row++, $headers, $colWidths);

        $byContractor = $concretePourings->groupBy('contractor');
        $dataRow = 0;
        foreach ($byContractor as $name => $group) {
            $totalVol = round($group->sum('estimated_volume'), 2);
            $avgVol   = round($group->avg('estimated_volume') ?? 0, 2);
            $this->writeTableRow($sheet2, $row++, $dataRow++, [
                $name ?: '(Unknown)',
                $group->count(),
                $group->where('status', 'approved')->count(),
                $group->where('status', 'disapproved')->count(),
                $group->where('status', 'requested')->count(),
                number_format($totalVol, 2),
                number_format($avgVol, 2),
            ], $colWidths);
        }

        // ── Sheet 3: Checklist Compliance ────────────────────────────────────
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Checklist Compliance');
        $this->writeAgencyHeader($sheet3, 'Checklist Compliance Rates', $range);

        $row = 6;
        $this->writeTableHeader($sheet3, $row++, ['Checklist Item', 'Checked', 'Total', 'Compliance Rate'], [45, 12, 12, 18]);

        $checklistFields = [
            'concrete_vibrator', 'field_density_test', 'protective_covering_materials',
            'beam_cylinder_molds', 'warning_signs_barricades', 'curing_materials',
            'concrete_saw', 'slump_cones', 'concrete_block_spacer', 'plumbness',
            'finishing_tools_equipment', 'quality_of_materials', 'line_grade_alignment',
            'lighting_system', 'required_construction_equipment', 'electrical_layout',
            'rebar_sizes_spacing', 'plumbing_layout', 'rebars_installation',
            'falseworks_formworks',
        ];

        $total   = $concretePourings->count();
        $dataRow = 0;
        foreach ($checklistFields as $field) {
            $checked = $concretePourings->where($field, true)->count();
            $rate    = $total > 0 ? round(($checked / $total) * 100, 1) : 0;
            $this->writeTableRow($sheet3, $row, $dataRow++, [
                ucfirst(str_replace('_', ' ', $field)),
                $checked,
                $total,
                $rate . '%',
            ], [45, 12, 12, 18]);

            // Colour the rate cell
            $rateColor = $rate >= 75 ? self::CLR_GREEN : ($rate >= 50 ? self::CLR_YELLOW : self::CLR_RED);
            $sheet3->getStyle('D' . $row)->getFont()->getColor()->setARGB($rateColor);
            $sheet3->getStyle('D' . $row)->getFont()->setBold(true);
            $row++;
        }

        // ── Sheet 4: Detailed Records ────────────────────────────────────────
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Detailed Records');
        $this->writeAgencyHeader($sheet4, 'Concrete Pourings – Detailed Records', $range);

        $row = 6;
        $headers = [
            'Ref #', 'Project Name', 'Contractor', 'Est. Volume (m³)',
            'Status', 'Date Requested', 'Checklist %', 'Requested By',
        ];
        $colWidths = [18, 40, 30, 16, 14, 16, 14, 28];
        $this->writeTableHeader($sheet4, $row++, $headers, $colWidths);

        $dataRow = 0;
        foreach ($concretePourings as $cp) {
            $this->writeTableRow($sheet4, $row++, $dataRow++, [
                $cp->reference_number ?? 'N/A',
                $cp->project_name ?? '—',
                $cp->contractor ?? '—',
                number_format($cp->estimated_volume, 2),
                strtoupper($cp->status),
                $cp->created_at->format('m/d/Y'),
                ($cp->checklist_progress ?? 0) . '%',
                optional($cp->requestedBy)->name ?? '—',
            ], $colWidths);

            $statusColor = match (strtolower($cp->status)) {
                'approved'    => self::CLR_GREEN,
                'disapproved' => self::CLR_RED,
                default       => self::CLR_YELLOW,
            };
            $sheet4->getStyle('E' . ($row - 1))->getFont()->getColor()->setARGB($statusColor);
            $sheet4->getStyle('E' . ($row - 1))->getFont()->setBold(true);
        }

        $this->writeTableFooter($sheet4, $row, count($headers), $colWidths);
        $this->writeRecordCount($sheet4, $row + 1, $concretePourings->count());

        $spreadsheet->setActiveSheetIndex(0);
        return $this->save($spreadsheet);
    }

    // -------------------------------------------------------------------------

    /**
     * Memos Excel Report
     */
    public function memosReport(
        Collection $memos,
        array      $summary,
        array      $range,
        array      $filters,
    ): string {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Memos Report')
            ->setCreator('DPWH Reporting System')
            ->setDescription('Memos Report – ' . $range['label']);

        // ── Sheet 1: Summary ─────────────────────────────────────────────────
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Summary');
        $this->writeAgencyHeader($sheet, 'Memos Report', $range);

        $row = 6;
        $this->writeSectionHeading($sheet, $row++, 'SUMMARY STATISTICS', 'A', 'F');

        $summaryItems = [
            ['Total Memos',       $summary['total'],              null],
            ['Sent',              $summary['sent'],               self::CLR_GREEN],
            ['Draft',             $summary['draft'],              self::CLR_MUTED],
            ['Scheduled',         $summary['scheduled'],          self::CLR_BLUE],
            ['Cancelled',         $summary['cancelled'] ?? 0,     self::CLR_RED],
            ['Total Recipients',  $summary['total_recipients'],   null],
            ['Total Read',        $summary['total_read'],         null],
            ['Avg Read Rate',     $summary['avg_read_rate'] . '%', null],
        ];

        foreach ($summaryItems as [$label, $value, $color]) {
            $this->writeSummaryRow($sheet, $row++, $label, $value, $color);
        }

        $row++;
        $this->writeSectionHeading($sheet, $row++, 'FILTERS APPLIED', 'A', 'F');
        $this->writeKeyValue($sheet, $row++, 'Date Range', $range['label']);
        $this->writeKeyValue($sheet, $row++, 'Status Filter', $filters['status'] ?? 'All');
        $this->writeKeyValue($sheet, $row++, 'Type Filter', $filters['type'] ?? 'All');

        // ── Sheet 2: By Type ─────────────────────────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('By Type');
        $this->writeAgencyHeader($sheet2, 'Memos by Type', $range);

        $row = 6;
        $this->writeTableHeader($sheet2, $row++, ['Type', 'Total', 'Sent', 'Draft', 'Scheduled'], [35, 12, 12, 12, 14]);
        $byType  = $memos->groupBy('type');
        $dataRow = 0;
        $types   = \App\Models\Memo::types();
        foreach ($byType as $type => $group) {
            $this->writeTableRow($sheet2, $row++, $dataRow++, [
                $types[$type] ?? ucfirst($type),
                $group->count(),
                $group->where('status', 'sent')->count(),
                $group->where('status', 'draft')->count(),
                $group->where('status', 'scheduled')->count(),
            ], [35, 12, 12, 12, 14]);
        }

        // ── Sheet 3: Read Rate Analysis ──────────────────────────────────────
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Read Rate Analysis');
        $this->writeAgencyHeader($sheet3, 'Memo Read Rate Analysis', $range);

        $row = 6;
        $headers   = ['Ref #', 'Subject', 'Type', 'Status', 'Sent At', 'Recipients', 'Read', 'Unread', 'Read Rate'];
        $colWidths  = [16, 44, 22, 14, 16, 12, 10, 10, 12];
        $this->writeTableHeader($sheet3, $row++, $headers, $colWidths);

        $dataRow = 0;
        foreach ($memos->where('status', \App\Models\Memo::STATUS_SENT) as $memo) {
            $total    = $memo->memoRecipients->count();
            $read     = $memo->memoRecipients->whereNotNull('read_at')->count();
            $unread   = $total - $read;
            $readRate = $total > 0 ? round(($read / $total) * 100, 1) : 0;

            $this->writeTableRow($sheet3, $row, $dataRow++, [
                $memo->reference_number ?? 'N/A',
                $memo->subject,
                $types[$memo->type] ?? ucfirst($memo->type),
                strtoupper($memo->status),
                $memo->sent_at ? $memo->sent_at->format('m/d/Y') : '—',
                $total,
                $read,
                $unread,
                $readRate . '%',
            ], $colWidths);

            $rateColor = $readRate >= 75 ? self::CLR_GREEN : ($readRate >= 50 ? self::CLR_YELLOW : self::CLR_RED);
            $sheet3->getStyle('I' . $row)->getFont()->getColor()->setARGB($rateColor);
            $sheet3->getStyle('I' . $row)->getFont()->setBold(true);
            $row++;
        }

        // ── Sheet 4: Detailed Records ────────────────────────────────────────
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Detailed Records');
        $this->writeAgencyHeader($sheet4, 'Memos – Detailed Records', $range);

        $row = 6;
        $headers = ['Ref #', 'Subject', 'Type', 'Status', 'Sender', 'Created', 'Sent At', 'Recipients', 'Read'];
        $colWidths = [16, 44, 22, 14, 26, 14, 14, 12, 10];
        $this->writeTableHeader($sheet4, $row++, $headers, $colWidths);

        $dataRow = 0;
        foreach ($memos as $memo) {
            $this->writeTableRow($sheet4, $row++, $dataRow++, [
                $memo->reference_number ?? 'N/A',
                $memo->subject,
                $types[$memo->type] ?? ucfirst($memo->type),
                strtoupper($memo->status),
                optional($memo->sender)->name ?? '—',
                $memo->created_at->format('m/d/Y'),
                $memo->sent_at ? $memo->sent_at->format('m/d/Y') : '—',
                $memo->memoRecipients->count(),
                $memo->memoRecipients->whereNotNull('read_at')->count(),
            ], $colWidths);

            $statusColor = match (strtolower($memo->status)) {
                'sent'      => self::CLR_GREEN,
                'draft'     => self::CLR_MUTED,
                'scheduled' => self::CLR_BLUE,
                'cancelled' => self::CLR_RED,
                default     => self::CLR_DARK,
            };
            $sheet4->getStyle('D' . ($row - 1))->getFont()->getColor()->setARGB($statusColor);
            $sheet4->getStyle('D' . ($row - 1))->getFont()->setBold(true);
        }

        $this->writeTableFooter($sheet4, $row, count($headers), $colWidths);
        $this->writeRecordCount($sheet4, $row + 1, $memos->count());

        $spreadsheet->setActiveSheetIndex(0);
        return $this->save($spreadsheet);
    }

    // -------------------------------------------------------------------------

    /**
     * Combined Overview Excel Report (all 3 modules in one workbook)
     */
    public function overviewReport(
        Collection $workRequests,
        array      $wrSummary,
        Collection $concretePourings,
        array      $cpSummary,
        Collection $memos,
        array      $memoSummary,
        array      $range,
    ): string {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('System Overview Report')
            ->setCreator('DPWH Reporting System')
            ->setDescription('Overview Report – ' . $range['label']);

        // ── Sheet 1: Overview ────────────────────────────────────────────────
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Overview');
        $this->writeAgencyHeader($sheet, 'System Overview Report', $range);

        $row = 6;

        // Work Requests block
        $this->writeSectionHeading($sheet, $row++, 'WORK REQUESTS', 'A', 'F');
        $items = [
            ['Total Requests', $wrSummary['total'], null],
            ['Approved',       $wrSummary['approved'], self::CLR_GREEN],
            ['Rejected',       $wrSummary['rejected'], self::CLR_RED],
            ['Pending',        $wrSummary['pending'],  self::CLR_YELLOW],
            ['In Review',      $wrSummary['in_review'] ?? 0, self::CLR_BLUE],
            ['Approval Rate',  $wrSummary['approval_rate'] . '%', null],
        ];
        foreach ($items as [$l, $v, $c]) {
            $this->writeSummaryRow($sheet, $row++, $l, $v, $c);
        }

        $row++;
        // Concrete Pourings block
        $this->writeSectionHeading($sheet, $row++, 'CONCRETE POURINGS', 'A', 'F');
        $items = [
            ['Total Requests',         $cpSummary['total'],                             null],
            ['Approved',               $cpSummary['approved'],                           self::CLR_GREEN],
            ['Disapproved',            $cpSummary['disapproved'],                        self::CLR_RED],
            ['Pending',                $cpSummary['pending'],                            self::CLR_YELLOW],
            ['Total Volume',           number_format($cpSummary['total_volume'], 2) . ' m³', null],
            ['Avg Volume / Request',   number_format($cpSummary['avg_volume'], 2) . ' m³',   null],
            ['Avg Checklist',          $cpSummary['avg_checklist_completion'] . '%',         null],
            ['Approval Rate',          $cpSummary['approval_rate'] . '%',                    null],
        ];
        foreach ($items as [$l, $v, $c]) {
            $this->writeSummaryRow($sheet, $row++, $l, $v, $c);
        }

        $row++;
        // Memos block
        $this->writeSectionHeading($sheet, $row++, 'MEMOS', 'A', 'F');
        $items = [
            ['Total Memos',      $memoSummary['total'],             null],
            ['Sent',             $memoSummary['sent'],              self::CLR_GREEN],
            ['Draft',            $memoSummary['draft'],             self::CLR_MUTED],
            ['Scheduled',        $memoSummary['scheduled'],         self::CLR_BLUE],
            ['Total Recipients', $memoSummary['total_recipients'],  null],
            ['Total Read',       $memoSummary['total_read'],        null],
            ['Avg Read Rate',    $memoSummary['avg_read_rate'] . '%', null],
        ];
        foreach ($items as [$l, $v, $c]) {
            $this->writeSummaryRow($sheet, $row++, $l, $v, $c);
        }

        $row += 2;
        $this->writeRecordCount($sheet, $row, $workRequests->count() + $concretePourings->count() + $memos->count());

        // ── Sheet 2: WR Snapshot ─────────────────────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('WR Snapshot');
        $this->writeAgencyHeader($sheet2, 'Work Requests Snapshot', $range);
        $row = 6;
        $this->writeTableHeader($sheet2, $row++, ['Ref #', 'Project', 'Contractor', 'Status', 'Date'], [18, 44, 30, 14, 14]);
        $dataRow = 0;
        foreach ($workRequests as $wr) {
            $this->writeTableRow($sheet2, $row++, $dataRow++, [
                $wr->reference_number ?? 'N/A',
                $wr->name_of_project ?? '—',
                $wr->contractor_name ?? '—',
                strtoupper($wr->status),
                $wr->created_at->format('m/d/Y'),
            ], [18, 44, 30, 14, 14]);
        }

        // ── Sheet 3: CP Snapshot ─────────────────────────────────────────────
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('CP Snapshot');
        $this->writeAgencyHeader($sheet3, 'Concrete Pourings Snapshot', $range);
        $row = 6;
        $this->writeTableHeader($sheet3, $row++, ['Ref #', 'Project', 'Contractor', 'Volume (m³)', 'Status', 'Date'], [18, 40, 30, 14, 14, 14]);
        $dataRow = 0;
        foreach ($concretePourings as $cp) {
            $this->writeTableRow($sheet3, $row++, $dataRow++, [
                $cp->reference_number ?? 'N/A',
                $cp->project_name ?? '—',
                $cp->contractor ?? '—',
                number_format($cp->estimated_volume, 2),
                strtoupper($cp->status),
                $cp->created_at->format('m/d/Y'),
            ], [18, 40, 30, 14, 14, 14]);
        }

        // ── Sheet 4: Memos Snapshot ──────────────────────────────────────────
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Memos Snapshot');
        $this->writeAgencyHeader($sheet4, 'Memos Snapshot', $range);
        $row = 6;
        $this->writeTableHeader($sheet4, $row++, ['Ref #', 'Subject', 'Type', 'Status', 'Recipients', 'Read', 'Date'], [16, 44, 22, 14, 12, 10, 14]);
        $dataRow = 0;
        $types   = \App\Models\Memo::types();
        foreach ($memos as $memo) {
            $this->writeTableRow($sheet4, $row++, $dataRow++, [
                $memo->reference_number ?? 'N/A',
                $memo->subject,
                $types[$memo->type] ?? ucfirst($memo->type),
                strtoupper($memo->status),
                $memo->memoRecipients->count(),
                $memo->memoRecipients->whereNotNull('read_at')->count(),
                $memo->created_at->format('m/d/Y'),
            ], [16, 44, 22, 14, 12, 10, 14]);
        }

        $spreadsheet->setActiveSheetIndex(0);
        return $this->save($spreadsheet);
    }

    // =========================================================================
    // PRIVATE  —  WRITE HELPERS
    // =========================================================================

    /**
     * Agency header: logo placeholder + title + date range band.
     * Occupies rows 1-5.
     */
    private function writeAgencyHeader(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $title, array $range): void
    {
        // Merge for header
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');
        $sheet->mergeCells('A4:H4');

        // Row 1 — Agency name
        $sheet->setCellValue('A1', 'DEPARTMENT OF PUBLIC WORKS AND HIGHWAYS');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => self::CLR_WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => self::CLR_HEADER_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Row 2 — Report title
        $sheet->setCellValue('A2', strtoupper($title));
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => false, 'size' => 10, 'color' => ['argb' => self::CLR_WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => self::CLR_HEADER_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(16);

        // Row 3 — Date range
        $sheet->setCellValue('A3', 'Report Period: ' . $range['label'] . '   |   Generated: ' . now()->format('M d, Y h:i A'));
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['size' => 8, 'color' => ['argb' => self::CLR_DARK]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFE2E8F0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(14);

        // Row 4 — spacer
        $sheet->getRowDimension(4)->setRowHeight(6);

        // Row 5 — spacer
        $sheet->getRowDimension(5)->setRowHeight(4);
    }

    private function writeSectionHeading(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int    $row,
        string $text,
        string $colStart,
        string $colEnd,
    ): void {
        $sheet->mergeCells("{$colStart}{$row}:{$colEnd}{$row}");
        $sheet->setCellValue("{$colStart}{$row}", $text);
        $sheet->getStyle("{$colStart}{$row}:{$colEnd}{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => self::CLR_SECTION_FG]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => self::CLR_SECTION_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::CLR_HEADER_BG]]],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(16);
    }

    private function writeSummaryRow(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int     $row,
        string  $label,
        mixed   $value,
        ?string $valueColor,
    ): void {
        $sheet->setCellValue('A' . $row, $label);
        $sheet->setCellValue('B' . $row, $value);

        $sheet->getStyle('A' . $row)->applyFromArray([
            'font'      => ['size' => 8, 'color' => ['argb' => self::CLR_MUTED]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => self::CLR_CARD_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
        ]);
        $sheet->getStyle('B' . $row)->applyFromArray([
            'font'      => [
                'bold'  => true,
                'size'  => 10,
                'color' => ['argb' => $valueColor ?? self::CLR_DARK],
            ],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => self::CLR_CARD_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
        ]);

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getRowDimension($row)->setRowHeight(14);
    }

    private function writeKeyValue(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int    $row,
        string $key,
        mixed  $value,
    ): void {
        $sheet->setCellValue('A' . $row, $key);
        $sheet->setCellValue('B' . $row, $value);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(8);
        $sheet->getStyle('B' . $row)->getFont()->setSize(8);
        $sheet->getRowDimension($row)->setRowHeight(13);
    }

    /** Write a styled table header row. */
    private function writeTableHeader(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int   $row,
        array $headers,
        array $colWidths,
    ): void {
        $colLetters = $this->columnLetters(count($headers));

        foreach ($headers as $i => $header) {
            $col = $colLetters[$i];
            $sheet->setCellValue("{$col}{$row}", $header);
            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 8, 'color' => ['argb' => self::CLR_HEADER_FG]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => self::CLR_HEADER_BG]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]],
            ]);
            $sheet->getColumnDimension($col)->setWidth($colWidths[$i]);
        }
        $sheet->getRowDimension($row)->setRowHeight(18);
    }

    /** Write a striped table data row. */
    private function writeTableRow(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int   $row,
        int   $rowIndex,
        array $cells,
        array $colWidths,
    ): void {
        $colLetters = $this->columnLetters(count($cells));
        $isAlt      = $rowIndex % 2 === 1;
        $bgColor    = $isAlt ? self::CLR_ALT_ROW : self::CLR_WHITE;

        foreach ($cells as $i => $cell) {
            $col = $colLetters[$i];
            $sheet->setCellValueExplicit("{$col}{$row}", (string) $cell, DataType::TYPE_STRING);
            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                'font'      => ['size' => 8, 'color' => ['argb' => self::CLR_DARK]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => $bgColor]],
                'alignment' => [
                    'horizontal' => $i === 0 ? Alignment::HORIZONTAL_LEFT : Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'indent'     => $i === 0 ? 1 : 0,
                    'wrapText'   => false,
                ],
                'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['argb' => 'FFCBD5E1']]],
            ]);
        }
        $sheet->getRowDimension($row)->setRowHeight(14);
    }

    private function writeTableFooter(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int $row,
        int $colCount,
        array $colWidths,
    ): void {
        $colLetters  = $this->columnLetters($colCount);
        $lastCol     = end($colLetters);
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->setCellValue("A{$row}", '— End of Data —');
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 7, 'color' => ['argb' => self::CLR_MUTED]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::CLR_HEADER_BG]]],
        ]);
    }

    private function writeRecordCount(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int $row,
        int $count,
    ): void {
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", "Total records: {$count}  |  This report is system-generated.");
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 7, 'color' => ['argb' => self::CLR_MUTED]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }

    // =========================================================================
    // PRIVATE  —  UTILITIES
    // =========================================================================

    /** Map 0-based column indices to Excel column letters (A, B, …, Z, AA, …). */
    private function columnLetters(int $count): array
    {
        $letters = [];
        for ($i = 0; $i < $count; $i++) {
            $letters[] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
        }
        return $letters;
    }

    /** Write the spreadsheet to storage and return the full path. */
    private function save(Spreadsheet $spreadsheet): string
    {
        $dir = storage_path('app/' . self::OUTPUT_DIR);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'report-' . now()->format('YmdHis') . '-' . uniqid() . '.xlsx';
        $path     = $dir . '/' . $filename;

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return $path;
    }
}