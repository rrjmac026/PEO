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

    <form method="post" action="{{ route('profile.employee.update') }}" class="space-y-6">
        @csrf
        @method('PATCH')

        {{-- Employee Number --}}
        <div>
            <label for="employee_number" class="block font-medium text-sm mb-2"
                   style="color: var(--profile-text);">
                {{ __('Employee Number') }}
            </label>
            <input id="employee_number" name="employee_number" type="text"
                   class="profile-input mt-1 block w-full px-3 py-2"
                   value="{{ old('employee_number', auth()->user()->employee?->employee_number) }}"
                   placeholder="e.g. EMP-0001" />
            @error('employee_number')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Position --}}
        <div>
            <label for="position" class="block font-medium text-sm mb-2"
                   style="color: var(--profile-text);">
                {{ __('Position') }}
            </label>
            <input id="position" name="position" type="text"
                   class="profile-input mt-1 block w-full px-3 py-2"
                   value="{{ old('position', auth()->user()->employee?->position) }}"
                   placeholder="e.g. Resident Engineer" />
            @error('position')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Department --}}
        <div>
            <label for="department" class="block font-medium text-sm mb-2"
                   style="color: var(--profile-text);">
                {{ __('Department') }}
            </label>
            <input id="department" name="department" type="text"
                   class="profile-input mt-1 block w-full px-3 py-2"
                   value="{{ old('department', auth()->user()->employee?->department) }}"
                   placeholder="e.g. Engineering" />
            @error('department')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Phone --}}
        <div>
            <label for="phone" class="block font-medium text-sm mb-2"
                   style="color: var(--profile-text);">
                {{ __('Phone') }}
            </label>
            <input id="phone" name="phone" type="tel"
                   class="profile-input mt-1 block w-full px-3 py-2"
                   value="{{ old('phone', auth()->user()->employee?->phone) }}"
                   placeholder="e.g. (+63) 912-345-6789" />
            @error('phone')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Office --}}
        <div>
            <label for="office" class="block font-medium text-sm mb-2"
                   style="color: var(--profile-text);">
                {{ __('Office') }}
            </label>
            <input id="office" name="office" type="text"
                   class="profile-input mt-1 block w-full px-3 py-2"
                   value="{{ old('office', auth()->user()->employee?->office) }}"
                   placeholder="e.g. Provincial Engineering Office" />
            @error('office')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="profile-btn-save">
                {{ __('Save Employee Info') }}
            </button>
        </div>
    </form>
</section>