<?php

namespace App\Services;

use App\Models\ConcretePouring;
use Carbon\Carbon;

/**
 * ConcretePouring PDF Generator — pixel-perfect match to the official form.
 *
 * Requires: composer require setasign/fpdf
 * Images:   public/assets/province_seal_small.png  (200x200px max)
 *           public/assets/app_logo_small.png        (200x200px max)
 *
 * Usage:
 *   $pdf = new ConcretePouringPdf($concretePouring);
 *   $pdf->Output('I', 'concrete-pouring.pdf');  // inline
 *   $pdf->Output('D', 'concrete-pouring.pdf');  // download
 */
class ConcretePouringPdf extends \FPDF
{
    // ── Page geometry (mm) ────────────────────────────────────────────────────
    private const ML = 14;   // left/right margin
    private const MT = 10;   // top margin
    private const BW = 182;  // body width (210 - 14 - 14)

    // ── Two-column layout (checklist table) ───────────────────────────────────
    private const CL = 91;   // left checklist column  (BW / 2)
    private const CR = 91;   // right checklist column (BW / 2)

    // ── Checkbox column inside each half ──────────────────────────────────────
    private const CB_W = 8;  // checkbox cell width
    private const LB_W = 83; // label cell width  (CL - CB_W)

    // ── Colors ────────────────────────────────────────────────────────────────
    private const BLUE  = [0, 176, 240];
    private const BLACK = [0, 0, 0];
    private const WHITE = [255, 255, 255];
    private const DGRAY = [80, 80, 80];

    private ConcretePouring $cp;

    /** Temp files created during rendering — cleaned up on destruct */
    private array $tmpFiles = [];

    public function __construct(ConcretePouring $concretePouring)
    {
        parent::__construct('P', 'mm', 'A4');
        $this->cp = $concretePouring;
        $this->SetMargins(self::ML, self::MT, self::ML);
        $this->SetAutoPageBreak(false);
        $this->AddPage();
        $this->SetFont('Arial', '', 8);
        $this->build();
    }

    public function __destruct()
    {
        foreach ($this->tmpFiles as $f) {
            if (file_exists($f)) {
                unlink($f);
            }
        }
    }

    // ─── MAIN BUILD ──────────────────────────────────────────────────────────

    private function build(): void
    {
        $y = self::MT;
        $y = $this->drawHeader($y);
        $y = $this->drawProjectInfo($y);
        $y = $this->drawRequested($y);
        $y = $this->drawChecklistBand($y);
        $y = $this->drawChecklist($y);
        $y = $this->drawCheckedBy($y);
        $y = $this->drawMeMtqaBlock($y);
        $y = $this->drawResidentEngineerBlock($y);
        $y = $this->drawApprovalRow($y);
        $y = $this->drawNotedBy($y);
    }

    // ─── HEADER ──────────────────────────────────────────────────────────────

    private function drawHeader(float $y): float
    {
        $imgSize = 18;
        $gap     = 3;
        $cw      = 80;
        $cx      = self::ML + (self::BW - $cw) / 2;

        $seal = public_path('assets/province_seal_small.png');
        if (file_exists($seal)) {
            $this->Image($seal, $cx - $imgSize - $gap, $y + 1, $imgSize, $imgSize);
        }

        $logo = public_path('assets/app_logo_small.png');
        if (file_exists($logo)) {
            $this->Image($logo, $cx + $cw + $gap, $y + 1, $imgSize, $imgSize);
        }

        $this->SetXY($cx, $y + 2);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell($cw, 4, 'Republic of the Philippines', 0, 2, 'C');

        $this->SetX($cx);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell($cw, 4, 'PROVINCE OF BUKIDNON', 0, 2, 'C');

        $this->SetX($cx);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell($cw, 4, 'PROVINCIAL ENGINEER\'S OFFICE', 0, 2, 'C');

        $this->SetX($cx);
        $this->SetFont('Arial', '', 8);
        $this->Cell($cw, 4, 'Provincial Capitol 8700', 0, 2, 'C');

        $headerBottom = $y + $imgSize + 4;

        // ── Blue title banner ─────────────────────────────────────────────────
        $this->SetXY(self::ML, $headerBottom);
        $this->SetFillColor(...self::BLUE);
        $this->SetTextColor(...self::WHITE);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(self::BW, 7, 'CONCRETE POURING FORM', 0, 1, 'C', true);

        $this->SetX(self::ML);
        $this->SetFillColor(...self::WHITE);
        $this->SetTextColor(...self::BLACK);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(self::BW, 4, '(In Triplicate)', 0, 1, 'C');

        return $headerBottom + 11;
    }

    // ─── PROJECT INFORMATION ROWS ─────────────────────────────────────────────

    private function drawProjectInfo(float $y): float
    {
        $rows = [
            ['Name of Project',            $this->val($this->cp->project_name)],
            ['Location',                   $this->val($this->cp->location)],
            ['Contractor',                 $this->val($this->cp->contractor)],
            ['Part of Structure to be poured', $this->val($this->cp->part_of_structure)],
            ['Estimated Volume (cu.m)',     $this->val((string) $this->cp->estimated_volume)],
            ['Station Limits/Section',     $this->val($this->cp->station_limits_section)],
            ['Date and Time of Pouring',   $this->fmtDatetime($this->cp->pouring_datetime)],
        ];

        $lh    = 6;
        $lblW  = 70;
        $colonW = 5;
        $valW  = self::BW - $lblW - $colonW;

        foreach ($rows as [$label, $value]) {
            $this->SetDrawColor(...self::BLACK);
            $this->SetLineWidth(0.2);

            // Full-width outer border for each row
            $this->Rect(self::ML, $y, self::BW, $lh);

            // Label
            $this->SetXY(self::ML + 1, $y + 1);
            $this->SetFont('Arial', '', 8);
            $this->SetTextColor(...self::BLACK);
            $this->Cell($lblW, $lh - 2, $label, 0);

            // Colon
            $this->SetFont('Arial', '', 8);
            $this->Cell($colonW, $lh - 2, ':', 0, 0, 'C');

            // Value
            $this->SetFont('Arial', 'B', 8);
            $this->Cell($valW - 2, $lh - 2, $value, 0);

            $y += $lh;
        }

        return $y;
    }

    // ─── REQUESTED BY LINE ────────────────────────────────────────────────────

    private function drawRequested(float $y): float
    {
        $y += 4;

        $this->SetXY(self::ML, $y);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::BW, 4, 'Requested by:', 0, 1, 'L');

        $y += 4;

        // Signature line for contractor
        $lineW = 70;
        $lineX = self::ML + (self::BW - $lineW) / 2;

        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.3);
        $this->Line($lineX, $y + 6, $lineX + $lineW, $y + 6);

        $this->SetXY($lineX, $y + 7);
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell($lineW, 3, 'Contractor', 0, 1, 'C');

        // If we have a requestedBy name, print it above the line
        if ($this->cp->requestedBy && $this->cp->requestedBy->user) {
            $name = $this->cp->requestedBy->user->name ?? '';
            $this->SetXY($lineX, $y + 2);
            $this->SetFont('Arial', 'B', 8);
            $this->SetTextColor(...self::BLACK);
            $this->Cell($lineW, 4, $name, 0, 0, 'C');
        }

        return $y + 12;
    }

    // ─── CHECKLIST BAND ───────────────────────────────────────────────────────

    private function drawChecklistBand(float $y): float
    {
        $this->SetXY(self::ML, $y);
        $this->SetFillColor(...self::BLUE);
        $this->SetTextColor(...self::WHITE);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(self::BW, 6, 'CHECKLIST', 1, 1, 'C', true);
        $this->resetColors();
        return $y + 6;
    }

    // ─── CHECKLIST ITEMS ──────────────────────────────────────────────────────

    private function drawChecklist(float $y): float
    {
        // Pairs: [left item label => model field,  right item label => model field]
        $pairs = [
            ['Concrete Vibrator',                          'concrete_vibrator',
             'Field Density Test (FDT)',                   'field_density_test'],
            ['Protective Covering Materials',              'protective_covering_materials',
             'BEAM/Cylinder Molds',                        'beam_cylinder_molds'],
            ['Warning Signs/Barricades/Flagmen',           'warning_signs_barricades',
             'Curing Materials',                           'curing_materials'],
            ['Concrete Saw',                               'concrete_saw',
             'Slump Cones',                                'slump_cones'],
            ['Concrete Block Spacer',                      'concrete_block_spacer',
             'Plumbness',                                  'plumbness'],
            ['Finishing Tools/Equipment (Screeder, Broom, etc)', 'finishing_tools_equipment',
             'Quality of Materials (Result of Design/Trial Mix Test Reports)', 'quality_of_materials'],
            ['Line and Grade Alignment (Form setting, elevation, etc)', 'line_grade_alignment',
             'Lighting System',                            'lighting_system'],
            ['Required Construction Equipment',            'required_construction_equipment',
             'Electrical Layout (Roughing-Ins/Embedment)', 'electrical_layout'],
            ['Rebar Sizes, Spacing and number',            'rebar_sizes_spacing',
             'Plumbing Layout (Roughing-Ins/Embedment)',   'plumbing_layout'],
            ['Rebars installation requirement',            'rebars_installation',
             'Falseworks/Formworks Adequacy',              'falseworks_formworks'],
        ];

        $rowH = 6;

        foreach ($pairs as [$leftLabel, $leftField, $rightLabel, $rightField]) {
            $this->drawChecklistRow($y, $leftLabel, $leftField, $rightLabel, $rightField, $rowH);
            $y += $rowH;
        }

        return $y;
    }

    private function drawChecklistRow(
        float  $y,
        string $leftLabel,
        string $leftField,
        string $rightLabel,
        string $rightField,
        float  $rowH
    ): void {
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);

        $xL  = self::ML;
        $xR  = self::ML + self::CL;

        // Left cell border
        $this->Rect($xL, $y, self::CL, $rowH);
        // Right cell border (shares left border with left cell)
        $this->Rect($xR, $y, self::CR, $rowH);

        // ── Left checkbox ─────────────────────────────────────────────────────
        $this->drawCheckbox($xL + 1, $y + 1.2, (bool) $this->cp->{$leftField});

        $this->SetXY($xL + self::CB_W + 1, $y + 1);
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::LB_W - 2, $rowH - 2, $leftLabel, 0);

        // ── Right checkbox ────────────────────────────────────────────────────
        $this->drawCheckbox($xR + 1, $y + 1.2, (bool) $this->cp->{$rightField});

        $this->SetXY($xR + self::CB_W + 1, $y + 1);
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::LB_W - 2, $rowH - 2, $rightLabel, 0);
    }

    /**
     * Draw a small checkbox at ($x, $y), optionally ticked.
     */
    private function drawCheckbox(float $x, float $y, bool $checked): void
    {
        $size = 3.5;
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);
        $this->Rect($x, $y, $size, $size);

        if ($checked) {
            // Draw a simple tick (two lines)
            $this->SetLineWidth(0.4);
            $this->Line($x + 0.5, $y + 1.8, $x + 1.4, $y + $size - 0.5);
            $this->Line($x + 1.4, $y + $size - 0.5, $x + $size - 0.3, $y + 0.5);
            $this->SetLineWidth(0.2);
        }
    }

    // ─── CHECKED BY LABEL ────────────────────────────────────────────────────

    private function drawCheckedBy(float $y): float
    {
        $y += 3;
        $this->SetXY(self::ML, $y);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::BW, 4, 'Checked by:', 0, 1, 'L');
        return $y + 5;
    }

    // ─── ME/MTQA REVIEW BLOCK ─────────────────────────────────────────────────

    private function drawMeMtqaBlock(float $y): float
    {
        $h = 22;
        $hw = self::BW / 2;  // half width

        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);
        $this->Rect(self::ML, $y, self::BW, $h);

        // Remarks label (left half)
        $this->SetXY(self::ML + 1, $y + 1);
        $this->SetFont('Arial', '', 7.5);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell(30, 3.5, 'Remarks/Recommendation :', 0);

        // Remarks value
        $this->SetXY(self::ML + 32, $y + 1);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->MultiCell(self::BW - 33, 3.5, $this->val($this->cp->me_mtqa_remarks), 0);

        // Divider between name and date
        $this->Line(self::ML + $hw, $y + $h * 0.55, self::ML + self::BW, $y + $h * 0.55);

        // ME/MTQA signature line
        $this->sigLine(
            self::ML,
            $y,
            $hw,
            $this->meMtqaName(),
            'ME/MTQA',
            null,
            $h
        );

        // Date block (right side)
        $this->dateLine(
            self::ML + $hw,
            $y,
            $hw,
            $this->fmtDate($this->cp->me_mtqa_date),
            'DATE',
            $h
        );

        return $y + $h;
    }

    // ─── RESIDENT ENGINEER REVIEW BLOCK ──────────────────────────────────────

    private function drawResidentEngineerBlock(float $y): float
    {
        $h  = 22;
        $hw = self::BW / 2;

        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);
        $this->Rect(self::ML, $y, self::BW, $h);

        // Remarks label
        $this->SetXY(self::ML + 1, $y + 1);
        $this->SetFont('Arial', '', 7.5);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell(30, 3.5, 'Remarks/Recommendation :', 0);

        // Remarks value
        $this->SetXY(self::ML + 32, $y + 1);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->MultiCell(self::BW - 33, 3.5, $this->val($this->cp->re_remarks), 0);

        // Divider
        $this->Line(self::ML + $hw, $y + $h * 0.55, self::ML + self::BW, $y + $h * 0.55);

        // Resident Engineer signature line
        $this->sigLine(
            self::ML,
            $y,
            $hw,
            $this->residentEngineerName(),
            'Resident Engineer/Project In-Charge',
            null,
            $h
        );

        // Date block
        $this->dateLine(
            self::ML + $hw,
            $y,
            $hw,
            $this->fmtDate($this->cp->re_date),
            'DATE',
            $h
        );

        return $y + $h;
    }

    // ─── APPROVAL / DISAPPROVAL ROW ───────────────────────────────────────────

    private function drawApprovalRow(float $y): float
    {
        $h = 12;
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(...self::BLACK);

        // Three segments: Request | Approved | Disapproved
        $segW = self::BW / 3;

        // Draw outer border
        $this->Rect(self::ML, $y, self::BW, $h);

        // Internal dividers
        $this->Line(self::ML + $segW,     $y, self::ML + $segW,     $y + $h);
        $this->Line(self::ML + $segW * 2, $y, self::ML + $segW * 2, $y + $h);

        // Request segment
        $this->SetXY(self::ML + 1, $y + 2);
        $this->SetFont('Arial', '', 7.5);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell(20, 3.5, 'Request :', 0);

        $this->SetXY(self::ML + 1, $y + 6);
        $this->SetFont('Arial', '', 7.5);
        $this->SetTextColor(...self::BLACK);
        $lineW = $segW - 4;
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.3);
        $this->Line(self::ML + 1, $y + 9, self::ML + 1 + $lineW, $y + 9);
        $this->SetLineWidth(0.2);

        // Approved segment
        $xApp = self::ML + $segW + 1;
        $this->SetXY($xApp, $y + 2);
        $this->SetFont('Arial', '', 7.5);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell(20, 3.5, 'Approved :', 0);

        $approvedName = $this->approverName();
        $this->SetXY($xApp, $y + 5.5);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell($segW - 2, 3.5, $approvedName, 0, 0, 'C');
        $this->SetLineWidth(0.3);
        $this->Line($xApp, $y + 9, $xApp + $segW - 2, $y + 9);
        $this->SetLineWidth(0.2);

        // Disapproved segment
        $xDis = self::ML + $segW * 2 + 1;
        $this->SetXY($xDis, $y + 2);
        $this->SetFont('Arial', '', 7.5);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell(25, 3.5, 'Disapproved :', 0);

        $disapprovedName = $this->disapproverName();
        $this->SetXY($xDis, $y + 5.5);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell($segW - 2, 3.5, $disapprovedName, 0, 0, 'C');
        $this->SetLineWidth(0.3);
        $this->Line($xDis, $y + 9, $xDis + $segW - 3, $y + 9);
        $this->SetLineWidth(0.2);

        // Approval remarks below
        $y += $h;

        // Approval remarks block
        $hRem = 18;
        $this->Rect(self::ML, $y, self::BW, $hRem);

        $this->SetXY(self::ML + 1, $y + 1);
        $this->SetFont('Arial', '', 7.5);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell(30, 3.5, 'Remarks/Recommendation :', 0);

        $this->SetXY(self::ML + 32, $y + 1);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->MultiCell(self::BW - 33, 3.5, $this->val($this->cp->approval_remarks), 0);

        return $y + $hRem;
    }

    // ─── NOTED BY (PROVINCIAL ENGINEER) ──────────────────────────────────────

    private function drawNotedBy(float $y): float
    {
        $h = 20;
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);
        $this->Rect(self::ML, $y, self::BW, $h);

        // "Noted by:" label (left)
        $this->SetXY(self::ML + 1, $y + $h - 8);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell(20, 4, 'Noted by:', 0);

        // Provincial Engineer name (underlined, centred on right portion)
        $nameAreaX = self::ML + 22;
        $nameAreaW = self::BW - 23;
        $peName    = 'DELIA E. DAMASCO';
        $peTitle   = 'Provincial Engineer';

        $this->SetXY($nameAreaX, $y + $h - 8);
        $this->SetFont('Arial', 'BU', 9);
        $this->SetTextColor(...self::BLACK);
        $this->Cell($nameAreaW, 4.5, $peName, 0, 2, 'C');

        $this->SetX($nameAreaX);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell($nameAreaW, 3.5, $peTitle, 0, 0, 'C');

        // If a noted-by employee exists, show their name above
        if ($this->cp->notedByEngineer && $this->cp->notedByEngineer->user) {
            $notedName = $this->cp->notedByEngineer->user->name ?? '';
            if ($notedName) {
                $this->SetXY($nameAreaX, $y + 2);
                $this->SetFont('Arial', 'B', 8);
                $this->SetTextColor(...self::BLACK);
                $this->Cell($nameAreaW, 4, $notedName, 0, 0, 'C');
            }
        }

        return $y + $h;
    }

    // ─── PRIMITIVES ──────────────────────────────────────────────────────────

    /**
     * Draw a signature/name block inside a cell.
     *
     * Layout (relative to $cellX / $cellY):
     *
     *   ┌──────────── cellW ─────────────┐
     *   │                                │
     *   │   PRINTED NAME  (bold, centred)│  ← lineY - 4
     *   │   ──────────────────────────   │  ← lineY
     *   │   Signature Over Printed Name  │  ← lineY + 0.5
     *   │            Role / Title        │  ← lineY + 3.5
     *   └────────────────────────────────┘
     */
    private function sigLine(
        float   $cellX,
        float   $cellY,
        float   $cellW,
        string  $name,
        string  $role,
        ?string $signatureValue = null,
        float   $cellH = 20
    ): void {
        $lineW = min(50, $cellW - 8);
        $lineX = $cellX + ($cellW - $lineW) / 2;
        $lineY = $cellY + $cellH - 8;

        // Printed name above line
        $this->SetXY($lineX, $lineY - 4);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell($lineW, 4, $name, 0, 0, 'C');

        // Horizontal line
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.3);
        $this->Line($lineX, $lineY, $lineX + $lineW, $lineY);
        $this->SetLineWidth(0.2);

        // Sub-labels
        $this->SetXY($cellX, $lineY + 0.5);
        $this->SetFont('Arial', '', 6);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell($cellW, 3, 'Signature Over Printed Name', 0, 2, 'C');

        $this->SetX($cellX);
        $this->SetFont('Arial', '', 6.5);
        $this->Cell($cellW, 3, $role, 0, 0, 'C');

        $this->resetColors();
    }

    /**
     * Draw a DATE block (right side of two-column review blocks).
     */
    private function dateLine(
        float  $cellX,
        float  $cellY,
        float  $cellW,
        string $dateValue,
        string $label,
        float  $cellH = 20
    ): void {
        $lineW = min(50, $cellW - 8);
        $lineX = $cellX + ($cellW - $lineW) / 2;
        $lineY = $cellY + $cellH - 8;

        // Date value above line
        $this->SetXY($lineX, $lineY - 4);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell($lineW, 4, $dateValue, 0, 0, 'C');

        // Horizontal line
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.3);
        $this->Line($lineX, $lineY, $lineX + $lineW, $lineY);
        $this->SetLineWidth(0.2);

        // Label
        $this->SetXY($cellX, $lineY + 0.5);
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell($cellW, 3, $label, 0, 0, 'C');

        $this->resetColors();
    }

    private function resetColors(): void
    {
        $this->SetFillColor(...self::WHITE);
        $this->SetTextColor(...self::BLACK);
        $this->SetDrawColor(...self::BLACK);
    }

    // ─── VALUE HELPERS ────────────────────────────────────────────────────────

    private function val(?string $v): string
    {
        return $v ?? '';
    }

    private function fmtDate($d, string $fmt = 'M d, Y'): string
    {
        if (!$d) {
            return '';
        }
        try {
            return Carbon::parse($d)->format($fmt);
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function fmtDatetime($d): string
    {
        if (!$d) {
            return '';
        }
        try {
            return Carbon::parse($d)->format('M d, Y h:i A');
        } catch (\Throwable $e) {
            return '';
        }
    }

    // ─── NAME RESOLVERS ───────────────────────────────────────────────────────

    private function meMtqaName(): string
    {
        return $this->cp->meMtqaChecker?->user?->name ?? '';
    }

    private function residentEngineerName(): string
    {
        return $this->cp->residentEngineer?->user?->name ?? '';
    }

    private function approverName(): string
    {
        return $this->cp->approver?->user?->name ?? '';
    }

    private function disapproverName(): string
    {
        return $this->cp->disapprover?->user?->name ?? '';
    }
}