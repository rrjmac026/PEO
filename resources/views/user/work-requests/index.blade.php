<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Work Requests') }}
            </h2>
            <a href="{{ route('user.work-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                {{ __('New Request') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filter Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('user.work-requests.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Search') }}
                            </label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Project name, location, contractor..." 
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Status') }}
                            </label>
                            <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">{{ __('All Statuses') }}</option>
                                @foreach(\App\Models\WorkRequest::getStatuses() as $status)
                                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-search mr-2"></i>
                                {{ __('Search') }}
                            </button>
                            @if(request('search') || request('status'))
                                <a href="{{ route('user.work-requests.index') }}" class="flex-1 px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 text-center">
                                    <i class="fas fa-times mr-2"></i>
                                    {{ __('Clear') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Work Requests Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-900 dark:text-gray-100">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 font-semibold">{{ __('Project Name') }}</th>
                                <th class="px-6 py-3 font-semibold">{{ __('Location') }}</th>
                                <th class="px-6 py-3 font-semibold">{{ __('Contractor') }}</th>
                                <th class="px-6 py-3 font-semibold">{{ __('Status') }}</th>
                                <th class="px-6 py-3 font-semibold">{{ __('Date') }}</th>
                                <th class="px-6 py-3 font-semibold text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($workRequests as $workRequest)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-4 font-medium">
                                        {{ $workRequest->name_of_project }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $workRequest->project_location }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $workRequest->contractor_name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'draft' => 'gray',
                                                'submitted' => 'blue',
                                                'inspected' => 'purple',
                                                'reviewed' => 'indigo',
                                                'approved' => 'green',
                                                'accepted' => 'green',
                                                'rejected' => 'red',
                                            ];
                                            $color = $statusColors[$workRequest->status] ?? 'gray';
                                        @endphp
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold leading-5 rounded-full bg-{{ $color }}-100 text-{{ $color }}-800 dark:bg-{{ $color }}-900 dark:text-{{ $color }}-200">
                                            {{ ucfirst($workRequest->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $workRequest->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('user.work-requests.show', $workRequest) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($workRequest->canEdit())
                                                <a href="{{ route('user.work-requests.edit', $workRequest) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('user.work-requests.destroy', $workRequest) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('user.work-requests.print', $workRequest) }}" target="_blank" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300" title="Print">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="{{ route('user.work-requests.download', $workRequest) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300" title="Download PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('No work requests found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($workRequests->hasPages())
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                        {{ $workRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
