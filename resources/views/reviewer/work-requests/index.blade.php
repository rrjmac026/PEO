{{-- resources/views/reviewer/work-requests/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Work Requests
            </h2>
            <span class="text-sm text-gray-500 capitalize">
                {{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ── Flash Messages ────────────────────────────── --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ── Filters ───────────────────────────────────── --}}
            <div class="bg-white rounded-lg shadow mb-6 p-6">
                <form method="GET" action="{{ route('reviewer.work-requests.index') }}"
                      class="flex flex-wrap gap-4 items-end">

                    {{-- Search --}}
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Project name, location, contractor..."
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                        />
                    </div>

                    {{-- Status Filter --}}
                    <div class="min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="draft"      {{ request('status') === 'draft'      ? 'selected' : '' }}>Draft</option>
                            <option value="submitted"  {{ request('status') === 'submitted'  ? 'selected' : '' }}>Submitted</option>
                            <option value="inspected"  {{ request('status') === 'inspected'  ? 'selected' : '' }}>Inspected</option>
                            <option value="reviewed"   {{ request('status') === 'reviewed'   ? 'selected' : '' }}>Reviewed</option>
                            <option value="approved"   {{ request('status') === 'approved'   ? 'selected' : '' }}>Approved</option>
                            <option value="rejected"   {{ request('status') === 'rejected'   ? 'selected' : '' }}>Rejected</option>
                            <option value="accepted"   {{ request('status') === 'accepted'   ? 'selected' : '' }}>Accepted</option>
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div class="min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input
                            type="date"
                            name="date_from"
                            value="{{ request('date_from') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                        />
                    </div>

                    {{-- Date To --}}
                    <div class="min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input
                            type="date"
                            name="date_to"
                            value="{{ request('date_to') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                        />
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                            Filter
                        </button>
                        <a href="{{ route('reviewer.work-requests.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- ── Role-specific notice ──────────────────────── --}}
            @php $role = Auth::user()->role; @endphp

            <div class="mb-4 p-4 rounded-lg
                {{ $role === 'site_inspector'    ? 'bg-blue-50 border border-blue-200 text-blue-700' : '' }}
                {{ $role === 'surveyor'          ? 'bg-purple-50 border border-purple-200 text-purple-700' : '' }}
                {{ $role === 'resident_engineer' ? 'bg-green-50 border border-green-200 text-green-700' : '' }}
                {{ $role === 'provincial_engineer' ? 'bg-yellow-50 border border-yellow-200 text-yellow-700' : '' }}
            ">
                @if($role === 'site_inspector')
                    <p class="text-sm">You can submit <strong>inspection findings</strong> on each work request.</p>
                @elseif($role === 'surveyor')
                    <p class="text-sm">You can submit <strong>survey findings</strong> on each work request.</p>
                @elseif($role === 'resident_engineer')
                    <p class="text-sm">You can submit <strong>engineer review</strong> on each work request.</p>
                @elseif($role === 'provincial_engineer')
                    <p class="text-sm">You can add <strong>provincial engineer notes</strong> on each work request.</p>
                @endif
            </div>

            {{-- ── Table ────────────────────────────────────────--}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Project
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Location
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contractor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Start Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>

                            {{-- Role-specific column header --}}
                            @if($role === 'site_inspector')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Inspected
                                </th>
                            @elseif($role === 'surveyor')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Surveyed
                                </th>
                            @elseif($role === 'resident_engineer')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reviewed
                                </th>
                            @elseif($role === 'provincial_engineer')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Noted
                                </th>
                            @endif

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($workRequests as $workRequest)
                            <tr class="hover:bg-gray-50 transition">
                                {{-- Project --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $workRequest->name_of_project }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        #{{ $workRequest->id }}
                                    </p>
                                </td>

                                {{-- Location --}}
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $workRequest->project_location }}
                                </td>

                                {{-- Contractor --}}
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $workRequest->contractor_name ?? '—' }}
                                </td>

                                {{-- Start Date --}}
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $workRequest->requested_work_start_date?->format('M d, Y') ?? '—' }}
                                </td>

                                {{-- Status Badge --}}
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $workRequest->status === 'approved'  ? 'bg-green-100 text-green-700'  : '' }}
                                        {{ $workRequest->status === 'rejected'  ? 'bg-red-100 text-red-700'    : '' }}
                                        {{ $workRequest->status === 'submitted' ? 'bg-blue-100 text-blue-700'  : '' }}
                                        {{ $workRequest->status === 'draft'     ? 'bg-gray-100 text-gray-700'  : '' }}
                                        {{ $workRequest->status === 'inspected' ? 'bg-cyan-100 text-cyan-700'  : '' }}
                                        {{ $workRequest->status === 'reviewed'  ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $workRequest->status === 'accepted'  ? 'bg-teal-100 text-teal-700'  : '' }}
                                    ">
                                        {{ ucfirst($workRequest->status) }}
                                    </span>
                                </td>

                                {{-- Role-specific completion column --}}
                                @if($role === 'site_inspector')
                                    <td class="px-6 py-4 text-sm">
                                        @if($workRequest->inspected_by_site_inspector)
                                            <span class="text-green-600 font-medium">✓ Done</span>
                                        @else
                                            <span class="text-yellow-500 font-medium">Pending</span>
                                        @endif
                                    </td>
                                @elseif($role === 'surveyor')
                                    <td class="px-6 py-4 text-sm">
                                        @if($workRequest->surveyor_name)
                                            <span class="text-green-600 font-medium">✓ Done</span>
                                        @else
                                            <span class="text-yellow-500 font-medium">Pending</span>
                                        @endif
                                    </td>
                                @elseif($role === 'resident_engineer')
                                    <td class="px-6 py-4 text-sm">
                                        @if($workRequest->resident_engineer_name)
                                            <span class="text-green-600 font-medium">✓ Done</span>
                                        @else
                                            <span class="text-yellow-500 font-medium">Pending</span>
                                        @endif
                                    </td>
                                @elseif($role === 'provincial_engineer')
                                    <td class="px-6 py-4 text-sm">
                                        @if($workRequest->approved_notes)
                                            <span class="text-green-600 font-medium">✓ Noted</span>
                                        @else
                                            <span class="text-yellow-500 font-medium">Not yet</span>
                                        @endif
                                    </td>
                                @endif

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('reviewer.work-requests.show', $workRequest) }}"
                                       class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No work requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- ── Pagination ────────────────────────────── --}}
                @if($workRequests->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $workRequests->withQueryString()->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>