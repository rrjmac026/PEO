@extends('layouts.app')

@section('title', 'Employees')

@section('content')
    <!-- Header with Title and Action Button -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Employees</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage and view all employees</p>
            </div>
            <a href="{{ route('admin.employees.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Add Employee
            </a>
        </div>
    </div>

    <!-- Session Messages -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <h3 class="text-red-800 dark:text-red-400 font-semibold mb-2">Errors</h3>
            <ul class="list-disc list-inside text-red-700 dark:text-red-300 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
            <span class="text-green-800 dark:text-green-400">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-6">
        <form method="GET" action="{{ route('admin.employees.index') }}" class="flex gap-2">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       placeholder="Search by name, ID, position, or department..." 
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <button type="submit" 
                    class="px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-700 dark:hover:bg-gray-600 transition ease-in-out duration-150 flex items-center gap-2">
                <i class="fas fa-search"></i> Search
            </button>
            @if (request('search'))
                <a href="{{ route('admin.employees.index') }}" 
                   class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition ease-in-out duration-150">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Employees Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        @if ($employees->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($employees as $employee)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                            <span class="text-indigo-600 dark:text-indigo-400 font-semibold text-sm">
                                                {{ substr($employee->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $employee->user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $employee->employee_id }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-gray-100">{{ $employee->position }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold rounded-full">
                                        {{ $employee->department }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-gray-100">
                                        @if ($employee->phone)
                                            {{ $employee->phone }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.employees.show', $employee) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded text-xs font-semibold hover:bg-blue-200 dark:hover:bg-blue-800 transition ease-in-out duration-150"
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.employees.edit', $employee) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 rounded text-xs font-semibold hover:bg-amber-200 dark:hover:bg-amber-800 transition ease-in-out duration-150"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-1 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded text-xs font-semibold hover:bg-red-200 dark:hover:bg-red-800 transition ease-in-out duration-150"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $employees->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400 text-lg">No employees found</p>
                <a href="{{ route('admin.employees.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline mt-2 inline-block">
                    Create the first employee
                </a>
            </div>
        @endif
    </div>
@endsection
