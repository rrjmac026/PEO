@extends('layouts.app')

@section('title', 'Import Employees')

@push('styles')
<style>
    :root {
        --im-surface:   #ffffff;
        --im-surface2:  #f8fafc;
        --im-border:    #e2e8f0;
        --im-text:      #0f172a;
        --im-text-sec:  #334155;
        --im-muted:     #64748b;
        --im-shadow:    0 1px 3px rgba(0,0,0,0.08);
    }
    .dark {
        --im-surface:   #1a1f2e;
        --im-surface2:  #1e2335;
        --im-border:    #2a3050;
        --im-text:      #e8eaf6;
        --im-text-sec:  #c5cae9;
        --im-muted:     #7c85a8;
        --im-shadow:    0 1px 4px rgba(0,0,0,0.35);
    }

    .im-panel {
        background: var(--im-surface);
        border: 1px solid var(--im-border);
        border-radius: 12px;
        box-shadow: var(--im-shadow);
        overflow: hidden;
    }

    /* Drop zone */
    .im-dropzone {
        border: 2px dashed var(--im-border);
        border-radius: 10px;
        padding: 40px 24px;
        text-align: center;
        cursor: pointer;
        transition: all .2s;
        background: var(--im-surface2);
        position: relative;
    }
    .im-dropzone:hover,
    .im-dropzone.drag-over { border-color: #6366f1; background: rgba(99,102,241,.05); }
    .im-dropzone input[type="file"] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .im-dropzone-icon { font-size: 40px; color: #6366f1; margin-bottom: 12px; display: block; }
    .im-dropzone-title { font-size: 16px; font-weight: 700; color: var(--im-text); margin-bottom: 6px; }
    .im-dropzone-sub   { font-size: 13px; color: var(--im-muted); }
    .im-file-name      { font-size: 13px; color: #4f46e5; font-weight: 600; margin-top: 10px; display: none; }

    /* Format badges */
    .im-formats { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; margin-top: 14px; }
    .im-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;
        border: 1px solid;
    }
    .im-badge.xlsx { color: #15803d; border-color: #86efac; background: #f0fdf4; }
    .im-badge.csv  { color: #1d4ed8; border-color: #93c5fd; background: #eff6ff; }
    .im-badge.pdf  { color: #b45309; border-color: #fde68a; background: #fffbeb; }
    .dark .im-badge.xlsx { color: #4ade80; border-color: rgba(74,222,128,.3); background: rgba(74,222,128,.1); }
    .dark .im-badge.csv  { color: #60a5fa; border-color: rgba(96,165,250,.3); background: rgba(96,165,250,.1); }
    .dark .im-badge.pdf  { color: #fbbf24; border-color: rgba(251,191,36,.3); background: rgba(251,191,36,.1); }

    /* Column map table */
    .im-map-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .im-map-table thead th {
        padding: 8px 14px; text-align: left; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .5px; color: var(--im-muted);
        background: var(--im-surface2); border-bottom: 1px solid var(--im-border);
    }
    .im-map-table tbody tr { border-bottom: 1px solid var(--im-border); }
    .im-map-table tbody tr:last-child { border-bottom: none; }
    .im-map-table td { padding: 8px 14px; color: var(--im-text-sec); }
    .im-map-table td:first-child { font-family: monospace; color: var(--im-muted); }
    .im-map-table td:last-child  { font-weight: 600; color: var(--im-text); }

    /* Errors list */
    .im-errors {
        background: #fff1f2; border: 1px solid #fca5a5; border-radius: 10px;
        padding: 16px 20px; font-size: 13px; color: #991b1b;
    }
    .dark .im-errors { background: rgba(220,38,38,.10); border-color: rgba(248,113,113,.3); color: #fca5a5; }
    .im-errors ul { margin-top: 8px; padding-left: 18px; }
    .im-errors li { margin-bottom: 4px; }

    /* Buttons */
    .im-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 22px; border-radius: 8px;
        font-size: 14px; font-weight: 600;
        border: 1px solid; cursor: pointer;
        transition: all .15s; text-decoration: none;
    }
    .im-btn-primary { background: #4f46e5; border-color: #4f46e5; color: #fff; }
    .im-btn-primary:hover { background: #4338ca; }
    .im-btn-secondary { background: var(--im-surface2); border-color: var(--im-border); color: var(--im-text-sec); }
    .im-btn-secondary:hover { border-color: var(--im-muted); }

    /* Progress bar */
    .im-progress { display: none; margin-top: 16px; }
    .im-progress-bar {
        height: 6px; border-radius: 3px; background: #e2e8f0; overflow: hidden;
    }
    .im-progress-fill {
        height: 100%; border-radius: 3px; background: #6366f1;
        animation: indeterminate 1.5s infinite ease-in-out;
        width: 40%;
    }
    @keyframes indeterminate {
        0%   { transform: translateX(-100%); }
        100% { transform: translateX(350%); }
    }
    .im-progress-label { font-size: 13px; color: var(--im-muted); margin-top: 6px; text-align: center; }
</style>
@endpush

@section('content')

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.employees.index') }}"
               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Import Employees</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">
                    Upload an Excel, CSV, or PDF file to bulk-import personnel records.
                </p>
            </div>
        </div>
    </div>

    <!-- Import errors from previous attempt -->
    @if (session('import_errors') && count(session('import_errors')))
        <div class="im-errors mb-6">
            <strong><i class="fas fa-exclamation-triangle mr-2"></i>Some rows could not be imported:</strong>
            <ul>
                @foreach (session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="im-errors mb-6">
            <strong><i class="fas fa-exclamation-circle mr-2"></i>Validation error:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Upload form -->
        <div class="lg:col-span-2 space-y-6">
            <div class="im-panel p-6 md:p-8">
                <h2 class="text-lg font-bold mb-1" style="color: var(--im-text);">Upload File</h2>
                <p class="text-sm mb-6" style="color: var(--im-muted);">
                    Supported formats: Excel (.xlsx, .xls), CSV (.csv), PDF (.pdf) — max 10 MB.
                </p>

                <form action="{{ route('admin.employees.import') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      id="importForm">
                    @csrf

                    <!-- Drop zone -->
                    <div class="im-dropzone" id="dropzone">
                        <input type="file" name="file" id="fileInput"
                               accept=".xlsx,.xls,.csv,.pdf"
                               onchange="handleFileSelect(this)">
                        <span class="im-dropzone-icon"><i class="fas fa-file-upload"></i></span>
                        <div class="im-dropzone-title">Drop your file here</div>
                        <div class="im-dropzone-sub">or click to browse</div>
                        <div class="im-formats">
                            <span class="im-badge xlsx"><i class="fas fa-file-excel"></i> XLSX / XLS</span>
                            <span class="im-badge csv"><i class="fas fa-file-csv"></i> CSV</span>
                            <span class="im-badge pdf"><i class="fas fa-file-pdf"></i> PDF</span>
                        </div>
                        <div class="im-file-name" id="fileName"></div>
                    </div>

                    <!-- Progress -->
                    <div class="im-progress" id="progress">
                        <div class="im-progress-bar"><div class="im-progress-fill"></div></div>
                        <div class="im-progress-label">Importing, please wait…</div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 mt-6">
                        <button type="submit" id="submitBtn"
                                class="im-btn im-btn-primary" disabled>
                            <i class="fas fa-file-import"></i> Start Import
                        </button>
                        <a href="{{ route('admin.employees.index') }}" class="im-btn im-btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Notes -->
            <div class="im-panel p-6">
                <h3 class="font-bold mb-3 flex items-center gap-2" style="color: var(--im-text);">
                    <i class="fas fa-info-circle text-indigo-500"></i> Import Notes
                </h3>
                <ul class="text-sm space-y-2" style="color: var(--im-muted);">
                    <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Rows with a duplicate <strong>ID Number (PDS)</strong> are automatically skipped.</li>
                    <li><i class="fas fa-check-circle text-green-500 mr-2"></i>A User account is created for each new employee. If the email already exists, the existing account is reused.</li>
                    <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Dates in Excel serial format are converted automatically.</li>
                    <li><i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>PDF imports work best when the source is a spreadsheet exported to PDF; scanned PDFs may not parse correctly.</li>
                    <li><i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>The <strong>Department</strong> column is optional — you can assign it manually after import.</li>
                </ul>
            </div>
        </div>

        <!-- Column mapping reference -->
        <div>
            <div class="im-panel overflow-hidden">
                <div class="p-5 border-b" style="border-color: var(--im-border);">
                    <h3 class="font-bold" style="color: var(--im-text);">
                        <i class="fas fa-table mr-2 text-indigo-500"></i>Expected Columns
                    </h3>
                    <p class="text-xs mt-1" style="color: var(--im-muted);">Column headers in your file (case-insensitive)</p>
                </div>
                <div class="overflow-y-auto" style="max-height: 480px;">
                    <table class="im-map-table">
                        <thead>
                            <tr>
                                <th>File Column</th>
                                <th>Maps to</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>First Name</td><td>first_name</td></tr>
                            <tr><td>Last Name</td><td>last_name</td></tr>
                            <tr><td>Middle Name</td><td>middle_name</td></tr>
                            <tr><td>Email Address</td><td>email</td></tr>
                            <tr><td>Date of Birth</td><td>date_of_birth</td></tr>
                            <tr><td>Blood Type</td><td>blood_type</td></tr>
                            <tr><td>Height (cm)</td><td>height_cm</td></tr>
                            <tr><td>Weight (kg)</td><td>weight_kg</td></tr>
                            <tr><td>Home Address</td><td>home_address</td></tr>
                            <tr><td>Phone Number</td><td>phone</td></tr>
                            <tr><td>Emergency Contact No.</td><td>emergency_contact_no</td></tr>
                            <tr><td>ID Number (PDS-…)</td><td>employee_number ✦</td></tr>
                            <tr><td>TIN</td><td>tin</td></tr>
                            <tr><td>Pag-IBIG No.</td><td>pagibig_no</td></tr>
                            <tr><td>PhilHealth</td><td>philhealth_no</td></tr>
                            <tr><td>GSIS No.</td><td>gsis_no</td></tr>
                            <tr><td>HMO Organization</td><td>hmo_organization</td></tr>
                            <tr><td>HMO #</td><td>hmo_number</td></tr>
                            <tr><td>Eligibility</td><td>eligibility</td></tr>
                            <tr><td>Position Title</td><td>position</td></tr>
                            <tr><td>Licence Number</td><td>license_number</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t text-xs" style="border-color: var(--im-border); color: var(--im-muted);">
                    ✦ Used as the unique identifier; rows with duplicate IDs are skipped.
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
    const dropzone  = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const fileLabel = document.getElementById('fileName');
    const submitBtn = document.getElementById('submitBtn');
    const form      = document.getElementById('importForm');
    const progress  = document.getElementById('progress');

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            fileLabel.textContent = '📎 ' + input.files[0].name;
            fileLabel.style.display = 'block';
            submitBtn.disabled = false;
        }
    }

    // Drag & drop
    ['dragenter','dragover'].forEach(e =>
        dropzone.addEventListener(e, ev => { ev.preventDefault(); dropzone.classList.add('drag-over'); })
    );
    ['dragleave','drop'].forEach(e =>
        dropzone.addEventListener(e, ev => { ev.preventDefault(); dropzone.classList.remove('drag-over'); })
    );
    dropzone.addEventListener('drop', ev => {
        const file = ev.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            handleFileSelect(fileInput);
        }
    });

    // Show progress on submit
    form.addEventListener('submit', () => {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing…';
        progress.style.display = 'block';
    });
</script>
@endpush