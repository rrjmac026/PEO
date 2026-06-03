<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserCredentialsMail;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Smalot\PdfParser\Parser as PdfParser;

class EmployeeImportController extends Controller
{
    // Default password assigned to every imported user.
    // They should be prompted to change it on first login.
    private const DEFAULT_PASSWORD = 'password';

    // Maps lowercase Excel header → Employee fillable field
    private const COLUMN_MAP = [
        'first name'                                                                                  => 'first_name',
        'last name'                                                                                   => 'last_name',
        'middle name'                                                                                 => 'middle_name',
        'email address'                                                                               => 'email_address',
        'date of birth'                                                                               => 'date_of_birth',
        'blood type'                                                                                  => 'blood_type',
        'height (cm)'                                                                                 => 'height_cm',
        'weight (kg)'                                                                                 => 'weight_kg',
        'home address (st., brgy, mun./city, prov.)'                                                  => 'home_address',
        'home address'                                                                                => 'home_address',
        'phone number (09xxxxxxxxx)'                                                                  => 'phone_number',
        'phone number'                                                                                => 'phone_number',
        'emergency contact no.   (09xxxxxxxxx)'                                                      => 'emergency_contact_no',
        'emergency contact no.'                                                                       => 'emergency_contact_no',
        'id number (pds-xxxxxxxxx)'                                                                   => 'id_number',
        'id number'                                                                                   => 'id_number',
        'tin (xxx-xxx-xxx)'                                                                           => 'tin',
        'tin'                                                                                         => 'tin',
        'pag-ibig no.'                                                                                => 'pagibig_no',
        'philhealth (15-000000000-6)'                                                                 => 'philhealth',
        'philhealth'                                                                                  => 'philhealth',
        'gsis no. (10 digit no.)'                                                                     => 'gsis_no',
        'gsis no.'                                                                                    => 'gsis_no',
        'hmo organization ( ex. 1 health coop - ficco )'                                              => 'hmo_organization',
        'hmo organization'                                                                            => 'hmo_organization',
        'hmo #'                                                                                       => 'hmo_number',
        'eligibility (csc, tesda nc ii, prc, others)'                                                 => 'eligibility',
        'eligibility'                                                                                 => 'eligibility',
        'position title (ex. administrative aide vi (clerk iii), architect iii, mason i (b), etc.)'  => 'position_title',
        'position title'                                                                              => 'position_title',
        'licence number'                                                                              => 'licence_number',
        'license number'                                                                              => 'licence_number',
    ];

    // Maps lowercase position title keywords → users.role value.
    // Order matters: more specific entries must come before broader ones.
    private const POSITION_ROLE_MAP = [
        'provincial engineer' => 'provincial_engineer',
        'engineer iv'         => 'engineeriv',
        'engineer iii'        => 'engineeriii',
        'resident engineer'   => 'resident_engineer',
        'site inspector'      => 'site_inspector',
        'surveyor'            => 'surveyor',
        'mtqa'                => 'mtqa',
        'contractor'          => 'contractor',
    ];

    // Columns to skip entirely (not stored).
    private const SKIP_COLUMNS = [
        'timestamp',
        'in compliance with the data privacy act',
    ];

    // Column headers used for both template downloads (single source of truth)
    private const TEMPLATE_HEADERS = [
        'First Name',
        'Last Name',
        'Middle Name',
        'Email Address',
        'Date of Birth',
        'Blood Type',
        'Height (cm)',
        'Weight (kg)',
        'Home Address (St., Brgy, Mun./City, Prov.)',
        'Phone Number (09xxxxxxxxx)',
        'Emergency Contact No.   (09xxxxxxxxx)',
        'ID Number (PDS-xxxxxxxxx)',
        'TIN (xxx-xxx-xxx)',
        'Pag-IBIG No.',
        'PhilHealth (15-000000000-6)',
        'GSIS No. (10 digit no.)',
        'HMO Organization ( ex. 1 Health Coop - FICCO )',
        'HMO #',
        'Eligibility (CSC, TESDA NC II, PRC, Others)',
        'Position Title (ex. Administrative Aide VI (Clerk III), Architect III, Mason I (B), etc.)',
        'Licence Number',
    ];

    // ── Show import form ───────────────────────────────────────────────────

    public function showImportForm()
    {
        return view('admin.employees.import');
    }

    // ── Template downloads ─────────────────────────────────────────────────

    public function downloadTemplateExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // ── Employee Import sheet ─────────────────────────────────────────
        $sheet    = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Employee Import');
        $colCount = count(self::TEMPLATE_HEADERS);
        $lastCol  = Coordinate::stringFromColumnIndex($colCount);

        // Colour palette (mirrors login blade CSS variables)
        $STONE      = '2C1E12';
        $ORANGE     = 'E05A00';
        $ORANGE_DRK = 'B84A00';
        $CREAM      = 'FFF8F0';
        $WARM_WHITE = 'FFFCF9';
        $GRAY_SOFT  = 'F5EDE4';
        $STONE_MID  = '6B4F3A';

        $solidFill  = Fill::FILL_SOLID;
        $thinStyle  = Border::BORDER_THIN;
        $medStyle   = Border::BORDER_MEDIUM;

        // Row 1 — title banner
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'Provincial Engineering Office — Employee Import Template');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => $solidFill, 'startColor' => ['argb' => "FF{$STONE}"]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        // Row 2 — subtitle
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2',
            'Fill in each column. Column headers must remain unchanged. Rows with duplicate ID Numbers will be skipped.');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['argb' => "FF{$STONE_MID}"], 'name' => 'Arial'],
            'fill'      => ['fillType' => $solidFill, 'startColor' => ['argb' => "FF{$GRAY_SOFT}"]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // Row 3 — column headers
        foreach (self::TEMPLATE_HEADERS as $i => $header) {
            $col  = Coordinate::stringFromColumnIndex($i + 1);
            $cell = "{$col}3";
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->applyFromArray([
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Arial'],
                'fill'      => ['fillType' => $solidFill, 'startColor' => ['argb' => "FF{$ORANGE}"]],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                'borders'   => [
                    'bottom' => ['borderStyle' => $medStyle, 'color' => ['argb' => "FF{$ORANGE_DRK}"]],
                ],
            ]);
        }
        $sheet->getRowDimension(3)->setRowHeight(40);

        // Rows 4–5 — sample / example data
        $samples = [
            ['Juan','Dela Cruz','Santos','juan.delacruz@example.com','1990-05-15','O+','170','65',
             '123 Rizal St., Brgy. Poblacion, Malaybalay City, Bukidnon',
             '09171234567','09181234567','PDS-000000001','123-456-789',
             '1234567890','15-000000000-6','1234567890','1 Health Coop - FICCO','HMO-001',
             'CSC Professional','Engineer III','PRC-12345'],
            ['Maria','Reyes','Cruz','maria.reyes@example.com','1985-08-20','A+','160','55',
             '456 Mabini Ave., Brgy. 5, Malaybalay City, Bukidnon',
             '09271234567','09281234567','PDS-000000002','987-654-321',
             '0987654321','15-111111111-1','0987654321','','',
             'TESDA NC II','Administrative Aide VI (Clerk III)',''],
        ];

        $thinBorderStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => $thinStyle, 'color' => ['argb' => 'FFD4B9A8']],
            ],
        ];

        foreach ($samples as $rowOffset => $rowData) {
            $excelRow = 4 + $rowOffset;
            $bg       = ($excelRow % 2 === 0) ? $CREAM : $WARM_WHITE;
            foreach ($rowData as $colOffset => $value) {
                $col  = Coordinate::stringFromColumnIndex($colOffset + 1);
                $cell = "{$col}{$excelRow}";
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->applyFromArray(array_merge($thinBorderStyle, [
                    'font' => ['size' => 9, 'color' => ['argb' => "FF{$STONE}"], 'name' => 'Arial'],
                    'fill' => ['fillType' => $solidFill, 'startColor' => ['argb' => "FF{$bg}"]],
                ]));
            }
            $sheet->getRowDimension($excelRow)->setRowHeight(16);
        }

        // Column widths (matches header content)
        $widths = [14,14,14,28,14,10,10,10,40,16,16,18,14,14,18,16,32,12,28,52,16];
        foreach ($widths as $i => $w) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $sheet->freezePane('A4');

        // ── Instructions sheet ────────────────────────────────────────────
        $info = $spreadsheet->createSheet();
        $info->setTitle('Instructions');

        $info->mergeCells('A1:B1');
        $info->setCellValue('A1', 'Import Instructions');
        $info->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => $solidFill, 'startColor' => ['argb' => "FF{$STONE}"]],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $info->getRowDimension(1)->setRowHeight(28);

        foreach (['A2' => 'Field', 'B2' => 'Notes'] as $cell => $label) {
            $info->setCellValue($cell, $label);
            $info->getStyle($cell)->applyFromArray([
                'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Arial'],
                'fill'      => ['fillType' => $solidFill, 'startColor' => ['argb' => "FF{$ORANGE}"]],
                'alignment' => ['horizontal' => 'center'],
            ]);
        }

        $instructions = [
            ['Required columns',  'First Name, Last Name, ID Number (PDS-xxxxxxxxx), Position Title'],
            ['Unique key',        'ID Number — rows with a duplicate ID are silently skipped'],
            ['Date format',       'YYYY-MM-DD  (e.g. 1990-05-15) or any common date format'],
            ['Phone numbers',     '09xxxxxxxxx — digits only, no spaces or dashes'],
            ['Email',             'Valid email address; used to create login credentials'],
            ['Blood Type',        'A+, A-, B+, B-, AB+, AB-, O+, O-'],
            ['TIN',               'xxx-xxx-xxx format'],
            ['PhilHealth',        '15-000000000-6 format'],
            ['GSIS No.',          '10-digit number'],
            ['Pag-IBIG',          'Numeric, no dashes'],
            ['Position Title',    "Maps to system role. e.g. 'Engineer III', 'Site Inspector', 'MTQA'"],
            ['File size limit',   '10 MB maximum'],
            ['How to upload',     'Go to Admin → Employees → Import, then drop this file in the upload zone'],
        ];

        foreach ($instructions as $i => [$field, $note]) {
            $row = $i + 3;
            $bg  = ($row % 2 === 0) ? $CREAM : $WARM_WHITE;
            $info->setCellValue("A{$row}", $field);
            $info->setCellValue("B{$row}", $note);
            foreach (['A', 'B'] as $col) {
                $info->getStyle("{$col}{$row}")->applyFromArray([
                    'font'    => [
                        'bold' => ($col === 'A'), 'size' => 9,
                        'color' => ['argb' => "FF{$STONE}"], 'name' => 'Arial',
                    ],
                    'fill'    => ['fillType' => $solidFill, 'startColor' => ['argb' => "FF{$bg}"]],
                    'borders' => ['allBorders' => ['borderStyle' => $thinStyle, 'color' => ['argb' => 'FFD4B9A8']]],
                ]);
            }
            $info->getRowDimension($row)->setRowHeight(16);
        }

        $info->getColumnDimension('A')->setWidth(22);
        $info->getColumnDimension('B')->setWidth(70);

        // ── Stream to browser ─────────────────────────────────────────────
        $spreadsheet->setActiveSheetIndex(0);
        $writer   = new XlsxWriter($spreadsheet);
        $filename = 'employee_import_template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function downloadTemplateCsv()
    {
        $lines   = [];
        // Header row
        $lines[] = implode(',', array_map(
            fn($h) => '"' . str_replace('"', '""', $h) . '"',
            self::TEMPLATE_HEADERS
        ));

        // Two example rows so users can see expected format
        $samples = [
            ['Juan','Dela Cruz','Santos','juan.delacruz@example.com','1990-05-15','O+','170','65',
             '123 Rizal St., Brgy. Poblacion, Malaybalay City, Bukidnon',
             '09171234567','09181234567','PDS-000000001','123-456-789',
             '1234567890','15-000000000-6','1234567890','1 Health Coop - FICCO','HMO-001',
             'CSC Professional','Engineer III','PRC-12345'],
            ['Maria','Reyes','Cruz','maria.reyes@example.com','1985-08-20','A+','160','55',
             '456 Mabini Ave., Brgy. 5, Malaybalay City, Bukidnon',
             '09271234567','09281234567','PDS-000000002','987-654-321',
             '0987654321','15-111111111-1','0987654321','','',
             'TESDA NC II','Administrative Aide VI (Clerk III)',''],
        ];

        foreach ($samples as $row) {
            $lines[] = implode(',', array_map(
                fn($v) => '"' . str_replace('"', '""', $v) . '"',
                $row
            ));
        }

        // UTF-8 BOM improves Excel compatibility on Windows
        $csv = "\xEF\xBB\xBF" . implode("\r\n", $lines);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="employee_import_template.csv"',
        ]);
    }

    // ── Handle upload ──────────────────────────────────────────────────────

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,pdf', 'max:10240'],
        ]);

        $file      = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            $rows = match ($extension) {
                'xlsx', 'xls' => $this->parseSpreadsheet($file->getRealPath(), $extension),
                'csv'         => $this->parseCsv($file->getRealPath()),
                'pdf'         => $this->parsePdf($file->getRealPath()),
                default       => throw new \Exception("Unsupported file type: {$extension}"),
            };

            [$imported, $skipped, $emailsSent, $errors] = $this->processRows($rows);

            $message = "Import complete: {$imported} imported, {$skipped} skipped (duplicate ID).";
            if ($emailsSent > 0) {
                $message .= " {$emailsSent} credential email(s) sent.";
            }
            if ($errors) {
                $message .= ' ' . count($errors) . ' row(s) had errors.';
            }

            return redirect()->route('admin.employees.index')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    // ── Parsers ────────────────────────────────────────────────────────────

    private function parseSpreadsheet(string $path, string $ext): array
    {
        $reader = IOFactory::createReader($ext === 'xls' ? 'Xls' : 'Xlsx');
        $reader->setReadDataOnly(true);
        $data = $reader->load($path)->getActiveSheet()->toArray(null, true, true, false);
        return $this->normalizeRows($data);
    }

    private function parseCsv(string $path): array
    {
        $content   = ltrim(file_get_contents($path), "\xEF\xBB\xBF"); // strip BOM
        $lines     = explode("\n", $content);
        $sample    = $lines[0] ?? '';
        $delimiter = substr_count($sample, "\t") > substr_count($sample, ',') ? "\t" : ',';

        $rows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '') {
                $rows[] = str_getcsv($line, $delimiter);
            }
        }

        return $this->normalizeRows($rows);
    }

    private function parsePdf(string $path): array
    {
        $text  = (new PdfParser())->parseFile($path)->getText();
        $lines = explode("\n", $text);
        $rows  = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $rows[] = str_contains($line, "\t")
                ? explode("\t", $line)
                : preg_split('/\s{2,}/', $line);
        }

        return $this->normalizeRows($rows);
    }

    // ── Row normalisation ──────────────────────────────────────────────────

    private function normalizeRows(array $raw): array
    {
        $headers = null;
        $rows    = [];

        foreach ($raw as $row) {
            if (empty(array_filter(array_map('trim', $row)))) continue;

            if ($headers === null) {
                $lower = array_map(fn($h) => mb_strtolower(trim((string) $h)), $row);

                // The real header row contains 'first name'.
                // Everything before it (Google Form timestamp, consent, etc.) is skipped.
                if (in_array('first name', $lower, true)) {
                    $headers = $lower;
                }
                continue;
            }

            $mapped = [];
            foreach ($headers as $i => $header) {
                if ($this->shouldSkip($header)) continue;

                $field = $this->resolveField($header);
                if ($field && isset($row[$i]) && trim((string) $row[$i]) !== '') {
                    $mapped[$field] = trim((string) $row[$i]);
                }
            }

            if (!empty($mapped)) {
                $rows[] = $mapped;
            }
        }

        return $rows;
    }

    private function shouldSkip(string $header): bool
    {
        foreach (self::SKIP_COLUMNS as $skip) {
            if (str_starts_with($header, $skip)) return true;
        }
        return false;
    }

    private function resolveField(string $header): ?string
    {
        $header = mb_strtolower(trim($header));

        if (isset(self::COLUMN_MAP[$header])) {
            return self::COLUMN_MAP[$header];
        }

        foreach (self::COLUMN_MAP as $pattern => $field) {
            if (str_contains($header, $pattern) || str_contains($pattern, $header)) {
                return $field;
            }
        }

        return null;
    }

    // ── Row processing ─────────────────────────────────────────────────────

    private function processRows(array $rows): array
    {
        $imported   = 0;
        $skipped    = 0;
        $emailsSent = 0;
        $errors     = [];

        foreach ($rows as $index => $row) {
            try {
                [$result, $sentEmail] = $this->importRow($row, $index + 1);

                if ($result) {
                    $imported++;
                    if ($sentEmail) $emailsSent++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $name     = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))
                    ?: "Row " . ($index + 1);
                $errors[] = "{$name}: " . $e->getMessage();
            }
        }

        return [$imported, $skipped, $emailsSent, $errors];
    }

    /**
     * @return array{bool, bool}  [wasImported, emailWasSent]
     */
    private function importRow(array $row, int $line): array
    {
        if (empty($row['last_name']) && empty($row['id_number'])) {
            throw new \Exception("Missing last name and ID number on line {$line}");
        }

        $idNumber = $this->sanitizeId($row['id_number'] ?? null)
            ?: ('IMP-' . Str::upper(Str::random(8)));

        // Skip exact ID number duplicates silently
        if (Employee::where('id_number', $idNumber)->exists()) {
            return [false, false];
        }

        // Same first + last name with a different ID → flag for human review
        $firstName = Str::title(trim($row['first_name'] ?? ''));
        $lastName  = Str::title(trim($row['last_name']  ?? ''));

        if ($firstName && $lastName) {
            $nameExists = Employee::where('first_name', $firstName)
                ->where('last_name', $lastName)
                ->exists();

            if ($nameExists) {
                throw new \Exception(
                    "Possible duplicate: {$firstName} {$lastName} already exists with a different ID. " .
                    "Verify and merge manually if needed."
                );
            }
        }

        $emailSent = false;

        DB::transaction(function () use ($row, $idNumber, $firstName, $lastName, &$emailSent) {
            $middleName = Str::title(trim($row['middle_name'] ?? ''));
            $fullName   = trim("{$firstName} {$middleName} {$lastName}") ?: 'Unknown';

            $email = filter_var($row['email_address'] ?? '', FILTER_VALIDATE_EMAIL)
                ? strtolower(trim($row['email_address']))
                : null;

            $role = $this->resolveRole($row['position_title'] ?? null);

            // Create or reuse User account.
            // firstOrCreate: if the email already exists we reuse the account as-is
            // (role is NOT overwritten to avoid silently changing existing access).
            $userCreated = false;

            if ($email) {
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name'     => $fullName,
                        'password' => Hash::make(self::DEFAULT_PASSWORD),
                        'role'     => $role,
                    ]
                );
                $userCreated = $user->wasRecentlyCreated;
            } else {
                // No email — generate a placeholder so the unique constraint is satisfied
                $user = User::create([
                    'name'     => $fullName,
                    'email'    => 'import.' . Str::lower(Str::random(8)) . '@placeholder.local',
                    'password' => Hash::make(self::DEFAULT_PASSWORD),
                    'role'     => $role,
                ]);
                $userCreated = true;
            }

            Employee::create(array_filter([
                'user_id'              => $user->id,
                'first_name'           => $firstName  ?: null,
                'last_name'            => $lastName   ?: null,
                'middle_name'          => $middleName ?: null,
                'email_address'        => $email,
                'date_of_birth'        => $this->parseDate($row['date_of_birth'] ?? null),
                'blood_type'           => $row['blood_type']       ?? null,
                'height_cm'            => $this->parseDecimal($row['height_cm']  ?? null),
                'weight_kg'            => $this->parseDecimal($row['weight_kg']  ?? null),
                'home_address'         => $row['home_address']     ?? null,
                'phone_number'         => $this->sanitizePhone($row['phone_number'] ?? null),
                'emergency_contact_no' => $this->sanitizePhone($row['emergency_contact_no'] ?? null),
                'id_number'            => $idNumber,
                'tin'                  => $this->sanitizeId($row['tin']          ?? null),
                'pagibig_no'           => $this->sanitizeId($row['pagibig_no']   ?? null),
                'philhealth'           => $this->sanitizeId($row['philhealth']   ?? null),
                'gsis_no'              => $this->sanitizeId($row['gsis_no']      ?? null),
                'hmo_organization'     => $row['hmo_organization'] ?? null,
                'hmo_number'           => $this->sanitizeId($row['hmo_number']   ?? null),
                'eligibility'          => $row['eligibility']      ?? null,
                'position_title'       => $row['position_title']   ?? null,
                'licence_number'       => $this->sanitizeId($row['licence_number'] ?? null),
            ], fn($v) => $v !== null && $v !== ''));

            // Send credentials only to newly created users with a real email
            if ($userCreated && $email) {
                try {
                    Mail::to($email)->send(new UserCredentialsMail($user, self::DEFAULT_PASSWORD));
                    $emailSent = true;
                } catch (\Exception $e) {
                    // Non-fatal: log it but don't roll back the import
                    Log::warning("Import: failed to send credentials to {$email} — " . $e->getMessage());
                }
            }
        });

        return [true, $emailSent];
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function sanitizeId(?string $value): ?string
    {
        if (!$value) return null;
        $clean = trim($value, " \t\n\r()");
        // Discard Excel scientific notation artifacts like "2.006577718E9"
        if (preg_match('/^\d+\.\d+E\d+$/i', $clean)) return null;
        return $clean ?: null;
    }

    private function sanitizePhone(?string $value): ?string
    {
        if (!$value) return null;
        // Convert Excel scientific notation to integer string
        if (preg_match('/^\d+\.\d+E\d+$/i', $value)) {
            $value = number_format((float) $value, 0, '.', '');
        }
        $value = preg_replace('/[^\d+]/', '', $value);
        return strlen($value) >= 7 ? $value : null;
    }

    private function parseDate(?string $value): ?string
    {
        if (!$value || in_array(strtolower(trim($value)), ['', 'n/a', 'none', 'null'])) {
            return null;
        }
        // Excel serial date (e.g. "34762.0")
        if (is_numeric($value)) {
            return date('Y-m-d', (int)(((float) $value - 25569) * 86400));
        }
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    private function parseDecimal(?string $value): ?float
    {
        if (!$value || in_array(strtolower(trim($value)), ['', 'n/a', 'none', 'null'])) {
            return null;
        }
        $clean = preg_replace('/[^\d.]/', '', $value);
        return is_numeric($clean) ? (float) $clean : null;
    }

    private function resolveRole(?string $positionTitle): string
    {
        if (!$positionTitle) return 'staff';

        $lower = mb_strtolower(trim($positionTitle));

        foreach (self::POSITION_ROLE_MAP as $keyword => $role) {
            if (str_contains($lower, $keyword)) {
                return $role;
            }
        }

        return 'staff';
    }
}