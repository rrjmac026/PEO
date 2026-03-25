<x-app-layout>

    @push('styles')
        @include('user.concrete-pouring._cp-styles')
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Concrete Pouring Request
            </h2>
            <div class="flex gap-2 flex-wrap">
                @if($concretePouring->status === 'requested' && is_null($concretePouring->me_mtqa_user_id))
                    <a href="{{ route('user.concrete-pouring.edit', $concretePouring) }}"
                       class="inline-flex items-center px-4 py-2 bg-yellow-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                @endif
                <a href="{{ route('user.concrete-pouring.print', $concretePouring) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-gray-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                    <i class="fas fa-print mr-2"></i> Print
                </a>
                <a href="{{ route('user.concrete-pouring.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 cp-wrap">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Hero --}}
            <div class="cp-hero">
                <div class="cp-hero-left">
                    <div>
                        <span class="cp-req-id">{{ $concretePouring->reference_number }}</span>
                    </div>
                    <div>
                        <div class="cp-project-name">{{ $concretePouring->project_name }}</div>
                        <div class="cp-project-loc">
                            <i class="fas fa-map-marker-alt text-xs mr-1"></i> {{ $concretePouring->location }}
                            @if($concretePouring->station_limits_section)
                                &nbsp;·&nbsp; {{ $concretePouring->station_limits_section }}
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <span class="cp-badge {{ $concretePouring->status }}">
                        <span class="cp-badge-dot" style="background:currentColor;border-radius:50%"></span>
                        {{ ucfirst($concretePouring->status) }}
                    </span>
                </div>
            </div>

            {{-- Meta chips --}}
            <div class="cp-meta-row">
                <div class="cp-meta-chip">
                    🕐 Submitted <strong>{{ $concretePouring->created_at->format('M d, Y · H:i') }}</strong>
                </div>
                <div class="cp-meta-chip">
                    📋 Step: <strong>{{ $concretePouring->current_step_label }}</strong>
                </div>
                @if($concretePouring->pouring_datetime)
                    <div class="cp-meta-chip">
                        🗓 Pouring: <strong>{{ $concretePouring->pouring_datetime->format('M d, Y H:i') }}</strong>
                    </div>
                @endif
            </div>

            {{-- Project Details --}}
            <div class="cp-card">
                <div class="cp-card-head">
                    <div class="cp-card-head-icon cyan"><i class="fas fa-hard-hat"></i></div>
                    <span class="cp-card-title">Project Details</span>
                </div>
                <div class="cp-card-body">
                    <div class="cp-info-grid cp-info-grid--three" style="grid-template-columns:repeat(3,1fr)">
                        <div class="cp-info-item">
                            <span class="cp-info-label">Project Name</span>
                            <span class="cp-info-value">{{ $concretePouring->project_name }}</span>
                        </div>
                        <div class="cp-info-item">
                            <span class="cp-info-label">Location</span>
                            <span class="cp-info-value">{{ $concretePouring->location }}</span>
                        </div>
                        <div class="cp-info-item">
                            <span class="cp-info-label">Contractor</span>
                            <span class="cp-info-value">{{ $concretePouring->contractor }}</span>
                        </div>
                        <div class="cp-info-item">
                            <span class="cp-info-label">Part of Structure</span>
                            <span class="cp-info-value">{{ $concretePouring->part_of_structure }}</span>
                        </div>
                        <div class="cp-info-item">
                            <span class="cp-info-label">Estimated Volume</span>
                            <span class="cp-info-value">{{ number_format($concretePouring->estimated_volume, 2) }} m³</span>
                        </div>
                        <div class="cp-info-item">
                            <span class="cp-info-label">Station / Section</span>
                            <span class="cp-info-value {{ !$concretePouring->station_limits_section ? 'empty' : '' }}">
                                {{ $concretePouring->station_limits_section ?? 'Not specified' }}
                            </span>
                        </div>
                        <div class="cp-info-item">
                            <span class="cp-info-label">Requested By</span>
                            <span class="cp-info-value">{{ $concretePouring->requestedBy?->name ?? '—' }}</span>
                        </div>
                        @if($concretePouring->workRequest)
                            <div class="cp-info-item">
                                <span class="cp-info-label">Linked Work Request</span>
                                <span class="cp-info-value mono">#{{ str_pad($concretePouring->work_request_id,6,'0',STR_PAD_LEFT) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Checklist --}}
            @php
                $checklistItems = [
                    'concrete_vibrator'               => 'Concrete Vibrator',
                    'field_density_test'              => 'Field Density Test',
                    'protective_covering_materials'   => 'Protective Covering Materials',
                    'beam_cylinder_molds'             => 'Beam / Cylinder Molds',
                    'warning_signs_barricades'        => 'Warning Signs & Barricades',
                    'curing_materials'                => 'Curing Materials',
                    'concrete_saw'                    => 'Concrete Saw',
                    'slump_cones'                     => 'Slump Cones',
                    'concrete_block_spacer'           => 'Concrete Block Spacer',
                    'plumbness'                       => 'Plumbness',
                    'finishing_tools_equipment'       => 'Finishing Tools & Equipment',
                    'quality_of_materials'            => 'Quality of Materials',
                    'line_grade_alignment'            => 'Line, Grade & Alignment',
                    'lighting_system'                 => 'Lighting System',
                    'required_construction_equipment' => 'Required Construction Equipment',
                    'electrical_layout'               => 'Electrical Layout',
                    'rebar_sizes_spacing'             => 'Rebar Sizes & Spacing',
                    'plumbing_layout'                 => 'Plumbing Layout',
                    'rebars_installation'             => 'Rebars Installation',
                    'falseworks_formworks'            => 'Falseworks / Formworks',
                ];
                $checkedCount = collect(array_keys($checklistItems))->filter(fn($f) => $concretePouring->$f)->count();
            @endphp
            <div class="cp-card">
                <div class="cp-card-head">
                    <div class="cp-card-head-icon green"><i class="fas fa-tasks"></i></div>
                    <span class="cp-card-title">Pre-Pouring Checklist</span>
                    <span class="ml-auto text-sm font-semibold" style="color:var(--cp-muted)">
                        {{ $checkedCount }} / {{ count($checklistItems) }} items
                    </span>
                </div>
                <div class="cp-card-body">
                    {{-- Progress bar --}}
                    <div class="mb-4">
                        <div style="height:6px;background:var(--cp-border);border-radius:99px;overflow:hidden">
                            <div style="height:100%;width:{{ $concretePouring->checklist_progress }}%;background:#059669;border-radius:99px;transition:width .4s"></div>
                        </div>
                        <p class="text-xs mt-1" style="color:var(--cp-muted)">{{ $concretePouring->checklist_progress }}% complete</p>
                    </div>
                    <div class="cp-checklist-grid">
                        @foreach($checklistItems as $field => $label)
                            <div class="cp-check-item {{ $concretePouring->$field ? 'checked' : 'unchecked' }}">
                                <span class="cp-check-icon">
                                    {{ $concretePouring->$field ? '✅' : '⬜' }}
                                </span>
                                <span>{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Review Pipeline --}}
            <div class="cp-card">
                <div class="cp-card-head">
                    <div class="cp-card-head-icon blue"><i class="fas fa-project-diagram"></i></div>
                    <span class="cp-card-title">Review Pipeline</span>
                </div>
                <div class="cp-card-body">
                    <div class="cp-timeline">

                        {{-- ME/MTQA --}}
                        @php
                            $mtqaDone   = !is_null($concretePouring->me_mtqa_date);
                            $mtqaActive = $concretePouring->current_review_step === 'mtqa';
                        @endphp
                        <div class="cp-timeline-item">
                            <div class="cp-tl-icon-wrap">
                                <div class="cp-tl-icon {{ $mtqaDone ? 'done' : ($mtqaActive ? 'active' : 'waiting') }}">
                                    @if($mtqaDone) <i class="fas fa-check"></i>
                                    @elseif($mtqaActive) <i class="fas fa-clock"></i>
                                    @else <i class="fas fa-circle"></i>
                                    @endif
                                </div>
                            </div>
                            <div style="flex:1">
                                <div class="cp-tl-label">Step 1 — ME / MTQA Review</div>
                                <div class="cp-tl-name">
                                    {{ $concretePouring->meMtqaChecker?->name ?? 'Not assigned' }}
                                </div>
                                @if($concretePouring->me_mtqa_date)
                                    <div class="cp-tl-date">Reviewed: {{ $concretePouring->me_mtqa_date->format('M d, Y') }}</div>
                                @endif
                                @if($concretePouring->me_mtqa_remarks)
                                    <div class="cp-tl-remark">"{{ $concretePouring->me_mtqa_remarks }}"</div>
                                @endif
                            </div>
                            <div>
                                @if($mtqaDone)
                                    <span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Done</span>
                                @elseif($mtqaActive)
                                    <span class="cp-badge requested" style="font-size:11px;padding:3px 8px">In Progress</span>
                                @else
                                    <span style="font-size:11px;color:var(--cp-muted)">Waiting</span>
                                @endif
                            </div>
                        </div>

                        {{-- Resident Engineer --}}
                        @php
                            $reDone   = !is_null($concretePouring->re_date);
                            $reActive = $concretePouring->current_review_step === 'resident_engineer';
                        @endphp
                        <div class="cp-timeline-item">
                            <div class="cp-tl-icon-wrap">
                                <div class="cp-tl-icon {{ $reDone ? 'done' : ($reActive ? 'active' : 'waiting') }}">
                                    @if($reDone) <i class="fas fa-check"></i>
                                    @elseif($reActive) <i class="fas fa-clock"></i>
                                    @else <i class="fas fa-circle"></i>
                                    @endif
                                </div>
                            </div>
                            <div style="flex:1">
                                <div class="cp-tl-label">Step 2 — Resident Engineer Review</div>
                                <div class="cp-tl-name">
                                    {{ $concretePouring->residentEngineer?->name ?? 'Not assigned' }}
                                </div>
                                @if($concretePouring->re_date)
                                    <div class="cp-tl-date">Reviewed: {{ $concretePouring->re_date->format('M d, Y') }}</div>
                                @endif
                                @if($concretePouring->re_remarks)
                                    <div class="cp-tl-remark">"{{ $concretePouring->re_remarks }}"</div>
                                @endif
                            </div>
                            <div>
                                @if($reDone)
                                    <span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Done</span>
                                @elseif($reActive)
                                    <span class="cp-badge requested" style="font-size:11px;padding:3px 8px">In Progress</span>
                                @else
                                    <span style="font-size:11px;color:var(--cp-muted)">Waiting</span>
                                @endif
                            </div>
                        </div>

                        {{-- Provincial Engineer --}}
                        @php
                            $peDone   = !is_null($concretePouring->noted_date);
                            $peActive = $concretePouring->current_review_step === 'provincial_engineer';
                        @endphp
                        <div class="cp-timeline-item">
                            <div class="cp-tl-icon-wrap">
                                <div class="cp-tl-icon {{ $peDone ? 'done' : ($peActive ? 'active' : 'waiting') }}">
                                    @if($peDone) <i class="fas fa-check"></i>
                                    @elseif($peActive) <i class="fas fa-clock"></i>
                                    @else <i class="fas fa-circle"></i>
                                    @endif
                                </div>
                            </div>
                            <div style="flex:1">
                                <div class="cp-tl-label">Step 3 — Noted by Provincial Engineer</div>
                                <div class="cp-tl-name">
                                    {{ $concretePouring->notedByEngineer?->name ?? 'Not assigned' }}
                                </div>
                                @if($concretePouring->noted_date)
                                    <div class="cp-tl-date">Noted: {{ $concretePouring->noted_date->format('M d, Y') }}</div>
                                @endif
                            </div>
                            <div>
                                @if($peDone)
                                    <span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Done</span>
                                @elseif($peActive)
                                    <span class="cp-badge requested" style="font-size:11px;padding:3px 8px">In Progress</span>
                                @else
                                    <span style="font-size:11px;color:var(--cp-muted)">Waiting</span>
                                @endif
                            </div>
                        </div>

                        {{-- Admin Final --}}
                        @php $adminActive = $concretePouring->current_review_step === 'admin_final'; @endphp
                        <div class="cp-timeline-item">
                            <div class="cp-tl-icon-wrap">
                                <div class="cp-tl-icon {{ in_array($concretePouring->status,['approved','disapproved']) ? 'done' : ($adminActive ? 'active' : 'waiting') }}">
                                    @if(in_array($concretePouring->status,['approved','disapproved']))
                                        <i class="fas fa-check"></i>
                                    @elseif($adminActive)
                                        <i class="fas fa-clock"></i>
                                    @else
                                        <i class="fas fa-circle"></i>
                                    @endif
                                </div>
                            </div>
                            <div style="flex:1">
                                <div class="cp-tl-label">Step 4 — Admin Final Decision</div>
                                <div class="cp-tl-name">
                                    @if($concretePouring->status === 'approved')
                                        Approved by {{ $concretePouring->approver?->name ?? '—' }}
                                    @elseif($concretePouring->status === 'disapproved')
                                        Disapproved by {{ $concretePouring->disapprover?->name ?? '—' }}
                                    @else
                                        Awaiting admin decision
                                    @endif
                                </div>
                                @if($concretePouring->approval_remarks)
                                    <div class="cp-tl-remark">"{{ $concretePouring->approval_remarks }}"</div>
                                @endif
                            </div>
                            <div>
                                @if($concretePouring->status === 'approved')
                                    <span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Approved</span>
                                @elseif($concretePouring->status === 'disapproved')
                                    <span class="cp-badge disapproved" style="font-size:11px;padding:3px 8px">Disapproved</span>
                                @elseif($adminActive)
                                    <span class="cp-badge requested" style="font-size:11px;padding:3px 8px">Pending</span>
                                @else
                                    <span style="font-size:11px;color:var(--cp-muted)">Waiting</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Danger zone --}}
            @if($concretePouring->status === 'requested' && is_null($concretePouring->me_mtqa_user_id))
                <div class="cp-danger-zone">
                    <div class="cp-danger-text">
                        <h4>Delete this request</h4>
                        <p>This action is permanent and cannot be undone.</p>
                    </div>
                    <form action="{{ route('user.concrete-pouring.destroy', $concretePouring) }}" method="POST"
                          onsubmit="return confirm('Are you sure? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="cp-btn-danger">
                            <i class="fas fa-trash"></i> Delete Request
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>