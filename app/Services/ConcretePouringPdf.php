<?php

namespace App\Services;

use App\Models\ConcretePouring;
use Carbon\Carbon;

class ConcretePouringPdf extends \FPDF
{
    private const ML = 14;
    private const MT = 10;
    private const BW = 182;
    private const CL = 91;
    private const CR = 91;
    private const CB_W = 8;
    private const LB_W = 83;
    private const RL = 91;
    private const RR = 91;
    private const BLUE  = [0, 176, 240];
    private const BLACK = [0,   0,   0];
    private const WHITE = [255, 255, 255];
    private const DGRAY = [80,  80,  80];

    private ConcretePouring $cp;
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
                @unlink($f);
            }
        }
    }

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

    private function drawRequestedBy(float $y): float
    {
        $y += 4;
        $lineW = 115;
        $lineX = self::ML + (self::BW - $lineW) / 1;

        $this->SetXY($lineX, $y);
        $this->SetFont('Arial', '', 8);
        $this->setColor('text', ...self::BLACK);
        $this->Cell($lineW, 4, 'Requested by:', 0, 1, 'L');

        $y += 4;

        $contractorName = $this->cp->requestedBy?->name ?? $this->cp->contractor ?? '';
        if ($contractorName) {
            $this->SetXY($lineX, $y + 1);
            $this->SetFont('Arial', 'B', 8);
            $this->setColor('text', ...self::BLACK);
            $this->Cell($lineW, 4, $contractorName, 0, 0, 'C');
        }

        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.3);
        $this->Line($lineX, $y + 6, $lineX + $lineW, $y + 6);
        $this->SetLineWidth(0.2);

        $this->SetXY($lineX, $y + 7);
        $this->SetFont('Arial', '', 7);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell($lineW, 3, 'Contractor', 0, 1, 'C');

        return $y + 12;
    }

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

    private function drawChecklist(float $y): float
    {
        $pairs = [
            ['Concrete Vibrator',                                        'concrete_vibrator',              'Field Density Test (FDT)',                                          'field_density_test'],
            ['Protective Covering Materials',                             'protective_covering_materials',  'BEAM/Cylinder Molds',                                              'beam_cylinder_molds'],
            ['Warning Signs/Barricades/Flagmen',                         'warning_signs_barricades',       'Curing Materials',                                                 'curing_materials'],
            ['Concrete Saw',                                             'concrete_saw',                   'Slump Cones',                                                      'slump_cones'],
            ['Concrete Block Spacer',                                    'concrete_block_spacer',          'Plumbness',                                                        'plumbness'],
            ['Finishing Tools/Equipment (Screeder, Broom, etc)',         'finishing_tools_equipment',      'Quality of Materials (Result of Design/Trial Mix Test Reports)',    'quality_of_materials'],
            ['Line and Grade Alignment (Form setting, elevation, etc)',  'line_grade_alignment',           'Lighting System',                                                  'lighting_system'],
            ['Required Construction Equipment',                          'required_construction_equipment','Electrical Layout (Roughing-Ins/Embedment)',                        'electrical_layout'],
            ['Rebar Sizes, Spacing and number',                          'rebar_sizes_spacing',            'Plumbing Layout (Roughing-Ins/Embedment)',                          'plumbing_layout'],
            ['Rebars installation requirement',                          'rebars_installation',            'Falseworks/Formworks Adequacy',                                    'falseworks_formworks'],
        ];

        $rowH = 6;
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);

        foreach ($pairs as [$ll, $lf, $rl, $rf]) {
            $xL = self::ML;
            $xR = self::ML + self::CL;

            $this->Rect($xL, $y, self::CL, $rowH);
            $this->Rect($xR, $y, self::CR, $rowH);

            $this->drawCheckbox($xL + 1, $y + 1.2, (bool) $this->cp->{$lf});
            $this->SetXY($xL + self::CB_W + 1, $y + 1);
            $this->SetFont('Arial', '', 7);
            $this->setColor('text', ...self::BLACK);
            $this->Cell(self::LB_W - 2, $rowH - 2, $ll, 0);

            $this->drawCheckbox($xR + 1, $y + 1.2, (bool) $this->cp->{$rf});
            $this->SetXY($xR + self::CB_W + 1, $y + 1);
            $this->SetFont('Arial', '', 7);
            $this->setColor('text', ...self::BLACK);
            $this->Cell(self::LB_W - 2, $rowH - 2, $rl, 0);

            $y += $rowH;
        }

        return $y;
    }

    private function drawCheckbox(float $x, float $y, bool $checked): void
    {
        $s = 3.5;
        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);
        $this->Rect($x, $y, $s, $s);

        if ($checked) {
            $this->SetLineWidth(0.4);
            $this->Line($x + 0.5,     $y + 1.8,      $x + 1.4,      $y + $s - 0.5);
            $this->Line($x + 1.4,     $y + $s - 0.5, $x + $s - 0.3, $y + 0.5);
            $this->SetLineWidth(0.2);
        }
    }

    private function drawCheckedByLabel(float $y): float
    {
        $y += 3;
        $this->SetXY(self::ML, $y);
        $this->SetFont('Arial', '', 8);
        $this->setColor('text', ...self::BLACK);
        $this->Cell(self::BW, 4, 'Checked by:', 0, 1, 'L');
        return $y + 5;
    }

    private function drawMeMtqaBlock(float $y): float
    {
        $h = 28;

        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);
        $this->Rect(self::ML, $y, self::BW, $h);

        $this->SetXY(self::ML + 1, $y + 1);
        $this->SetFont('Arial', '', 7.5);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell(32, 3.5, 'Remarks/Recommendation :', 0);

        $this->SetXY(self::ML + 34, $y + 1);
        $this->SetFont('Arial', '', 8);
        $this->setColor('text', ...self::BLACK);
        $this->MultiCell(self::BW - 35, 3.5, $this->v($this->cp->me_mtqa_remarks), 0);

        $dividerY = $y + 10;
        $this->SetLineWidth(0.2);
        $this->Line(self::ML, $dividerY, self::ML + self::BW, $dividerY);
        $this->Line(self::ML + self::RL, $dividerY, self::ML + self::RL, $y + $h);

        $this->sigLine(
            cellX:          self::ML,
            cellY:          $dividerY,
            cellW:          self::RL,
            cellH:          $h - ($dividerY - $y),
            name:           $this->cp->meMtqaChecker?->name ?? '',
            role:           'ME/MTQA',
            signatureValue: $this->cp->me_mtqa_signature ?? null,
        );

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

    private function drawResidentEngineerBlock(float $y): float
    {
        $h = 28;

        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.2);
        $this->Rect(self::ML, $y, self::BW, $h);

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

        $this->sigLine(
            cellX:          self::ML,
            cellY:          $dividerY,
            cellW:          self::RL,
            cellH:          $h - ($dividerY - $y),
            name:           $this->cp->residentEngineer?->name ?? '',
            role:           'Resident Engineer/Project In-Charge',
            signatureValue: $this->cp->re_signature ?? null,
        );

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

    private function drawApprovalRow(float $y): float
    {
        $h    = 12;
        $segW = self::BW / 3;

        $this->SetLineWidth(0.2);
        $this->SetDrawColor(...self::BLACK);
        $this->Rect(self::ML, $y, self::BW, $h);
        $this->Line(self::ML + $segW,     $y, self::ML + $segW,     $y + $h);
        $this->Line(self::ML + $segW * 2, $y, self::ML + $segW * 2, $y + $h);

        $this->lbl(self::ML + 1, $y + 2, 'Request :');
        $this->SetLineWidth(0.3);
        $this->Line(self::ML + 1, $y + 9, self::ML + $segW - 2, $y + 9);
        $this->SetLineWidth(0.2);

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

    private function drawNotedByBlock(float $y): void
    {
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

        $h = 28;
        $this->Rect(self::ML, $y, self::BW, $h);

        $this->SetXY(self::ML + 1, $y + $h - 10);
        $this->SetFont('Arial', '', 8);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell(22, 4, 'Noted by:', 0);

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
    // PRIMITIVES
    // =========================================================================

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
        $lineY = $cellY + $cellH - 8;

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

        $nameStyle = $underlineName ? 'BU' : 'B';
        $this->SetXY($lineX, $lineY - 4);
        $this->SetFont('Arial', $nameStyle, 8);
        $this->setColor('text', ...self::BLACK);
        $this->Cell($lineW, 4, $name, 0, 0, 'C');

        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.3);
        $this->Line($lineX, $lineY, $lineX + $lineW, $lineY);
        $this->SetLineWidth(0.2);

        $this->SetXY($cellX, $lineY + 0.5);
        $this->SetFont('Arial', '', 6);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell($cellW, 3, 'Signature Over Printed Name', 0, 2, 'C');

        $this->SetX($cellX);
        $this->SetFont('Arial', '', 6.5);
        $this->Cell($cellW, 3, $role, 0, 0, 'C');

        $this->resetColors();
    }

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

        $this->SetXY($lineX, $lineY - 4);
        $this->SetFont('Arial', 'B', 8);
        $this->setColor('text', ...self::BLACK);
        $this->Cell($lineW, 4, $dateValue, 0, 0, 'C');

        $this->SetDrawColor(...self::BLACK);
        $this->SetLineWidth(0.3);
        $this->Line($lineX, $lineY, $lineX + $lineW, $lineY);
        $this->SetLineWidth(0.2);

        $this->SetXY($cellX, $lineY + 0.5);
        $this->SetFont('Arial', '', 7);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell($cellW, 3, $label, 0, 0, 'C');

        $this->resetColors();
    }

    // =========================================================================
    // SIGNATURE RESOLVER
    // =========================================================================

    /**
     * Convert a signature value to an absolute filesystem path FPDF can embed.
     *
     * Accepts:
     *   1. data:image/png;base64,…      — data URI (canvas draw)
     *   2. iVBOR…                       — raw base64 (no prefix)
     *   3. http(s)://…/storage/…        — full URL (Docker: strips to local path)
     *   4. signatures/abc.png           — storage-relative path
     *
     * All resolved files are passed through normaliseToPng() which removes
     * white/near-white backgrounds so only ink strokes appear in the PDF.
     */
    private function resolveSignatureToFile(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // ── Case 3: Full HTTP/HTTPS URL → resolve to local storage path ───────
        // In Docker the hidden input is set to asset('storage/...') which
        // produces a full URL. FPDF cannot fetch HTTP — convert to local path.
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            $urlPath = parse_url($value, PHP_URL_PATH); // "/storage/signatures/foo.png"
            if ($urlPath && str_contains($urlPath, '/storage/')) {
                $relative = substr($urlPath, strpos($urlPath, '/storage/') + strlen('/storage/'));
                $absolute = storage_path('app/public/' . ltrim($relative, '/'));
                if (file_exists($absolute)) {
                    return $this->normaliseToPng($absolute);
                }
            }
            return null; // URL not resolvable locally — skip
        }

        // ── Cases 1 & 2: base64 (data-URI or raw) ────────────────────────────
        if (str_starts_with($value, 'data:image') || $this->looksLikeBase64($value)) {
            $raw = preg_replace('/^data:image\/\w+;base64,/', '', $value);
            $raw = base64_decode($raw, true);

            if ($raw === false || strlen($raw) < 100) {
                return null;
            }

            $tmpFile = tempnam(sys_get_temp_dir(), 'cpsig_') . '.png';

            if (substr($raw, 0, 3) === "\xff\xd8\xff") {
                // JPEG → convert to PNG so FPDF gets alpha support
                $src = @imagecreatefromstring($raw);
                if (!$src) {
                    return null;
                }
                imagepng($src, $tmpFile);
                imagedestroy($src);
            } else {
                file_put_contents($tmpFile, $raw);
            }

            $this->tmpFiles[] = $tmpFile;

            // Base64 PNGs from canvas may still have a white fill if the JS
            // side wrote one — run through normalise to strip it.
            return $this->normaliseToPng($tmpFile, ownedByUs: true);
        }

        // ── Case 4: storage-relative path ────────────────────────────────────
        $absolute = storage_path('app/public/' . ltrim($value, '/'));
        if (file_exists($absolute)) {
            return $this->normaliseToPng($absolute);
        }

        $pub = public_path('storage/' . ltrim($value, '/'));
        if (file_exists($pub)) {
            return $this->normaliseToPng($pub);
        }

        return null;
    }

    /**
     * Ensure the image at $filePath is a transparent-background PNG.
     *
     * • If the image already has genuine alpha transparency → return as-is (PNG)
     *   or re-save as PNG (JPEG/GIF).
     * • Otherwise walk every pixel and make near-white (R,G,B > 230) transparent.
     *
     * @param bool $ownedByUs  True when $filePath is already a tmp file we wrote;
     *                         we can overwrite it in-place instead of making a new one.
     */
    private function normaliseToPng(string $filePath, bool $ownedByUs = false): ?string
    {
        $info = @getimagesize($filePath);
        if (!$info) {
            return $filePath;
        }

        [$width, $height, $type] = $info;

        $src = match ($type) {
            IMAGETYPE_PNG  => @imagecreatefrompng($filePath),
            IMAGETYPE_JPEG => @imagecreatefromjpeg($filePath),
            IMAGETYPE_GIF  => @imagecreatefromgif($filePath),
            default        => null,
        };

        if (!$src) {
            return $filePath;
        }

        // ── Detect genuine alpha transparency ─────────────────────────────────
        // imageistruecolor() is true for canvas-exported PNGs.
        // We sample a grid of pixels rather than just corners, because ink
        // pixels are opaque (alpha=0 in GD's 0–127 inverted scale) and the
        // background pixels should be transparent (alpha=127) if the canvas
        // had no white fill.  A white-filled canvas has alpha=0 everywhere.
        $hasTransparency = false;

        if (imageistruecolor($src)) {
            // Enable alpha reading
            imagesavealpha($src, true);
            // Sample a 5×5 grid across the image
            $stepX = max(1, (int) ($width  / 5));
            $stepY = max(1, (int) ($height / 5));
            for ($px = 0; $px < $width && !$hasTransparency; $px += $stepX) {
                for ($py = 0; $py < $height && !$hasTransparency; $py += $stepY) {
                    $rgba  = imagecolorsforindex($src, imagecolorat($src, $px, $py));
                    // GD alpha: 0=opaque, 127=fully transparent
                    if (($rgba['alpha'] ?? 0) > 10) {
                        $hasTransparency = true;
                    }
                }
            }
        }

        if ($hasTransparency && $type === IMAGETYPE_PNG) {
            // Already a good transparent PNG — nothing to do
            imagedestroy($src);
            return $filePath;
        }

        // ── Build output canvas with alpha support ────────────────────────────
        $out = imagecreatetruecolor($width, $height);
        imagealphablending($out, false);
        imagesavealpha($out, true);

        $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
        imagefilledrectangle($out, 0, 0, $width, $height, $transparent);

        // Blit source onto transparent canvas
        imagealphablending($out, true);
        imagecopy($out, $src, 0, 0, 0, 0, $width, $height);
        imagedestroy($src);

        // ── Punch out near-white pixels ───────────────────────────────────────
        imagealphablending($out, false);
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgba = imagecolorsforindex($out, imagecolorat($out, $x, $y));
                if ($rgba['red'] > 230 && $rgba['green'] > 230 && $rgba['blue'] > 230) {
                    imagesetpixel($out, $x, $y, $transparent);
                }
            }
        }

        // ── Save ──────────────────────────────────────────────────────────────
        $outFile = $ownedByUs ? $filePath : tempnam(sys_get_temp_dir(), 'cpsig_') . '.png';
        imagesavealpha($out, true);
        imagepng($out, $outFile);
        imagedestroy($out);

        if (!$ownedByUs) {
            $this->tmpFiles[] = $outFile;
        }

        return $outFile;
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
            return false;
        }
        return (bool) preg_match('/^[A-Za-z0-9+\/=]+$/', substr($value, 0, 100));
    }

    // =========================================================================
    // UTILITY HELPERS
    // =========================================================================

    private function lbl(float $x, float $y, string $text): void
    {
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 7);
        $this->setColor('text', ...self::DGRAY);
        $this->Cell($this->GetStringWidth($text) + 1, 3.5, $text, 0);
    }

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