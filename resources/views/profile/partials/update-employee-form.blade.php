<section class="profile-form-group space-y-6">
    <header class="profile-header">
        <h2>{{ __('Employee Information') }}</h2>
        <p>{{ __('Fill in your employee details. This information is used in official documents and requests.') }}</p>
    </header>

    @if(session('status') === 'employee-updated')
        <p x-data="{ show: true }" x-show="show" x-transition
           x-init="setTimeout(() => show = false, 3000)"
           class="text-sm text-green-600 dark:text-green-400 font-medium">
            {{ __('Employee info saved successfully.') }}
        </p>
    @endif

    @php $emp = auth()->user()->employee; @endphp

    <form method="post" action="{{ route('profile.employee.update') }}" class="space-y-6">
        @csrf
        @method('PATCH')

        {{-- ── Personal ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('First Name') }}</label>
                <input name="first_name" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('first_name', $emp?->first_name) }}" placeholder="e.g. Juan" />
                @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Middle Name') }}</label>
                <input name="middle_name" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('middle_name', $emp?->middle_name) }}" placeholder="e.g. Santos" />
                @error('middle_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Last Name') }}</label>
                <input name="last_name" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('last_name', $emp?->last_name) }}" placeholder="e.g. Dela Cruz" />
                @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Date of Birth') }}</label>
                <input name="date_of_birth" type="date" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('date_of_birth', $emp?->date_of_birth?->format('Y-m-d')) }}" />
                @error('date_of_birth') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Blood Type') }}</label>
                <input name="blood_type" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('blood_type', $emp?->blood_type) }}" placeholder="e.g. O+" />
                @error('blood_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Height (cm)') }}</label>
                <input name="height_cm" type="number" step="0.01" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('height_cm', $emp?->height_cm) }}" placeholder="e.g. 165" />
                @error('height_cm') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Weight (kg)') }}</label>
                <input name="weight_kg" type="number" step="0.01" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('weight_kg', $emp?->weight_kg) }}" placeholder="e.g. 60" />
                @error('weight_kg') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Home Address') }}</label>
            <input name="home_address" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                   value="{{ old('home_address', $emp?->home_address) }}" placeholder="St., Brgy, Mun./City, Prov." />
            @error('home_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Phone Number') }}</label>
                <input name="phone_number" type="tel" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('phone_number', $emp?->phone_number) }}" placeholder="09xxxxxxxxx" />
                @error('phone_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Emergency Contact No.') }}</label>
                <input name="emergency_contact_no" type="tel" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('emergency_contact_no', $emp?->emergency_contact_no) }}" placeholder="09xxxxxxxxx" />
                @error('emergency_contact_no') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- ── Professional ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Position Title') }}</label>
                <input name="position_title" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('position_title', $emp?->position_title) }}" placeholder="e.g. Resident Engineer" />
                @error('position_title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Department') }}</label>
                <input name="department" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('department', $emp?->department) }}" placeholder="e.g. Engineering" />
                @error('department') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Office') }}</label>
                <input name="office" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('office', $emp?->office) }}" placeholder="e.g. Provincial Engineering Office" />
                @error('office') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Licence Number') }}</label>
                <input name="licence_number" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('licence_number', $emp?->licence_number) }}" placeholder="e.g. 0066614" />
                @error('licence_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Eligibility') }}</label>
            <input name="eligibility" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                   value="{{ old('eligibility', $emp?->eligibility) }}" placeholder="e.g. Bar and Board Examination Eligibility (RA 1080)" />
            @error('eligibility') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- ── Government IDs ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('ID Number (PDS)') }}</label>
                <input name="id_number" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('id_number', $emp?->id_number) }}" placeholder="PDS-xxxxxxxxx" />
                @error('id_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('TIN') }}</label>
                <input name="tin" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('tin', $emp?->tin) }}" placeholder="xxx-xxx-xxx" />
                @error('tin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('Pag-IBIG No.') }}</label>
                <input name="pagibig_no" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('pagibig_no', $emp?->pagibig_no) }}" />
                @error('pagibig_no') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('PhilHealth') }}</label>
                <input name="philhealth" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('philhealth', $emp?->philhealth) }}" placeholder="15-000000000-6" />
                @error('philhealth') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('GSIS No.') }}</label>
                <input name="gsis_no" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('gsis_no', $emp?->gsis_no) }}" />
                @error('gsis_no') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('HMO Organization') }}</label>
                <input name="hmo_organization" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                       value="{{ old('hmo_organization', $emp?->hmo_organization) }}" placeholder="e.g. 1 Health Coop - Ficco" />
                @error('hmo_organization') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block font-medium text-sm mb-2" style="color:var(--profile-text);">{{ __('HMO Number') }}</label>
            <input name="hmo_number" type="text" class="profile-input mt-1 block w-full px-3 py-2"
                   value="{{ old('hmo_number', $emp?->hmo_number) }}" />
            @error('hmo_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="profile-btn-save">
                {{ __('Save Employee Info') }}
            </button>
        </div>
    </form>
</section>