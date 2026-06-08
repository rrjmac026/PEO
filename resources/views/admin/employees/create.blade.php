@extends('layouts.app')

@section('title', 'Create Employee')

@section('content')
    <!-- Header -->
    <div class="mb-8 max-w-3xl mx-auto">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.employees.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New Employee</h1>
        </div>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Add a new employee to the system</p>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="mb-6 max-w-3xl mx-auto p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <h3 class="text-red-800 dark:text-red-400 font-semibold mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>Validation Errors
            </h3>
            <ul class="list-disc list-inside text-red-700 dark:text-red-300 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container -->
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('admin.employees.store') }}" method="POST">
            @csrf

            {{-- ── AUTO USER NOTE ──────────────────────────────────────────── --}}
            <div class="mb-6 max-w-3xl mx-auto p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg text-sm text-blue-700 dark:text-blue-300">
                <i class="fas fa-info-circle mr-2"></i>
                A <strong>User account</strong> will be created automatically using the employee's name and email address.
                The default password is <code class="font-mono bg-blue-100 dark:bg-blue-800 px-1 rounded">password</code> — remind them to change it on first login.
            </div>

            {{-- ── PERSONAL INFORMATION ──────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-id-card mr-2 text-indigo-500"></i>Personal Information
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" id="first_name" required
                               value="{{ old('first_name') }}" placeholder="e.g., Juan"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('first_name') border-red-500 @enderror">
                        @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" id="last_name" required
                               value="{{ old('last_name') }}" placeholder="e.g., Dela Cruz"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('last_name') border-red-500 @enderror">
                        @error('last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Middle Name
                        </label>
                        <input type="text" name="middle_name" id="middle_name"
                               value="{{ old('middle_name') }}" placeholder="e.g., Santos"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('middle_name') border-red-500 @enderror">
                        @error('middle_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email Address
                        </label>
                        <input type="email" name="email_address" id="email_address"
                               value="{{ old('email_address') }}" placeholder="e.g., juan@example.com"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('email_address') border-red-500 @enderror">
                        @error('email_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Date of Birth
                        </label>
                        <input type="date" name="date_of_birth" id="date_of_birth"
                               value="{{ old('date_of_birth') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Blood Type -->
                    <div>
                        <label for="blood_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Blood Type
                        </label>
                        <select name="blood_type" id="blood_type"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('blood_type') border-red-500 @enderror">
                            <option value="">— Select —</option>
                            @foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                                <option value="{{ $bt }}" @selected(old('blood_type') == $bt)>{{ $bt }}</option>
                            @endforeach
                        </select>
                        @error('blood_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Height -->
                    <div>
                        <label for="height_cm" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Height (cm)
                        </label>
                        <input type="number" step="0.01" name="height_cm" id="height_cm"
                               value="{{ old('height_cm') }}" placeholder="e.g., 165.00"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('height_cm') border-red-500 @enderror">
                        @error('height_cm')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Weight -->
                    <div>
                        <label for="weight_kg" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Weight (kg)
                        </label>
                        <input type="number" step="0.01" name="weight_kg" id="weight_kg"
                               value="{{ old('weight_kg') }}" placeholder="e.g., 60.00"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('weight_kg') border-red-500 @enderror">
                        @error('weight_kg')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Home Address (full width) -->
                    <div class="md:col-span-2">
                        <label for="home_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Home Address
                        </label>
                        <textarea name="home_address" id="home_address" rows="2"
                                  placeholder="St., Brgy, Mun./City, Prov."
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('home_address') border-red-500 @enderror">{{ old('home_address') }}</textarea>
                        @error('home_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Phone Number
                        </label>
                        <input type="text" name="phone_number" id="phone_number"
                               value="{{ old('phone_number') }}" placeholder="09xxxxxxxxx"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('phone_number') border-red-500 @enderror">
                        @error('phone_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Emergency Contact -->
                    <div>
                        <label for="emergency_contact_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Emergency Contact No.
                        </label>
                        <input type="text" name="emergency_contact_no" id="emergency_contact_no"
                               value="{{ old('emergency_contact_no') }}" placeholder="09xxxxxxxxx"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('emergency_contact_no') border-red-500 @enderror">
                        @error('emergency_contact_no')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>

            {{-- ── GOVERNMENT IDs ────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-landmark mr-2 text-indigo-500"></i>Government IDs
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- ID Number -->
                    <div>
                        <label for="id_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            ID Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="id_number" id="id_number" required
                               value="{{ old('id_number') }}" placeholder="PDS-xxxxxxxxx"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('id_number') border-red-500 @enderror">
                        @error('id_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- TIN -->
                    <div>
                        <label for="tin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            TIN
                        </label>
                        <input type="text" name="tin" id="tin"
                               value="{{ old('tin') }}" placeholder="xxx-xxx-xxx"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('tin') border-red-500 @enderror">
                        @error('tin')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Pag-IBIG -->
                    <div>
                        <label for="pagibig_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Pag-IBIG No.
                        </label>
                        <input type="text" name="pagibig_no" id="pagibig_no"
                               value="{{ old('pagibig_no') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('pagibig_no') border-red-500 @enderror">
                        @error('pagibig_no')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- PhilHealth -->
                    <div>
                        <label for="philhealth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            PhilHealth No.
                        </label>
                        <input type="text" name="philhealth" id="philhealth"
                               value="{{ old('philhealth') }}" placeholder="15-000000000-6"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('philhealth') border-red-500 @enderror">
                        @error('philhealth')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- GSIS -->
                    <div>
                        <label for="gsis_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            GSIS No.
                        </label>
                        <input type="text" name="gsis_no" id="gsis_no"
                               value="{{ old('gsis_no') }}" placeholder="10-digit number"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('gsis_no') border-red-500 @enderror">
                        @error('gsis_no')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>

            {{-- ── HMO ──────────────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-hospital mr-2 text-indigo-500"></i>HMO
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- HMO Organization -->
                    <div>
                        <label for="hmo_organization" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            HMO Organization
                        </label>
                        <input type="text" name="hmo_organization" id="hmo_organization"
                               value="{{ old('hmo_organization') }}" placeholder="e.g., 1 Health Coop - FICCO"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('hmo_organization') border-red-500 @enderror">
                        @error('hmo_organization')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- HMO Number -->
                    <div>
                        <label for="hmo_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            HMO #
                        </label>
                        <input type="text" name="hmo_number" id="hmo_number"
                               value="{{ old('hmo_number') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('hmo_number') border-red-500 @enderror">
                        @error('hmo_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>

            {{-- ── PROFESSIONAL ─────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-briefcase mr-2 text-indigo-500"></i>Professional Details
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- Position Title -->
                    <div class="md:col-span-2">
                        <label for="position_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Position Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="position_title" id="position_title" required
                               value="{{ old('position_title') }}"
                               placeholder="e.g., Administrative Aide VI (Clerk III), Architect III"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('position_title') border-red-500 @enderror">
                        @error('position_title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Eligibility -->
                    <div>
                        <label for="eligibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Eligibility
                        </label>
                        <input type="text" name="eligibility" id="eligibility"
                               value="{{ old('eligibility') }}" placeholder="e.g., CSC, TESDA NC II, PRC"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('eligibility') border-red-500 @enderror">
                        @error('eligibility')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Licence Number -->
                    <div>
                        <label for="licence_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Licence Number
                        </label>
                        <input type="text" name="licence_number" id="licence_number"
                               value="{{ old('licence_number') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('licence_number') border-red-500 @enderror">
                        @error('licence_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Department
                        </label>
                        <input type="text" name="department" id="department"
                               value="{{ old('department') }}" placeholder="e.g., Engineering"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('department') border-red-500 @enderror">
                        @error('department')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Office -->
                    <div>
                        <label for="office" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Office
                        </label>
                        <input type="text" name="office" id="office"
                               value="{{ old('office') }}" placeholder="e.g., District Engineering Office"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('office') border-red-500 @enderror">
                        @error('office')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-3">
                <a href="{{ route('admin.employees.index') }}"
                   class="inline-flex items-center px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition">
                    <i class="fas fa-save mr-2"></i>Create Employee
                </button>
            </div>

        </form>
    </div>
@endsection