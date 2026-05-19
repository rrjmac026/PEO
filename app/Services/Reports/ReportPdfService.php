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
    // ── Brand colours — sourced from login blade palette (RGB) ───────────────
    private const COLOR_HEADER_BG  = [224,  90,   0];   // --orange       #E05A00
    private const COLOR_HEADER_FG  = [255, 255, 255];   // white
    private const COLOR_ALT_ROW    = [255, 248, 240];   // --cream        #FFF8F0
    private const COLOR_WHITE      = [255, 255, 255];
    private const COLOR_DARK       = [ 44,  30,  18];   // --stone        #2C1E12
    private const COLOR_MUTED      = [107,  79,  58];   // --stone-mid    #6B4F3A
    private const COLOR_SECTION_BG = [245, 237, 228];   // --gray-soft    #F5EDE4
    private const COLOR_SECTION_FG = [184,  74,   0];   // --orange-dark  #B84A00
    private const COLOR_GREEN      = [ 22, 163,  74];
    private const COLOR_RED        = [220,  38,  38];
    private const COLOR_YELLOW     = [202, 138,   4];
    private const COLOR_BLUE       = [ 37,  99, 235];

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
            ['Total Requests',        $summary['total']],
            ['Approved',              $summary['approved']],
            ['Rejected',              $summary['rejected']],
            ['Pending / In-Review',   $summary['pending'] + ($summary['in_review'] ?? 0)],
            ['Approval Rate',         $summary['approval_rate'] . '%'],
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
        // Reserve room for section title (8) + header row (7) + first data row (6) = ~25 mm
        $this->checkPageBreak($pdf, 25);
        $this->sectionTitle($pdf, 'Detailed Records');

        $detailCols   = ['Ref #', 'Project', 'Contractor', 'Status', 'Submitted'];
        $detailWidths = [30, 60, 45, 30, 25];
        $this->tableHeader($pdf, $detailCols, $detailWidths);

        $row = 0;
        foreach ($workRequests as $wr) {
            // Re-draw column header whenever a new page is needed
            if ($pdf->GetY() > (297 - 30 - 8)) {
                $pdf->AddPage();
                $pdf->Ln(4);
                $this->tableHeader($pdf, $detailCols, $detailWidths);
            }
            $this->tableRow($pdf, $row++, [
                $wr->reference_number ?? 'N/A',
                $this->truncate($wr->name_of_project, 38),
                $this->truncate($wr->contractor_name, 28),
                strtoupper($wr->status),
                $wr->created_at->format('m/d/Y'),
            ], $detailWidths);
        }

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
            ['Total Requests',           $summary['total']],
            ['Approved',                 $summary['approved']],
            ['Disapproved',              $summary['disapproved']],
            ['Pending',                  $summary['pending']],
            // enc() converts m³ -> "m\xb3" so FPDF renders it correctly
            ['Total Estimated Volume',   number_format($summary['total_volume'], 2) . ' m' . chr(179)],
            ['Avg Volume / Request',     number_format($summary['avg_volume'], 2)   . ' m' . chr(179)],
            ['Avg Checklist Completion', $summary['avg_checklist_completion'] . '%'],
            ['Approval Rate',            $summary['approval_rate'] . '%'],
        ]);

        // ── Contractor breakdown ─────────────────────────────────────────────
        $byContractor = $concretePourings->groupBy('contractor');
        if ($byContractor->count()) {
            $this->sectionTitle($pdf, 'Breakdown by Contractor');
            $this->tableHeader($pdf, ['Contractor', 'Total', 'Approved', 'Disapproved', 'Volume (m' . chr(179) . ')'], [60, 25, 28, 32, 45]);
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
        // Always start Detailed Records on a fresh page so the section title,
        // column header, and first data row are never orphaned at page bottom.
        $pdf->AddPage();
        $pdf->Ln(4);
        $this->sectionTitle($pdf, 'Detailed Records');

        $detailCols   = ['Ref #', 'Project', 'Contractor', 'Volume', 'Status', 'Date'];
        $detailWidths = [28, 55, 45, 20, 22, 20];
        $this->tableHeader($pdf, $detailCols, $detailWidths);

        $row = 0;
        foreach ($concretePourings as $cp) {
            // Re-draw column header whenever a new page is needed
            if ($pdf->GetY() > (297 - 30 - 8)) {
                $pdf->AddPage();
                $pdf->Ln(4);
                $this->tableHeader($pdf, $detailCols, $detailWidths);
            }
            $this->tableRow($pdf, $row++, [
                $cp->reference_number ?? 'N/A',
                $this->truncate($cp->project_name, 34),
                $this->truncate($cp->contractor ?? chr(151), 28),
                number_format($cp->estimated_volume, 2),
                strtoupper($cp->status),
                $cp->created_at->format('m/d/Y'),
            ], $detailWidths);
        }

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
            ['Total Memos',      $summary['total']],
            ['Sent',             $summary['sent']],
            ['Draft',            $summary['draft']],
            ['Scheduled',        $summary['scheduled']],
            ['Total Recipients', $summary['total_recipients']],
            ['Total Read',       $summary['total_read']],
            ['Avg Read Rate',    $summary['avg_read_rate'] . '%'],
        ]);

        // ── By type ──────────────────────────────────────────────────────────
        $byType = $memos->groupBy('type');
        if ($byType->count()) {
            $this->sectionTitle($pdf, 'Breakdown by Type');
            $this->tableHeader($pdf, ['Type', 'Total', 'Sent', 'Draft / Scheduled'], [80, 35, 35, 40]);
            $row   = 0;
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
        // Reserve room for section title (8) + header row (7) + first data row (6) = ~25 mm
        $this->checkPageBreak($pdf, 25);
        $this->sectionTitle($pdf, 'Detailed Records');

        $detailCols   = ['Ref #', 'Subject', 'Type', 'Status', 'Recipients', 'Read', 'Date'];
        $detailWidths = [25, 55, 28, 20, 22, 18, 22];
        $this->tableHeader($pdf, $detailCols, $detailWidths);

        $row = 0;
        foreach ($memos as $memo) {
            // Re-draw column header whenever a new page is needed
            if ($pdf->GetY() > (297 - 30 - 8)) {
                $pdf->AddPage();
                $pdf->Ln(4);
                $this->tableHeader($pdf, $detailCols, $detailWidths);
            }
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
            ], $detailWidths);
        }

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
            ['Total',         $wrSummary['total']],
            ['Approved',      $wrSummary['approved']],
            ['Rejected',      $wrSummary['rejected']],
            ['Approval Rate', $wrSummary['approval_rate'] . '%'],
        ]);

        $this->sectionTitle($pdf, 'Concrete Pourings');
        $this->summaryGrid($pdf, [
            ['Total',          $cpSummary['total']],
            ['Approved',       $cpSummary['approved']],
            ['Disapproved',    $cpSummary['disapproved']],
            ['Total Volume',   number_format($cpSummary['total_volume'], 2) . ' m' . chr(179)],
            ['Approval Rate',  $cpSummary['approval_rate'] . '%'],
        ]);

        $this->sectionTitle($pdf, 'Memos');
        $this->summaryGrid($pdf, [
            ['Total',            $memoSummary['total']],
            ['Sent',             $memoSummary['sent']],
            ['Total Recipients', $memoSummary['total_recipients']],
            ['Avg Read Rate',    $memoSummary['avg_read_rate'] . '%'],
        ]);

        return $pdf->Output('S');
    }

    // =========================================================================
    // PRIVATE  —  PDF BUILDING HELPERS
    // =========================================================================

    /**
     * Bootstrap a new FPDF subclass instance with agency header and a
     * sticky footer that FPDF calls automatically on every page.
     *
     * Logo layout mirrors ConcretePouringPdf::drawHeader():
     *   [province_seal]   centered text block   [app_logo]
     */
    private function makePdf(string $title, array $range): \FPDF
    {
        // ── Anonymous subclass — gives us a real Footer() override ──────────
        $recordCount = 0; // will be set after content is written via setFooterCount()

        $pdf = new class(
            'P', 'mm', 'A4',
            self::COLOR_MUTED,
            self::COLOR_SECTION_BG,
            self::PAGE_W,
        ) extends \FPDF {
            private array  $mutedColor;
            private array  $sectionBg;
            private int    $pageW;
            public  int    $footerRecordCount = 0;

            public function __construct(
                string $orientation,
                string $unit,
                string $size,
                array  $mutedColor,
                array  $sectionBg,
                int    $pageW,
            ) {
                parent::__construct($orientation, $unit, $size);
                $this->mutedColor = $mutedColor;
                $this->sectionBg  = $sectionBg;
                $this->pageW      = $pageW;
            }

            /** Called automatically by FPDF before each page break / Output(). */
            public function Footer(): void
            {
                // Position 15 mm from the bottom
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 7);
                $this->SetTextColor(...$this->mutedColor);
                $this->SetFillColor(...$this->sectionBg);

                // Thin top border line
                $this->SetLineWidth(0.2);
                $this->SetDrawColor(...$this->mutedColor);
                $this->Line(10, $this->GetY(), 10 + $this->pageW, $this->GetY());
                $this->Ln(1);

                $left  = 'Total records: ' . $this->footerRecordCount . '  |  This report is system-generated.';
                $right = 'Page ' . $this->PageNo() . ' of {nb}';

                $this->Cell($this->pageW / 2, 5, $left,  0, 0, 'L');
                $this->Cell($this->pageW / 2, 5, $right, 0, 0, 'R');
            }
        };

        $pdf->AliasNbPages();          // enables {nb} total-page placeholder
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        // ── Logo / seal layout ───────────────────────────────────────────────
        $imgSize = 18;
        $gap     = 3;
        $cw      = 80;
        $cx      = 10 + (self::PAGE_W - $cw) / 2;
        $imgY    = 10;

        $seal = public_path('assets/province_seal_small.png');
        if (file_exists($seal)) {
            $pdf->Image($seal, $cx - $imgSize - $gap, $imgY + 1, $imgSize, $imgSize);
        }

        $logo = public_path('assets/app_logo_small.png');
        if (file_exists($logo)) {
            $pdf->Image($logo, $cx + $cw + $gap, $imgY + 1, $imgSize, $imgSize);
        }

        // ── Centred agency text ──────────────────────────────────────────────
        $pdf->SetXY($cx, $imgY + 2);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(...self::COLOR_DARK);
        $pdf->Cell($cw, 4, 'Republic of the Philippines', 0, 2, 'C');

        $pdf->SetX($cx);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($cw, 4, 'PROVINCE OF BUKIDNON', 0, 2, 'C');

        $pdf->SetX($cx);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($cw, 4, "PROVINCIAL ENGINEER'S OFFICE", 0, 2, 'C');

        $pdf->SetX($cx);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($cw, 4, 'Provincial Capitol 8700', 0, 2, 'C');

        // ── Orange title band (below logos) ─────────────────────────────────
        $headerBottom = $imgY + $imgSize + 4;

        $pdf->SetXY(10, $headerBottom);
        $pdf->SetFillColor(...self::COLOR_HEADER_BG);
        $pdf->SetTextColor(...self::COLOR_HEADER_FG);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(self::PAGE_W, 7, strtoupper($title), 0, 1, 'C', true);

        // ── Date range band ──────────────────────────────────────────────────
        $pdf->SetFillColor(...self::COLOR_SECTION_BG);
        $pdf->SetTextColor(...self::COLOR_DARK);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(10);
        // enc() used here: range['label'] may contain em-dash from Carbon
        $pdf->Cell(self::PAGE_W / 2, 6, $this->enc('Report Period: ' . $range['label']), 0, 0, 'L', true);
        $pdf->Cell(self::PAGE_W / 2, 6, 'Generated: ' . now()->format('M d, Y  h:i A'),  0, 1, 'R', true);

        $pdf->SetTextColor(...self::COLOR_DARK);
        $pdf->Ln(4);

        return $pdf;
    }

    private function sectionTitle(\FPDF $pdf, string $title): void
    {
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(...self::COLOR_SECTION_FG);
        $pdf->SetFillColor(...self::COLOR_SECTION_BG);
        $pdf->Cell(self::PAGE_W, 6, strtoupper($title), 'B', 1, 'L', true);
        $pdf->SetTextColor(...self::COLOR_DARK);
        $pdf->Ln(1);
    }

    /**
     * Render a 2-column key/value summary grid (max 4 per row).
     */
    private function summaryGrid(\FPDF $pdf, array $items): void
    {
        $perRow = 4;
        $cellW  = self::PAGE_W / $perRow;

        $pdf->SetFont('Arial', '', 8);

        $chunks = array_chunk($items, $perRow);
        foreach ($chunks as $chunk) {
            $y = $pdf->GetY();
            $x = 10;
            foreach ($chunk as $item) {
                $pdf->SetXY($x, $y);
                $pdf->SetFillColor(...self::COLOR_ALT_ROW);
                $pdf->Rect($x, $y, $cellW - 1, 12, 'F');
                $pdf->SetFont('Arial', '', 7);
                $pdf->SetTextColor(...self::COLOR_MUTED);
                $pdf->SetXY($x + 1, $y + 1);
                $pdf->Cell($cellW - 2, 4, $this->enc((string) $item[0]), 0, 1, 'L');
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetTextColor(...self::COLOR_DARK);
                $pdf->SetXY($x + 1, $y + 5);
                $pdf->Cell($cellW - 2, 6, $this->enc((string) $item[1]), 0, 0, 'L');
                $x += $cellW;
            }
            $pdf->SetXY(10, $y + 14);
        }
        $pdf->Ln(2);
    }

    private function tableHeader(\FPDF $pdf, array $cols, array $widths): void
    {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(...self::COLOR_HEADER_BG);
        $pdf->SetTextColor(...self::COLOR_HEADER_FG);
        $pdf->SetLineWidth(0);

        foreach ($cols as $i => $col) {
            $pdf->Cell($widths[$i], 7, $this->enc($col), 0, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetTextColor(...self::COLOR_DARK);
    }

    private function tableRow(\FPDF $pdf, int $rowIndex, array $cells, array $widths): void
    {
        $pdf->SetFont('Arial', '', 7.5);
        $fill = ($rowIndex % 2 === 1);
        $pdf->SetFillColor(...self::COLOR_ALT_ROW);

        foreach ($cells as $i => $cell) {
            $align = ($i === 0) ? 'L' : 'C';
            $pdf->Cell($widths[$i], 6, $this->enc((string) $cell), 0, 0, $align, $fill);
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

    // =========================================================================
    // PRIVATE  —  UTILITIES
    // =========================================================================

    /**
     * Convert a UTF-8 string to ISO-8859-1 so FPDF renders special characters
     * correctly (e.g. em-dash U+2014 -> chr(151), superscript-3 U+00B3 -> chr(179)).
     *
     * Characters that have no ISO-8859-1 equivalent are transliterated or dropped.
     */
    private function enc(string $text): string
    {
        // iconv with //TRANSLIT falls back gracefully for unmapped chars
        $converted = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);
        return $converted !== false ? $converted : $text;
    }

    private function truncate(string $text, int $max): string
    {
        return mb_strlen($text) > $max ? mb_substr($text, 0, $max - 1) . '...' : $text;
    }
}