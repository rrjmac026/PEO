<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Import Work Requests') }}
            </h2>
            <a href="{{ route('admin.work-requests.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3 text-lg"></i>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- Warning Message (partial import with row failures) --}}
            @if(session('warning'))
                <div class="mb-6 bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-600 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded relative" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-3 text-lg"></i>
                        <span class="block sm:inline">{{ session('warning') }}</span>
                    </div>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded relative" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- Import Instructions Card --}}
            <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-6 mb-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div class="ml-4 w-full">
                        <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-2">
                            {{ __('Import Instructions') }}
                        </h3>
                        <div class="text-blue-800 dark:text-blue-200 text-sm">
                            <p class="mb-3">{{ __('Prepare your CSV file with the following columns. Required fields are marked with *.') }}</p>

                            {{-- Project Information --}}
                            <p class="font-semibold mt-3 mb-1">{{ __('Project Information') }}</p>
                            <ul class="list-disc list-inside space-y-1 mb-2">
                                <li><strong>name_of_project</strong> * – {{ __('Name of the project') }}</li>
                                <li><strong>project_location</strong> * – {{ __('Location of the project') }}</li>
                                <li><strong>for_office</strong> – {{ __('Office the request is addressed to') }}</li>
                                <li><strong>from_requester</strong> – {{ __('Office or person sending the request') }}</li>
                            </ul>

                            {{-- Request Details --}}
                            <p class="font-semibold mt-3 mb-1">{{ __('Request Details') }}</p>
                            <ul class="list-disc list-inside space-y-1 mb-2">
                                <li><strong>requested_by</strong> * – {{ __('Name of the requester') }}</li>
                                <li><strong>requested_work_start_date</strong> * – {{ __('Requested work start date (YYYY-MM-DD)') }}</li>
                                <li><strong>requested_work_start_time</strong> – {{ __('Requested work start time') }}</li>
                                <li><strong>description_of_work_requested</strong> * – {{ __('Full description of the work') }}</li>
                            </ul>

                            {{-- Pay Item Details --}}
                            <p class="font-semibold mt-3 mb-1">{{ __('Pay Item Details') }}</p>
                            <ul class="list-disc list-inside space-y-1 mb-2">
                                <li><strong>item_no</strong> – {{ __('Pay item number') }}</li>
                                <li><strong>description</strong> – {{ __('Pay item description') }}</li>
                                <li><strong>equipment_to_be_used</strong> – {{ __('Equipment to be used') }}</li>
                                <li><strong>estimated_quantity</strong> – {{ __('Estimated quantity (numeric)') }}</li>
                                <li><strong>unit</strong> – {{ __('Unit of measurement') }}</li>
                            </ul>

                            {{-- Submission --}}
                            <p class="font-semibold mt-3 mb-1">{{ __('Submission') }}</p>
                            <ul class="list-disc list-inside space-y-1 mb-2">
                                <li><strong>submitted_by</strong> – {{ __('Submitted by') }}</li>
                                <li><strong>submitted_date</strong> – {{ __('Submission date (YYYY-MM-DD)') }}</li>
                                <li><strong>contractor_name</strong> – {{ __('Contractor name') }}</li>
                            </ul>

                            {{-- Inspection --}}
                            <p class="font-semibold mt-3 mb-1">{{ __('Inspection') }}</p>
                            <ul class="list-disc list-inside space-y-1 mb-2">
                                <li><strong>inspected_by_site_inspector</strong> – {{ __('Site inspector name') }}</li>
                                <li><strong>site_inspector_signature</strong> – {{ __('Site inspector signature') }}</li>
                                <li><strong>surveyor_name</strong> – {{ __('Surveyor name') }}</li>
                                <li><strong>surveyor_signature</strong> – {{ __('Surveyor signature') }}</li>
                                <li><strong>resident_engineer_name</strong> – {{ __('Resident engineer name') }}</li>
                                <li><strong>resident_engineer_signature</strong> – {{ __('Resident engineer signature') }}</li>
                            </ul>

                            {{-- Findings --}}
                            <p class="font-semibold mt-3 mb-1">{{ __('Findings & Recommendations') }}</p>
                            <ul class="list-disc list-inside space-y-1 mb-2">
                                <li><strong>findings_comments</strong> – {{ __('Findings or comments') }}</li>
                                <li><strong>recommendation</strong> – {{ __('Recommendation') }}</li>
                                <li><strong>recommended_action</strong> – {{ __('Recommended action') }}</li>
                            </ul>

                            {{-- Review and Approval --}}
                            <p class="font-semibold mt-3 mb-1">{{ __('Review & Approval') }}</p>
                            <ul class="list-disc list-inside space-y-1 mb-2">
                                <li><strong>checked_by_mtqa</strong> – {{ __('MTQA checker name') }}</li>
                                <li><strong>mtqa_signature</strong> – {{ __('MTQA signature') }}</li>
                                <li><strong>reviewed_by</strong> – {{ __('Reviewer name') }}</li>
                                <li><strong>reviewer_designation</strong> – {{ __('Reviewer designation') }}</li>
                                <li><strong>recommending_approval_by</strong> – {{ __('Name recommending approval') }}</li>
                                <li><strong>recommending_approval_designation</strong> – {{ __('Designation of recommending officer') }}</li>
                                <li><strong>recommending_approval_signature</strong> – {{ __('Recommending approval signature') }}</li>
                                <li><strong>approved_by</strong> – {{ __('Approving authority name') }}</li>
                                <li><strong>approved_by_designation</strong> – {{ __('Approving authority designation') }}</li>
                                <li><strong>approved_signature</strong> – {{ __('Approving authority signature') }}</li>
                            </ul>

                            {{-- Acceptance --}}
                            <p class="font-semibold mt-3 mb-1">{{ __('Acceptance') }}</p>
                            <ul class="list-disc list-inside space-y-1 mb-2">
                                <li><strong>accepted_by_contractor</strong> – {{ __('Contractor who accepted') }}</li>
                                <li><strong>accepted_date</strong> – {{ __('Acceptance date (YYYY-MM-DD)') }}</li>
                                <li><strong>accepted_time</strong> – {{ __('Acceptance time') }}</li>
                                <li><strong>received_by</strong> – {{ __('Received by') }}</li>
                                <li><strong>received_date</strong> – {{ __('Received date (YYYY-MM-DD)') }}</li>
                                <li><strong>received_time</strong> – {{ __('Received time') }}</li>
                            </ul>

                            {{-- Status --}}
                            <p class="font-semibold mt-3 mb-1">{{ __('Status & Notes') }}</p>
                            <ul class="list-disc list-inside space-y-1 mb-4">
                                <li><strong>status</strong> – {{ __('One of: draft, submitted, inspected, reviewed, approved, accepted, rejected. Defaults to') }} <em>draft</em> {{ __('if omitted.') }}</li>
                                <li><strong>notes</strong> – {{ __('Additional notes') }}</li>
                            </ul>

                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                {{ __('Note: Rows with validation errors will be skipped and reported. Max file size: 10MB.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Import Form Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.work-requests.import.csv') }}" 
                          method="POST" 
                          enctype="multipart/form-data"
                          id="import-form">
                        @csrf

                        {{-- File Input --}}
                        <div class="mb-6">
                            <label for="csv_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Select CSV File') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-orange-400 dark:hover:border-orange-500 transition duration-150 @error('csv_file') border-red-500 dark:border-red-500 @enderror"
                                 id="file-drop-zone">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-8l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 20h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                        <label for="csv_file" class="relative cursor-pointer rounded-md font-medium text-orange-600 dark:text-orange-400 hover:text-orange-500 dark:hover:text-orange-300">
                                            <span>{{ __('Click to upload') }}</span>
                                            <input id="csv_file" 
                                                   name="csv_file" 
                                                   type="file" 
                                                   class="sr-only" 
                                                   accept=".csv,.txt"
                                                   required
                                                   onchange="updateFileName(this)">
                                        </label>
                                        <p class="pl-1">{{ __('or drag and drop') }}</p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('CSV or TXT file up to 10MB') }}
                                    </p>
                                    <p id="file-name" class="text-sm font-semibold text-orange-600 dark:text-orange-400 mt-2"></p>
                                </div>
                            </div>
                            @error('csv_file')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="flex gap-3 justify-end">
                            <a href="{{ route('admin.work-requests.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-times mr-2"></i>
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:bg-green-700 dark:focus:bg-green-600 active:bg-green-900 dark:active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                    id="submit-btn">
                                <i class="fas fa-upload mr-2"></i>
                                {{ __('Import Work Requests') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sample CSV Preview --}}
            <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">
                    {{ __('Expected CSV Format (required columns shown)') }}
                </h3>
                <div class="bg-white dark:bg-gray-800 rounded p-4 overflow-x-auto">
                    <pre class="text-xs text-gray-600 dark:text-gray-300"><code>name_of_project,project_location,requested_by,requested_work_start_date,description_of_work_requested,for_office,from_requester,requested_work_start_time,item_no,description,equipment_to_be_used,estimated_quantity,unit,submitted_by,submitted_date,contractor_name,inspected_by_site_inspector,site_inspector_signature,surveyor_name,surveyor_signature,resident_engineer_name,resident_engineer_signature,findings_comments,recommendation,recommended_action,checked_by_mtqa,mtqa_signature,reviewed_by,reviewer_designation,recommending_approval_by,recommending_approval_designation,recommending_approval_signature,approved_by,approved_by_designation,approved_signature,accepted_by_contractor,accepted_date,accepted_time,received_by,received_date,received_time,status,notes
Road Widening Project,Brgy. San Jose,Juan dela Cruz,2025-01-15,Clearing and grubbing of road right-of-way,Provincial Engineers Office,DPWH Region X,07:00:00,Item 100,Clearing and Grubbing,Bulldozer,500.00,sq.m.,Maria Santos,2025-01-10,ABC Construction,Pedro Reyes,,Carlos Gomez,,Engr. Ana Lim,,No issues found,Proceed with clearing,Mobilize equipment,Engr. Ben Cruz,,Engr. Rosa Tan,District Engineer,Engr. Luis Vera,Provincial Engineer,,Gov. Ramon Dela Cruz,Provincial Governor,,ABC Construction Inc.,2025-01-16,08:00:00,Records Office,2025-01-16,09:00:00,approved,Priority project
Bridge Rehabilitation,Brgy. Poblacion,Jose Rizal,2025-02-01,Structural repair of bridge deck,Provincial Engineers Office,LGU Misamis,08:00:00,Item 302,Concrete Works,Transit Mixer,120.00,cu.m.,Ana Reyes,2025-01-25,XYZ Builders,,,,,,,,Pending inspection,,,,,,,,,,,,,,,,,draft,For review</code></pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Drag and drop functionality
        const fileDropZone = document.getElementById('file-drop-zone');
        const fileInput = document.getElementById('csv_file');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileDropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileDropZone.addEventListener(eventName, () => {
                fileDropZone.classList.add('border-orange-400', 'dark:border-orange-500', 'bg-orange-50', 'dark:bg-orange-900/20');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileDropZone.addEventListener(eventName, () => {
                fileDropZone.classList.remove('border-orange-400', 'dark:border-orange-500', 'bg-orange-50', 'dark:bg-orange-900/20');
            });
        });

        fileDropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            updateFileName(fileInput);
        });

        // Update file name display
        function updateFileName(input) {
            const fileName = document.getElementById('file-name');
            if (input.files && input.files[0]) {
                fileName.textContent = '✓ ' + input.files[0].name + ' (' + formatFileSize(input.files[0].size) + ')';
            } else {
                fileName.textContent = '';
            }
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Form submission
        document.getElementById('import-form').addEventListener('submit', function(e) {
            if (!fileInput.files || fileInput.files.length === 0) {
                e.preventDefault();
                alert('Please select a CSV file to import.');
            }
        });
    </script>
</x-app-layout>