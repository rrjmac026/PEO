@extends('layouts.app')

@section('title', 'Create Employee')

@section('content')
    <!-- Header -->
    <div class="mb-8">
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
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
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
    <div class="max-w-2xl">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <form action="{{ route('admin.employees.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
                @csrf

                <!-- User Selection -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-user mr-2 text-indigo-600 dark:text-indigo-400"></i>User
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id" required 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition ease-in-out duration-150 @error('user_id') border-red-500 @enderror">
                        <option value="">-- Select a user --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">Only users without an existing employee record are shown</p>
                </div>

                <!-- Employee Number -->
                <div>
                    <label for="employee_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-id-card mr-2 text-indigo-600 dark:text-indigo-400"></i>Employee Number
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="employee_number" id="employee_number" required
                           value="{{ old('employee_number') }}"
                           placeholder="e.g., EMP001"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition ease-in-out duration-150 @error('employee_number') border-red-500 @enderror">
                    @error('employee_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position -->
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-briefcase mr-2 text-indigo-600 dark:text-indigo-400"></i>Position
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="position" id="position" required
                           value="{{ old('position') }}"
                           placeholder="e.g., Senior Manager"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition ease-in-out duration-150 @error('position') border-red-500 @enderror">
                    @error('position')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-sitemap mr-2 text-indigo-600 dark:text-indigo-400"></i>Department
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="department" id="department" required
                           value="{{ old('department') }}"
                           placeholder="e.g., Human Resources"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition ease-in-out duration-150 @error('department') border-red-500 @enderror">
                    @error('department')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-phone mr-2 text-indigo-600 dark:text-indigo-400"></i>Phone
                    </label>
                    <input type="tel" name="phone" id="phone"
                           value="{{ old('phone') }}"
                           placeholder="e.g., (+1) 555-1234"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition ease-in-out duration-150 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Office -->
                <div>
                    <label for="office" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-building mr-2 text-indigo-600 dark:text-indigo-400"></i>Office
                    </label>
                    <input type="text" name="office" id="office"
                           value="{{ old('office') }}"
                           placeholder="e.g., Building A, Floor 3"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition ease-in-out duration-150 @error('office') border-red-500 @enderror">
                    @error('office')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex gap-3">
                    <a href="{{ route('admin.employees.index') }}" 
                       class="inline-flex items-center px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 dark:hover:bg-indigo-700 transition ease-in-out duration-150">
                        <i class="fas fa-save mr-2"></i>Create Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection