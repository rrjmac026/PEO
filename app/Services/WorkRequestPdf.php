<?php

namespace App\Services;

use App\Models\WorkRequest;
use Carbon\Carbon;

/**
 * WorkRequest PDF Generator — pixel-perfect match to original document.
 *
 * Requires: composer require setasign/fpdf
 * Images:   public/assets/province_seal_small.png  (200x200px max)
 *           public/assets/app_logo_small.png        (200x200px max)
 *
 * Usage:
 *   $pdf = new WorkRequestPdf($workRequest);
 *   $pdf->Output('I', 'work-request.pdf');  // inline
 *   $pdf->Output('D', 'work-request.pdf');  // download
 */
class WorkRequestPdf extends \FPDF
{
    // Page geometry (mm)
    private const ML = 14;   // left/right margin
    private const MT = 10;   // top margin
    private const BW = 182;  // body width (210 - 14 - 14)

    // 3-column widths (must sum to BW = 182)
    private const CA = 64;   // left col
    private const CB = 59;   // mid  col
    private const CC = 59;   // right col

    // Colors
    private const BLUE  = [0, 176, 240];
    private const BLACK = [0, 0, 0];
    private const WHITE = [255, 255, 255];
    private const DGRAY = [80, 80, 80];

    private WorkRequest $wr;

    public function __construct(WorkRequest $workRequest)
    {
        parent::__construct('P', 'mm', 'A4');
        $this->wr = $workRequest;
        $this->SetMargins(self::ML, self::MT, self::ML);
        $this->SetAutoPageBreak(false);
        $this->AddPage();
        $this->SetFont('Arial', '', 8);
        $this->build();
    }

    private function build(): void
    {
        $y = self::MT;
        $y = $this->drawHeader($y);
        $y = $this->drawProjectLines($y);
        $y = $this->drawBanner($y);
        $y = $this->drawFormTable($y);
    }

    // HEADER
    private function drawHeader(float $y): float
    {
        $imgSize = 18;
        $gap     = 3;  // gap between logo edge and center text block

        // Center text block — fixed width, truly centered on the page
        $cw = 80;
        $cx = self::ML + (self::BW - $cw) / 2;

        // Seal sits just to the left of the text block
        $sealX = $cx - $imgSize - $gap;
        $seal  = public_path('assets/province_seal_small.png');
        if (file_exists($seal)) {
            $this->Image($seal, $sealX, $y + 1, $imgSize, $imgSize);
        }

        // Logo sits just to the right of the text block
        $logoX = $cx + $cw + $gap;
        $logo  = public_path('assets/app_logo_small.png');
        if (file_exists($logo)) {
            $this->Image($logo, $logoX, $y + 1, $imgSize, $imgSize);
        }

        // Center text
        $this->SetXY($cx, $y + 2);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell($cw, 4, 'Republic of the Philippines', 0, 2, 'C');
        $this->SetX($cx);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell($cw, 5, 'PROVINCE OF BUKIDNON', 0, 2, 'C');
        $this->SetX($cx);
        $this->SetFont('Arial', '', 8);
        $this->Cell($cw, 4, 'Provincial Capitol 8700', 0, 2, 'C');

        return $y + $imgSize + 4;
    }

    // PROJECT LINES
    private function drawProjectLines(float $y): float
    {
        $lh = 5;
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);

        $this->SetXY(self::ML, $y);
        $this->Cell(28, $lh, 'Name of Project', 0, 0);
        $this->Cell(6, $lh, ':', 0, 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(self::BW - 34, $lh, $this->val($this->wr->name_of_project), 'B', 1);

        $this->SetFont('Arial', '', 8);
        $this->SetX(self::ML);
        $this->Cell(28, $lh, 'Project Location', 0, 0);
        $this->Cell(6, $lh, ':', 0, 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(self::BW - 34, $lh, $this->val($this->wr->project_location), 'B', 1);

        return $y + $lh * 2 + 2;
    }

    // BANNER
    private function drawBanner(float $y): float
    {
        $this->SetXY(self::ML, $y);
        $this->SetFillColor(...self::BLUE);
        $this->SetTextColor(...self::WHITE);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(self::BW, 7, 'WORK REQUEST', 0, 1, 'C', true);

        $this->SetX(self::ML);
        $this->SetFillColor(...self::WHITE);
        $this->SetTextColor(...self::BLACK);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(self::BW, 4, '(In Triplicate)', 0, 1, 'C');

        return $y + 11;
    }

    // FORM TABLE
    private function drawFormTable(float $y): float
    {
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);

        $y = $this->rowForRequested($y);
        $y = $this->rowFrom($y);
        $y = $this->rowPayItemBand($y);
        $y = $this->rowItemEquipment($y);
        $y = $this->rowDescEst($y);
        $y = $this->rowWorkDesc($y);
        $y = $this->rowSubmittedReceived($y);
        $y = $this->rowInspectionHeader($y);
        $y = $this->rowInspector($y, 'inspected_by_site_inspector', 'Site Inspector',                       'findings_comments',  'recommendation',          true);
        $y = $this->rowInspector($y, 'surveyor_name',               'Surveyor',                            'findings_surveyor',  'recommendation_surveyor', false);
        $y = $this->rowInspector($y, 'resident_engineer_name',      'Resident Engineer/Project In-Charge', 'findings_engineer',  'recommendation_engineer', false);
        $y = $this->rowCheckedBy($y);
        $y = $this->rowApproval($y, 'Reviewed by :',           $this->val($this->wr->reviewed_by           ?? 'RANDY P. DIAZ'),    'Engineer IV/Chief, MTQC Division',          $this->val($this->wr->reviewed_by_notes          ?? ''));
        $y = $this->rowApproval($y, 'Recommending Approval :', $this->val($this->wr->recommending_approval_by ?? 'SANITA E. MAIZA'), 'Engineer III/ OIC, Construction Division',  $this->val($this->wr->recommending_approval_notes ?? ''));
        $y = $this->rowApproval($y, 'Approved :',              $this->val($this->wr->approved_by           ?? 'DELIA E. DAMASCO'), 'Provincial Engineer',                        $this->val($this->wr->approved_notes             ?? ''));
        $y = $this->rowAccepted($y);

        return $y;
    }

    // ROW: For | Requested Work to Start On
    private function rowForRequested(float $y): float
    {
        $h = 18;
        $this->box(self::ML,            $y, self::CA,            $h);
        $this->box(self::ML + self::CA, $y, self::CB + self::CC, $h);

        $this->lbl(self::ML + 1, $y + 1, 'For :');
        $this->SetXY(self::ML + 1, $y + 5);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::CA - 2, 4, $this->val($this->wr->for_office ?? 'PROVINCIAL ENGINEERS OFFICE'), 0);

        $rx = self::ML + self::CA + 1;
        $rw = self::CB + self::CC - 2;

        $this->lbl($rx, $y + 1, 'Requested Work to Start on');
        $this->lbl($rx, $y + 6, 'Date :');
        $this->SetXY($rx, $y + 10);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(38, 4, $this->fmtDate($this->wr->requested_work_start_date ?? null), 0);

        $this->lbl($rx + 40, $y + 6, 'Time:');
        $this->SetXY($rx + 40, $y + 10);
        $this->Cell(30, 4, $this->val($this->wr->requested_work_start_time ?? ''), 0);

        $this->SetXY($rx, $y + 14);
        $this->SetFont('Arial', 'I', 6);
        $this->SetTextColor(...self::DGRAY);
        $this->MultiCell($rw, 3, 'Note: has to submit request in triplicate and with a minimum of 72 hours in advance of scheduled start', 0);

        return $y + $h;
    }

    // ROW: From
    private function rowFrom(float $y): float
    {
        $h = 8;
        $this->box(self::ML, $y, self::BW, $h);
        $this->lbl(self::ML + 1, $y + 1, 'From :');
        $this->SetXY(self::ML + 1, $y + 5);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::BW - 2, 3, $this->val($this->wr->from_requester ?? ''), 0);
        return $y + $h;
    }

    // ROW: PAY ITEM band
    private function rowPayItemBand(float $y): float
    {
        $this->SetXY(self::ML, $y);
        $this->SetFillColor(...self::BLUE);
        $this->SetTextColor(...self::WHITE);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(self::BW, 6, 'PAY ITEM REQUESTED', 1, 1, 'C', true);
        $this->resetColors();
        return $y + 6;
    }

    // ROW: Item No | Equipment / Qty / Unit
    private function rowItemEquipment(float $y): float
    {
        $h = 10;
        $this->box(self::ML,            $y, self::CA,            $h);
        $this->box(self::ML + self::CA, $y, self::CB + self::CC, $h);

        $this->lbl(self::ML + 1, $y + 1, 'Item No. :');
        $this->val8(self::ML + 1, $y + 5, self::CA - 2, $this->val($this->wr->item_no ?? ''));

        $rx  = self::ML + self::CA + 1;
        $eqW = 38; $qW = 22;
        $uW  = self::CB + self::CC - $eqW - $qW - 4;

        $this->lbl($rx, $y + 1, 'Equipment to be used :');
        $this->val8($rx, $y + 5, $eqW, $this->val($this->wr->equipment_to_be_used ?? ''));

        $this->lbl($rx + $eqW + 1, $y + 1, 'Quantity :');
        $this->val8($rx + $eqW + 1, $y + 5, $qW, $this->val($this->wr->quantity ?? ''));

        $this->lbl($rx + $eqW + $qW + 2, $y + 1, 'Unit :');
        $this->val8($rx + $eqW + $qW + 2, $y + 5, $uW, $this->val($this->wr->unit ?? ''));

        return $y + $h;
    }

    // ROW: Description | Estimated Qty
    private function rowDescEst(float $y): float
    {
        $h = 10;
        $this->box(self::ML,            $y, self::CA,            $h);
        $this->box(self::ML + self::CA, $y, self::CB + self::CC, $h);

        $this->lbl(self::ML + 1, $y + 1, 'Description :');
        $this->val8(self::ML + 1, $y + 5, self::CA - 2, $this->val($this->wr->description ?? ''));

        $rx = self::ML + self::CA + 1;
        $this->lbl($rx, $y + 1, 'Estimated Quantity to be Accomplished :');
        $this->val8($rx, $y + 5, self::CB + self::CC - 2, $this->val($this->wr->estimated_quantity ?? ''));

        return $y + $h;
    }

    // ROW: Description of Work Requested
    private function rowWorkDesc(float $y): float
    {
        $h = 16;
        $this->box(self::ML, $y, self::BW, $h);
        $this->lbl(self::ML + 1, $y + 1, 'Description of Work Requested :');
        $this->SetXY(self::ML + 1, $y + 5);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->MultiCell(self::BW - 2, 4, $this->val($this->wr->description_of_work_requested ?? ''), 0);
        return $y + $h;
    }

    // ROW: Submitted By | Received By
    private function rowSubmittedReceived(float $y): float
    {
        $h = 14;
        $this->box(self::ML,            $y, self::CA,            $h);
        $this->box(self::ML + self::CA, $y, self::CB + self::CC, $h);

        $this->lbl(self::ML + 1, $y + 1, 'Submitted by :');
        $this->SetXY(self::ML + 1, $y + 5);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::CA - 2, 4, $this->val($this->wr->submitted_by ?? ''), 'B');
        $this->SetXY(self::ML + 1, $y + 11);
        $this->SetFont('Arial', '', 6.5);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell(self::CA - 2, 3, 'Contractor', 0, 0, 'C');

        $rx = self::ML + self::CA + 1;
        $this->lbl($rx, $y + 1, 'Received By :');
        $this->SetXY($rx, $y + 5);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(38, 4, $this->val($this->wr->received_by ?? ''), 'B');

        $this->lbl($rx + 40, $y + 1, 'Date :');
        $this->SetXY($rx + 40, $y + 5);
        $this->Cell(26, 4, $this->fmtDate($this->wr->received_date ?? null, 'm/d/Y'), 'B');

        $this->lbl($rx + 68, $y + 1, 'Time:');
        $this->SetXY($rx + 68, $y + 5);
        $this->Cell(self::CB + self::CC - 69, 4, $this->val($this->wr->received_time ?? ''), 'B');

        return $y + $h;
    }

    // ROW: Inspection header (no bottom border — merges into first inspector row)
    private function rowInspectionHeader(float $y): float
    {
        $h = 5;
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(...self::BLACK);

        // Top + left + right only — no bottom line on any of the 3 cells
        foreach ([
            [self::ML,                       self::CA],
            [self::ML + self::CA,             self::CB],
            [self::ML + self::CA + self::CB,  self::CC],
        ] as [$x, $w]) {
            $this->Line($x,      $y,      $x + $w, $y);      // top
            $this->Line($x,      $y,      $x,      $y + $h); // left
            $this->Line($x + $w, $y,      $x + $w, $y + $h); // right
        }

        $this->lbl(self::ML + 1,                       $y + 1, 'Inspected by :');
        $this->centLbl(self::ML + self::CA,             $y + 1, self::CB, 'Findings/Comments');
        $this->centLbl(self::ML + self::CA + self::CB,  $y + 1, self::CC, 'Recommendation');

        return $y + $h;
    }

    // ROW: Inspector sig row
    private function rowInspector(float $y, string $nameField, string $role, string $findF, string $recF, bool $noTop = false): float
    {
        $h = 19;
        $draw = $noTop ? 'boxNoTop' : 'box';
        $this->$draw(self::ML,                      $y, self::CA, $h);
        $this->$draw(self::ML + self::CA,            $y, self::CB, $h);
        $this->$draw(self::ML + self::CA + self::CB, $y, self::CC, $h);

        $this->sigLine(self::ML, $y, self::CA, $this->val($this->wr->$nameField ?? ''), $role);

        $this->SetXY(self::ML + self::CA + 1, $y + 2);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->MultiCell(self::CB - 2, 4, $this->val($this->wr->$findF ?? ''), 0);

        $this->SetXY(self::ML + self::CA + self::CB + 1, $y + 2);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->MultiCell(self::CC - 2, 4, $this->val($this->wr->$recF ?? ''), 0);

        return $y + $h;
    }

    // ROW: Checked By + Recommended Action
    private function rowCheckedBy(float $y): float
    {
        $hh = 5;
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(...self::BLACK);
        // Top + left + right only — no bottom line
        foreach ([
            [self::ML,            self::CA],
            [self::ML + self::CA, self::CB + self::CC],
        ] as [$x, $w]) {
            $this->Line($x,      $y,      $x + $w, $y);       // top
            $this->Line($x,      $y,      $x,      $y + $hh); // left
            $this->Line($x + $w, $y,      $x + $w, $y + $hh); // right
        }
        $this->lbl(self::ML + 1,            $y + 1, 'Checked by :');
        $this->centLbl(self::ML + self::CA, $y + 1, self::CB + self::CC, 'Recommended Action');
        $y += $hh;

        $h = 19;
        $this->boxNoTop(self::ML,            $y, self::CA,            $h);
        $this->boxNoTop(self::ML + self::CA, $y, self::CB + self::CC, $h);

        $this->sigLine(self::ML, $y, self::CA, $this->val($this->wr->checked_by_mtqa ?? ''), 'MTQA (assigned)');

        $this->SetXY(self::ML + self::CA + 1, $y + 2);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->MultiCell(self::CB + self::CC - 2, 4, $this->val($this->wr->recommended_action ?? ''), 0);

        return $y + $h;
    }

    // ROW: Approval (Reviewed / Recommending / Approved)
    private function rowApproval(float $y, string $label, string $name, string $role, string $notes): float
    {
        $h = 18;
        $this->box(self::ML,            $y, self::CA,            $h);
        $this->box(self::ML + self::CA, $y, self::CB + self::CC, $h);

        $this->SetXY(self::ML + 1, $y + 1);
        $this->SetFont('Arial', 'B', 7.5);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::CA - 2, 3.5, $label, 0);

        $this->sigLine(self::ML, $y, self::CA, $name, $role);

        $this->SetXY(self::ML + self::CA + 1, $y + 2);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->MultiCell(self::CB + self::CC - 2, 4, $notes, 0);

        return $y + $h;
    }

    // ROW: Accepted By
    private function rowAccepted(float $y): float
    {
        $h = 16;
        $this->box(self::ML,            $y, self::CA,            $h);
        $this->box(self::ML + self::CA, $y, self::CB + self::CC, $h);

        $this->lbl(self::ML + 1, $y + 1, 'Accepted by :');
        $this->SetXY(self::ML + 1, $y + 5);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::CA - 2, 4, $this->val($this->wr->accepted_by_contractor ?? ''), 'B');
        $this->SetXY(self::ML + 1, $y + 11);
        $this->SetFont('Arial', '', 6.5);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell(self::CA - 2, 3, 'Contractor', 0, 0, 'C');

        $rx = self::ML + self::CA + 1;
        $this->lbl($rx, $y + 1, 'Date:');
        $this->SetXY($rx, $y + 5);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(55, 4, $this->fmtDate($this->wr->accepted_date ?? null, 'm/d/Y'), 'B');

        $this->lbl($rx + 57, $y + 1, 'Time:');
        $this->SetXY($rx + 57, $y + 5);
        $this->Cell(self::CB + self::CC - 59, 4, $this->val($this->wr->accepted_time ?? ''), 'B');

        return $y + $h;
    }

    // PRIMITIVES

    private function box(float $x, float $y, float $w, float $h): void
    {
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(...self::BLACK);
        $this->Rect($x, $y, $w, $h);
    }

    private function boxNoTop(float $x, float $y, float $w, float $h): void
    {
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(...self::BLACK);
        $this->Line($x,      $y,      $x,      $y + $h); // left
        $this->Line($x + $w, $y,      $x + $w, $y + $h); // right
        $this->Line($x,      $y + $h, $x + $w, $y + $h); // bottom
    }

    private function lbl(float $x, float $y, string $text): void
    {
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 6.5);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell($this->GetStringWidth($text) + 1, 3, $text, 0);
    }

    private function centLbl(float $x, float $y, float $w, string $text): void
    {
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 6.5);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell($w, 3, $text, 0, 0, 'C');
    }

    private function val8(float $x, float $y, float $w, string $text): void
    {
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell($w, 4, $text, 0);
    }

    private function sigLine(float $cellX, float $cellY, float $cellW, string $name, string $role): void
    {
        $lineW = min(46, $cellW - 8);
        $lineX = $cellX + ($cellW - $lineW) / 2;
        $lineY = $cellY + 11;

        $this->SetXY($lineX, $lineY - 4);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell($lineW, 4, $name, 0, 0, 'C');

        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.3);
        $this->Line($lineX, $lineY, $lineX + $lineW, $lineY);

        $this->SetXY($cellX, $lineY + 0.5);
        $this->SetFont('Arial', '', 6);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell($cellW, 3, 'Signature Over Printed Name', 0, 2, 'C');
        $this->SetX($cellX);
        $this->SetFont('Arial', '', 6.5);
        $this->Cell($cellW, 3, $role, 0, 0, 'C');

        $this->resetColors();
    }

    private function resetColors(): void
    {
        $this->SetFillColor(...self::WHITE);
        $this->SetTextColor(...self::BLACK);
        $this->SetDrawColor(...self::BLACK);
    }

    private function val(?string $v): string { return $v ?? ''; }

    private function fmtDate(?string $d, string $fmt = 'M d, Y'): string
    {
        if (!$d) return '';
        try { return Carbon::parse($d)->format($fmt); } catch (\Throwable $e) { return ''; }
    }
}