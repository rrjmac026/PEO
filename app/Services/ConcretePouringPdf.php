<?php

namespace App\Services;

use App\Models\ConcretePouring;
use Carbon\Carbon;

/**
 * ConcretePouring PDF Generator — pixel-perfect match to the official
 * Province of Bukidnon Concrete Pouring Form.
 *
 * Mirrors WorkRequestPdf conventions:
 *   • resolveSignatureToFile()  — base64 data-URI / raw b64 / storage path → tmp PNG/JPG
 *   • sigLine()                 — signature image + printed name + underline + sub-labels
 *   • dateLine()                — date value + underline + DATE label
 *   • All reviewer signatures (ME/MTQA, Resident Engineer, Provincial Engineer)
 *     are embedded in the appropriate review boxes.
 *
 * Requires : composer require setasign/fpdf
 * Images   : public/assets/province_seal_small.png
 *            public/assets/app_logo_small.png
 *
 * Usage:
 *   $pdf = new ConcretePouringPdf($concretePouring);
 *   return response($pdf->Output('S'), 200, [
 *       'Content-Type'        => 'application/pdf',
 *       'Content-Disposition' => 'inline; filename="cp.pdf"',
 *   ]);
 */
class ConcretePouringPdf extends \FPDF
{
    // ── Page geometry (mm) ────────────────────────────────────────────────────
    private const ML = 14;    // left / right margin
    private const MT = 10;    // top margin
    private const BW = 182;   // body width  (210 - 14 - 14)

    // ── Two-column layout (checklist table) ───────────────────────────────────
    private const CL = 91;    // left  checklist col  (BW / 2)
    private const CR = 91;    // right checklist col  (BW / 2)

    // ── Checkbox column inside each checklist half ────────────────────────────
    private const CB_W = 8;   // checkbox cell width
    private const LB_W = 83;  // label cell width   (CL - CB_W)

    // ── Review-block column split (left name/sig | right date) ────────────────
    private const RL = 91;    // left  half of a review block  (BW / 2)
    private const RR = 91;    // right half of a review block

    // ── Colors ────────────────────────────────────────────────────────────────
    private const BLUE  = [0, 176, 240];
    private const BLACK = [0,   0,   0];
    private const WHITE = [255, 255, 255];
    private const DGRAY = [80,  80,  80];

    private ConcretePouring $cp;

    /** Temp files written for base64 signatures – cleaned up on destruct. */
    private array $tmpFiles = [];

    // =========================================================================
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
                @unlink($f);
            }
        }
    }

    // =========================================================================
    // MAIN BUILD
    // =========================================================================
    private function build(): void
    {
        $y = self::MT;
        $y = $this->drawHeader($y);
        $y = $this->drawProjectInfo($y);
        $y = $this->drawRequestedBy($y);
        $y = $this->drawChecklistBand($y);
        $y = $this->drawChecklist($y);
        $y = $this->drawCheckedByLabel($y);
        $y = $this->drawMeMtqaBlock($y);
        $y = $this->drawResidentEngineerBlock($y);
        $y = $this->drawApprovalRow($y);
        $this->drawNotedByBlock($y);
    }

    // =========================================================================
    // SECTION: HEADER
    // =========================================================================
    private function drawHeader(float $y): float
    {
        $imgSize = 18;
        $gap     = 3;
        $cw      = 80;   // centre text column width
        $cx      = self::ML + (self::BW - $cw) / 2;

        // Logos
        $seal = public_path('assets/province_seal_small.png');
        if (file_exists($seal)) {
            $this->Image($seal, $cx - $imgSize - $gap, $y + 1, $imgSize, $imgSize);
        }
        $logo = public_path('assets/app_logo_small.png');
        if (file_exists($logo)) {
            $this->Image($logo, $cx + $cw + $gap, $y + 1, $imgSize, $imgSize);
        }

        // Centre text block
        $this->SetXY($cx, $y + 2);
        $this->SetFont('Arial', '', 8);
        $this->setColor('text', ...self::BLACK);
        $this->Cell($cw, 4, 'Republic of the Philippines', 0, 2, 'C');

        $this->SetX($cx);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell($cw, 4, 'PROVINCE OF BUKIDNON', 0, 2, 'C');

        $this->SetX($cx);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell($cw, 4, "PROVINCIAL ENGINEER'S OFFICE", 0, 2, 'C');

        $this->SetX($cx);
        $this->SetFont('Arial', '', 8);
        $this->Cell($cw, 4, 'Provincial Capitol 8700', 0, 2, 'C');

        $headerBottom = $y + $imgSize + 4;

        // Blue title banner
        $this->SetXY(self::ML, $headerBottom);
        $this->SetFillColor(...self::BLUE);
        $this->setColor('text', ...self::WHITE);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(self::BW, 7, 'CONCRETE POURING FORM', 0, 1, 'C', true);

        $this->SetX(self::ML);
        $this->SetFillColor(...self::WHITE);
        $this->setColor('text', ...self::BLACK);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(self::BW, 4, '(In Triplicate)', 0, 1, 'C');

        return $headerBottom + 11;
    }

    // =========================================================================
    // SECTION: PROJECT INFORMATION TABLE
    // =========================================================================
    private function drawProjectInfo(float $y): float
    {
        $rows = [
            ['Name of Project',                 $this->v($this->cp->project_name)],
            ['Location',                        $this->v($this->cp->location)],
            ['Contractor',                      $this->v($this->cp->contractor)],
            ['Part of Structure to be poured',  $this->v($this->cp->part_of_structure)],
            ['Estimated Volume (cu.m)',          $this->v((string) $this->cp->estimated_volume)],
            ['Station Limits/Section',          $this->v($this->cp->station_limits_section)],
            ['Date and Time of Pouring',        $this->fmtDatetime($this->cp->pouring_datetime)],
        ];

        $lh     = 6;
        $lblW   = 70;
        $colonW = 5;
        $valW   = self::BW - $lblW - $colonW;

        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);

        foreach ($rows as [$label, $value]) {
            $this->Rect(self::ML, $y, self::BW, $lh);

            $this->SetXY(self::ML + 1, $y + 1.2);
            $this->SetFont('Arial', '', 8);
            $this->setColor('text', ...self::BLACK);
            $this->Cell($lblW, $lh - 2, $label, 0);
            $this->SetFont('Arial', '', 8);
            $this->Cell($colonW, $lh - 2, ':', 0, 0, 'C');
            $this->SetFont('Arial', 'B', 8);
            $this->Cell($valW - 2, $lh - 2, $value, 0);

            $y += $lh;
        }

        return $y;
    }

    // =========================================================================
    // SECTION: REQUESTED BY (contractor signature line)
    // =========================================================================
    private function drawRequestedBy(float $y): float
    {
        $y += 4;

        $this->SetXY(self::ML, $y);
        $this->SetFont('Arial', '', 8);
        $this->setColor('text', ...self::BLACK);
        $this->Cell(self::BW, 4, 'Requested by:', 0, 1, 'L');

        $y += 4;

        $lineW = 70;
        $lineX = self::ML + (self::BW - $lineW) / 2;

        // Contractor name above the line (if available)
        $contractorName = $this->cp->requestedBy?->name ?? $this->cp->contractor ?? '';
        if ($contractorName) {
            $this->SetXY($lineX, $y + 1);
            $this->SetFont('Arial', 'B', 8);
            $this->setColor('text', ...self::BLACK);
            $this->Cell($lineW, 4, $contractorName, 0, 0, 'C');
        }

        // Signature line
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.3);
        $this->Line($lineX, $y + 6, $lineX + $lineW, $y + 6);
        $this->SetLineWidth(0.2);

        // "Contractor" sub-label
        $this->SetXY($lineX, $y + 7);
        $this->SetFont('Arial', '', 7);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell($lineW, 3, 'Contractor', 0, 1, 'C');

        return $y + 12;
    }

    // =========================================================================
    // SECTION: CHECKLIST BAND
    // =========================================================================
    private function drawChecklistBand(float $y): float
    {
        $this->SetXY(self::ML, $y);
        $this->SetFillColor(...self::BLUE);
        $this->setColor('text', ...self::WHITE);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(self::BW, 6, 'CHECKLIST', 1, 1, 'C', true);
        $this->resetColors();
        return $y + 6;
    }

    // =========================================================================
    // SECTION: CHECKLIST ROWS
    // =========================================================================
    private function drawChecklist(float $y): float
    {
        // [left label, left field, right label, right field]
        $pairs = [
            ['Concrete Vibrator',
             'concrete_vibrator',
             'Field Density Test (FDT)',
             'field_density_test'],

            ['Protective Covering Materials',
             'protective_covering_materials',
             'BEAM/Cylinder Molds',
             'beam_cylinder_molds'],

            ['Warning Signs/Barricades/Flagmen',
             'warning_signs_barricades',
             'Curing Materials',
             'curing_materials'],

            ['Concrete Saw',
             'concrete_saw',
             'Slump Cones',
             'slump_cones'],

            ['Concrete Block Spacer',
             'concrete_block_spacer',
             'Plumbness',
             'plumbness'],

            ['Finishing Tools/Equipment (Screeder, Broom, etc)',
             'finishing_tools_equipment',
             'Quality of Materials (Result of Design/Trial Mix Test Reports)',
             'quality_of_materials'],

            ['Line and Grade Alignment (Form setting, elevation, etc)',
             'line_grade_alignment',
             'Lighting System',
             'lighting_system'],

            ['Required Construction Equipment',
             'required_construction_equipment',
             'Electrical Layout (Roughing-Ins/Embedment)',
             'electrical_layout'],

            ['Rebar Sizes, Spacing and number',
             'rebar_sizes_spacing',
             'Plumbing Layout (Roughing-Ins/Embedment)',
             'plumbing_layout'],

            ['Rebars installation requirement',
             'rebars_installation',
             'Falseworks/Formworks Adequacy',
             'falseworks_formworks'],
        ];

        $rowH = 6;
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);

        foreach ($pairs as [$ll, $lf, $rl, $rf]) {
            $xL = self::ML;
            $xR = self::ML + self::CL;

            $this->Rect($xL, $y, self::CL, $rowH);
            $this->Rect($xR, $y, self::CR, $rowH);

            // Left checkbox + label
            $this->drawCheckbox($xL + 1, $y + 1.2, (bool) $this->cp->{$lf});
            $this->SetXY($xL + self::CB_W + 1, $y + 1);
            $this->SetFont('Arial', '', 7);
            $this->setColor('text', ...self::BLACK);
            $this->Cell(self::LB_W - 2, $rowH - 2, $ll, 0);

            // Right checkbox + label
            $this->drawCheckbox($xR + 1, $y + 1.2, (bool) $this->cp->{$rf});
            $this->SetXY($xR + self::CB_W + 1, $y + 1);
            $this->SetFont('Arial', '', 7);
            $this->setColor('text', ...self::BLACK);
            $this->Cell(self::LB_W - 2, $rowH - 2, $rl, 0);

            $y += $rowH;
        }

        return $y;
    }

    // ─── small checkbox ───────────────────────────────────────────────────────
    private function drawCheckbox(float $x, float $y, bool $checked): void
    {
        $s = 3.5;
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);
        $this->Rect($x, $y, $s, $s);

        if ($checked) {
            $this->SetLineWidth(0.4);
            $this->Line($x + 0.5,    $y + 1.8,     $x + 1.4,   $y + $s - 0.5);
            $this->Line($x + 1.4,    $y + $s - 0.5,$x + $s - 0.3, $y + 0.5);
            $this->SetLineWidth(0.2);
        }
    }

    // =========================================================================
    // SECTION: "Checked by:" label
    // =========================================================================
    private function drawCheckedByLabel(float $y): float
    {
        $y += 3;
        $this->SetXY(self::ML, $y);
        $this->SetFont('Arial', '', 8);
        $this->setColor('text', ...self::BLACK);
        $this->Cell(self::BW, 4, 'Checked by:', 0, 1, 'L');
        return $y + 5;
    }

    // =========================================================================
    // SECTION: ME / MTQA REVIEW BLOCK
    //   Left half  → signature + name + "ME/MTQA" + DATE
    //   Right half → date value
    //   Full width → Remarks/Recommendation label + value
    // =========================================================================
    private function drawMeMtqaBlock(float $y): float
    {
        $h = 28;   // taller to accommodate signature image

        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);
        $this->Rect(self::ML, $y, self::BW, $h);

        // ── Remarks label + value (top portion) ──────────────────────────────
        $this->SetXY(self::ML + 1, $y + 1);
        $this->SetFont('Arial', '', 7.5);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell(32, 3.5, 'Remarks/Recommendation :', 0);

        $this->SetXY(self::ML + 34, $y + 1);
        $this->SetFont('Arial', '', 8);
        $this->setColor('text', ...self::BLACK);
        $this->MultiCell(self::BW - 35, 3.5, $this->v($this->cp->me_mtqa_remarks), 0);

        // ── Horizontal divider separating remarks from sig/date ───────────────
        $dividerY = $y + 10;
        $this->SetLineWidth(0.2);
        $this->Line(self::ML, $dividerY, self::ML + self::BW, $dividerY);

        // ── Vertical divider between name/sig and date ────────────────────────
        $this->Line(self::ML + self::RL, $dividerY, self::ML + self::RL, $y + $h);

        // ── Left: ME/MTQA signature block ─────────────────────────────────────
        $this->sigLine(
            cellX:          self::ML,
            cellY:          $dividerY,
            cellW:          self::RL,
            cellH:          $h - ($dividerY - $y),
            name:           $this->cp->meMtqaChecker?->name ?? '',
            role:           'ME/MTQA',
            signatureValue: $this->cp->me_mtqa_signature ?? null,
        );

        // ── Right: date block ─────────────────────────────────────────────────
        $this->dateLine(
            cellX:      self::ML + self::RL,
            cellY:      $dividerY,
            cellW:      self::RR,
            cellH:      $h - ($dividerY - $y),
            dateValue:  $this->fmtDate($this->cp->me_mtqa_date),
            label:      'DATE',
        );

        return $y + $h;
    }

    // =========================================================================
    // SECTION: RESIDENT ENGINEER REVIEW BLOCK
    // =========================================================================
    private function drawResidentEngineerBlock(float $y): float
    {
        $h = 28;

        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);
        $this->Rect(self::ML, $y, self::BW, $h);

        // Remarks
        $this->SetXY(self::ML + 1, $y + 1);
        $this->SetFont('Arial', '', 7.5);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell(32, 3.5, 'Remarks/Recommendation :', 0);

        $this->SetXY(self::ML + 34, $y + 1);
        $this->SetFont('Arial', '', 8);
        $this->setColor('text', ...self::BLACK);
        $this->MultiCell(self::BW - 35, 3.5, $this->v($this->cp->re_remarks), 0);

        $dividerY = $y + 10;
        $this->SetLineWidth(0.2);
        $this->Line(self::ML, $dividerY, self::ML + self::BW, $dividerY);
        $this->Line(self::ML + self::RL, $dividerY, self::ML + self::RL, $y + $h);

        // Left: Resident Engineer signature block
        $this->sigLine(
            cellX:          self::ML,
            cellY:          $dividerY,
            cellW:          self::RL,
            cellH:          $h - ($dividerY - $y),
            name:           $this->cp->residentEngineer?->name ?? '',
            role:           'Resident Engineer/Project In-Charge',
            signatureValue: $this->cp->re_signature ?? null,
        );

        // Right: date block
        $this->dateLine(
            cellX:      self::ML + self::RL,
            cellY:      $dividerY,
            cellW:      self::RR,
            cellH:      $h - ($dividerY - $y),
            dateValue:  $this->fmtDate($this->cp->re_date),
            label:      'DATE',
        );

        return $y + $h;
    }

    // =========================================================================
    // SECTION: REQUEST / APPROVED / DISAPPROVED ROW  +  approval remarks block
    // =========================================================================
    private function drawApprovalRow(float $y): float
    {
        $h    = 12;
        $segW = self::BW / 3;

        $this->SetLineWidth(0.2);
        $this->SetDrawColor(...self::BLACK);
        $this->Rect(self::ML, $y, self::BW, $h);
        $this->Line(self::ML + $segW,     $y, self::ML + $segW,     $y + $h);
        $this->Line(self::ML + $segW * 2, $y, self::ML + $segW * 2, $y + $h);

        // ── Request segment ───────────────────────────────────────────────────
        $this->lbl(self::ML + 1, $y + 2, 'Request :');
        $this->SetLineWidth(0.3);
        $this->Line(self::ML + 1, $y + 9, self::ML + $segW - 2, $y + 9);
        $this->SetLineWidth(0.2);

        // ── Approved segment ──────────────────────────────────────────────────
        $xApp = self::ML + $segW + 1;
        $this->lbl($xApp, $y + 2, 'Approved :');

        $approverName = $this->cp->approver?->name ?? '';
        if ($approverName) {
            $this->SetXY($xApp, $y + 5);
            $this->SetFont('Arial', 'B', 8);
            $this->setColor('text', ...self::BLACK);
            $this->Cell($segW - 2, 3.5, $approverName, 0, 0, 'C');
        }
        $this->SetLineWidth(0.3);
        $this->Line($xApp, $y + 9, $xApp + $segW - 2, $y + 9);
        $this->SetLineWidth(0.2);

        // ── Disapproved segment ───────────────────────────────────────────────
        $xDis = self::ML + $segW * 2 + 1;
        $this->lbl($xDis, $y + 2, 'Disapproved :');

        $disapproverName = $this->cp->disapprover?->name ?? '';
        if ($disapproverName) {
            $this->SetXY($xDis, $y + 5);
            $this->SetFont('Arial', 'B', 8);
            $this->setColor('text', ...self::BLACK);
            $this->Cell($segW - 2, 3.5, $disapproverName, 0, 0, 'C');
        }
        $this->SetLineWidth(0.3);
        $this->Line($xDis, $y + 9, $xDis + $segW - 3, $y + 9);
        $this->SetLineWidth(0.2);

        return $y + $h;
    }

    // =========================================================================
    // SECTION: NOTED BY — Provincial Engineer
    //   Has its own Remarks/Recommendation row first, then the noted-by block.
    // =========================================================================
    private function drawNotedByBlock(float $y): void
    {
        // ── Approval / provincial remarks ─────────────────────────────────────
        $hRem = 16;
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(...self::BLACK);
        $this->Rect(self::ML, $y, self::BW, $hRem);

        $this->SetXY(self::ML + 1, $y + 1);
        $this->SetFont('Arial', '', 7.5);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell(32, 3.5, 'Remarks/Recommendation :', 0);

        $this->SetXY(self::ML + 34, $y + 1);
        $this->SetFont('Arial', '', 8);
        $this->setColor('text', ...self::BLACK);
        $this->MultiCell(self::BW - 35, 3.5, $this->v($this->cp->approval_remarks), 0);

        $y += $hRem;

        // ── Noted-by block ────────────────────────────────────────────────────
        $h = 28;
        $this->Rect(self::ML, $y, self::BW, $h);

        // "Noted by:" label on left
        $this->SetXY(self::ML + 1, $y + $h - 10);
        $this->SetFont('Arial', '', 8);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell(22, 4, 'Noted by:', 0);

        // Provincial Engineer signature (right portion of the block)
        $sigAreaX = self::ML + 24;
        $sigAreaW = self::BW - 25;

        $this->sigLine(
            cellX:          $sigAreaX,
            cellY:          $y,
            cellW:          $sigAreaW,
            cellH:          $h,
            name:           $this->cp->notedByEngineer?->name ?? 'DELIA E. DAMASCO',
            role:           'Provincial Engineer',
            signatureValue: $this->cp->noted_by_signature ?? null,
            underlineName:  true,
        );
    }

    // =========================================================================
    // PRIMITIVES — signature block
    // =========================================================================

    /**
     * Draw a reviewer signature block inside a virtual cell.
     *
     * Layout inside the cell (relative to cellX/cellY, bottom-aligned):
     *
     *    ┌─────────── cellW ──────────────┐
     *    │                               │  ← top padding
     *    │   [signature img 36×10 mm]    │  ← sigY  = lineY − 10.5
     *    │      PRINTED NAME (bold)      │  ← lineY − 4
     *    │   ───────────────────────     │  ← lineY = cellY + cellH − 8
     *    │   Signature Over Printed Name │  ← lineY + 0.5
     *    │          Role / Title         │  ← lineY + 3.5
     *    └───────────────────────────────┘
     */
    private function sigLine(
        float   $cellX,
        float   $cellY,
        float   $cellW,
        float   $cellH,
        string  $name,
        string  $role,
        ?string $signatureValue = null,
        bool    $underlineName  = false
    ): void {
        $lineW = min(54, $cellW - 8);
        $lineX = $cellX + ($cellW - $lineW) / 2;
        $lineY = $cellY + $cellH - 8;   // signature underline position

        // ── Signature image (if present) ──────────────────────────────────────
        $sigFile = $this->resolveSignatureToFile($signatureValue);

        if ($sigFile) {
            $sigW = 36;
            $sigH = 10;
            $sigX = $cellX + ($cellW - $sigW) / 2;
            $sigY = $lineY - $sigH - 0.5;

            try {
                $this->Image($sigFile, $sigX, $sigY, $sigW, $sigH);
            } catch (\Throwable) {
                // Silently skip corrupt / unsupported image
            }
        }

        // ── Printed name (centred, above the line) ────────────────────────────
        $nameStyle = $underlineName ? 'BU' : 'B';
        $this->SetXY($lineX, $lineY - 4);
        $this->SetFont('Arial', $nameStyle, 8);
        $this->setColor('text', ...self::BLACK);
        $this->Cell($lineW, 4, $name, 0, 0, 'C');

        // ── Underline / horizontal rule ───────────────────────────────────────
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.3);
        $this->Line($lineX, $lineY, $lineX + $lineW, $lineY);
        $this->SetLineWidth(0.2);

        // ── Sub-labels ────────────────────────────────────────────────────────
        $this->SetXY($cellX, $lineY + 0.5);
        $this->SetFont('Arial', '', 6);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell($cellW, 3, 'Signature Over Printed Name', 0, 2, 'C');

        $this->SetX($cellX);
        $this->SetFont('Arial', '', 6.5);
        $this->Cell($cellW, 3, $role, 0, 0, 'C');

        $this->resetColors();
    }

    /**
     * Draw a DATE block (right column of a review box).
     */
    private function dateLine(
        float  $cellX,
        float  $cellY,
        float  $cellW,
        float  $cellH,
        string $dateValue,
        string $label = 'DATE'
    ): void {
        $lineW = min(50, $cellW - 8);
        $lineX = $cellX + ($cellW - $lineW) / 2;
        $lineY = $cellY + $cellH - 8;

        // Date value above line
        $this->SetXY($lineX, $lineY - 4);
        $this->SetFont('Arial', 'B', 8);
        $this->setColor('text', ...self::BLACK);
        $this->Cell($lineW, 4, $dateValue, 0, 0, 'C');

        // Horizontal rule
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.3);
        $this->Line($lineX, $lineY, $lineX + $lineW, $lineY);
        $this->SetLineWidth(0.2);

        // Label
        $this->SetXY($cellX, $lineY + 0.5);
        $this->SetFont('Arial', '', 7);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell($cellW, 3, $label, 0, 0, 'C');

        $this->resetColors();
    }

    // =========================================================================
    // SIGNATURE RESOLVER  (mirrors WorkRequestPdf::resolveSignatureToFile)
    // =========================================================================

    /**
     * Convert a signature value to an absolute filesystem path FPDF can embed.
     *
     * Accepts:
     *   1. data:image/png;base64,…  — data URI
     *   2. iVBOR…                   — raw base64 (no prefix)
     *   3. signatures/abc.png       — storage-relative path
     *
     * Returns absolute path (possibly a tempfile) or null on failure.
     */
    private function resolveSignatureToFile(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // ── Case 1 & 2 : base64 ──────────────────────────────────────────────
        if (str_starts_with($value, 'data:image') || $this->looksLikeBase64($value)) {
            $raw = preg_replace('/^data:image\/\w+;base64,/', '', $value);
            $raw = base64_decode($raw, true);

            if ($raw === false || strlen($raw) < 100) {
                return null;
            }

            $ext     = (substr($raw, 0, 3) === "\xff\xd8\xff") ? 'jpg' : 'png';
            $tmpFile = tempnam(sys_get_temp_dir(), 'cpsig_') . '.' . $ext;

            if (file_put_contents($tmpFile, $raw) === false) {
                return null;
            }

            $this->tmpFiles[] = $tmpFile;
            return $tmpFile;
        }

        // ── Case 3 : storage-relative path ───────────────────────────────────
        $absolute = storage_path('app/public/' . ltrim($value, '/'));
        if (file_exists($absolute)) {
            return $absolute;
        }

        $pub = public_path('storage/' . ltrim($value, '/'));
        if (file_exists($pub)) {
            return $pub;
        }

        return null;
    }

    /**
     * Heuristic: does the string look like raw base64 (no data: prefix)?
     */
    private function looksLikeBase64(string $value): bool
    {
        if (strlen($value) < 100) {
            return false;
        }
        if (str_contains($value, '/') && str_contains($value, '.')) {
            return false; // looks like a file path
        }
        return (bool) preg_match('/^[A-Za-z0-9+\/=]+$/', substr($value, 0, 100));
    }

    // =========================================================================
    // UTILITY HELPERS
    // =========================================================================

    /** Small label in DGRAY. */
    private function lbl(float $x, float $y, string $text): void
    {
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 7);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell($this->GetStringWidth($text) + 1, 3.5, $text, 0);
    }

    /** Wrapper so we can call SetTextColor or SetDrawColor uniformly. */
    private function setColor(string $type, int $r, int $g, int $b): void
    {
        match ($type) {
            'text' => $this->SetTextColor($r, $g, $b),
            'draw' => $this->SetDrawColor($r, $g, $b),
            'fill' => $this->SetFillColor($r, $g, $b),
            default => null,
        };
    }

    private function resetColors(): void
    {
        $this->SetFillColor(...self::WHITE);
        $this->SetTextColor(...self::BLACK);
        $this->SetDrawColor(...self::BLACK);
    }

    /** Null-safe string value. */
    private function v(?string $value): string
    {
        return $value ?? '';
    }

    private function fmtDate(mixed $d, string $fmt = 'M d, Y'): string
    {
        if (!$d) {
            return '';
        }
        try {
            return Carbon::parse($d)->format($fmt);
        } catch (\Throwable) {
            return '';
        }
    }

    private function fmtDatetime(mixed $d): string
    {
        if (!$d) {
            return '';
        }
        try {
            return Carbon::parse($d)->format('M d, Y h:i A');
        } catch (\Throwable) {
            return '';
        }
    }
}