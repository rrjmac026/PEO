<x-app-layout>

    @push('styles')
        @include('user.concrete-pouring._cp-styles')
        <style>
            .rv-form-box {
                background: var(--cp-surface2);
                border: 1.5px solid rgba(8,145,178,0.35);
                border-radius: 10px; padding: 20px;
                margin-top: 20px;
            }
            .dark .rv-form-box { border-color: rgba(34,211,238,0.25); background: rgba(8,145,178,0.06); }
            .rv-form-title { font-size: 14px; font-weight: 700; color: var(--cp-text); margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
            .rv-readonly-box {
                background: var(--cp-surface2);
                border: 1px solid var(--cp-border);
                border-radius: 10px; padding: 16px;
                margin-top: 16px;
                font-size: 13px; color: var(--cp-muted);
            }

            /* ── E-Signature Styles ─────────────────────────────────────────── */
            .cp-sig-wrap { margin-top: 16px; }
            .cp-sig-option-box {
                padding: 14px 16px;
                background: var(--cp-surface2);
                border: 1px solid var(--cp-border);
                border-radius: 8px;
                margin-bottom: 14px;
            }
            .cp-sig-option-title { font-size: 13px; color: var(--cp-muted); margin-bottom: 10px; font-weight: 600; }
            .cp-sig-saved-img {
                max-width: 240px; max-height: 80px;
                border: 1px solid var(--cp-border);
                border-radius: 6px;
                padding: 4px;
                background: var(--cp-surface);
                display: block;
            }
            .cp-sig-radio-label {
                display: flex; align-items: center; gap: 7px;
                font-size: 13px; color: var(--cp-text); cursor: pointer;
            }
            .cp-sig-draw-hint { font-size: 13px; color: var(--cp-muted); margin-bottom: 10px; font-weight: 600; }
            .cp-sig-canvas {
                border: 2px solid var(--cp-border);
                border-radius: 8px;
                background: var(--cp-surface);
                cursor: crosshair;
                display: block;
                touch-action: none;
            }
            .cp-sig-canvas:focus { outline: none; box-shadow: 0 0 0 3px rgba(8,145,178,0.25); }
            .cp-sig-clear-btn {
                margin-top: 8px;
                padding: 5px 14px;
                background: #ef4444; color: #fff;
                border: none; border-radius: 6px;
                font-size: 12px; cursor: pointer;
                transition: opacity 0.2s;
            }
            .cp-sig-clear-btn:hover { opacity: 0.85; }
            .cp-sig-preview-label { font-size: 12px; color: var(--cp-muted); margin-bottom: 6px; font-weight: 600; }
            .cp-sig-preview-img {
                border: 2px solid var(--cp-border);
                border-radius: 8px;
                background: var(--cp-surface);
                min-width: 190px; height: 90px;
                object-fit: contain;
            }
            .cp-sig-preview-empty {
                border: 2px dashed var(--cp-border);
                border-radius: 8px;
                background: var(--cp-surface2);
                min-width: 190px; height: 90px;
                display: flex; align-items: center; justify-content: center;
                color: var(--cp-muted); font-size: 12px;
            }

            /* ── Signature display badge (show view, read-only) ─────────────── */
            .cp-sig-display {
                display: flex; align-items: center; gap: 10px;
                padding: 10px 14px;
                background: rgba(5,150,105,0.06);
                border: 1px solid rgba(5,150,105,0.25);
                border-radius: 8px;
                margin-top: 8px;
            }
            .cp-sig-display img {
                max-width: 160px; max-height: 60px;
                border: 1px solid var(--cp-border);
                border-radius: 5px;
                background: var(--cp-surface);
                padding: 3px;
            }
            .cp-sig-display-label {
                font-size: 11px; font-weight: 700;
                color: #059669; text-transform: uppercase; letter-spacing: 0.5px;
            }
            .dark .cp-sig-display { background: rgba(52,211,153,0.06); border-color: rgba(52,211,153,0.2); }
            .dark .cp-sig-display-label { color: #34d399; }
        </style>
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Concrete Pouring — Review
            </h2>
            <a href="{{ route('reviewer.concrete-pouring.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Queue
            </a>
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
                    <span class="cp-req-id">{{ $concretePouring->reference_number }}</span>
                    <div>
                        <div class="cp-project-name">{{ $concretePouring->project_name }}</div>
                        <div class="cp-project-loc">
                            <i class="fas fa-map-marker-alt text-xs mr-1"></i> {{ $concretePouring->location }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($isMyTurn)
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
                            <i class="fas fa-user-check mr-1"></i> Your Turn
                        </span>
                    @endif
                    <span class="cp-badge {{ $concretePouring->status }}">
                        <span class="cp-badge-dot" style="background:currentColor;border-radius:50%"></span>
                        {{ ucfirst($concretePouring->status) }}
                    </span>
                </div>
            </div>

            {{-- Meta --}}
            <div class="cp-meta-row">
                <div class="cp-meta-chip">🏗 <strong>{{ $concretePouring->contractor }}</strong></div>
                <div class="cp-meta-chip">📐 <strong>{{ number_format($concretePouring->estimated_volume, 2) }} m³</strong></div>
                <div class="cp-meta-chip">🗓 Pouring: <strong>{{ $concretePouring->pouring_datetime?->format('M d, Y H:i') ?? '—' }}</strong></div>
                <div class="cp-meta-chip">📋 Current Step: <strong>{{ $concretePouring->current_step_label }}</strong></div>
            </div>

            {{-- Project Details --}}
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
                    <span class="ml-auto text-sm" style="color:var(--cp-muted)">
                        {{ $concretePouring->checklist_progress }}% complete
                    </span>
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

            {{-- Review Pipeline & Forms --}}
            <div class="cp-card">
                <div class="cp-card-head">
                    <div class="cp-card-head-icon blue"><i class="fas fa-project-diagram"></i></div>
                    <span class="cp-card-title">Review Pipeline</span>
                </div>
                <div class="cp-card-body">
                    <div class="cp-timeline">

                        {{-- ════════════════════════════════════════
                            STEP 1 — ME / MTQA
                        ════════════════════════════════════════ --}}
                        @php
                            $mtqaDone   = !is_null($concretePouring->me_mtqa_date);
                            $mtqaActive = $concretePouring->current_review_step === 'mtqa';
                            $isMyMtqa   = $isMyTurn && $mtqaActive;
                            $mtqaSigUrl = $concretePouring->resolveSignatureUrl($concretePouring->me_mtqa_signature);
                            // Reviewer sees OWN signature only
                            $showMtqaSig = $mtqaSigUrl && (Auth::id() == $concretePouring->me_mtqa_user_id);
                        @endphp
                        <div class="cp-timeline-item">
                            <div class="cp-tl-icon-wrap">
                                <div class="cp-tl-icon {{ $mtqaDone ? 'done' : ($mtqaActive ? 'active' : 'waiting') }}">
                                    @if($mtqaDone)<i class="fas fa-check"></i>
                                    @elseif($mtqaActive)<i class="fas fa-clock"></i>
                                    @else<i class="fas fa-circle"></i>@endif
                                </div>
                            </div>
                            <div style="flex:1">
                                <div class="cp-tl-label">Step 1 — ME / MTQA Review</div>
                                <div class="cp-tl-name">{{ $concretePouring->meMtqaChecker?->name ?? 'Not assigned' }}</div>
                                @if($concretePouring->me_mtqa_date)
                                    <div class="cp-tl-date">Reviewed: {{ $concretePouring->me_mtqa_date->format('M d, Y') }}</div>
                                @endif
                                @if($concretePouring->me_mtqa_remarks)
                                    <div class="cp-tl-remark">"{{ $concretePouring->me_mtqa_remarks }}"</div>
                                @endif

                                {{-- Signature — shown only to the ME/MTQA themselves --}}
                                @if($showMtqaSig)
                                    <div class="cp-sig-display">
                                        <div>
                                            <div class="cp-sig-display-label"><i class="fas fa-pen-nib mr-1"></i> Your Signature</div>
                                            <img src="{{ $mtqaSigUrl }}" alt="ME/MTQA Signature">
                                        </div>
                                    </div>
                                @elseif($mtqaDone && $mtqaSigUrl)
                                    {{-- Other reviewers & admin see a "signed" indicator, not the image --}}
                                    <div style="margin-top:8px;display:flex;align-items:center;gap:6px;font-size:12px;color:#059669;font-weight:600;">
                                        <i class="fas fa-signature"></i> Signed by {{ $concretePouring->meMtqaChecker?->name }}
                                    </div>
                                @endif

                                {{-- ── MTQA submission form ── --}}
                                @if($isMyMtqa)
                                    <div class="rv-form-box">
                                        <div class="rv-form-title">
                                            <i class="fas fa-pen text-cyan-500"></i>
                                            Submit Your ME/MTQA Review & Signature
                                        </div>
                                        <form action="{{ route('reviewer.concrete-pouring.store-mtqa-review', $concretePouring) }}"
                                              method="POST" id="mtqa-review-form">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="cp-label">Remarks <span style="color:var(--cp-muted)">(optional)</span></label>
                                                <textarea name="me_mtqa_remarks" rows="3" class="cp-textarea"
                                                          placeholder="Enter your review remarks, observations, or notes…">{{ old('me_mtqa_remarks') }}</textarea>
                                            </div>

                                            {{-- E-Signature pad --}}
                                            @include('reviewer.concrete-pouring.partials._signature-pad', [
                                                'cp_prefix'     => 'mtqa',
                                                'cp_radioName'  => 'mtqa_sig_mode',
                                                'cp_hiddenName' => 'me_mtqa_signature',
                                            ])

                                            <div style="margin-top:16px;">
                                                <button type="submit"
                                                        class="px-6 py-2.5 bg-cyan-600 text-white text-sm font-semibold rounded-lg hover:bg-cyan-700 transition inline-flex items-center gap-2">
                                                    <i class="fas fa-check-circle"></i> Submit ME/MTQA Review
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @elseif(!$mtqaDone && !$mtqaActive)
                                    <div class="rv-readonly-box">Waiting for ME/MTQA step to become active.</div>
                                @endif
                            </div>
                            <div>
                                @if($mtqaDone)<span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Done</span>
                                @elseif($mtqaActive)<span class="cp-badge requested" style="font-size:11px;padding:3px 8px">In Progress</span>
                                @else<span style="font-size:11px;color:var(--cp-muted)">Waiting</span>@endif
                            </div>
                        </div>

                        {{-- ════════════════════════════════════════
                            STEP 2 — Resident Engineer
                        ════════════════════════════════════════ --}}
                        @php
                            $reDone   = !is_null($concretePouring->re_date);
                            $reActive = $concretePouring->current_review_step === 'resident_engineer';
                            $isMyRe   = $isMyTurn && $reActive;
                            $reSigUrl = $concretePouring->resolveSignatureUrl($concretePouring->re_signature);
                            $showReSig = $reSigUrl && (Auth::id() == $concretePouring->resident_engineer_user_id);
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
                                <div class="cp-tl-label">Step 2 — Resident Engineer Review</div>
                                <div class="cp-tl-name">{{ $concretePouring->residentEngineer?->name ?? 'Not assigned' }}</div>
                                @if($concretePouring->re_date)
                                    <div class="cp-tl-date">Reviewed: {{ $concretePouring->re_date->format('M d, Y') }}</div>
                                @endif
                                @if($concretePouring->re_remarks)
                                    <div class="cp-tl-remark">"{{ $concretePouring->re_remarks }}"</div>
                                @endif

                                {{-- Signature — shown only to the Resident Engineer --}}
                                @if($showReSig)
                                    <div class="cp-sig-display">
                                        <div>
                                            <div class="cp-sig-display-label"><i class="fas fa-pen-nib mr-1"></i> Your Signature</div>
                                            <img src="{{ $reSigUrl }}" alt="Resident Engineer Signature">
                                        </div>
                                    </div>
                                @elseif($reDone && $reSigUrl)
                                    <div style="margin-top:8px;display:flex;align-items:center;gap:6px;font-size:12px;color:#059669;font-weight:600;">
                                        <i class="fas fa-signature"></i> Signed by {{ $concretePouring->residentEngineer?->name }}
                                    </div>
                                @endif

                                @if($isMyRe)
                                    <div class="rv-form-box">
                                        <div class="rv-form-title">
                                            <i class="fas fa-pen text-blue-500"></i>
                                            Submit Your Resident Engineer Review & Signature
                                        </div>
                                        <form action="{{ route('reviewer.concrete-pouring.store-engineer-review', $concretePouring) }}"
                                              method="POST" id="re-review-form">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="cp-label">Remarks <span style="color:var(--cp-muted)">(optional)</span></label>
                                                <textarea name="re_remarks" rows="3" class="cp-textarea"
                                                          placeholder="Enter your engineering review remarks…">{{ old('re_remarks') }}</textarea>
                                            </div>

                                            @include('reviewer.concrete-pouring.partials._signature-pad', [
                                                'cp_prefix'     => 're',
                                                'cp_radioName'  => 're_sig_mode',
                                                'cp_hiddenName' => 're_signature',
                                            ])

                                            <div style="margin-top:16px;">
                                                <button type="submit"
                                                        class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition inline-flex items-center gap-2">
                                                    <i class="fas fa-check-circle"></i> Submit Engineer Review
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @elseif(!$reDone && !$reActive)
                                    <div class="rv-readonly-box">Waiting for Resident Engineer step to become active.</div>
                                @endif
                            </div>
                            <div>
                                @if($reDone)<span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Done</span>
                                @elseif($reActive)<span class="cp-badge requested" style="font-size:11px;padding:3px 8px">In Progress</span>
                                @else<span style="font-size:11px;color:var(--cp-muted)">Waiting</span>@endif
                            </div>
                        </div>

                        {{-- ════════════════════════════════════════
                            STEP 3 — Provincial Engineer
                        ════════════════════════════════════════ --}}
                        @php
                            $peDone     = !is_null($concretePouring->noted_date);
                            $peActive   = $concretePouring->current_review_step === 'provincial_engineer';
                            $isMyPe     = $isMyTurn && $peActive;
                            $peSigUrl   = $concretePouring->resolveSignatureUrl($concretePouring->noted_by_signature);
                            $showPeSig  = $peSigUrl && (Auth::id() == $concretePouring->noted_by_user_id);
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
                                <div class="cp-tl-label">Step 3 — Noted by Provincial Engineer</div>
                                <div class="cp-tl-name">{{ $concretePouring->notedByEngineer?->name ?? 'Not assigned' }}</div>
                                @if($concretePouring->noted_date)
                                    <div class="cp-tl-date">Noted: {{ $concretePouring->noted_date->format('M d, Y') }}</div>
                                @endif
                                @if($concretePouring->approval_remarks && $peDone && !in_array($concretePouring->status, ['approved','disapproved']))
                                    <div class="cp-tl-remark">"{{ $concretePouring->approval_remarks }}"</div>
                                @endif

                                {{-- Signature — shown only to the Provincial Engineer --}}
                                @if($showPeSig)
                                    <div class="cp-sig-display">
                                        <div>
                                            <div class="cp-sig-display-label"><i class="fas fa-pen-nib mr-1"></i> Your Signature</div>
                                            <img src="{{ $peSigUrl }}" alt="Provincial Engineer Signature">
                                        </div>
                                    </div>
                                @elseif($peDone && $peSigUrl)
                                    <div style="margin-top:8px;display:flex;align-items:center;gap:6px;font-size:12px;color:#059669;font-weight:600;">
                                        <i class="fas fa-signature"></i> Signed by {{ $concretePouring->notedByEngineer?->name }}
                                    </div>
                                @endif

                                @if($isMyPe)
                                    <div class="rv-form-box">
                                        <div class="rv-form-title">
                                            <i class="fas fa-pen text-orange-500"></i>
                                            Submit Your Note & Signature
                                        </div>
                                        <form action="{{ route('reviewer.concrete-pouring.store-provincial-note', $concretePouring) }}"
                                              method="POST" id="pe-review-form">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="cp-label">Provincial Remarks <span style="color:var(--cp-muted)">(optional)</span></label>
                                                <textarea name="provincial_remarks" rows="3" class="cp-textarea"
                                                          placeholder="Enter your note or observations as Provincial Engineer…">{{ old('provincial_remarks') }}</textarea>
                                            </div>

                                            @include('reviewer.concrete-pouring.partials._signature-pad', [
                                                'cp_prefix'     => 'pe',
                                                'cp_radioName'  => 'pe_sig_mode',
                                                'cp_hiddenName' => 'noted_by_signature',
                                            ])

                                            <div style="margin-top:16px;">
                                                <button type="submit"
                                                        class="px-6 py-2.5 bg-orange-500 text-white text-sm font-semibold rounded-lg hover:bg-orange-600 transition inline-flex items-center gap-2">
                                                    <i class="fas fa-check-circle"></i> Submit Note
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @elseif(!$peDone && !$peActive)
                                    <div class="rv-readonly-box">Waiting for Provincial Engineer step to become active.</div>
                                @endif
                            </div>
                            <div>
                                @if($peDone)<span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Noted</span>
                                @elseif($peActive)<span class="cp-badge requested" style="font-size:11px;padding:3px 8px">In Progress</span>
                                @else<span style="font-size:11px;color:var(--cp-muted)">Waiting</span>@endif
                            </div>
                        </div>

                        {{-- ════════════════════════════════════════
                            STEP 4 — Admin Final Decision
                        ════════════════════════════════════════ --}}
                        @php $adminActive = $concretePouring->current_review_step === 'admin_final'; @endphp
                        <div class="cp-timeline-item">
                            <div class="cp-tl-icon-wrap">
                                <div class="cp-tl-icon {{ in_array($concretePouring->status,['approved','disapproved']) ? 'done' : ($adminActive ? 'active' : 'waiting') }}">
                                    @if(in_array($concretePouring->status,['approved','disapproved']))<i class="fas fa-check"></i>
                                    @elseif($adminActive)<i class="fas fa-clock"></i>
                                    @else<i class="fas fa-circle"></i>@endif
                                </div>
                            </div>
                            <div style="flex:1">
                                <div class="cp-tl-label">Step 4 — Admin Final Decision</div>
                                <div class="cp-tl-name">
                                    @if($concretePouring->status === 'approved')
                                        Approved by {{ $concretePouring->approver?->name ?? '—' }}
                                        @if($concretePouring->approved_date)
                                            <span class="cp-tl-date">on {{ $concretePouring->approved_date->format('M d, Y') }}</span>
                                        @endif
                                    @elseif($concretePouring->status === 'disapproved')
                                        Disapproved by {{ $concretePouring->disapprover?->name ?? '—' }}
                                        @if($concretePouring->disapproved_date)
                                            <span class="cp-tl-date">on {{ $concretePouring->disapproved_date->format('M d, Y') }}</span>
                                        @endif
                                    @elseif($adminActive)
                                        Forwarded to admin for final decision
                                    @else
                                        Awaiting reviewer completion
                                    @endif
                                </div>
                                @if($concretePouring->approval_remarks && in_array($concretePouring->status,['approved','disapproved']))
                                    <div class="cp-tl-remark">"{{ $concretePouring->approval_remarks }}"</div>
                                @endif
                            </div>
                            <div>
                                @if($concretePouring->status === 'approved')
                                    <span class="cp-badge approved" style="font-size:11px;padding:3px 8px">Approved</span>
                                @elseif($concretePouring->status === 'disapproved')
                                    <span class="cp-badge disapproved" style="font-size:11px;padding:3px 8px">Disapproved</span>
                                @elseif($adminActive)
                                    <span class="cp-badge requested" style="font-size:11px;padding:3px 8px">Pending Admin</span>
                                @else
                                    <span style="font-size:11px;color:var(--cp-muted)">Waiting</span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Not my turn notice --}}
            @if(!$isMyTurn && !in_array($concretePouring->status, ['approved','disapproved']))
                <div class="p-4 rounded-lg text-sm font-medium"
                     style="background:rgba(8,145,178,0.07);border:1px solid rgba(8,145,178,0.3);color:var(--cp-accent)">
                    <i class="fas fa-info-circle mr-2"></i>
                    You are assigned to this request, but it is not currently your turn to review.
                    The current step is <strong>{{ $concretePouring->current_step_label }}</strong>.
                </div>
            @endif

        </div>
    </div>

    {{-- ── E-Signature JavaScript ────────────────────────────────────────────── --}}
    @push('scripts')
    <script>
    /**
     * Initialise a single signature canvas.
     *
     * @param {string} prefix       — e.g. 'mtqa', 're', 'pe'
     * @param {string} radioName    — name attribute of the mode radio buttons
     */
    function initCpSignaturePad(prefix, radioName) {
        const canvas      = document.getElementById(`${prefix}-cp-canvas`);
        const output      = document.getElementById(`${prefix}-cp-output`);
        const clearBtn    = document.getElementById(`${prefix}-cp-clear`);
        const preview     = document.getElementById(`${prefix}-cp-preview`);
        const previewEmpty= document.getElementById(`${prefix}-cp-preview-empty`);
        const padWrap     = document.getElementById(`${prefix}-cp-pad-wrap`);

        if (!canvas) return; // pad not rendered (not this reviewer's step)

        const ctx = canvas.getContext('2d');
        let drawing = false;

        // ── drawing helpers ──────────────────────────────────────────────────
        const startDraw = () => { drawing = true; ctx.beginPath(); };
        const endDraw   = () => {
            if (!drawing) return;
            drawing = false;
            const dataUrl = canvas.toDataURL('image/png');
            output.value = dataUrl;
            if (preview && previewEmpty) {
                preview.src = dataUrl;
                preview.style.display = 'block';
                previewEmpty.style.display = 'none';
            }
        };
        const moveDraw = (x, y) => {
            if (!drawing) return;
            const rect = canvas.getBoundingClientRect();
            ctx.lineTo(x - rect.left, y - rect.top);
            ctx.lineWidth = 2;
            ctx.lineCap   = 'round';
            ctx.lineJoin  = 'round';
            ctx.strokeStyle = document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#1e293b';
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(x - rect.left, y - rect.top);
        };

        // Mouse
        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mouseup',   endDraw);
        canvas.addEventListener('mouseleave',endDraw);
        canvas.addEventListener('mousemove', e => moveDraw(e.clientX, e.clientY));

        // Touch
        canvas.addEventListener('touchstart', e => { e.preventDefault(); startDraw(); }, { passive: false });
        canvas.addEventListener('touchend',   e => { e.preventDefault(); endDraw();   }, { passive: false });
        canvas.addEventListener('touchmove',  e => {
            e.preventDefault();
            const t = e.touches[0];
            moveDraw(t.clientX, t.clientY);
        }, { passive: false });

        // Clear button
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                output.value = '';
                if (preview && previewEmpty) {
                    preview.style.display = 'none';
                    preview.src = '';
                    previewEmpty.style.display = 'flex';
                }
            });
        }

        // Radio toggle: saved ↔ draw
        document.querySelectorAll(`input[name="${radioName}"]`).forEach(radio => {
            radio.addEventListener('change', e => {
                if (e.target.value === 'draw') {
                    padWrap.style.display = 'block';
                    output.value = '';
                    if (preview && previewEmpty) {
                        preview.style.display = 'none';
                        preview.src = '';
                        previewEmpty.style.display = 'flex';
                    }
                } else {
                    // "saved" — restore the pre-filled saved-signature URL
                    padWrap.style.display = 'none';
                    const savedUrl = "{{ Auth::user()->signature_path ? asset('storage/' . Auth::user()->signature_path) : '' }}";
                    output.value = savedUrl;
                }
            });
        });
    }

    // Initialise all three pads (only the one whose canvas exists in DOM will do anything)
    initCpSignaturePad('mtqa', 'mtqa_sig_mode');
    initCpSignaturePad('re',   're_sig_mode');
    initCpSignaturePad('pe',   'pe_sig_mode');
    </script>
    @endpush

</x-app-layout>