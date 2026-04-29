<x-app-layout>

    @push('styles')
        @include('user.concrete-pouring._cp-styles')
        <style>
            /* ── Tab Nav ── */
            .rv-tabs {
                display: flex; gap: 4px; flex-wrap: wrap;
                background: var(--cp-surface2);
                border: 1px solid var(--cp-border);
                border-radius: 12px;
                padding: 6px;
                margin-bottom: 24px;
            }
            .rv-tab {
                display: inline-flex; align-items: center; gap: 7px;
                padding: 8px 18px; border-radius: 8px;
                font-size: 13px; font-weight: 600;
                text-decoration: none; border: none; cursor: pointer;
                color: var(--cp-muted); background: transparent;
                transition: background 0.18s, color 0.18s, box-shadow 0.18s;
                white-space: nowrap;
            }
            .rv-tab:hover { background: var(--cp-surface); color: var(--cp-text); }
            .rv-tab.active {
                background: var(--cp-surface);
                color: var(--cp-text);
                box-shadow: 0 1px 4px rgba(0,0,0,0.10);
            }
            .rv-tab.active.tab-all        { color: #0891b2; }
            .rv-tab.active.tab-pending    { color: #d97706; }
            .rv-tab.active.tab-approved   { color: #059669; }
            .rv-tab.active.tab-disapproved{ color: #dc2626; }
            .rv-tab.active.tab-completed  { color: #7c3aed; }

            .rv-tab-count {
                font-size: 11px; font-weight: 700;
                padding: 2px 7px; border-radius: 10px; line-height: 1.4;
            }
            .tab-all         .rv-tab-count { background: rgba(8,145,178,0.15);  color: #0891b2; }
            .tab-pending     .rv-tab-count { background: rgba(217,119,6,0.15);  color: #d97706; }
            .tab-approved    .rv-tab-count { background: rgba(5,150,105,0.15);  color: #059669; }
            .tab-disapproved .rv-tab-count { background: rgba(220,38,38,0.15);  color: #dc2626; }
            .tab-completed   .rv-tab-count { background: rgba(124,58,237,0.15); color: #7c3aed; }

            /* ── Section heading ── */
            .rv-section-heading {
                font-size: 18px; font-weight: 700; color: var(--cp-text);
                display: flex; align-items: center; gap: 8px; margin-bottom: 16px;
            }

            /* ── Empty state ── */
            .rv-empty {
                text-align: center; padding: 56px 24px;
            }
            .rv-empty-icon {
                font-size: 40px; margin-bottom: 12px; opacity: 0.5;
            }
            .rv-empty-msg {
                font-size: 14px; color: var(--cp-muted);
            }

            /* ── Step badge colours ── */
            .step-re   { background: rgba(37,99,235,0.1);  color: #2563eb; }
            .step-pe   { background: rgba(234,88,12,0.1);  color: #ea580c; }
            .step-mtqa { background: rgba(124,58,237,0.1); color: #7c3aed; }
            .dark .step-re   { background: rgba(96,165,250,0.15);  color: #60a5fa; }
            .dark .step-pe   { background: rgba(251,146,60,0.15);  color: #fb923c; }
            .dark .step-mtqa { background: rgba(192,132,252,0.15); color: #c084fc; }

            /* ── Timeline indicator (pipeline progress) ── */
            .rv-pipeline {
                display: flex; align-items: center; gap: 4px; margin-top: 6px;
            }
            .rv-pip-step {
                width: 24px; height: 5px; border-radius: 99px;
                background: var(--cp-border);
            }
            .rv-pip-step.done    { background: #059669; }
            .rv-pip-step.active  { background: #0891b2; }
            .rv-pip-step.waiting { background: var(--cp-border); }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Concrete Pouring — Review Queue
        </h2>
    </x-slot>

    @php
        $activeTab = request('tab', 'queue');

        // Count badges per tab
        $countQueue       = $concretePourings->total();
        $countPending     = $allPending->count();
        $countApproved    = $allApproved->count();
        $countDisapproved = $allDisapproved->count();
        $countCompleted   = $completed->count();
    @endphp

    <div class="py-10 cp-wrap">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ══════════════════════════════════════
                 TAB NAV
            ══════════════════════════════════════ --}}
            <div class="rv-tabs">
                <a href="{{ route('reviewer.concrete-pouring.index', ['tab' => 'queue']) }}"
                   class="rv-tab tab-all {{ $activeTab === 'queue' ? 'active' : '' }}">
                    <i class="fas fa-inbox"></i> My Queue
                    @if($countQueue > 0)
                        <span class="rv-tab-count">{{ $countQueue }}</span>
                    @endif
                </a>
                <a href="{{ route('reviewer.concrete-pouring.index', ['tab' => 'pending']) }}"
                   class="rv-tab tab-pending {{ $activeTab === 'pending' ? 'active' : '' }}">
                    <i class="fas fa-hourglass-half"></i> Pending
                    @if($countPending > 0)
                        <span class="rv-tab-count">{{ $countPending }}</span>
                    @endif
                </a>
                <a href="{{ route('reviewer.concrete-pouring.index', ['tab' => 'approved']) }}"
                   class="rv-tab tab-approved {{ $activeTab === 'approved' ? 'active' : '' }}">
                    <i class="fas fa-check-circle"></i> Approved
                    @if($countApproved > 0)
                        <span class="rv-tab-count">{{ $countApproved }}</span>
                    @endif
                </a>
                <a href="{{ route('reviewer.concrete-pouring.index', ['tab' => 'disapproved']) }}"
                   class="rv-tab tab-disapproved {{ $activeTab === 'disapproved' ? 'active' : '' }}">
                    <i class="fas fa-times-circle"></i> Disapproved
                    @if($countDisapproved > 0)
                        <span class="rv-tab-count">{{ $countDisapproved }}</span>
                    @endif
                </a>
                <a href="{{ route('reviewer.concrete-pouring.index', ['tab' => 'completed']) }}"
                   class="rv-tab tab-completed {{ $activeTab === 'completed' ? 'active' : '' }}">
                    <i class="fas fa-history"></i> My History
                    @if($countCompleted > 0)
                        <span class="rv-tab-count">{{ $countCompleted }}</span>
                    @endif
                </a>
            </div>

            {{-- ══════════════════════════════════════
                 TAB: MY QUEUE (awaiting my turn)
            ══════════════════════════════════════ --}}
            @if($activeTab === 'queue')
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
                                        <th>Pipeline</th>
                                        <th>My Step</th>
                                        <th>Checklist</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($concretePourings as $cp)
                                        @php
                                            $reDone   = !is_null($cp->re_date);
                                            $peDone   = !is_null($cp->noted_date);
                                            $mtqaDone = !is_null($cp->me_mtqa_date);
                                        @endphp
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
                                                {{-- Mini pipeline progress bar --}}
                                                <div class="rv-pipeline" title="RE → PE → MTQA">
                                                    <div class="rv-pip-step {{ $reDone ? 'done' : ($cp->current_review_step === 'resident_engineer' ? 'active' : 'waiting') }}"></div>
                                                    <div class="rv-pip-step {{ $peDone ? 'done' : ($cp->current_review_step === 'provincial_engineer' ? 'active' : 'waiting') }}"></div>
                                                    <div class="rv-pip-step {{ $mtqaDone ? 'done' : ($cp->current_review_step === 'mtqa' ? 'active' : 'waiting') }}"></div>
                                                </div>
                                                <p class="text-xs mt-1" style="color:var(--cp-muted)">RE → PE → MTQA</p>
                                            </td>
                                            <td>
                                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                                    @if($cp->current_review_step === 'mtqa') step-mtqa
                                                    @elseif($cp->current_review_step === 'resident_engineer') step-re
                                                    @elseif($cp->current_review_step === 'provincial_engineer') step-pe
                                                    @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 @endif">
                                                    {{ $cp->current_step_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="flex items-center gap-2">
                                                    <div style="height:5px;width:60px;background:var(--cp-border);border-radius:99px;overflow:hidden">
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
                                            <td colspan="9">
                                                <div class="rv-empty">
                                                    <div class="rv-empty-icon">✅</div>
                                                    <p class="rv-empty-msg">No requests currently awaiting your review.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($concretePourings->hasPages())
                            <div class="px-6 py-4" style="background:var(--cp-surface2);border-top:1px solid var(--cp-border)">
                                {{ $concretePourings->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ══════════════════════════════════════
                 TAB: PENDING (all in-review, not yet decided)
            ══════════════════════════════════════ --}}
            @if($activeTab === 'pending')
                <div>
                    <h2 class="rv-section-heading">
                        <i class="fas fa-hourglass-half text-yellow-500"></i>
                        All Pending Requests
                        <span class="text-sm font-normal ml-2 px-2.5 py-0.5 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">
                            {{ $allPending->count() }}
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
                                        <th>Pipeline Progress</th>
                                        <th>Current Step</th>
                                        <th>Submitted</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allPending as $cp)
                                        @php
                                            $reDone   = !is_null($cp->re_date);
                                            $peDone   = !is_null($cp->noted_date);
                                            $mtqaDone = !is_null($cp->me_mtqa_date);
                                        @endphp
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
                                                <div class="rv-pipeline">
                                                    <div class="rv-pip-step {{ $reDone ? 'done' : ($cp->current_review_step === 'resident_engineer' ? 'active' : 'waiting') }}"></div>
                                                    <div class="rv-pip-step {{ $peDone ? 'done' : ($cp->current_review_step === 'provincial_engineer' ? 'active' : 'waiting') }}"></div>
                                                    <div class="rv-pip-step {{ $mtqaDone ? 'done' : ($cp->current_review_step === 'mtqa' ? 'active' : 'waiting') }}"></div>
                                                </div>
                                                <p class="text-xs mt-1" style="color:var(--cp-muted)">
                                                    {{ $reDone ? '✓' : '○' }} RE &nbsp;
                                                    {{ $peDone ? '✓' : '○' }} PE &nbsp;
                                                    {{ $mtqaDone ? '✓' : '○' }} MTQA
                                                </p>
                                            </td>
                                            <td>
                                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                                    @if($cp->current_review_step === 'mtqa') step-mtqa
                                                    @elseif($cp->current_review_step === 'resident_engineer') step-re
                                                    @elseif($cp->current_review_step === 'provincial_engineer') step-pe
                                                    @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 @endif">
                                                    {{ $cp->current_step_label ?? 'Unassigned' }}
                                                </span>
                                            </td>
                                            <td class="text-xs" style="color:var(--cp-muted)">
                                                {{ $cp->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('reviewer.concrete-pouring.show', $cp) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-600 text-white text-xs font-semibold rounded-lg hover:bg-gray-700 transition">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9">
                                                <div class="rv-empty">
                                                    <div class="rv-empty-icon">📭</div>
                                                    <p class="rv-empty-msg">No pending requests found.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ══════════════════════════════════════
                 TAB: APPROVED
            ══════════════════════════════════════ --}}
            @if($activeTab === 'approved')
                <div>
                    <h2 class="rv-section-heading">
                        <i class="fas fa-check-circle text-green-500"></i>
                        Approved Requests
                        <span class="text-sm font-normal ml-2 px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                            {{ $allApproved->count() }}
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
                                        <th>Approved By (MTQA)</th>
                                        <th>Decision Date</th>
                                        <th>Remarks</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allApproved as $cp)
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
                                                <span class="font-medium text-sm">{{ $cp->meMtqaChecker?->name ?? '—' }}</span>
                                            </td>
                                            <td class="text-xs" style="color:var(--cp-muted)">
                                                {{ $cp->me_mtqa_date?->format('M d, Y') ?? '—' }}
                                            </td>
                                            <td>
                                                @if($cp->me_mtqa_remarks)
                                                    <span class="text-xs" style="color:var(--cp-muted)" title="{{ $cp->me_mtqa_remarks }}">
                                                        {{ Str::limit($cp->me_mtqa_remarks, 40) }}
                                                    </span>
                                                @else
                                                    <span class="text-xs" style="color:var(--cp-muted)">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('reviewer.concrete-pouring.show', $cp) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9">
                                                <div class="rv-empty">
                                                    <div class="rv-empty-icon">📋</div>
                                                    <p class="rv-empty-msg">No approved requests yet.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ══════════════════════════════════════
                 TAB: DISAPPROVED
            ══════════════════════════════════════ --}}
            @if($activeTab === 'disapproved')
                <div>
                    <h2 class="rv-section-heading">
                        <i class="fas fa-times-circle text-red-500"></i>
                        Disapproved Requests
                        <span class="text-sm font-normal ml-2 px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                            {{ $allDisapproved->count() }}
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
                                        <th>Disapproved By (MTQA)</th>
                                        <th>Decision Date</th>
                                        <th>Reason</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allDisapproved as $cp)
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
                                                <span class="font-medium text-sm">{{ $cp->meMtqaChecker?->name ?? '—' }}</span>
                                            </td>
                                            <td class="text-xs" style="color:var(--cp-muted)">
                                                {{ $cp->me_mtqa_date?->format('M d, Y') ?? '—' }}
                                            </td>
                                            <td>
                                                @if($cp->me_mtqa_remarks)
                                                    <span class="text-xs" style="color:var(--cp-muted)" title="{{ $cp->me_mtqa_remarks }}">
                                                        {{ Str::limit($cp->me_mtqa_remarks, 40) }}
                                                    </span>
                                                @else
                                                    <span class="text-xs" style="color:var(--cp-muted)">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('reviewer.concrete-pouring.show', $cp) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9">
                                                <div class="rv-empty">
                                                    <div class="rv-empty-icon">✅</div>
                                                    <p class="rv-empty-msg">No disapproved requests.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ══════════════════════════════════════
                 TAB: MY HISTORY (completed by me)
            ══════════════════════════════════════ --}}
            @if($activeTab === 'completed')
                <div>
                    <h2 class="rv-section-heading">
                        <i class="fas fa-history text-purple-500"></i>
                        My Review History
                        <span class="text-sm font-normal ml-2 px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                            {{ $completed->count() }}
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
                                        <th>My Role in Pipeline</th>
                                        <th>Final Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($completed as $cp)
                                        @php
                                            $user = Auth::user();
                                            $myStep = null;
                                            if ($cp->resident_engineer_user_id == $user->id && !is_null($cp->re_date)) {
                                                $myStep = 'Step 1 — RE Review';
                                            } elseif ($cp->noted_by_user_id == $user->id && !is_null($cp->noted_date)) {
                                                $myStep = 'Step 2 — PE Note';
                                            } elseif ($cp->me_mtqa_user_id == $user->id && !is_null($cp->me_mtqa_date)) {
                                                $myStep = 'Step 3 — MTQA Final';
                                            }
                                        @endphp
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
                                                @if($myStep)
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                                        @if(str_contains($myStep,'RE')) step-re
                                                        @elseif(str_contains($myStep,'PE')) step-pe
                                                        @else step-mtqa @endif">
                                                        {{ $myStep }}
                                                    </span>
                                                @else
                                                    <span class="text-xs" style="color:var(--cp-muted)">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="cp-badge {{ $cp->status }}">
                                                    <span class="cp-badge-dot" style="background:currentColor;border-radius:50%"></span>
                                                    {{ ucfirst($cp->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('reviewer.concrete-pouring.show', $cp) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-600 text-white text-xs font-semibold rounded-lg hover:bg-purple-700 transition">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8">
                                                <div class="rv-empty">
                                                    <div class="rv-empty-icon">📝</div>
                                                    <p class="rv-empty-msg">You haven't reviewed any requests yet.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>