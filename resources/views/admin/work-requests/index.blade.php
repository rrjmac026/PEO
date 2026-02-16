<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Work Requests') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.work-requests.import.form') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:bg-green-700 dark:focus:bg-green-600 active:bg-green-900 dark:active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-upload mr-2"></i>
                    {{ __('Import Employees') }}
                </a>
                <a href="{{ route('admin.work-requests.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-orange-600 dark:bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-orange-600 focus:bg-orange-700 dark:focus:bg-orange-600 active:bg-orange-900 dark:active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-plus-circle mr-2"></i>
                    {{ __('New Work Request') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- Filters and Search --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.work-requests.index') }}" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search by project name, location, or requester..." 
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm">
                        </div>
                        <div class="min-w-[150px]">
                            <select name="status" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm">
                                <option value="">All Status</option>
                                @foreach(\App\Models\WorkRequest::getStatuses() as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-search mr-2"></i>
                                {{ __('Filter') }}
                            </button>
                            @if(request('search') || request('status'))
                                <a href="{{ route('admin.work-requests.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <i class="fas fa-times mr-2"></i>
                                    {{ __('Clear') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Work Requests Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Project Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Location
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Requested By
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Start Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($workRequests as $request)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            #{{ $request->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ Str::limit($request->name_of_project, 30) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ Str::limit($request->project_location, 25) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $request->requested_by }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $request->requested_work_start_date ? $request->requested_work_start_date->format('M d, Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'draft'     => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                    'submitted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                    'inspected' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                                    'reviewed'  => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                    'approved'  => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                    'accepted'  => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300',
                                                    'rejected'  => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                ];
                                                $colorClass = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">

                                                {{-- View Button --}}
                                                <a href="{{ route('admin.work-requests.show', $request) }}" 
                                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" 
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                {{-- Edit Button --}}
                                                @if($request->canEdit())
                                                    <a href="{{ route('admin.work-requests.edit', $request) }}" 
                                                       class="text-orange-600 dark:text-orange-400 hover:text-orange-900 dark:hover:text-orange-300" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif

                                                {{-- Activity Log Button --}}
                                                <a href="{{ route('admin.work-requests.logs', $request) }}"
                                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300"
                                                   title="View Activity Log">
                                                    <i class="fas fa-history"></i>
                                                </a>

                                                {{-- Print Button --}}
                                                <a href="{{ route('admin.work-requests.print', $request) }}" 
                                                   class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300" 
                                                   title="Print PDF" 
                                                   target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>

                                                {{-- Download Button --}}
                                                <a href="{{ route('admin.work-requests.download', $request) }}" 
                                                   class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300" 
                                                   title="Download PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>

                                                {{-- Delete Button --}}
                                                @if($request->status === 'draft')
                                                    <form action="{{ route('admin.work-requests.destroy', $request) }}" 
                                                          method="POST" 
                                                          class="inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this work request?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" 
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-inbox text-6xl mb-4 opacity-50"></i>
                                                <p class="text-lg font-medium mb-2">No work requests found</p>
                                                <p class="text-sm mb-4">Get started by creating your first work request</p>
                                                <a href="{{ route('admin.work-requests.create') }}" 
                                                   class="inline-flex items-center px-4 py-2 bg-orange-600 dark:bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-orange-600 focus:bg-orange-700 dark:focus:bg-orange-600 active:bg-orange-900 dark:active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                                    <i class="fas fa-plus-circle mr-2"></i>
                                                    Create Work Request
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($workRequests->hasPages())
                        <div class="mt-6">
                            {{ $workRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                @php
                    $stats = [
                        'draft'     => ['label' => 'Draft',     'icon' => 'fa-file',         'color' => 'gray'],
                        'submitted' => ['label' => 'Submitted', 'icon' => 'fa-paper-plane',  'color' => 'blue'],
                        'approved'  => ['label' => 'Approved',  'icon' => 'fa-check-circle', 'color' => 'green'],
                        'rejected'  => ['label' => 'Rejected',  'icon' => 'fa-times-circle', 'color' => 'red'],
                    ];
                @endphp

                @foreach($stats as $status => $stat)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas {{ $stat['icon'] }} text-3xl text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                            {{ $stat['label'] }}
                                        </dt>
                                        <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                            {{ \App\Models\WorkRequest::where('status', $status)->count() }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>