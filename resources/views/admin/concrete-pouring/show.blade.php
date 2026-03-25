<x-app-layout>

    @push('styles')
    <style>
        :root {
            --cp-surface:   #ffffff;
            --cp-surface2:  #f8fafc;
            --cp-border:    #e2e8f0;
            --cp-text:      #0f172a;
            --cp-text-sec:  #334155;
            --cp-muted:     #64748b;
            --cp-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        }
        .dark {
            --cp-surface:   #1a1f2e;
            --cp-surface2:  #1e2335;
            --cp-border:    #2a3050;
            --cp-text:      #e8eaf6;
            --cp-text-sec:  #c5cae9;
            --cp-muted:     #7c85a8;
        }
        .cp-page-title { font-size: 28px; font-weight: 800; color: var(--cp-text); }
        .cp-page-sub   { font-size: 14px; color: var(--cp-muted); margin-top: 4px; }
        .cp-panel { background: var(--cp-surface); border: 1px solid var(--cp-border); border-radius: 12px; overflow: hidden; box-shadow: var(--cp-shadow); }
        .cp-panel-body { padding: 20px 24px; }
        .cp-input { width: 100%; background: var(--cp-surface2); border: 1px solid var(--cp-border); border-radius: 8px; padding: 8px 14px; font-size: 14px; color: var(--cp-text); outline: none; }
        .cp-input:focus { border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,.12); }
        .cp-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid; cursor: pointer; transition: all .15s; text-decoration: none; white-space: nowrap; }
        .cp-btn-dark { background: #1e293b; border-color: #1e293b; color: #fff; }
        .cp-btn-dark:hover { background: #334155; }
        .dark .cp-btn-dark { background: #e2e8f0; border-color: #e2e8f0; color: #0f172a; }
        .cp-btn-secondary { background: var(--cp-surface2); border-color: var(--cp-border); color: var(--cp-text-sec); }
        .cp-btn-blue   { background: #2563eb; border-color: #2563eb; color: #fff; }
        .cp-btn-blue:hover { background: #1d4ed8; }
        .cp-btn-purple { background: #7c3aed; border-color: #7c3aed; color: #fff; }
        .cp-btn-purple:hover { background: #6d28d9; }
        .cp-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid; }
        .cp-badge-dot { width: 6px; height: 6px; border-radius: 50%; }
        .cp-badge.approved    { color: #047857; border-color: #6ee7b7; background: #f0fdf4; }
        .cp-badge.disapproved { color: #b91c1c; border-color: #fca5a5; background: #fff1f2; }
        .cp-badge.requested   { color: #92400e; border-color: #fcd34d; background: #fffbeb; }
        .dark .cp-badge.approved    { color: #34d399; border-color: rgba(52,211,153,.3);  background: rgba(52,211,153,.08); }
        .dark .cp-badge.disapproved { color: #f87171; border-color: rgba(248,113,113,.3); background: rgba(248,113,113,.08); }
        .dark .cp-badge.requested   { color: #fbbf24; border-color: rgba(251,191,36,.3);  background: rgba(251,191,36,.08); }
        .cp-progress-wrap { display: flex; align-items: center; gap: 8px; }
        .cp-progress-track { width: 64px; height: 6px; border-radius: 99px; background: #e2e8f0; overflow: hidden; flex-shrink: 0; }
        .dark .cp-progress-track { background: rgba(255,255,255,.12); }
        .cp-progress-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #3b82f6, #6366f1); }
        .cp-progress-label { font-size: 12px; color: var(--cp-muted); }
        .cp-action-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 7px; font-size: 13px; border: 1px solid; cursor: pointer; transition: all .15s; text-decoration: none; background: none; }
        .cp-action-btn.view  { color: #ea580c; border-color: #fed7aa; background: #fff7ed; }
        .cp-action-btn.print { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }
        .cp-action-btn.view:hover  { background: #ffedd5; }
        .cp-action-btn.print:hover { background: #dbeafe; }
        .dark .cp-action-btn.view  { color: #fb923c; border-color: rgba(251,146,60,.3); background: rgba(251,146,60,.1); }
        .dark .cp-action-btn.print { color: #60a5fa; border-color: rgba(96,165,250,.3); background: rgba(96,165,250,.1); }
        .cp-alert { display: flex; align-items: flex-start; justify-content: space-between; padding: 12px 16px; border-radius: 10px; border: 1px solid; margin-bottom: 16px; font-size: 14px; }
        .cp-alert.success { background: #f0fdf4; border-color: #86efac; color: #166534; }
        .dark .cp-alert.success { background: rgba(5,150,105,.12); border-color: rgba(52,211,153,.3); color: #6ee7b7; }
        .cp-alert-close { background: none; border: none; cursor: pointer; font-size: 14px; opacity: .6; color: inherit; padding: 0; margin-left: 12px; }
        .cp-alert-close:hover { opacity: 1; }
    </style>
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="cp-page-title">
                    Concrete Pouring #{{ $concretePouring->id }}
                </h2>
                @if($concretePouring->reference_number)
                    <p class="cp-page-sub">
                        {{ $concretePouring->reference_number }}
                    </p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.concrete-pouring.print', $concretePouring) }}"
                   target="_blank"
                   class="cp-btn cp-btn-blue">
                    <i class="fas fa-print"></i> Print
                </a>
                <a href="{{ route('admin.concrete-pouring.index') }}"
                   class="cp-btn cp-btn-dark">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="cp-alert success">
                    <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                    <button class="cp-alert-close" onclick="this.closest('.cp-alert').remove()"><i class="fas fa-times"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div class="cp-alert success">
                    <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
                    <button class="cp-alert-close" onclick="this.closest('.cp-alert').remove()"><i class="fas fa-times"></i></button>
                </div>
            @endif
            @if(session('info'))
                <div class="cp-alert success">
                    <span><i class="fas fa-info-circle mr-2"></i>{{ session('info') }}</span>
                    <button class="cp-alert-close" onclick="this.closest('.cp-alert').remove()"><i class="fas fa-times"></i></button>
                </div>
            @endif

            {{-- ── Status Hero ── --}}
            <div class="cp-panel cp-panel-body flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Current Status</p>
                    <span class="cp-badge {{ match($concretePouring->status) {
                        'approved' => 'approved',
                        'disapproved' => 'disapproved',
                        'requested' => 'requested',
                        default => 'requested'
                    } }}">
                        <span class="cp-badge-dot"></span>{{ ucfirst($concretePouring->status) }}
                    </span>
                    @if($concretePouring->current_review_step)
                        <span class="ml-3 text-sm" style="color: var(--cp-muted);">
                            Current step: <strong>{{ $concretePouring->current_step_label }}</strong>
                        </span>
                    @endif
                </div>
                {{-- Admin action buttons --}}
                <div class="flex gap-2 flex-wrap">
                    {{-- Assign / Re-assign (only when still requested and not yet assigned) --}}
                    @if($concretePouring->status === 'requested')
                        <a href="{{ route('admin.concrete-pouring.assign-form', $concretePouring) }}"
                           class="cp-btn cp-btn-purple">
                            <i class="fas fa-user-plus"></i>
                            {{ $concretePouring->assigned_by_admin_id ? 'Re-assign Reviewers' : 'Assign Reviewers' }}
                        </a>
                    @endif

                    {{-- Final decision (only when at admin_final step) --}}
                    @if($concretePouring->current_review_step === 'admin_final')
                        <a href="{{ route('admin.concrete-pouring.decision-form', $concretePouring) }}"
                           class="cp-btn cp-btn-blue">
                            <i class="fas fa-check-circle"></i> Final Decision
                        </a>
                    @endif

                    {{-- Delete --}}
                    <form action="{{ route('admin.concrete-pouring.destroy', $concretePouring) }}" method="POST"
                          onsubmit="return confirm('Delete this concrete pouring request?')" style="display: inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="cp-btn" style="background: #dc2626; border-color: #dc2626; color: #fff;">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── Project Information ── --}}
            <div class="cp-panel">
                <div class="px-6 py-4 border-b" style="border-color: var(--cp-border); background: var(--cp-surface2);">
                    <h3 style="font-weight: 600; color: var(--cp-text);">📁 Project Information</h3>
                </div>
                <div class="cp-panel-body grid grid-cols-1 md:grid-cols-2 gap-5">
                    @php
                        $infoFields = [
                            'Reference No.'        => $concretePouring->reference_number,
                            'Project Name'         => $concretePouring->project_name,
                            'Location'             => $concretePouring->location,
                            'Contractor'           => $concretePouring->contractor,
                            'Part of Structure'    => $concretePouring->part_of_structure,
                            'Station Limits'       => $concretePouring->station_limits_section,
                            'Estimated Volume'     => $concretePouring->estimated_volume ? $concretePouring->estimated_volume . ' m³' : null,
                            'Pouring Date & Time'  => $concretePouring->pouring_datetime?->format('M d, Y H:i'),
                            'Requested By'         => $concretePouring->requestedBy?->name,
                            'Linked Work Request'  => $concretePouring->workRequest
                                                        ? '#' . $concretePouring->workRequest->id . ' — ' . $concretePouring->workRequest->name_of_project
                                                        : null,
                        ];
                    @endphp
                    @foreach($infoFields as $label => $value)
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color: var(--cp-muted);">{{ $label }}</p>
                            <p style="color: var(--cp-text); font-size: 14px; {{ !$value ? 'font-style: italic; color: var(--cp-muted);' : '' }}">
                                {{ $value ?? '—' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Checklist ── --}}
            <div class="cp-panel">
                <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: var(--cp-border); background: var(--cp-surface2);">
                    <h3 style="font-weight: 600; color: var(--cp-text);">✅ Checklist</h3>
                    <span style="font-size: 14px; font-weight: 500; color: var(--cp-text-sec);">
                        {{ $concretePouring->checklist_progress }}% complete
                    </span>
                </div>
                <div class="cp-panel-body">
                    <div style="width: 100%; height: 6px; border-radius: 99px; background: #e2e8f0; overflow: hidden; margin-bottom: 16px;">
                        <div style="height: 100%; border-radius: 99px; background: linear-gradient(90deg, #3b82f6, #6366f1); width: {{ $concretePouring->checklist_progress }}%;"></div>
                    </div>
                </div>
                @php
                    $checklistItems = [
                        'concrete_vibrator'               => 'Concrete Vibrator',
                        'field_density_test'              => 'Field Density Test (FDT)',
                        'protective_covering_materials'   => 'Protective Covering Materials',
                        'beam_cylinder_molds'             => 'BEAM/Cylinder Molds',
                        'warning_signs_barricades'        => 'Warning Signs/Barricades/Flagmen',
                        'curing_materials'                => 'Curing Materials',
                        'concrete_saw'                    => 'Concrete Saw',
                        'slump_cones'                     => 'Slump Cones',
                        'concrete_block_spacer'           => 'Concrete Block Spacer',
                        'plumbness'                       => 'Plumbness',
                        'finishing_tools_equipment'       => 'Finishing Tools/Equipment',
                        'quality_of_materials'            => 'Quality of Materials',
                        'line_grade_alignment'            => 'Line and Grade Alignment',
                        'lighting_system'                 => 'Lighting System',
                        'required_construction_equipment' => 'Required Construction Equipment',
                        'electrical_layout'               => 'Electrical Layout (Roughing-Ins)',
                        'rebar_sizes_spacing'             => 'Rebar Sizes, Spacing and Number',
                        'plumbing_layout'                 => 'Plumbing Layout (Roughing-Ins)',
                        'rebars_installation'             => 'Rebars Installation Requirement',
                        'falseworks_formworks'            => 'Falseworks/Formworks Adequacy',
                    ];
                @endphp
                <div class="cp-panel-body grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($checklistItems as $field => $label)
                        <div class="flex items-center gap-2" style="font-size: 14px;">
                            @if($concretePouring->$field)
                                <span style="color: #10b981;"><i class="fas fa-check-circle"></i></span>
                            @else
                                <span style="color: var(--cp-border);"><i class="fas fa-circle"></i></span>
                            @endif
                            <span style="color: {{ $concretePouring->$field ? 'var(--cp-text)' : 'var(--cp-muted)' }};">
                                {{ $label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Review Pipeline ── --}}
            <div class="cp-panel">
                <div class="px-6 py-4 border-b" style="border-color: var(--cp-border); background: var(--cp-surface2);">
                    <h3 style="font-weight: 600; color: var(--cp-text);">📋 Review Pipeline</h3>
                </div>
                <div class="cp-panel-body space-y-4">
                    @php
                        $steps = [
                            [
                                'step'      => 'mtqa',
                                'label'     => 'ME/MTQA',
                                'icon'      => '🔬',
                                'assigned'  => $concretePouring->meMtqaChecker,
                                'done'      => (bool) $concretePouring->me_mtqa_date,
                                'remarks'   => $concretePouring->me_mtqa_remarks,
                                'date'      => $concretePouring->me_mtqa_date?->format('M d, Y'),
                            ],
                            [
                                'step'      => 'resident_engineer',
                                'label'     => 'Resident Engineer',
                                'icon'      => '🛠️',
                                'assigned'  => $concretePouring->residentEngineer,
                                'done'      => (bool) $concretePouring->re_date,
                                'remarks'   => $concretePouring->re_remarks,
                                'date'      => $concretePouring->re_date?->format('M d, Y'),
                            ],
                            [
                                'step'      => 'provincial_engineer',
                                'label'     => 'Provincial Engineer',
                                'icon'      => '👔',
                                'assigned'  => $concretePouring->notedByEngineer,
                                'done'      => (bool) $concretePouring->noted_date,
                                'remarks'   => null,
                                'date'      => $concretePouring->noted_date?->format('M d, Y'),
                            ],
                            [
                                'step'      => 'admin_final',
                                'label'     => 'Admin Final Decision',
                                'icon'      => '⚙️',
                                'assigned'  => null,
                                'done'      => in_array($concretePouring->status, ['approved','disapproved']),
                                'remarks'   => $concretePouring->approval_remarks,
                                'date'      => $concretePouring->approved_date?->format('M d, Y')
                                               ?? $concretePouring->disapproved_date?->format('M d, Y'),
                            ],
                        ];
                    @endphp

                    @foreach($steps as $i => $s)
                        @php
                            $isCurrent = $concretePouring->current_review_step === $s['step'];
                            $isDone    = $s['done'];
                            $isSkipped = !$isCurrent && !$isDone && is_null($s['assigned']) && $s['step'] !== 'admin_final';
                        @endphp
                        <div class="flex gap-4 items-start {{ $i < count($steps)-1 ? 'pb-4 border-b' : '' }}" style="border-color: var(--cp-border);">
                            {{-- Circle --}}
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold"
                                 style="
                                    @if($isDone)
                                        background: #dcfce7; color: #16a34a;
                                    @elseif($isCurrent)
                                        background: #f0f4ff; color: #2563eb; border: 2px solid #2563eb;
                                    @else
                                        background: var(--cp-surface2); color: var(--cp-muted);
                                    @endif
                                 ">
                                @if($isDone) ✓
                                @elseif($isCurrent) →
                                @else ○
                                @endif
                            </div>
                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <p style="font-size: 14px; font-weight: 600;
                                        @if($isDone)
                                            color: #16a34a;
                                        @elseif($isCurrent)
                                            color: #2563eb;
                                        @else
                                            color: var(--cp-muted);
                                        @endif">
                                        {{ $s['icon'] }} {{ $s['label'] }}
                                        @if($isCurrent)
                                            <span style="margin-left: 8px; padding: 2px 8px; font-size: 11px; background: #eff6ff; color: #2563eb; border-radius: 999px; font-weight: 600;">Waiting</span>
                                        @endif
                                        @if($isSkipped)
                                            <span style="margin-left: 8px; font-size: 11px; color: var(--cp-muted); font-style: italic;">(skipped)</span>
                                        @endif
                                    </p>
                                    @if($s['assigned'])
                                        <span style="font-size: 12px; color: var(--cp-muted);">{{ $s['assigned']->name }}</span>
                                    @elseif($s['step'] === 'admin_final')
                                        <span style="font-size: 12px; color: var(--cp-muted);">Admin</span>
                                    @endif
                                </div>
                                @if($s['done'] && $s['date'])
                                    <p style="font-size: 12px; color: var(--cp-muted); margin-top: 4px;">Completed {{ $s['date'] }}</p>
                                @endif
                                @if($s['remarks'])
                                    <p style="font-size: 12px; color: var(--cp-text-sec); margin-top: 4px; font-style: italic;">{{ $s['remarks'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- Final decision result --}}
                    @if(in_array($concretePouring->status, ['approved','disapproved']))
                        <div style="
                            margin-top: 16px;
                            padding: 16px;
                            border-radius: 8px;
                            border-left: 4px solid {{ $concretePouring->status === 'approved' ? '#10b981' : '#ef4444' }};
                            background: {{ $concretePouring->status === 'approved' ? '#ecfdf5' : '#fef2f2' }};
                        ">
                            <p style="
                                font-weight: 600;
                                font-size: 14px;
                                color: {{ $concretePouring->status === 'approved' ? '#16a34a' : '#dc2626' }};
                            ">
                                Final Decision: {{ ucfirst($concretePouring->status) }}
                                @if($concretePouring->status === 'approved' && $concretePouring->approver)
                                    by {{ $concretePouring->approver->name }}
                                @elseif($concretePouring->status === 'disapproved' && $concretePouring->disapprover)
                                    by {{ $concretePouring->disapprover->name }}
                                @endif
                            </p>
                            @if($concretePouring->approval_remarks)
                                <p style="font-size: 14px; margin-top: 4px; color: var(--cp-text);">{{ $concretePouring->approval_remarks }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>