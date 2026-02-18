<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Concrete Pouring Reports
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.concrete-pouring.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white text-xs font-semibold rounded-md hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Date Range Filter --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.concrete-pouring.reports') }}" class="flex flex-wrap gap-4">
                        <div class="min-w-[180px]">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                            <input type="date" 
                                   name="start_date" 
                                   value="{{ $startDate }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm">
                        </div>
                        <div class="min-w-[180px]">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                            <input type="date" 
                                   name="end_date" 
                                   value="{{ $endDate }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm">
                        </div>
                        <div class="min-w-[180px]">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select name="status" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm">
                                <option value="">All Status</option>
                                <option value="requested" {{ request('status') == 'requested' ? 'selected' : '' }}>Requested</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="disapproved" {{ request('status') == 'disapproved' ? 'selected' : '' }}>Disapproved</option>
                            </select>
                        </div>
                        <div class="min-w-[180px]">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contractor</label>
                            <select name="contractor" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm">
                                <option value="">All Contractors</option>
                                @foreach($contractors as $contractor)
                                    <option value="{{ $contractor }}" {{ request('contractor') == $contractor ? 'selected' : '' }}>
                                        {{ $contractor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-filter mr-2"></i>
                                Filter
                            </button>
                            <a href="{{ route('admin.concrete-pouring.print-report', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-print mr-2"></i>
                                Print Report
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Summary Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Requests</h3>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $summary['total_requests'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-green-700 dark:text-green-300 mb-2">Approved</h3>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $summary['approved'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-red-700 dark:text-red-300 mb-2">Disapproved</h3>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $summary['disapproved'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-yellow-700 dark:text-yellow-300 mb-2">Pending</h3>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $summary['pending'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-2">Total Volume (m³)</h3>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $summary['total_volume'] }}</p>
                </div>
            </div>

            {{-- Details Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Average Volume</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $summary['avg_volume'] }} m³</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Avg. Checklist Completion</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $summary['avg_checklist_completion'] }}%</p>
                </div>
            </div>

            {{-- Contractor Breakdown --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Contractor Breakdown</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Contractor
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Approved
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Disapproved
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pending
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total Volume (m³)
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($contractorBreakdown as $contractor => $data)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $contractor }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ $data['total'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400">
                                            {{ $data['approved'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 dark:text-red-400">
                                            {{ $data['disapproved'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 dark:text-yellow-400">
                                            {{ $data['pending'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 dark:text-blue-400">
                                            {{ $data['total_volume'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400 text-center">
                                            No contractors found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
