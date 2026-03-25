<x-app-layout>

    @push('styles')
        @include('user.concrete-pouring._cp-styles')
        <style>
            .rv-section-heading {
                font-size: 18px; font-weight: 700; color: var(--cp-text);
                display: flex; align-items: center; gap: 8px; margin-bottom: 16px;
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Concrete Pouring — My Review Queue
        </h2>
    </x-slot>

    <div class="py-10 cp-wrap">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ── Pending queue ── --}}
            <div>
                <h2 class="rv-section-heading">
                    <i class="fas fa-inbox text-cyan-500"></i>
                    Awaiting My Review
                    <span class="text-sm font-normal ml-2 px-2.5 py-0.5 rounded-full bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
                        {{ $concretePourings->total() }}
                    </span>
                </h2>

                <div class="cp-table-container">
                    <div class="overflow-x-auto">
                        <table class="cp-table">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Project</th>
                                    <th>Location</th>
                                    <th>Contractor</th>
                                    <th>Pouring Date</th>
                                    <th>My Step</th>
                                    <th>Checklist</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($concretePourings as $cp)
                                    <tr>
                                        <td>
                                            <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                {{ $cp->reference_number }}
                                            </span>
                                        </td>
                                        <td>
                                            <p class="font-medium">{{ $cp->project_name }}</p>
                                            <p class="text-xs" style="color:var(--cp-muted)">{{ $cp->part_of_structure }}</p>
                                        </td>
                                        <td>{{ $cp->location }}</td>
                                        <td>{{ $cp->contractor }}</td>
                                        <td>{{ $cp->pouring_datetime?->format('M d, Y') ?? '—' }}</td>
                                        <td>
                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                                @if($cp->current_review_step === 'mtqa') bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300
                                                @elseif($cp->current_review_step === 'resident_engineer') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                                @elseif($cp->current_review_step === 'provincial_engineer') bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300
                                                @else bg-gray-100 text-gray-600 @endif">
                                                {{ $cp->current_step_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <div style="height:5px;width:70px;background:var(--cp-border);border-radius:99px;overflow:hidden">
                                                    <div style="height:100%;width:{{ $cp->checklist_progress }}%;background:#059669;border-radius:99px"></div>
                                                </div>
                                                <span class="text-xs" style="color:var(--cp-muted)">{{ $cp->checklist_progress }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('reviewer.concrete-pouring.show', $cp) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-cyan-600 text-white text-xs font-semibold rounded-lg hover:bg-cyan-700 transition">
                                                <i class="fas fa-clipboard-check"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="cp-empty">
                                                <div class="cp-empty-icon">✅</div>
                                                <p class="cp-empty-msg">No requests currently awaiting your review.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($concretePourings->hasPages())
                        <div class="px-6 py-4" style="background:var(--cp-surface2);border-top:1px solid var(--cp-border)">
                            {{ $concretePourings->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Recently reviewed ── --}}
            @if($completed->count())
                <div>
                    <h2 class="rv-section-heading">
                        <i class="fas fa-history text-green-500"></i>
                        Recently Reviewed
                    </h2>

                    <div class="cp-table-container">
                        <div class="overflow-x-auto">
                            <table class="cp-table">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Project</th>
                                        <th>Contractor</th>
                                        <th>Pouring Date</th>
                                        <th>Status</th>
                                        <th class="text-center">View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completed as $cp)
                                        <tr>
                                            <td>
                                                <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                    {{ $cp->reference_number }}
                                                </span>
                                            </td>
                                            <td>
                                                <p class="font-medium">{{ $cp->project_name }}</p>
                                                <p class="text-xs" style="color:var(--cp-muted)">{{ $cp->location }}</p>
                                            </td>
                                            <td>{{ $cp->contractor }}</td>
                                            <td>{{ $cp->pouring_datetime?->format('M d, Y') ?? '—' }}</td>
                                            <td>
                                                <span class="cp-badge {{ $cp->status }}">
                                                    <span class="cp-badge-dot" style="background:currentColor;border-radius:50%"></span>
                                                    {{ ucfirst($cp->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('reviewer.concrete-pouring.show', $cp) }}"
                                                   style="color:var(--cp-accent)">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>