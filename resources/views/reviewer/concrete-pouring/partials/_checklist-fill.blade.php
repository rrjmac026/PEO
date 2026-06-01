{{-- resources/views/reviewer/concrete-pouring/partials/_checklist-fill.blade.php --}}
{{-- Variables expected: $concretePouring, $canFillChecklist --}}

@php
    // ── Per-item checker map (latest log entry per field) ──────────────
    $itemCheckers = [];
    foreach ($concretePouring->checklistLogs as $log) {
        if (!isset($itemCheckers[$log->field])) {
            $itemCheckers[$log->field] = [
                'user'    => $log->user,
                'checked' => $log->checked,
                'at'      => $log->updated_at,
            ];
        }
    }

    // ── Checklist items map ─────────────────────────────────────────────
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

    // ── Role label + colour helper ──────────────────────────────────────
    $getRoleStyle = function (?string $role): array {
        return match ($role) {
            'mtqa'              => ['label' => 'ME/MTQA',           'bg' => 'rgba(234,88,12,0.1)',   'color' => '#ea580c', 'border' => 'rgba(234,88,12,0.3)'],
            'resident_engineer' => ['label' => 'Resident Engineer', 'bg' => 'rgba(37,99,235,0.1)',  'color' => '#2563eb', 'border' => 'rgba(37,99,235,0.3)'],
            default             => ['label' => ucfirst(str_replace('_', ' ', $role ?? '')), 'bg' => 'rgba(100,116,139,0.1)', 'color' => '#64748b', 'border' => 'rgba(100,116,139,0.3)'],
        };
    };

    // ── Overall filler (most-recent save) ──────────────────────────────
    $filler = $concretePouring->checklistFilledBy ?? null;
    if (!$filler && $concretePouring->checklist_filled_by_user_id) {
        $filler = \App\Models\User::find($concretePouring->checklist_filled_by_user_id);
    }
    $hasBeenFilled   = !is_null($filler);
    $filledAt        = $concretePouring->checklist_filled_at
                         ? \Illuminate\Support\Carbon::parse($concretePouring->checklist_filled_at)
                         : null;
    $fillerRoleColor = $filler ? $getRoleStyle($filler->role) : $getRoleStyle(null);
    $fillerRoleLabel = $fillerRoleColor['label'];

    // ── Current authenticated user's role style ─────────────────────────
    $currentUser      = Auth::user();
    $currentRoleColor = $getRoleStyle($currentUser->role ?? null);
    $currentRoleLabel = $currentRoleColor['label'];
@endphp

@if($concretePouring->status === 'approved')
<div class="cp-card">
    <div class="cp-card-head">
        <div class="cp-card-head-icon green"><i class="fas fa-tasks"></i></div>
        <span class="cp-card-title">Pre-Pouring Checklist</span>
        <span class="ml-auto text-sm" style="color:var(--cp-muted)">
            {{ $concretePouring->checklist_progress }}% complete
        </span>
    </div>
    <div class="cp-card-body">

        {{-- ── Who filled the checklist indicator ── --}}
        @if($hasBeenFilled)
            <div style="display:flex; align-items:center; gap:12px; padding:12px 16px;
                        background:rgba(5,150,105,0.06); border:1px solid rgba(5,150,105,0.25);
                        border-radius:10px; margin-bottom:16px; flex-wrap:wrap;">
                <div style="width:36px; height:36px; border-radius:50%;
                            background:rgba(5,150,105,0.15); border:1.5px solid rgba(5,150,105,0.4);
                            display:flex; align-items:center; justify-content:center;
                            font-size:15px; color:#059669; flex-shrink:0;">
                    <i class="fas fa-user-check"></i>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:13px; font-weight:700; color:var(--cp-text); margin-bottom:2px;">
                        Checked by {{ $filler->name }}
                    </div>
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <span style="font-size:11px; font-weight:600; padding:2px 8px; border-radius:20px;
                                     background:{{ $fillerRoleColor['bg'] }};
                                     color:{{ $fillerRoleColor['color'] }};
                                     border:1px solid {{ $fillerRoleColor['border'] }};">
                            {{ $fillerRoleLabel }}
                        </span>
                        @if($filledAt)
                            <span style="font-size:12px; color:var(--cp-muted);">
                                <i class="fas fa-clock" style="margin-right:3px; font-size:10px;"></i>
                                {{ $filledAt->format('M d, Y · H:i') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div style="flex-shrink:0;">
                    @if($concretePouring->checklist_progress == 100)
                        <span style="font-size:12px; font-weight:700; padding:4px 10px; border-radius:20px;
                                     background:rgba(5,150,105,0.12); color:#059669;
                                     border:1px solid rgba(5,150,105,0.3);">
                            <i class="fas fa-check-circle" style="margin-right:4px;"></i> All Complete
                        </span>
                    @else
                        <span style="font-size:12px; font-weight:700; padding:4px 10px; border-radius:20px;
                                     background:rgba(217,119,6,0.1); color:#d97706;
                                     border:1px solid rgba(217,119,6,0.3);">
                            <i class="fas fa-exclamation-circle" style="margin-right:4px;"></i>
                            {{ $concretePouring->checklist_progress }}% Done
                        </span>
                    @endif
                </div>
            </div>
        @else
            <div style="display:flex; align-items:center; gap:10px; padding:12px 16px;
                        background:rgba(217,119,6,0.06); border:1px solid rgba(217,119,6,0.25);
                        border-radius:10px; margin-bottom:16px;">
                <i class="fas fa-hourglass-half" style="color:#d97706; font-size:14px; flex-shrink:0;"></i>
                <span style="font-size:13px; color:#d97706; font-weight:500;">
                    Awaiting checklist review by assigned ME/MTQA or Resident Engineer.
                </span>
            </div>
        @endif

        {{-- Progress bar --}}
        <div class="mb-4">
            <div style="height:6px;background:var(--cp-border);border-radius:99px;overflow:hidden">
                <div style="height:100%;width:{{ $concretePouring->checklist_progress }}%;background:#059669;border-radius:99px;transition:width .4s"></div>
            </div>
        </div>

        @if($canFillChecklist)
            {{-- ── Editable checklist form ── --}}
            <div class="rv-form-box" style="margin-bottom:20px; border-color:rgba(5,150,105,0.35);">
                <div class="rv-form-title">
                    <i class="fas fa-clipboard-check text-green-500"></i>
                    Fill in the checklist items
                    <span style="font-size:11px; font-weight:400; color:var(--cp-muted); margin-left:4px;">
                        — checking as <strong style="color:var(--cp-text);">{{ Auth::user()->name }}</strong>
                        <span style="padding:1px 7px; border-radius:20px; font-size:10px; font-weight:700; margin-left:4px;
                                     background:{{ $currentRoleColor['bg'] }};
                                     color:{{ $currentRoleColor['color'] }};
                                     border:1px solid {{ $currentRoleColor['border'] }};">
                            {{ $currentRoleLabel }}
                        </span>
                    </span>
                </div>
                <form action="{{ route('reviewer.concrete-pouring.store-checklist', $concretePouring) }}"
                      method="POST">
                    @csrf
                    <div class="cp-checklist-fill-grid">
                        @foreach($checklistItems as $field => $label)
                            @php
                                $itemInfo  = $itemCheckers[$field] ?? null;
                                $itemUser  = $itemInfo['user'] ?? null;
                                $isMine    = $itemUser?->id === Auth::id();
                                $roleStyle = $itemUser ? $getRoleStyle($itemUser->role) : [];
                            @endphp
                            <label class="cp-fill-check-label">
                                <input type="checkbox"
                                    name="{{ $field }}"
                                    value="1"
                                    {{ $concretePouring->$field ? 'checked' : '' }}>
                                <span class="cp-fill-check-box" style="margin-top:1px;"><i class="fas fa-check"></i></span>
                                <div style="flex:1; min-width:0;">
                                    <div style="font-size:13px;">{{ $label }}</div>
                                    @if($itemUser && !$isMine)
                                        <div style="margin-top:3px;">
                                            <span style="font-size:10px; font-weight:600; padding:2px 6px;
                                                        border-radius:6px; display:inline-block;
                                                        word-break:break-word; line-height:1.4;
                                                        background:{{ $roleStyle['bg'] }};
                                                        color:{{ $roleStyle['color'] }};
                                                        border:1px solid {{ $roleStyle['border'] }};">
                                                <i class="fas fa-user" style="font-size:8px; margin-right:2px;"></i>
                                                {{ $concretePouring->$field ? 'Checked' : 'Unchecked' }} by {{ $itemUser->name }}
                                            </span>
                                        </div>
                                    @elseif($itemUser && $isMine && $concretePouring->$field)
                                        <div style="margin-top:3px; font-size:10px; color:var(--cp-muted);">
                                            <i class="fas fa-check" style="font-size:8px; margin-right:2px; color:#059669;"></i>
                                            You checked this
                                        </div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <div style="margin-top:16px;">
                        <button type="submit"
                                class="px-6 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition inline-flex items-center gap-2">
                            <i class="fas fa-save"></i> Save Checklist
                        </button>
                    </div>
                </form>
            </div>

        @else
            {{-- ── Read-only grid with per-item checker indicator ── --}}
            <div class="cp-checklist-grid">
                @foreach($checklistItems as $field => $label)
                    @php
                        $itemInfo  = $itemCheckers[$field] ?? null;
                        $itemUser  = $itemInfo['user'] ?? null;
                        $roleStyle = $itemUser ? $getRoleStyle($itemUser->role) : [];
                    @endphp
                    <div class="cp-check-item {{ $concretePouring->$field ? 'checked' : 'unchecked' }}">
                        <span class="cp-check-icon" style="flex-shrink:0; margin-top:1px;">
                            {{ $concretePouring->$field ? '✅' : '⬜' }}
                        </span>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:13px;">{{ $label }}</div>
                            @if($concretePouring->$field && $itemUser)
                                <div style="margin-top:3px;">
                                    <span style="font-size:10px; font-weight:600; padding:2px 6px;
                                                border-radius:6px; display:inline-block;
                                                word-break:break-word; line-height:1.4;
                                                background:{{ $roleStyle['bg'] }};
                                                color:{{ $roleStyle['color'] }};
                                                border:1px solid {{ $roleStyle['border'] }};">
                                        <i class="fas fa-user" style="font-size:8px; margin-right:2px;"></i>
                                        {{ $itemUser->name }} · {{ $roleStyle['label'] }}
                                    </span>
                                </div>
                            @elseif(!$concretePouring->$field && $itemUser)
                                <div style="margin-top:3px;">
                                    <span style="font-size:10px; font-weight:600; padding:2px 6px;
                                                border-radius:6px; display:inline-block;
                                                word-break:break-word; line-height:1.4;
                                                background:rgba(220,38,38,0.07);
                                                color:#dc2626;
                                                border:1px solid rgba(220,38,38,0.2);">
                                        <i class="fas fa-times" style="font-size:8px; margin-right:2px;"></i>
                                        Unchecked by {{ $itemUser->name }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endif

<style>
    .cp-checklist-fill-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 10px;
    }
    .cp-fill-check-label {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 11px 14px;
        background: var(--cp-surface2);
        border: 1.5px solid var(--cp-border);
        border-radius: 8px;
        cursor: pointer;
        transition: border-color .18s, background .18s;
        color: var(--cp-text);
        user-select: none;
    }
    .cp-fill-check-label input[type=checkbox] { display: none; }
    .cp-fill-check-box {
        width: 18px; height: 18px; border-radius: 4px;
        border: 2px solid var(--cp-border);
        background: var(--cp-surface);
        flex-shrink: 0; margin-top: 1px;
        display: flex; align-items: center; justify-content: center;
        transition: all .18s; font-size: 11px;
    }
    .cp-fill-check-label:has(input:checked) {
        border-color: rgba(5,150,105,0.5);
        background: rgba(5,150,105,0.06);
    }
    .cp-fill-check-label:has(input:checked) .cp-fill-check-box {
        background: #059669; border-color: #059669; color: white;
    }
    .cp-check-item {
        align-items: flex-start !important;
    }
</style>