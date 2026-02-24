{{-- resources/views/reviewer/dashboard.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $stats['title'] }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ── Site Inspector ────────────────────────────── --}}
            @if($role === 'site_inspector')
                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Pending Inspection</p>
                        <p class="text-4xl font-bold text-yellow-500">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Inspected</p>
                        <p class="text-4xl font-bold text-green-500">{{ $stats['done'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Total Requests</p>
                        <p class="text-4xl font-bold text-blue-500">{{ $stats['total'] }}</p>
                    </div>
                </div>

            {{-- ── Surveyor ──────────────────────────────────── --}}
            @elseif($role === 'surveyor')
                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Pending Survey</p>
                        <p class="text-4xl font-bold text-yellow-500">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Surveyed</p>
                        <p class="text-4xl font-bold text-green-500">{{ $stats['done'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Total Requests</p>
                        <p class="text-4xl font-bold text-blue-500">{{ $stats['total'] }}</p>
                    </div>
                </div>

            {{-- ── Resident Engineer ─────────────────────────── --}}
            @elseif($role === 'resident_engineer')
                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Pending Review</p>
                        <p class="text-4xl font-bold text-yellow-500">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Reviewed</p>
                        <p class="text-4xl font-bold text-green-500">{{ $stats['done'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Total Requests</p>
                        <p class="text-4xl font-bold text-blue-500">{{ $stats['total'] }}</p>
                    </div>
                </div>

            {{-- ── Provincial Engineer ───────────────────────── --}}
            @elseif($role === 'provincial_engineer')
                <div class="grid grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Total Requests</p>
                        <p class="text-4xl font-bold text-blue-500">{{ $stats['total'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Approved</p>
                        <p class="text-4xl font-bold text-green-500">{{ $stats['approved'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Pending</p>
                        <p class="text-4xl font-bold text-yellow-500">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Rejected</p>
                        <p class="text-4xl font-bold text-red-500">{{ $stats['rejected'] }}</p>
                    </div>
                </div>

                {{-- Concrete Pouring Stats (Provincial Engineer only) --}}
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Concrete Pouring Overview</h3>
                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Total Pourings</p>
                        <p class="text-4xl font-bold text-blue-500">{{ $stats['cp_total'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Approved</p>
                        <p class="text-4xl font-bold text-green-500">{{ $stats['cp_approved'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-sm text-gray-500">Pending</p>
                        <p class="text-4xl font-bold text-yellow-500">{{ $stats['cp_pending'] }}</p>
                    </div>
                </div>
            @endif

            {{-- ── Recent Work Requests (all roles) ─────────── --}}
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-700">
                        {{ $role === 'provincial_engineer' ? 'Recent Requests' : 'Needs Your Action' }}
                    </h3>
                </div>
                <div class="p-6">
                    @forelse($stats['recent'] as $workRequest)
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div>
                                <p class="font-medium text-gray-800">{{ $workRequest->name_of_project }}</p>
                                <p class="text-sm text-gray-500">{{ $workRequest->project_location }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs px-2 py-1 rounded-full 
                                    {{ $workRequest->status === 'approved' ? 'bg-green-100 text-green-700' : 
                                       ($workRequest->status === 'rejected' ? 'bg-red-100 text-red-700' : 
                                       'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($workRequest->status) }}
                                </span>
                                <a href="{{ route('reviewer.work-requests.show', $workRequest) }}"
                                   class="text-sm text-blue-600 hover:underline">
                                    View
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No pending requests.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
