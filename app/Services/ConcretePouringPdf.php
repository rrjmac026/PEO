<?php

namespace App\Services;

use App\Models\ConcretePouring;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;

/**
 * ConcretePouringPdf — FPDI Overlay Edition
 *
 * Imports the official blank Concrete Pouring Form PDF as a background page,
 * then stamps all dynamic data (text, checkboxes, signatures) on top at
 * precise coordinates read from the coordinate grid.
 *
 * Coordinate reference (from grid.pdf):
 *   Header logos/title:      Y=0–35mm
 *   Project info table:      Y=37–80mm
 *   "Requested by" area:     Y=80–93mm
 *   CHECKLIST band:          Y=93–99mm
 *   Checklist rows:          Y=99–165mm  (10 rows × ~6.5mm)
 *   "Checked by:" label:     Y=155mm
 *   ME/MTQA box:             Y=162–198mm
 *   RE box:                  Y=198–235mm
 *   Approval row:            Y=238–250mm
 *   Noted by box:            Y=250–275mm
 */
class ConcretePouringPdf extends Fpdi
{
    private const PAGE_W = 210;
    private const PAGE_H = 297;
    private const ML     = 14;

    private const TEMPLATE_PATH = 'pdf-templates/concrete-pouring-template.pdf';

    // =========================================================================
    // PROJECT INFO ROWS
    // From grid: value column starts at X≈95 (after the colon at ~X=92)
    // Row Y values read from grid (top of text in each row):
    //   Name of Project  ≈ Y=39
    //   Location         ≈ Y=45
    //   Contractor       ≈ Y=51
    //   Part of Struct   ≈ Y=57
    //   Est. Volume      ≈ Y=63
    //   Station Limits   ≈ Y=69
    //   Date/Time        ≈ Y=75
    // =========================================================================
    private const VALUE_X = 95;
    private const VALUE_W = 101;

    private const PROJECT_INFO_ROWS = [
        'project_name'           => 39.0,
        'location'               => 44.5,
        'contractor'             => 50.0,
        'part_of_structure'      => 54.9,
        'estimated_volume'       => 59.8,
        'station_limits_section' => 64.8,
        'pouring_datetime'       => 69.5,
    ];

    // =========================================================================
    // CHECKLIST
    // From grid:
    //   First row top ≈ Y=99mm
    //   Row height ≈ 6.5mm
    //   Left col checkbox X ≈ 15mm
    //   Right col checkbox X ≈ 106mm
    // =========================================================================
    private const CHECKLIST_START_Y = 97.8;
    private const CHECKLIST_ROW_H   = 6.5;
    private const CHECKLIST_LEFT_X  = 29.0;
    private const CHECKLIST_RIGHT_X = 107.0;

    private const CHECKLIST_MAP = [
        'concrete_vibrator'               => ['L', 0],
        'protective_covering_materials'   => ['L', 1],
        'warning_signs_barricades'        => ['L', 2],
        'concrete_saw'                    => ['L', 3],
        'concrete_block_spacer'           => ['L', 4],
        'finishing_tools_equipment'       => ['L', 5],
        'line_grade_alignment'            => ['L', 6],
        'required_construction_equipment' => ['L', 7],
        'rebar_sizes_spacing'             => ['L', 8],
        'rebars_installation'             => ['L', 9],
        'field_density_test'              => ['R', 0],
        'beam_cylinder_molds'             => ['R', 1],
        'curing_materials'                => ['R', 2],
        'slump_cones'                     => ['R', 3],
        'plumbness'                       => ['R', 4],
        'quality_of_materials'            => ['R', 5],
        'lighting_system'                 => ['R', 6],
        'electrical_layout'               => ['R', 7],
        'plumbing_layout'                 => ['R', 8],
        'falseworks_formworks'            => ['R', 9],
    ];

    // =========================================================================
    // ME / MTQA BLOCK
    // From grid:
    //   Box top ≈ Y=162mm
    //   Remarks text area ≈ Y=165mm
    //   Divider line ≈ Y=172mm
    //   Signature underline ≈ Y=188mm  ("ME/MTQA" label printed at Y≈190)
    //   Left half: X=14 → X=105 (width=91mm), centre X=59.5
    //   Right half: X=105 → X=196 (width=91mm), centre X=150.5
    // =========================================================================
    private const MTQA_REMARKS_X  = 55.0;
    private const MTQA_REMARKS_Y  = 165.0;
    private const MTQA_REMARKS_W  = 137.0;

    private const MTQA_SIG_LINE_Y = 188.0;
    private const MTQA_SIG_CENTER = 59.5;   // centre of left half
    private const MTQA_NAME_X     = 14.0;
    private const MTQA_NAME_W     = 91.0;

    private const MTQA_DATE_X     = 105.0;
    private const MTQA_DATE_W     = 91.0;

    // =========================================================================
    // RESIDENT ENGINEER BLOCK
    // From grid:
    //   Box top ≈ Y=198mm
    //   Remarks text ≈ Y=201mm
    //   Signature underline ≈ Y=225mm  ("Resident Engineer/Project In-Charge" at Y≈227)
    //   Same left/right split as MTQA block
    // =========================================================================
    private const RE_REMARKS_X  = 55.0;
    private const RE_REMARKS_Y  = 201.0;
    private const RE_REMARKS_W  = 137.0;

    private const RE_SIG_LINE_Y = 225.0;
    private const RE_SIG_CENTER = 59.5;
    private const RE_NAME_X     = 14.0;
    private const RE_NAME_W     = 91.0;

    private const RE_DATE_X     = 105.0;
    private const RE_DATE_W     = 91.0;

    // =========================================================================
    // APPROVAL ROW
    // From grid: row at Y≈238–250mm
    // BW = 182mm, 3 segments each = 60.67mm
    // Seg 1 (Request):     X=14
    // Seg 2 (Approved):    X=74.67
    // Seg 3 (Disapproved): X=135.33
    // =========================================================================
    private const APPROVAL_ROW_Y = 241.0;
    private const APPROVAL_SEG_W = 60.67;

    // =========================================================================
    // NOTED BY — Provincial Engineer
    // From grid:
    //   Remarks box top ≈ Y=250mm, text ≈ Y=253mm
    //   "Noted by:" label ≈ Y=265mm
    //   "DELIA E. DAMASCO" text ≈ Y=265mm, X≈635px→~168mm
    //   Signature sits above Y=265, centred at X≈148mm (right portion)
    // =========================================================================
    private const PE_REMARKS_X  = 55.0;
    private const PE_REMARKS_Y  = 253.0;
    private const PE_REMARKS_W  = 137.0;

    private const PE_NAME_Y     = 265.0;
    private const PE_NAME_X     = 105.0;
    private const PE_NAME_W     = 87.0;

    private const PE_SIG_CENTER = 148.5;   // 105 + 87/2
    private const PE_SIG_LINE_Y = 265.0;

    // =========================================================================
    // CONSTRUCTOR
    // =========================================================================

    private ConcretePouring $cp;
    private array $tmpFiles = [];

    public function __construct(ConcretePouring $concretePouring)
    {
        parent::__construct('P', 'mm', 'A4');
        $this->cp = $concretePouring;
        $this->SetMargins(self::ML, 10, self::ML);
        $this->SetAutoPageBreak(false);
        $this->AddPage();
        $this->importTemplate();
        $this->stampAllFields();
    }

    public function __destruct()
    {
        foreach ($this->tmpFiles as $f) {
            if (file_exists($f)) @unlink($f);
        }
    }

    // =========================================================================
    // TEMPLATE IMPORT
    // =========================================================================

    private function importTemplate(): void
    {
        $path = storage_path('app/' . self::TEMPLATE_PATH);

        if (!file_exists($path)) {
            logger()->warning('ConcretePouringPdf: template not found at ' . $path);
            return;
        }

        try {
            $this->setSourceFile($path);
            $tplId = $this->importPage(1);
            $this->useTemplate($tplId, 0, 0, self::PAGE_W, self::PAGE_H);
        } catch (\Throwable $e) {
            logger()->error('ConcretePouringPdf template import failed: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // ORCHESTRATOR
    // =========================================================================

    private function stampAllFields(): void
    {
        $this->stampProjectInfo();
        $this->stampChecklist();
        $this->stampMeMtqaBlock();
        $this->stampResidentEngineerBlock();
        $this->stampApprovalRow();
        $this->stampNotedByBlock();
    }

    // =========================================================================
    // PROJECT INFORMATION
    // =========================================================================

    private function stampProjectInfo(): void
    {
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(0, 0, 0);

        foreach (self::PROJECT_INFO_ROWS as $field => $y) {
            $value = match ($field) {
                'pouring_datetime' => $this->fmtDatetime($this->cp->pouring_datetime),
                'estimated_volume' => $this->cp->estimated_volume
                                      ? number_format((float) $this->cp->estimated_volume, 2)
                                      : '',
                default => $this->v($this->cp->{$field}),
            };

            $this->SetXY(self::VALUE_X, $y);
            $this->Cell(self::VALUE_W, 5, $value, 0, 0, 'L');
        }
    }

    // =========================================================================
    // CHECKLIST
    // =========================================================================

    // =========================================================================
// CHECKLIST — draw a proper ✓ checkmark using two diagonal lines
// =========================================================================
    private function stampChecklist(): void
    {
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.5);

        foreach (self::CHECKLIST_MAP as $field => [$col, $rowIdx]) {
            if (!$this->cp->{$field}) continue;

            $x = ($col === 'L') ? self::CHECKLIST_LEFT_X : self::CHECKLIST_RIGHT_X;
            $y = self::CHECKLIST_START_Y + ($rowIdx * self::CHECKLIST_ROW_H);

            // Centre the checkmark inside the checkbox square (≈3.5×3.5mm box)
            // Box is at ($x+0.5, $y+1.3) size 3.2×3.2
            $bx = $x + 0.5;   // box left
            $by = $y + 1.3;   // box top
            $bw = 3.2;         // box width
            $bh = 3.2;         // box height

            // ✓ shape: short left leg then long right leg
            // Left leg:  bottom-left corner going up-right to ~40% width
            // Right leg: from that midpoint going up-right to top-right corner
            $midX = $bx + $bw * 0.35;
            $midY = $by + $bh * 0.75;

            $this->Line(
                $bx + 0.3,          // start X (slightly inside left)
                $by + $bh * 0.5,    // start Y (middle height)
                $midX,              // mid X
                $midY               // mid Y (bottom of tick)
            );
            $this->Line(
                $midX,              // mid X
                $midY,              // mid Y
                $bx + $bw - 0.2,   // end X (slightly inside right)
                $by + 0.3           // end Y (near top)
            );
        }

        // Reset
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(0, 0, 0);
    }

    // =========================================================================
    // ME / MTQA BLOCK
    // =========================================================================

    private function stampMeMtqaBlock(): void
    {
        // Remarks
        if ($this->cp->me_mtqa_remarks) {
            $this->SetFont('Arial', '', 8);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(self::MTQA_REMARKS_X, self::MTQA_REMARKS_Y);
            $this->MultiCell(self::MTQA_REMARKS_W, 4, $this->v($this->cp->me_mtqa_remarks), 0);
        }

        // Signature image — sits above the underline
        $this->placeSignature(
            value:   $this->cp->me_mtqa_signature,
            centerX: self::MTQA_SIG_CENTER,
            lineY:   self::MTQA_SIG_LINE_Y,
            sigW:    36,
            sigH:    10
        );

        // Printed name — centred in left half, just above the underline
        $name = $this->cp->meMtqaChecker?->name ?? '';
        if ($name) {
            $this->SetFont('Arial', 'B', 8);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(self::MTQA_NAME_X, self::MTQA_SIG_LINE_Y - 4.5);
            $this->Cell(self::MTQA_NAME_W, 4, $name, 0, 0, 'C');
        }

        // Date — centred in right half
        if ($this->cp->me_mtqa_date) {
            $this->SetFont('Arial', 'B', 8);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(self::MTQA_DATE_X, self::MTQA_SIG_LINE_Y - 4.5);
            $this->Cell(self::MTQA_DATE_W, 4, $this->fmtDate($this->cp->me_mtqa_date), 0, 0, 'C');
        }
    }

    // =========================================================================
    // RESIDENT ENGINEER BLOCK
    // =========================================================================

    private function stampResidentEngineerBlock(): void
    {
        // Remarks
        if ($this->cp->re_remarks) {
            $this->SetFont('Arial', '', 8);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(self::RE_REMARKS_X, self::RE_REMARKS_Y);
            $this->MultiCell(self::RE_REMARKS_W, 4, $this->v($this->cp->re_remarks), 0);
        }

        // Signature image
        $this->placeSignature(
            value:   $this->cp->re_signature,
            centerX: self::RE_SIG_CENTER,
            lineY:   self::RE_SIG_LINE_Y,
            sigW:    36,
            sigH:    10
        );

        // Printed name
        $name = $this->cp->residentEngineer?->name ?? '';
        if ($name) {
            $this->SetFont('Arial', 'B', 8);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(self::RE_NAME_X, self::RE_SIG_LINE_Y - 4.5);
            $this->Cell(self::RE_NAME_W, 4, $name, 0, 0, 'C');
        }

        // Date
        if ($this->cp->re_date) {
            $this->SetFont('Arial', 'B', 8);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(self::RE_DATE_X, self::RE_SIG_LINE_Y - 4.5);
            $this->Cell(self::RE_DATE_W, 4, $this->fmtDate($this->cp->re_date), 0, 0, 'C');
        }
    }

    // =========================================================================
    // APPROVAL ROW
    // =========================================================================

    private function stampApprovalRow(): void
    {
        // Approved — middle segment
        if ($this->cp->status === 'approved' && $this->cp->approver?->name) {
            $this->SetFont('Arial', 'B', 8);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(self::ML + self::APPROVAL_SEG_W, self::APPROVAL_ROW_Y);
            $this->Cell(self::APPROVAL_SEG_W, 4, $this->cp->approver->name, 0, 0, 'C');
        }

        // Disapproved — right segment
        if ($this->cp->status === 'disapproved' && $this->cp->disapprover?->name) {
            $this->SetFont('Arial', 'B', 8);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(self::ML + self::APPROVAL_SEG_W * 2, self::APPROVAL_ROW_Y);
            $this->Cell(self::APPROVAL_SEG_W, 4, $this->cp->disapprover->name, 0, 0, 'C');
        }
    }

    // =========================================================================
    // NOTED BY — PROVINCIAL ENGINEER
    // =========================================================================

    private function stampNotedByBlock(): void
    {
        // Approval / provincial remarks
        if ($this->cp->approval_remarks) {
            $this->SetFont('Arial', '', 8);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(self::PE_REMARKS_X, self::PE_REMARKS_Y);
            $this->MultiCell(self::PE_REMARKS_W, 4, $this->v($this->cp->approval_remarks), 0);
        }

        // Signature image — above the name line
        $this->placeSignature(
            value:   $this->cp->noted_by_signature,
            centerX: self::PE_SIG_CENTER,
            lineY:   self::PE_SIG_LINE_Y,
            sigW:    40,
            sigH:    10
        );

        // Provincial Engineer name
        // The template already prints "DELIA E. DAMASCO" so we only override
        // if a different user is assigned as noted_by.
        $assignedName = $this->cp->notedByEngineer?->name ?? '';
        if ($assignedName && strtoupper($assignedName) !== 'DELIA E. DAMASCO') {
            $this->SetFont('Arial', 'B', 8);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(self::PE_NAME_X, self::PE_NAME_Y);
            $this->Cell(self::PE_NAME_W, 4, $assignedName, 0, 0, 'C');
        }
    }

    // =========================================================================
    // SIGNATURE PLACEMENT
    // =========================================================================

    private function placeSignature(
        ?string $value,
        float   $centerX,
        float   $lineY,
        float   $sigW = 40,
        float   $sigH = 12
    ): void {
        $file = $this->resolveSignatureToFile($value);
        if (!$file) return;

        $x = $centerX - ($sigW / 2);
        $y = $lineY - $sigH - 0.5;

        try {
            $this->Image($file, $x, $y, $sigW, $sigH);
        } catch (\Throwable) {
            // Skip corrupt or unsupported image
        }
    }

    // =========================================================================
    // SIGNATURE FILE RESOLVER
    // =========================================================================

    private function resolveSignatureToFile(?string $value): ?string
    {
        if (empty($value)) return null;

        if (str_starts_with($value, 'data:image') || $this->looksLikeBase64($value)) {
            $raw = preg_replace('/^data:image\/\w+;base64,/', '', $value);
            $raw = base64_decode($raw, true);

            if ($raw === false || strlen($raw) < 100) return null;

            $ext     = (substr($raw, 0, 3) === "\xff\xd8\xff") ? 'jpg' : 'png';
            $tmpFile = tempnam(sys_get_temp_dir(), 'cpsig_') . '.' . $ext;

            if (file_put_contents($tmpFile, $raw) === false) return null;

            $this->tmpFiles[] = $tmpFile;
            return $tmpFile;
        }

        $absolute = storage_path('app/public/' . ltrim($value, '/'));
        if (file_exists($absolute)) return $absolute;

        $pub = public_path('storage/' . ltrim($value, '/'));
        if (file_exists($pub)) return $pub;

        return null;
    }

    private function looksLikeBase64(string $value): bool
    {
        if (strlen($value) < 100) return false;
        if (str_contains($value, '/') && str_contains($value, '.')) return false;
        return (bool) preg_match('/^[A-Za-z0-9+\/=]+$/', substr($value, 0, 100));
    }

    // =========================================================================
    // UTILITIES
    // =========================================================================

    private function v(?string $value): string
    {
        return $value ?? '';
    }

    private function fmtDate(mixed $d, string $fmt = 'M d, Y'): string
    {
        if (!$d) return '';
        try { return Carbon::parse($d)->format($fmt); }
        catch (\Throwable) { return ''; }
    }

    private function fmtDatetime(mixed $d): string
    {
        if (!$d) return '';
        try { return Carbon::parse($d)->format('M d, Y h:i A'); }
        catch (\Throwable) { return ''; }
    }
}