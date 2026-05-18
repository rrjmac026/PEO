<?php

namespace App\Services\Reports;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * ReportPdfService
 *
 * Generates PDF reports using setasign/fpdf (^1.8).
 *
 * All public methods accept pre-fetched Eloquent collections + summary arrays
 * and return the raw PDF string (FPDF::Output('S')).
 *
 * Usage:
 *   $pdf  = new ReportPdfService();
 *   $raw  = $pdf->workRequestsReport($workRequests, $summary, $range, $filters);
 *   return response($raw, 200, ['Content-Type' => 'application/pdf', ...]);
 */
class ReportPdfService
{
    // ── Brand colours (RGB) ──────────────────────────────────────────────────
    private const COLOR_HEADER_BG = [30, 64, 175];    // Indigo-700
    private const COLOR_ALT_ROW   = [241, 245, 249];  // Slate-100
    private const COLOR_WHITE     = [255, 255, 255];
    private const COLOR_DARK      = [15,  23,  42];   // Slate-900
    private const COLOR_MUTED     = [100, 116, 139];  // Slate-500
    private const COLOR_GREEN     = [22, 163, 74];
    private const COLOR_RED       = [220, 38, 38];
    private const COLOR_YELLOW    = [202, 138, 4];
    private const COLOR_BLUE      = [37, 99, 235];

    // ── Column widths (portrait A4 = 190 usable mm) ─────────────────────────
    private const PAGE_W = 190;  // usable width (A4 portrait, 10 mm margins each side)

    // =========================================================================
    // PUBLIC  —  REPORT GENERATORS
    // =========================================================================

    /**
     * Work Requests Report PDF
     */
    public function workRequestsReport(
        Collection $workRequests,
        array      $summary,
        array      $range,
        array      $filters,
    ): string {
        $pdf = $this->makePdf('Work Requests Report', $range);

        // ── Cover summary ────────────────────────────────────────────────────
        $this->sectionTitle($pdf, 'Summary');
        $this->summaryGrid($pdf, [
            ['Total Requests',    $summary['total']],
            ['Approved',          $summary['approved']],
            ['Rejected',          $summary['rejected']],
            ['Pending / In-Review', $summary['pending'] + ($summary['in_review'] ?? 0)],
            ['Approval Rate',     $summary['approval_rate'] . '%'],
        ]);

        // ── Contractor breakdown ─────────────────────────────────────────────
        $byContractor = $workRequests->groupBy('contractor_name');
        if ($byContractor->count()) {
            $this->sectionTitle($pdf, 'Breakdown by Contractor');
            $this->tableHeader($pdf, ['Contractor', 'Total', 'Approved', 'Rejected', 'Pending'], [75, 28, 28, 28, 31]);
            $row = 0;
            foreach ($byContractor as $contractor => $group) {
                $this->tableRow($pdf, $row++, [
                    $contractor ?: '(Unknown)',
                    $group->count(),
                    $group->where('status', 'approved')->count(),
                    $group->where('status', 'rejected')->count(),
                    $group->whereNotIn('status', ['approved', 'rejected'])->count(),
                ], [75, 28, 28, 28, 31]);
            }
        }

        // ── Detail table ─────────────────────────────────────────────────────
        $this->checkPageBreak($pdf);
        $this->sectionTitle($pdf, 'Detailed Records');

        $cols   = ['Ref #', 'Project', 'Contractor', 'Status', 'Submitted'];
        $widths = [30, 60, 45, 30, 25];
        $this->tableHeader($pdf, $cols, $widths);

        $row = 0;
        foreach ($workRequests as $wr) {
            $this->checkPageBreak($pdf, 8);
            $this->tableRow($pdf, $row++, [
                $wr->reference_number ?? 'N/A',
                $this->truncate($wr->name_of_project, 38),
                $this->truncate($wr->contractor_name, 28),
                strtoupper($wr->status),
                $wr->created_at->format('m/d/Y'),
            ], $widths);
        }

        $this->footer($pdf, $workRequests->count());

        return $pdf->Output('S');
    }

    /**
     * Concrete Pourings Report PDF
     */
    public function concretePouringsReport(
        Collection $concretePourings,
        array      $summary,
        array      $range,
        array      $filters,
    ): string {
        $pdf = $this->makePdf('Concrete Pourings Report', $range);

        // ── Summary ──────────────────────────────────────────────────────────
        $this->sectionTitle($pdf, 'Summary');
        $this->summaryGrid($pdf, [
            ['Total Requests',          $summary['total']],
            ['Approved',                $summary['approved']],
            ['Disapproved',             $summary['disapproved']],
            ['Pending',                 $summary['pending']],
            ['Total Estimated Volume',  number_format($summary['total_volume'], 2) . ' m³'],
            ['Avg Volume / Request',    number_format($summary['avg_volume'], 2) . ' m³'],
            ['Avg Checklist Completion',$summary['avg_checklist_completion'] . '%'],
            ['Approval Rate',           $summary['approval_rate'] . '%'],
        ]);

        // ── Contractor breakdown ─────────────────────────────────────────────
        $byContractor = $concretePourings->groupBy('contractor');
        if ($byContractor->count()) {
            $this->sectionTitle($pdf, 'Breakdown by Contractor');
            $this->tableHeader($pdf, ['Contractor', 'Total', 'Approved', 'Disapproved', 'Volume (m³)'], [60, 25, 28, 32, 45]);
            $row = 0;
            foreach ($byContractor as $contractor => $group) {
                $this->tableRow($pdf, $row++, [
                    $this->truncate($contractor ?: '(Unknown)', 36),
                    $group->count(),
                    $group->where('status', 'approved')->count(),
                    $group->where('status', 'disapproved')->count(),
                    number_format($group->sum('estimated_volume'), 2),
                ], [60, 25, 28, 32, 45]);
            }
        }

        // ── Checklist completion ─────────────────────────────────────────────
        if ($concretePourings->count()) {
            $this->checkPageBreak($pdf);
            $this->sectionTitle($pdf, 'Checklist Compliance Rate');
            $fields = [
                'concrete_vibrator', 'field_density_test', 'protective_covering_materials',
                'beam_cylinder_molds', 'warning_signs_barricades', 'curing_materials',
                'concrete_saw', 'slump_cones', 'concrete_block_spacer', 'plumbness',
                'finishing_tools_equipment', 'quality_of_materials', 'line_grade_alignment',
                'lighting_system', 'required_construction_equipment', 'electrical_layout',
                'rebar_sizes_spacing', 'plumbing_layout', 'rebars_installation',
                'falseworks_formworks',
            ];
            $total = $concretePourings->count();
            $this->tableHeader($pdf, ['Checklist Item', 'Checked', 'Total', 'Rate'], [110, 25, 25, 30]);
            $row = 0;
            foreach ($fields as $field) {
                $checked = $concretePourings->where($field, true)->count();
                $rate    = $total > 0 ? round(($checked / $total) * 100, 1) : 0;
                $this->checkPageBreak($pdf, 8);
                $this->tableRow($pdf, $row++, [
                    ucfirst(str_replace('_', ' ', $field)),
                    $checked,
                    $total,
                    $rate . '%',
                ], [110, 25, 25, 30]);
            }
        }

        // ── Detail table ─────────────────────────────────────────────────────
        $this->checkPageBreak($pdf);
        $this->sectionTitle($pdf, 'Detailed Records');
        $this->tableHeader($pdf, ['Ref #', 'Project', 'Contractor', 'Volume', 'Status', 'Date'], [28, 55, 45, 20, 22, 20]);
        $row = 0;
        foreach ($concretePourings as $cp) {
            $this->checkPageBreak($pdf, 8);
            $this->tableRow($pdf, $row++, [
                $cp->reference_number ?? 'N/A',
                $this->truncate($cp->project_name, 34),
                $this->truncate($cp->contractor ?? '—', 28),
                number_format($cp->estimated_volume, 2),
                strtoupper($cp->status),
                $cp->created_at->format('m/d/Y'),
            ], [28, 55, 45, 20, 22, 20]);
        }

        $this->footer($pdf, $concretePourings->count());

        return $pdf->Output('S');
    }

    /**
     * Memos Report PDF
     */
    public function memosReport(
        Collection $memos,
        array      $summary,
        array      $range,
        array      $filters,
    ): string {
        $pdf = $this->makePdf('Memos Report', $range);

        $this->sectionTitle($pdf, 'Summary');
        $this->summaryGrid($pdf, [
            ['Total Memos',       $summary['total']],
            ['Sent',              $summary['sent']],
            ['Draft',             $summary['draft']],
            ['Scheduled',         $summary['scheduled']],
            ['Total Recipients',  $summary['total_recipients']],
            ['Total Read',        $summary['total_read']],
            ['Avg Read Rate',     $summary['avg_read_rate'] . '%'],
        ]);

        // ── By type ──────────────────────────────────────────────────────────
        $byType = $memos->groupBy('type');
        if ($byType->count()) {
            $this->sectionTitle($pdf, 'Breakdown by Type');
            $this->tableHeader($pdf, ['Type', 'Total', 'Sent', 'Draft / Scheduled'], [80, 35, 35, 40]);
            $row = 0;
            $types = \App\Models\Memo::types();
            foreach ($byType as $type => $group) {
                $this->tableRow($pdf, $row++, [
                    $types[$type] ?? ucfirst($type),
                    $group->count(),
                    $group->where('status', 'sent')->count(),
                    $group->whereIn('status', ['draft', 'scheduled'])->count(),
                ], [80, 35, 35, 40]);
            }
        }

        // ── Detail table ─────────────────────────────────────────────────────
        $this->checkPageBreak($pdf);
        $this->sectionTitle($pdf, 'Detailed Records');
        $this->tableHeader($pdf, ['Ref #', 'Subject', 'Type', 'Status', 'Recipients', 'Read', 'Date'], [25, 55, 28, 20, 22, 18, 22]);
        $row = 0;
        foreach ($memos as $memo) {
            $this->checkPageBreak($pdf, 8);
            $recipientCount = $memo->memoRecipients->count();
            $readCount      = $memo->memoRecipients->whereNotNull('read_at')->count();
            $this->tableRow($pdf, $row++, [
                $memo->reference_number ?? 'N/A',
                $this->truncate($memo->subject, 34),
                $this->truncate($memo->type_label, 18),
                strtoupper($memo->status),
                $recipientCount,
                $readCount,
                $memo->created_at->format('m/d/Y'),
            ], [25, 55, 28, 20, 22, 18, 22]);
        }

        $this->footer($pdf, $memos->count());

        return $pdf->Output('S');
    }

    /**
     * Combined Overview Report PDF (all three modules)
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
        $pdf = $this->makePdf('System Overview Report', $range);

        // ── Overall stats ────────────────────────────────────────────────────
        $this->sectionTitle($pdf, 'Work Requests');
        $this->summaryGrid($pdf, [
            ['Total',        $wrSummary['total']],
            ['Approved',     $wrSummary['approved']],
            ['Rejected',     $wrSummary['rejected']],
            ['Approval Rate',$wrSummary['approval_rate'] . '%'],
        ]);

        $this->sectionTitle($pdf, 'Concrete Pourings');
        $this->summaryGrid($pdf, [
            ['Total',          $cpSummary['total']],
            ['Approved',       $cpSummary['approved']],
            ['Disapproved',    $cpSummary['disapproved']],
            ['Total Volume',   number_format($cpSummary['total_volume'], 2) . ' m³'],
            ['Approval Rate',  $cpSummary['approval_rate'] . '%'],
        ]);

        $this->sectionTitle($pdf, 'Memos');
        $this->summaryGrid($pdf, [
            ['Total',           $memoSummary['total']],
            ['Sent',            $memoSummary['sent']],
            ['Total Recipients',$memoSummary['total_recipients']],
            ['Avg Read Rate',   $memoSummary['avg_read_rate'] . '%'],
        ]);

        $this->footer($pdf, $workRequests->count() + $concretePourings->count() + $memos->count());

        return $pdf->Output('S');
    }

    // =========================================================================
    // PRIVATE  —  PDF BUILDING HELPERS
    // =========================================================================

    /**
     * Bootstrap a new FPDF instance with agency header.
     */
    private function makePdf(string $title, array $range): \FPDF
    {
        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        // ── Agency header bar ────────────────────────────────────────────────
        $pdf->SetFillColor(...self::COLOR_HEADER_BG);
        $pdf->SetTextColor(...self::COLOR_WHITE);
        $pdf->Rect(10, 10, self::PAGE_W, 18, 'F');

        $pdf->SetFont('Helvetica', 'B', 14);
        $pdf->SetXY(12, 13);
        $pdf->Cell(self::PAGE_W - 4, 7, 'DEPARTMENT OF PUBLIC WORKS AND HIGHWAYS', 0, 1, 'L');

        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetXY(12, 20);
        $pdf->Cell(self::PAGE_W - 4, 5, strtoupper($title), 0, 1, 'L');

        // ── Date range band ──────────────────────────────────────────────────
        $pdf->SetFillColor(226, 232, 240);   // Slate-200
        $pdf->SetTextColor(...self::COLOR_DARK);
        $pdf->SetXY(10, 30);
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetFillColor(226, 232, 240);
        $pdf->Rect(10, 30, self::PAGE_W, 7, 'F');
        $pdf->SetXY(12, 31);
        $pdf->Cell(self::PAGE_W / 2, 5, 'Report Period: ' . $range['label'], 0, 0, 'L');
        $pdf->Cell(self::PAGE_W / 2, 5, 'Generated: ' . now()->format('M d, Y  h:i A'), 0, 1, 'R');

        $pdf->Ln(4);

        return $pdf;
    }

    private function sectionTitle(\FPDF $pdf, string $title): void
    {
        $pdf->Ln(2);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(...self::COLOR_HEADER_BG);
        $pdf->Cell(self::PAGE_W, 6, strtoupper($title), 'B', 1, 'L');
        $pdf->SetTextColor(...self::COLOR_DARK);
        $pdf->Ln(1);
    }

    /**
     * Render a 2-column key/value summary grid (max 4 per row).
     */
    private function summaryGrid(\FPDF $pdf, array $items): void
    {
        $perRow  = 4;
        $cellW   = self::PAGE_W / $perRow;

        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetFillColor(248, 250, 252);

        $chunks = array_chunk($items, $perRow);
        foreach ($chunks as $chunk) {
            $y = $pdf->GetY();
            $x = 10;
            foreach ($chunk as $item) {
                $pdf->SetXY($x, $y);
                $pdf->SetFillColor(241, 245, 249);
                $pdf->Rect($x, $y, $cellW - 1, 12, 'F');
                $pdf->SetFont('Helvetica', '', 7);
                $pdf->SetTextColor(...self::COLOR_MUTED);
                $pdf->SetXY($x + 1, $y + 1);
                $pdf->Cell($cellW - 2, 4, $item[0], 0, 1, 'L');
                $pdf->SetFont('Helvetica', 'B', 10);
                $pdf->SetTextColor(...self::COLOR_DARK);
                $pdf->SetXY($x + 1, $y + 5);
                $pdf->Cell($cellW - 2, 6, (string) $item[1], 0, 0, 'L');
                $x += $cellW;
            }
            $pdf->SetXY(10, $y + 14);
        }
        $pdf->Ln(2);
    }

    private function tableHeader(\FPDF $pdf, array $cols, array $widths): void
    {
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->SetFillColor(...self::COLOR_HEADER_BG);
        $pdf->SetTextColor(...self::COLOR_WHITE);
        $pdf->SetLineWidth(0);

        foreach ($cols as $i => $col) {
            $pdf->Cell($widths[$i], 7, $col, 0, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetTextColor(...self::COLOR_DARK);
    }

    private function tableRow(\FPDF $pdf, int $rowIndex, array $cells, array $widths): void
    {
        $pdf->SetFont('Helvetica', '', 7.5);
        $fill = ($rowIndex % 2 === 1);
        $pdf->SetFillColor(...self::COLOR_ALT_ROW);

        foreach ($cells as $i => $cell) {
            $align = ($i === 0) ? 'L' : 'C';
            $pdf->Cell($widths[$i], 6, (string) $cell, 0, 0, $align, $fill);
        }
        $pdf->Ln();
    }

    private function checkPageBreak(\FPDF $pdf, int $reserveLines = 20): void
    {
        if ($pdf->GetY() > (297 - 30 - $reserveLines)) {
            $pdf->AddPage();
            $pdf->Ln(4);
        }
    }

    private function footer(\FPDF $pdf, int $recordCount): void
    {
        $pdf->Ln(4);
        $pdf->SetFont('Helvetica', 'I', 7);
        $pdf->SetTextColor(...self::COLOR_MUTED);
        $pdf->Cell(self::PAGE_W, 5, "Total records: {$recordCount}  |  This report is system-generated.", 'T', 1, 'C');
    }

    private function truncate(string $text, int $max): string
    {
        return mb_strlen($text) > $max ? mb_substr($text, 0, $max - 1) . '…' : $text;
    }
}