@extends('layouts.app')

@section('title', ($employee->full_name ?: $employee->user->name) . ' - Employee Details')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.employees.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Employee Details</h1>
        </div>
    </div>

    @php
        $displayName = $employee->full_name ?: $employee->user->name;
        $initial     = strtoupper(substr($displayName, 0, 1));
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ── Main Content ── -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Profile Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="h-32 bg-gradient-to-r from-indigo-600 to-blue-600 dark:from-indigo-700 dark:to-blue-700"></div>

                <div class="px-6 md:px-8 pb-8">
                    <div class="flex items-end gap-4 -mt-16 mb-6">
                        <div class="h-32 w-32 rounded-full border-4 border-white dark:border-gray-800 bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center shadow-lg">
                            <span class="text-5xl font-bold text-indigo-600 dark:text-indigo-400">{{ $initial }}</span>
                        </div>
                        <div class="pb-2">
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $displayName }}</h2>
                            <p class="text-gray-500 dark:text-gray-400">{{ $employee->position }}</p>
                        </div>
                    </div>

                    <!-- ── Section: Basic Info ── -->
                    <h3 class="text-xs font-bold uppercase tracking-widest text-indigo-500 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Employee ID</p>
                            <p class="text-base font-mono text-gray-900 dark:text-white">{{ $employee->employee_number ?? '—' }}</p>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Department</p>
                            @if ($employee->department)
                                <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm font-semibold rounded-full">
                                    {{ $employee->department }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Email</p>
                            <a href="mailto:{{ $employee->email ?? $employee->user->email }}"
                               class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium flex items-center gap-2">
                                <i class="fas fa-envelope"></i>{{ $employee->email ?? $employee->user->email }}
                            </a>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Phone</p>
                            @if ($employee->phone)
                                <a href="tel:{{ $employee->phone }}"
                                   class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium flex items-center gap-2">
                                    <i class="fas fa-phone"></i>{{ $employee->phone }}
                                </a>
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Date of Birth</p>
                            <p class="text-gray-900 dark:text-white">
                                {{ $employee->date_of_birth?->format('F d, Y') ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Blood Type</p>
                            <p class="text-gray-900 dark:text-white">{{ $employee->blood_type ?? '—' }}</p>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Height / Weight</p>
                            <p class="text-gray-900 dark:text-white">
                                {{ $employee->height_cm ? $employee->height_cm . ' cm' : '—' }}
                                &nbsp;/&nbsp;
                                {{ $employee->weight_kg ? $employee->weight_kg . ' kg' : '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Emergency Contact</p>
                            <p class="text-gray-900 dark:text-white">{{ $employee->emergency_contact_no ?? '—' }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Home Address</p>
                            <p class="text-gray-900 dark:text-white">{{ $employee->home_address ?? '—' }}</p>
                        </div>

                        @if ($employee->office)
                        <div class="md:col-span-2">
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Office Location</p>
                            <p class="text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-building text-indigo-600 dark:text-indigo-400"></i>{{ $employee->office }}
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- ── Section: Government IDs ── -->
                    <h3 class="text-xs font-bold uppercase tracking-widest text-indigo-500 mb-4">Government IDs</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        @foreach ([
                            'TIN'         => $employee->tin,
                            'Pag-IBIG'    => $employee->pagibig_no,
                            'PhilHealth'  => $employee->philhealth_no,
                            'GSIS'        => $employee->gsis_no,
                        ] as $label => $value)
                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ $label }}</p>
                            <p class="font-mono text-gray-900 dark:text-white text-sm">{{ $value ?? '—' }}</p>
                        </div>
                        @endforeach
                    </div>

                    <!-- ── Section: HMO & Professional ── -->
                    <h3 class="text-xs font-bold uppercase tracking-widest text-indigo-500 mb-4">HMO & Professional</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">HMO Organization</p>
                            <p class="text-gray-900 dark:text-white">{{ $employee->hmo_organization ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">HMO Number</p>
                            <p class="font-mono text-gray-900 dark:text-white text-sm">{{ $employee->hmo_number ?? '—' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Eligibility</p>
                            <p class="text-gray-900 dark:text-white">{{ $employee->eligibility ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">License Number</p>
                            <p class="font-mono text-gray-900 dark:text-white text-sm">{{ $employee->license_number ?? '—' }}</p>
                        </div>
                    </div>

                    <!-- Timestamps + Actions -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Created</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $employee->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Last Updated</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $employee->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-3 flex-wrap">
                            <a href="{{ route('admin.employees.edit', $employee) }}"
                               class="inline-flex items-center px-4 py-2 bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 rounded-lg font-semibold hover:bg-amber-200 dark:hover:bg-amber-800 transition ease-in-out duration-150">
                                <i class="fas fa-edit mr-2"></i>Edit Employee
                            </a>
                            <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg font-semibold hover:bg-red-200 dark:hover:bg-red-800 transition ease-in-out duration-150">
                                    <i class="fas fa-trash mr-2"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /main -->

        <!-- ── Sidebar ── -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-600 dark:text-indigo-400"></i>Quick Info
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Status</span>
                        <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-semibold rounded-full">Active</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">User Role</span>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-semibold">
                            {{ Str::ucfirst($employee->user->role ?? 'Employee') }}
                        </span>
                    </div>
                    @if ($employee->blood_type)
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Blood Type</span>
                        <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded text-xs font-semibold">
                            {{ $employee->blood_type }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection