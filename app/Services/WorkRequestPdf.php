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

    // PAY ITEM rows — right side split into: EstQty/Equipment | Quantity | Unit
    // All three together = CB + CC = 118
    private const EQW = 64;  // Equipment to be used / Estimated Qty
    private const QW  = 27;  // Quantity
    private const UW  = 27;  // Unit     64+27+27=118 ✓

    // Colors
    private const BLUE  = [0, 176, 240];
    private const BLACK = [0, 0, 0];
    private const WHITE = [255, 255, 255];
    private const DGRAY = [80, 80, 80];

    private WorkRequest $wr;

    // Temp files created during rendering — cleaned up on destruct
    private array $tmpFiles = [];

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

    public function __destruct()
    {
        foreach ($this->tmpFiles as $f) {
            if (file_exists($f)) {
                unlink($f);
            }
        }
    }

    private function build(): void
    {
        $y = self::MT;
        $y = $this->drawHeader($y);
        $y = $this->drawProjectLines($y);
        $y = $this->drawBanner($y);
        $y = $this->drawFormTable($y);
    }

    // ─── HEADER ────────────────────────────────────────────────────────────────
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
        $this->SetFont('Arial', 'B', 11);
        $this->Cell($cw, 5, 'PROVINCE OF BUKIDNON', 0, 2, 'C');
        $this->SetX($cx);
        $this->SetFont('Arial', '', 8);
        $this->Cell($cw, 4, 'Provincial Capitol 8700', 0, 2, 'C');

        return $y + $imgSize + 4;
    }

    // ─── PROJECT LINES ─────────────────────────────────────────────────────────
    // All 3 rows share identical column widths so colons line up:
    //   label = 28mm | colon = 6mm | value = BW-34mm
    private function drawProjectLines(float $y): float
    {
        $lh = 5;

        // ── Row 1: Reference Number ───────────────────────────────────────────
        $this->SetXY(self::ML, $y);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::DGRAY);
        $this->Cell(28, $lh, 'Ref. No.', 0, 0);
        $this->Cell(6,  $lh, ':', 0, 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::BW - 34, $lh, $this->val($this->wr->reference_number ?? ''), 'B', 1);

        $y += $lh;

        // ── Row 2: Name of Project ────────────────────────────────────────────
        $this->SetXY(self::ML, $y);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(28, $lh, 'Name of Project', 0, 0);
        $this->Cell(6,  $lh, ':', 0, 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(self::BW - 34, $lh, $this->val($this->wr->name_of_project), 'B', 1);

        // ── Row 3: Project Location ───────────────────────────────────────────
        $this->SetXY(self::ML, $y + $lh);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(28, $lh, 'Project Location', 0, 0);
        $this->Cell(6,  $lh, ':', 0, 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(self::BW - 34, $lh, $this->val($this->wr->project_location), 'B', 1);

        return $y + $lh * 2 + 2;
    }

    // ─── BANNER ────────────────────────────────────────────────────────────────
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

    // ─── FORM TABLE ────────────────────────────────────────────────────────────
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
        $y = $this->rowInspector(
                $y,
                'inspected_by_site_inspector',
                'Site Inspector',
                'findings_comments',
                'recommendation',
                true,
                'site_inspector_signature'
             );
        $y = $this->rowInspector(
                $y,
                'surveyor_name',
                'Surveyor',
                'findings_surveyor',
                'recommendation_surveyor',
                false,
                'surveyor_signature'
             );
        $y = $this->rowInspector(
                $y,
                'resident_engineer_name',
                'Resident Engineer/Project In-Charge',
                'findings_engineer',
                'recommendation_engineer',
                false,
                'resident_engineer_signature'
             );
        $y = $this->rowCheckedBy($y);
        $y = $this->rowApproval(
            $y,
            'Reviewed by :',
            $this->val($this->wr->reviewed_by ?? ''),
            'Engineer IV/Chief, MTQC Division',
            $this->val($this->wr->reviewed_by_recommendation_action ?? ''),  // ← fixed
            $this->wr->reviewer_signature ?? null                             // ← added
        );
        $y = $this->rowApproval(
            $y,
            'Recommending Approval :',
            $this->val($this->wr->recommending_approval_by ?? ''),
            'Engineer III/ OIC, Construction Division',
            $this->val($this->wr->recommending_approval_recommendation_action ?? ''),  // ← fixed
            $this->wr->recommending_approval_signature ?? null
        );
        $y = $this->rowApproval(
            $y,
            'Approved :',
            $this->val($this->wr->approved_by ?? ''),
            'Provincial Engineer',
            $this->val($this->wr->approved_recommendation_action ?? ''),  // ← fixed
            $this->wr->approved_signature ?? null
        );
        $y = $this->rowAccepted($y);

        return $y;
    }

    // ─── ROW: For | Requested Work to Start On ─────────────────────────────────
    private function rowForRequested(float $y): float
    {
        $h  = 16;
        $xR = self::ML + self::CA;

        $this->box(self::ML, $y, self::CA,            $h);
        $this->box($xR,      $y, self::CB + self::CC, $h);

        $this->lbl(self::ML + 1, $y + 1, 'For :');
        $this->SetXY(self::ML + 1, $y + 5);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::CA - 2, 4, $this->val($this->wr->for_office ?? 'PROVINCIAL ENGINEERS OFFICE'), 0);

        $rx = $xR + 1;
        $this->lbl($rx, $y + 1, 'Requested Work to Start on');
        $this->lbl($rx, $y + 6, 'Date :');
        $this->SetXY($rx, $y + 10);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(38, 4, $this->fmtDate($this->wr->requested_work_start_date ?? null), 0);
        $this->lbl($rx + 40, $y + 6, 'Time:');
        $this->SetXY($rx + 40, $y + 10);
        $this->Cell(30, 4, $this->val($this->wr->requested_work_start_time ?? ''), 0);

        return $y + $h;
    }

    // ─── ROW: From ─────────────────────────────────────────────────────────────
    private function rowFrom(float $y): float
    {
        $h  = 12;
        $xR = self::ML + self::CA;

        $this->box(self::ML, $y, self::CA,            $h);
        $this->box($xR,      $y, self::CB + self::CC, $h);

        $this->lbl(self::ML + 1, $y + 1, 'From :');
        $this->SetXY(self::ML + 1, $y + 5);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::CA - 2, 4, $this->val($this->wr->from_requester ?? ''), 0);

        $this->SetXY($xR + 1, $y + 2);
        $this->SetFont('Arial', 'I', 11);
        $this->SetTextColor(...self::DGRAY);
        $this->MultiCell(self::CB + self::CC - 2, 3.5, 'Note: has to submit request in triplicate and with a minimum of 72 hours in advance of scheduled start', 0);

        return $y + $h;
    }

    // ─── ROW: PAY ITEM band ────────────────────────────────────────────────────
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

    // ─── ROW: Item No. | Equipment to be used | Quantity | Unit ────────────────
    private function rowItemEquipment(float $y): float
    {
        $h   = 10;
        $xA  = self::ML;
        $xB  = $xA + 24;
        $xEq = self::ML + self::CA;
        $xQ  = $xEq + self::EQW;
        $xU  = $xQ  + self::QW;

        $this->box($xA,  $y, 24,        $h);
        $this->box($xB,  $y, 40,        $h);
        $this->box($xEq, $y, self::EQW, $h);
        $this->box($xQ,  $y, self::QW,  $h);
        $this->box($xU,  $y, self::UW,  $h);

        $this->lbl($xA + 1, $y + 1, 'Item No.');
        $this->SetXY($xB + 1, $y + 1);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(38, 5, ': ' . $this->val($this->wr->item_no ?? ''), 0);

        $this->lbl($xEq + 1, $y + 1, 'Equipment to be used:');
        $this->val8($xEq + 1, $y + 5, self::EQW - 2, $this->val($this->wr->equipment_to_be_used ?? ''));

        return $y + $h;
    }

    // ─── ROW: Description | Est. Qty | Quantity | Unit ─────────────────────────
    private function rowDescEst(float $y): float
    {
        $h    = 10;
        $xA   = self::ML;
        $xB   = $xA + 24;
        $xEst = self::ML + self::CA;
        $xQ   = $xEst + self::EQW;
        $xU   = $xQ   + self::QW;

        $this->box($xA,   $y, 24,        $h);
        $this->box($xB,   $y, 40,        $h);
        $this->box($xEst, $y, self::EQW, $h);
        $this->box($xQ,   $y, self::QW,  $h);
        $this->box($xU,   $y, self::UW,  $h);

        $this->lbl($xA + 1, $y + 1, 'Description');
        $this->SetXY($xB + 1, $y + 1);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(38, 5, ': ' . $this->val($this->wr->description ?? ''), 0);

        $this->lbl($xEst + 1, $y + 1, 'Estimated Quantity to be Accomplished:');
        $this->val8($xEst + 1, $y + 5, self::EQW - 2, $this->val($this->wr->estimated_quantity ?? ''));

        $this->lbl($xQ + 1, $y + 1, 'Quantity:');
        $this->val8($xQ + 1, $y + 5, self::QW - 2, $this->val($this->wr->quantity ?? ''));

        $this->lbl($xU + 1, $y + 1, 'Unit:');
        $this->val8($xU + 1, $y + 5, self::UW - 2, $this->val($this->wr->unit ?? ''));

        return $y + $h;
    }

    // ─── ROW: Description of Work Requested ────────────────────────────────────
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

    // ─── ROW: Submitted By | Received By ───────────────────────────────────────
    private function rowSubmittedReceived(float $y): float
    {
        $h = 14;
        $this->box(self::ML,            $y, self::CA,            $h);
        $this->box(self::ML + self::CA, $y, self::CB + self::CC, $h);

        $this->lbl(self::ML + 1, $y + 1, 'Submitted by :');
        $this->SetXY(self::ML + 1, $y + 5);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::CA - 2, 4, $this->val($this->wr->contractor_name ?? ''), 'B');
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

    // ─── ROW: Inspection header ─────────────────────────────────────────────────
    private function rowInspectionHeader(float $y): float
    {
        $h = 5;
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(...self::BLACK);

        foreach ([
            [self::ML,                      self::CA],
            [self::ML + self::CA,            self::CB],
            [self::ML + self::CA + self::CB, self::CC],
        ] as [$x, $w]) {
            $this->Line($x,      $y,      $x + $w, $y);
            $this->Line($x,      $y,      $x,      $y + $h);
            $this->Line($x + $w, $y,      $x + $w, $y + $h);
        }

        $this->lbl(self::ML + 1,                      $y + 1, 'Inspected by :');
        $this->centLbl(self::ML + self::CA,            $y + 1, self::CB, 'Findings/Comments');
        $this->centLbl(self::ML + self::CA + self::CB, $y + 1, self::CC, 'Recommendation');

        return $y + $h;
    }

    // ─── ROW: Inspector sig row ─────────────────────────────────────────────────
    private function rowInspector(
        float   $y,
        string  $nameField,
        string  $role,
        string  $findF,
        string  $recF,
        bool    $noTop    = false,
        ?string $sigField = null
    ): float {
        $h    = 18;
        $draw = $noTop ? 'boxNoTop' : 'box';
        $this->$draw(self::ML,                      $y, self::CA, $h);
        $this->$draw(self::ML + self::CA,            $y, self::CB, $h);
        $this->$draw(self::ML + self::CA + self::CB, $y, self::CC, $h);

        $sig = ($sigField && !empty($this->wr->$sigField))
             ? $this->wr->$sigField
             : null;

        $this->sigLine(
            self::ML, $y, self::CA,
            $this->val($this->wr->$nameField ?? ''),
            $role,
            $sig
        );

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

    // ─── ROW: Checked By + Recommended Action ──────────────────────────────────
    private function rowCheckedBy(float $y): float
    {
        $hh = 5;
        $hc = 18;
        $rx = self::ML + self::CA;
        $rw = self::CB + self::CC;

        $this->SetLineWidth(0.2);
        $this->SetDrawColor(...self::BLACK);

        $this->Line($rx,       $y,       $rx + $rw, $y);
        $this->Line($rx + $rw, $y,       $rx + $rw, $y + $hh);
        $this->Line($rx,       $y + $hh, $rx + $rw, $y + $hh);
        $this->centLbl($rx, $y + 1, $rw, 'Recommended Action');

        $y2 = $y + $hh;
        $this->Rect(self::ML, $y2, self::CA, $hc);
        $this->boxNoTop($rx, $y2, $rw, $hc);

        $this->lbl(self::ML + 1, $y2 + 1, 'Checked by :');

        $mtqaSig = !empty($this->wr->mtqa_signature) ? $this->wr->mtqa_signature : null;
        $this->sigLine(
            self::ML, $y2, self::CA,
            $this->val($this->wr->checked_by_mtqa ?? ''),
            'MTQA (assigned)',
            $mtqaSig
        );

        $this->SetXY($rx + 1, $y2 + 2);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->MultiCell($rw - 2, 4, $this->val($this->wr->recommended_action ?? ''), 0);

        return $y2 + $hc;
    }

    // ─── ROW: Approval (Reviewed / Recommending / Approved) ────────────────────
    private function rowApproval(
        float   $y,
        string  $label,
        string  $name,
        string  $role,
        string  $notes,
        ?string $signature = null
    ): float {
        $h = 18;
        $this->box(self::ML,            $y, self::CA,            $h);
        $this->box(self::ML + self::CA, $y, self::CB + self::CC, $h);

        $this->SetXY(self::ML + 1, $y + 1);
        $this->SetFont('Arial', 'B', 7.5);
        $this->SetTextColor(...self::BLACK);
        $this->Cell(self::CA - 2, 3.5, $label, 0);

        $this->sigLine(self::ML, $y, self::CA, $name, $role, $signature);

        $this->SetXY(self::ML + self::CA + 1, $y + 2);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(...self::BLACK);
        $this->MultiCell(self::CB + self::CC - 2, 4, $notes, 0);

        return $y + $h;
    }

    // ─── ROW: Accepted By ──────────────────────────────────────────────────────
    private function rowAccepted(float $y): float
    {
        $h = 15;
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

    // ─── PRIMITIVES ────────────────────────────────────────────────────────────

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
        $this->Line($x,      $y,      $x,      $y + $h);
        $this->Line($x + $w, $y,      $x + $w, $y + $h);
        $this->Line($x,      $y + $h, $x + $w, $y + $h);
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

    /**
     * Resolve a signature value to a local file path FPDF can read.
     *
     * The signature field can contain one of three things:
     *   1. A base64 data-URI  — "data:image/png;base64,iVBOR..."
     *   2. A raw base64 string — "iVBOR..."  (no prefix)
     *   3. A storage-relative path — "signatures/abc.png"
     *      which lives at  storage/app/public/signatures/abc.png
     *
     * Returns an absolute file path on success, or null on failure.
     */
    private function resolveSignatureToFile(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // ── Case 1 & 2 : base64 (data-URI or raw) ────────────────────────────
        if (str_starts_with($value, 'data:image') || $this->looksLikeBase64($value)) {
            $raw = preg_replace('/^data:image\/\w+;base64,/', '', $value);
            $raw = base64_decode($raw, true);

            if ($raw === false || strlen($raw) < 100) {
                return null;
            }

            $ext     = (substr($raw, 0, 3) === "\xff\xd8\xff") ? 'jpg' : 'png';
            $tmpFile = tempnam(sys_get_temp_dir(), 'wrsig_') . '.' . $ext;

            if (file_put_contents($tmpFile, $raw) === false) {
                return null;
            }

            $this->tmpFiles[] = $tmpFile;
            return $tmpFile;
        }

        // ── Case 3 : storage-relative path ────────────────────────────────────
        // e.g. "signatures/abc.png" stored via Storage::disk('public')
        $absolute = storage_path('app/public/' . ltrim($value, '/'));
        if (file_exists($absolute)) {
            return $absolute;
        }

        // Also try public_path in case it was stored there
        $pub = public_path('storage/' . ltrim($value, '/'));
        if (file_exists($pub)) {
            return $pub;
        }

        return null;
    }

    /**
     * Heuristic: does the string look like raw base64?
     * Base64 chars only, length divisible by 4, reasonably long.
     */
    private function looksLikeBase64(string $value): bool
    {
        if (strlen($value) < 100) {
            return false;
        }
        // If it contains slashes/dots typical of a file path, it's a path
        if (str_contains($value, '/') && str_contains($value, '.')) {
            return false;
        }
        return (bool) preg_match('/^[A-Za-z0-9+\/=]+$/', substr($value, 0, 100));
    }

    /**
     * Draw a signature block inside a cell.
     *
     * Layout (relative to $cellX / $cellY, cell height assumed ≥ 15mm):
     *
     *   ┌──────────────────── cellW ─────────────────────┐
     *   │                                                │
     *   │    [signature image 36 × 10 mm if present]    │  ← sigY (lineY - 10.5)
     *   │         PRINTED NAME  (bold 8 pt, centred)    │  ← lineY - 4
     *   │    ────────────────────────────────────────    │  ← lineY (cellY + 11)
     *   │         Signature Over Printed Name           │  ← lineY + 0.5
     *   │                  Role / Title                 │  ← lineY + 3.5
     *   └────────────────────────────────────────────────┘
     */
    private function sigLine(
        float   $cellX,
        float   $cellY,
        float   $cellW,
        string  $name,
        string  $role,
        ?string $signatureValue = null
    ): void {
        $lineW = min(46, $cellW - 8);
        $lineX = $cellX + ($cellW - $lineW) / 2;
        $lineY = $cellY + 11;

        // ── Signature image ───────────────────────────────────────────────────
        $sigFile = $this->resolveSignatureToFile($signatureValue);

        if ($sigFile) {
            $sigW = 36;
            $sigH = 10;
            $sigX = $cellX + ($cellW - $sigW) / 2;
            $sigY = $lineY - $sigH - 0.5;

            try {
                $this->Image($sigFile, $sigX, $sigY, $sigW, $sigH);
            } catch (\Throwable $e) {
                // Silently skip corrupt / unsupported image
            }
        }

        // ── Printed name (always shown, centred above the line) ───────────────
        $this->SetXY($lineX, $lineY - 4);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(...self::BLACK);
        $this->Cell($lineW, 4, $name, 0, 0, 'C');

        // ── Horizontal signature line ─────────────────────────────────────────
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.3);
        $this->Line($lineX, $lineY, $lineX + $lineW, $lineY);

        // ── Sub-labels below the line ─────────────────────────────────────────
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

    private function val(?string $v): string
    {
        return $v ?? '';
    }

    private function fmtDate(?string $d, string $fmt = 'M d, Y'): string
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
}