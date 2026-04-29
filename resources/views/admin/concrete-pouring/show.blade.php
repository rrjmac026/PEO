{{--
    admin/concrete-pouring/show.blade.php
    Pipeline: Resident Engineer → Provincial Engineer → ME/MTQA (Final Decision)
--}}
<x-app-layout>

    @push('styles')
        @include('user.concrete-pouring._cp-styles')
        <style>
            .cp-sig-card {
                display: flex; flex-direction: column; gap: 6px;
                padding: 16px;
                background: var(--cp-surface2);
                border: 1px solid var(--cp-border);
                border-radius: 10px;
            }
            .cp-sig-card-label {
                font-size: 11px; font-weight: 700; text-transform: uppercase;
                letter-spacing: 0.5px; color: var(--cp-muted);
            }
            .cp-sig-card-name {
                font-size: 13px; font-weight: 600; color: var(--cp-text);
            }
            .cp-sig-card img {
                max-width: 220px; max-height: 80px;
                border: 1px solid var(--cp-border);
                border-radius: 6px; padding: 4px;
                background: var(--cp-surface);
                margin-top: 6px;
            }
            .cp-sig-card .cp-sig-none {
                font-size: 12px; color: var(--cp-muted); font-style: italic;
                margin-top: 4px;
            }
            .cp-sig-signed-badge {
                display: inline-flex; align-items: center; gap: 5px;
                font-size: 11px; font-weight: 700; color: #059669;
                background: rgba(5,150,105,0.08);
                border: 1px solid rgba(5,150,105,0.2);
                border-radius: 20px;
                padding: 2px 9px;
                margin-top: 5px;
            }
            .dark .cp-sig-signed-badge {
                color: #34d399;
                background: rgba(52,211,153,0.08);
                border-color: rgba(52,211,153,0.2);
            }
        </style>
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Concrete Pouring — Detail
            </h2>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('admin.concrete-pouring.print', $concretePouring) }}"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    <i class="fas fa-print mr-2"></i> Print
                </a>
                <a href="{{ route('admin.concrete-pouring.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 cp-wrap">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Flash --}}
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
                    <span class="cp-req-id">{{ $concretePouring->reference_number }}</span>
                    <div>
                        <div class="cp-project-name">{{ $concretePouring->project_name }}</div>
                        <div class="cp-project-loc">
                            <i class="fas fa-map-marker-alt text-xs mr-1"></i> {{ $concretePouring->location }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="cp-badge {{ $concretePouring->status }}">
                        <span class="cp-badge-dot" style="background:currentColor;border-radius:50%"></span>
                        {{ ucfirst($concretePouring->status) }}
                    </span>

                    {{-- Only show assign button if not yet assigned --}}
                    @if($concretePouring->status === 'requested' && is_null($concretePouring->assigned_by_admin_id))
                        <a href="{{ route('admin.concrete-pouring.assign-form', $concretePouring) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-user-plus"></i> Assign Reviewers
                        </a>
                    @endif
                </div>
            </div>

            {{-- Meta --}}
            <div class="cp-meta-row">
                <div class="cp-meta-chip">🏗 <strong>{{ $concretePouring->contractor }}</strong></div>
                <div class="cp-meta-chip">📐 <strong>{{ number_format($concretePouring->estimated_volume, 2) }} m³</strong></div>
                <div class="cp-meta-chip">🗓 Pouring: <strong>{{ $concretePouring->pouring_datetime?->format('M d, Y H:i') ?? '—' }}</strong></div>
                <div class="cp-meta-chip">📋 Step: <strong>{{ $concretePouring->current_step_label }}</strong></div>
            </div>

            {{-- Project Info --}}
            <div class="cp-card">
                <div class="cp-card-head">
                    <div class="cp-card-head-icon cyan"><i class="fas fa-hard-hat"></i></div>
                    <span class="cp-card-title">Project Information</span>
                </div>
                <div class="cp-card-body">
                    <div class="cp-info-grid" style="grid-template-columns:repeat(3,1fr)">
                        <div class="cp-info-item">
                            <span class="cp-info-label">Part of Structure</span>
                            <span class="cp-info-value">{{ $concretePouring->part_of_structure }}</span>
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
                        <div class="cp-info-item">
                            <span class="cp-info-label">Assigned By Admin</span>
                            <span class="cp-info-value {{ !$concretePouring->assignedByAdmin ? 'empty' : '' }}">
                                {{ $concretePouring->assignedByAdmin?->name ?? 'Not yet assigned' }}
                            </span>
                        </div>
                        <div class="cp-info-item">
                            <span class="cp-info-label">Assigned At</span>
                            <span class="cp-info-value {{ !$concretePouring->assigned_at ? 'empty' : '' }}">
                                {{ $concretePouring->assigned_at?->format('M d, Y H:i') ?? '—' }}
                            </span>
                        </div>
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
            @endphp
            <div class="cp-card">
                <div class="cp-card-head">
                    <div class="cp-card-head-icon green"><i class="fas fa-tasks"></i></div>
                    <span class="cp-card-title">Pre-Pouring Checklist</span>
                    <span class="ml-auto text-sm" style="color:var(--cp-muted)">{{ $concretePouring->checklist_progress }}% complete</span>
                </div>
                <div class="cp-card-body">
                    <div class="mb-4">
                        <div style="height:6px;background:var(--cp-border);border-radius:99px;overflow:hidden">
                            <div style="height:100%;width:{{ $concretePouring->checklist_progress }}%;background:#059669;border-radius:99px"></div>
                        </div>
                    </div>
                    <div class="cp-checklist-grid">
                        @foreach($checklistItems as $field => $label)
                            <div class="cp-check-item {{ $concretePouring->$field ? 'checked' : 'unchecked' }}">
                                <span class="cp-check-icon">{{ $concretePouring->$field ? '✅' : '⬜' }}</span>
                                <span>{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Review Signatures (Admin read-only view) --}}
            @php
                $reSigUrl   = $concretePouring->resolveSignatureUrl($concretePouring->re_signature);
                $peSigUrl   = $concretePouring->resolveSignatureUrl($concretePouring->noted_by_signature);
                $mtqaSigUrl = $concretePouring->resolveSignatureUrl($concretePouring->me_mtqa_signature);
                $anySignature = $reSigUrl || $peSigUrl || $mtqaSigUrl;
            @endphp
            @if($anySignature)
            <div class="cp-card">
                <div class="cp-card-head">
                    <div class="cp-card-head-icon blue"><i class="fas fa-signature"></i></div>
                    <span class="cp-card-title">Review Signatures</span>
                    <span class="ml-auto text-xs font-medium px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                        Admin View — All Signatures Visible
                    </span>
                </div>
                <div class="cp-card-body">
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;">

                        {{-- Resident Engineer --}}
                        <div class="cp-sig-card">
                            <span class="cp-sig-card-label"><i class="fas fa-hard-hat mr-1"></i> Resident Engineer</span>
                            <span class="cp-sig-card-name">{{ $concretePouring->residentEngineer?->name ?? 'Not assigned' }}</span>
                            @if($reSigUrl)
                                <img src="{{ $reSigUrl }}" alt="Resident Engineer Signature">
                                @if($concretePouring->re_date)
                                    <span style="font-size:11px;color:var(--cp-muted);">Signed {{ $concretePouring->re_date->format('M d, Y') }}</span>
                                @endif
                            @else
                                <span class="cp-sig-none">No signature submitted yet.</span>
                            @endif
                        </div>

                        {{-- Provincial Engineer --}}
                        <div class="cp-sig-card">
                            <span class="cp-sig-card-label"><i class="fas fa-user-tie mr-1"></i> Provincial Engineer</span>
                            <span class="cp-sig-card-name">{{ $concretePouring->notedByEngineer?->name ?? 'Not assigned' }}</span>
                            @if($peSigUrl)
                                <img src="{{ $peSigUrl }}" alt="Provincial Engineer Signature">
                                @if($concretePouring->noted_date)
                                    <span style="font-size:11px;color:var(--cp-muted);">Signed {{ $concretePouring->noted_date->format('M d, Y') }}</span>
                                @endif
                            @else
                                <span class="cp-sig-none">No signature submitted yet.</span>
                            @endif
                        </div>

                        {{-- ME/MTQA (Final) --}}
                        <div class="cp-sig-card" style="border-color: rgba(16,185,129,0.3); background: rgba(16,185,129,0.04);">
                            <span class="cp-sig-card-label" style="color:#059669;">
                                <i class="fas fa-clipboard-check mr-1"></i> ME / MTQA
                                <span style="margin-left:4px;font-size:10px;background:#dcfce7;color:#16a34a;border-radius:20px;padding:1px 6px;">FINAL</span>
                            </span>
                            <span class="cp-sig-card-name">{{ $concretePouring->meMtqaChecker?->name ?? 'Not assigned' }}</span>
                            @if($mtqaSigUrl)
                                <img src="{{ $mtqaSigUrl }}" alt="ME/MTQA Signature">
                                @if($concretePouring->me_mtqa_date)
                                    <span style="font-size:11px;color:var(--cp-muted);">Signed {{ $concretePouring->me_mtqa_date->format('M d, Y') }}</span>
                                @endif
                            @else
                                <span class="cp-sig-none">No signature submitted yet.</span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            @endif

            {{-- Review Pipeline --}}
            <div class="cp-card">
                <div class="cp-card-head">
                    <div class="cp-card-head-icon blue"><i class="fas fa-project-diagram"></i></div>
                    <span class="cp-card-title">Review Pipeline</span>
                </div>
                <div class="cp-card-body">
                    <div class="cp-timeline">

                        {{-- Step 1: Resident Engineer --}}
                        @php
                            $reDone   = !is_null($concretePouring->re_date);
                            $reActive = $concretePouring->current_review_step === 'resident_engineer';
                        @endphp
                        <div class="cp-timeline-item">
                            <div class="cp-tl-icon-wrap">
                                <div class="cp-tl-icon {{ $reDone ? 'done' : ($reActive ? 'active' : 'waiting') }}">
                                    @if($reDone)<i class="fas fa-check"></i>
                                    @elseif($reActive)<i class="fas fa-clock"></i>
                                    @else<i class="fas fa-circle"></i>@endif
                                </div>
                            </div>
                            <div style="flex:1">
                                <div class="cp-tl-label">Step 1 — Resident Engineer Review</div>
                                <div class="cp-tl-name">{{ $concretePouring->residentEngineer?->name ?? 'Not assigned' }}</div>
                                @if($concretePouring->re_date)
                                    <div class="cp-tl-date">Reviewed: {{ $concretePouring->re_date->format('M d, Y') }}</div>
                                @endif
                                @if($concretePouring->re_remarks)
                                    <div class="cp-tl-remark">"{{ $concretePouring->re_remarks }}"</div>
                                @endif
                                @if($reSigUrl)
                                    <span class="cp-sig-signed-badge"><i class="fas fa-pen-nib"></i> Signature on file</span>
                                @endif
                            </div>
                            <div>
                                @if($reDone)<span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Done</span>
                                @elseif($reActive)<span class="cp-badge requested" style="font-size:11px;padding:3px 8px">In Progress</span>
                                @else<span style="font-size:11px;color:var(--cp-muted)">Waiting</span>@endif
                            </div>
                        </div>

                        {{-- Step 2: Provincial Engineer --}}
                        @php
                            $peDone   = !is_null($concretePouring->noted_date);
                            $peActive = $concretePouring->current_review_step === 'provincial_engineer';
                        @endphp
                        <div class="cp-timeline-item">
                            <div class="cp-tl-icon-wrap">
                                <div class="cp-tl-icon {{ $peDone ? 'done' : ($peActive ? 'active' : 'waiting') }}">
                                    @if($peDone)<i class="fas fa-check"></i>
                                    @elseif($peActive)<i class="fas fa-clock"></i>
                                    @else<i class="fas fa-circle"></i>@endif
                                </div>
                            </div>
                            <div style="flex:1">
                                <div class="cp-tl-label">Step 2 — Noted by Provincial Engineer</div>
                                <div class="cp-tl-name">{{ $concretePouring->notedByEngineer?->name ?? 'Not assigned' }}</div>
                                @if($concretePouring->noted_date)
                                    <div class="cp-tl-date">Noted: {{ $concretePouring->noted_date->format('M d, Y') }}</div>
                                @endif
                                @if($peSigUrl)
                                    <span class="cp-sig-signed-badge"><i class="fas fa-pen-nib"></i> Signature on file</span>
                                @endif
                            </div>
                            <div>
                                @if($peDone)<span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Noted</span>
                                @elseif($peActive)<span class="cp-badge requested" style="font-size:11px;padding:3px 8px">In Progress</span>
                                @else<span style="font-size:11px;color:var(--cp-muted)">Waiting</span>@endif
                            </div>
                        </div>

                        {{-- Step 3: ME/MTQA — FINAL DECISION --}}
                        @php
                            $mtqaDone   = !is_null($concretePouring->me_mtqa_date);
                            $mtqaActive = $concretePouring->current_review_step === 'mtqa';
                        @endphp
                        <div class="cp-timeline-item">
                            <div class="cp-tl-icon-wrap">
                                <div class="cp-tl-icon {{ in_array($concretePouring->status, ['approved','disapproved']) ? 'done' : ($mtqaActive ? 'active' : 'waiting') }}">
                                    @if(in_array($concretePouring->status, ['approved','disapproved']))<i class="fas fa-check"></i>
                                    @elseif($mtqaActive)<i class="fas fa-clock"></i>
                                    @else<i class="fas fa-circle"></i>@endif
                                </div>
                            </div>
                            <div style="flex:1">
                                <div class="cp-tl-label" style="display:flex;align-items:center;gap:8px;">
                                    Step 3 — ME/MTQA Final Decision
                                    <span style="font-size:10px;background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0;border-radius:20px;padding:1px 8px;font-weight:700;">FINAL</span>
                                </div>
                                <div class="cp-tl-name">{{ $concretePouring->meMtqaChecker?->name ?? 'Not assigned' }}</div>
                                @if($concretePouring->status === 'approved')
                                    <div class="cp-tl-date" style="color:#059669;">
                                        ✓ Approved on {{ $concretePouring->approved_date?->format('M d, Y') ?? '—' }}
                                    </div>
                                @elseif($concretePouring->status === 'disapproved')
                                    <div class="cp-tl-date" style="color:#dc2626;">
                                        ✗ Disapproved on {{ $concretePouring->disapproved_date?->format('M d, Y') ?? '—' }}
                                    </div>
                                @elseif($mtqaActive)
                                    <div class="cp-tl-date">Awaiting ME/MTQA decision</div>
                                @endif
                                @if($concretePouring->me_mtqa_remarks)
                                    <div class="cp-tl-remark">"{{ $concretePouring->me_mtqa_remarks }}"</div>
                                @endif
                                @if($concretePouring->approval_remarks && in_array($concretePouring->status, ['approved','disapproved']))
                                    <div class="cp-tl-remark">"{{ $concretePouring->approval_remarks }}"</div>
                                @endif
                                @if($mtqaSigUrl)
                                    <span class="cp-sig-signed-badge"><i class="fas fa-pen-nib"></i> Signature on file</span>
                                @endif
                            </div>
                            <div>
                                @if($concretePouring->status === 'approved')
                                    <span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Approved</span>
                                @elseif($concretePouring->status === 'disapproved')
                                    <span class="cp-badge disapproved" style="font-size:11px;padding:3px 8px">Disapproved</span>
                                @elseif($mtqaActive)
                                    <span class="cp-badge requested" style="font-size:11px;padding:3px 8px">Pending Decision</span>
                                @else
                                    <span style="font-size:11px;color:var(--cp-muted)">Waiting</span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Delete --}}
            <div class="flex justify-end">
                <form action="{{ route('admin.concrete-pouring.destroy', $concretePouring) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this request? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash"></i> Delete Request
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>