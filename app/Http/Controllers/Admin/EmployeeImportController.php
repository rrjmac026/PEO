<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class EmployeeImportController extends Controller
{
    // Maps lowercase Excel header → Employee fillable field
    private const COLUMN_MAP = [
        'first name'                         => 'first_name',
        'last name'                          => 'last_name',
        'middle name'                        => 'middle_name',
        'email address'                      => 'email_address',
        'date of birth'                      => 'date_of_birth',
        'blood type'                         => 'blood_type',
        'height (cm)'                        => 'height_cm',
        'weight (kg)'                        => 'weight_kg',
        'home address (st., brgy, mun./city, prov.)' => 'home_address',
        'home address'                       => 'home_address',
        'phone number (09xxxxxxxxx)'         => 'phone_number',
        'phone number'                       => 'phone_number',
        'emergency contact no.   (09xxxxxxxxx)' => 'emergency_contact_no',
        'emergency contact no.'              => 'emergency_contact_no',
        'id number (pds-xxxxxxxxx)'          => 'id_number',
        'id number'                          => 'id_number',
        'tin (xxx-xxx-xxx)'                  => 'tin',
        'tin'                                => 'tin',
        'pag-ibig no.'                       => 'pagibig_no',
        'philhealth (15-000000000-6)'        => 'philhealth',
        'philhealth'                         => 'philhealth',
        'gsis no. (10 digit no.)'            => 'gsis_no',
        'gsis no.'                           => 'gsis_no',
        'hmo organization ( ex. 1 health coop - ficco )' => 'hmo_organization',
        'hmo organization'                   => 'hmo_organization',
        'hmo #'                              => 'hmo_number',
        'eligibility (csc, tesda nc ii, prc, others)' => 'eligibility',
        'eligibility'                        => 'eligibility',
        'position title (ex. administrative aide vi (clerk iii), architect iii, mason i (b), etc.)' => 'position_title',
        'position title'                     => 'position_title',
        'licence number'                     => 'licence_number',
        'license number'                     => 'licence_number',
    ];

    // Maps lowercase position title keywords → users.role value
    // Order matters: more specific entries (e.g. 'engineer iv') must come
    // before broader ones (e.g. 'engineer') to prevent wrong matches.
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
    // NOTE: 'email address' is intentionally NOT listed here — it is a real
    // data column we want to import. The Google Form auto-captured submitter
    // email appears *before* the 'first name' header and is already ignored
    // by normalizeRows(), which only starts reading after it finds 'first name'.
    private const SKIP_COLUMNS = [
        'timestamp',
        'in compliance with the data privacy act', // consent column (starts with this)
    ];

    // ── Show import form ───────────────────────────────────────────────────

    public function showImportForm()
    {
        return view('admin.employees.import');
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

            [$imported, $skipped, $errors] = $this->processRows($rows);

            $message = "Import complete: {$imported} imported, {$skipped} skipped (duplicate ID).";
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
                // Everything before this row (including the Google Form
                // auto-captured email column) is safely skipped.
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
        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $index => $row) {
            try {
                $this->importRow($row, $index + 1) ? $imported++ : $skipped++;
            } catch (\Exception $e) {
                $name     = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))
                    ?: "Row " . ($index + 1);
                $errors[] = "{$name}: " . $e->getMessage();
            }
        }

        return [$imported, $skipped, $errors];
    }

    private function importRow(array $row, int $line): bool
    {
        if (empty($row['last_name']) && empty($row['id_number'])) {
            throw new \Exception("Missing last name and ID number on line {$line}");
        }

        $idNumber = $this->sanitizeId($row['id_number'] ?? null)
            ?: ('IMP-' . Str::upper(Str::random(8)));

        // Primary check: skip exact ID number duplicates silently
        if (Employee::where('id_number', $idNumber)->exists()) {
            return false;
        }

        // Secondary check: same first + last name, flag for human review
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

        // Skip duplicates
        if (Employee::where('id_number', $idNumber)->exists()) {
            return false;
        }

        DB::transaction(function () use ($row, $idNumber) {
            $firstName  = Str::title(trim($row['first_name']  ?? ''));
            $middleName = Str::title(trim($row['middle_name'] ?? ''));
            $lastName   = Str::title(trim($row['last_name']   ?? ''));
            $fullName   = trim("{$firstName} {$middleName} {$lastName}") ?: 'Unknown';

            $email = filter_var($row['email_address'] ?? '', FILTER_VALIDATE_EMAIL)
                ? strtolower(trim($row['email_address']))
                : null;

            $role = $this->resolveRole($row['position_title'] ?? null);

            // Create or reuse User account.
            // If the email already exists, we reuse the account as-is (role is
            // not overwritten to avoid silently changing an existing user's access).
            $user = $email
                ? User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name'     => $fullName,
                        'password' => Hash::make(Str::random(16)),
                        'role'     => $role,
                    ]
                )
                : User::create([
                    'name'     => $fullName,
                    'email'    => 'import.' . Str::lower(Str::random(8)) . '@placeholder.local',
                    'password' => Hash::make(Str::random(16)),
                    'role'     => $role,
                ]);

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
        });

        return true;
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

        return 'staff'; // fallback — can log in but no reviewer/contractor access
    }
}