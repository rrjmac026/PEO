@extends('layouts.app')

@section('title', $employee->user->name . ' - Employee Details')

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Employee Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <!-- Header with Avatar -->
                <div class="h-32 bg-gradient-to-r from-indigo-600 to-blue-600 dark:from-indigo-700 dark:to-blue-700"></div>
                
                <div class="px-6 md:px-8 pb-8">
                    <!-- Avatar -->
                    <div class="flex items-end gap-4 -mt-16 mb-6">
                        <div class="h-32 w-32 rounded-full border-4 border-white dark:border-gray-800 bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center shadow-lg">
                            <span class="text-5xl font-bold text-indigo-600 dark:text-indigo-400">
                                {{ substr($employee->user->name, 0, 1) }}
                            </span>
                        </div>
                        <div class="pb-2">
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $employee->user->name }}</h2>
                            <p class="text-gray-500 dark:text-gray-400">{{ $employee->position }}</p>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Employee ID -->
                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Employee ID</p>
                            <p class="text-lg font-mono text-gray-900 dark:text-white">{{ $employee->employee_id }}</p>
                        </div>

                        <!-- Department -->
                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Department</p>
                            <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm font-semibold rounded-full">
                                {{ $employee->department }}
                            </span>
                        </div>

                        <!-- Email -->
                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Email</p>
                            <a href="mailto:{{ $employee->user->email }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium flex items-center gap-2">
                                <i class="fas fa-envelope"></i>{{ $employee->user->email }}
                            </a>
                        </div>

                        <!-- Phone -->
                        <div>
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Phone</p>
                            @if ($employee->phone)
                                <a href="tel:{{ $employee->phone }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium flex items-center gap-2">
                                    <i class="fas fa-phone"></i>{{ $employee->phone }}
                                </a>
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </div>

                        <!-- Office -->
                        <div class="md:col-span-2">
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Office Location</p>
                            @if ($employee->office)
                                <p class="text-gray-900 dark:text-white font-medium flex items-center gap-2">
                                    <i class="fas fa-building text-indigo-600 dark:text-indigo-400"></i>{{ $employee->office }}
                                </p>
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-2 gap-4 md:gap-6">
                            <div>
                                <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Created</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $employee->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 mb-1">Last Updated</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $employee->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 flex gap-3 flex-wrap">
                        <a href="{{ route('admin.employees.edit', $employee) }}" 
                           class="inline-flex items-center px-4 py-2 bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 rounded-lg font-semibold hover:bg-amber-200 dark:hover:bg-amber-800 transition ease-in-out duration-150">
                            <i class="fas fa-edit mr-2"></i>Edit Employee
                        </a>
                        <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg font-semibold hover:bg-red-200 dark:hover:bg-red-800 transition ease-in-out duration-150">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-600 dark:text-indigo-400"></i>Quick Info
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Status</span>
                        <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-semibold rounded-full">
                            Active
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">User Type</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            @if($employee->user->is_admin)
                                <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded text-xs">Admin</span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs">Employee</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
