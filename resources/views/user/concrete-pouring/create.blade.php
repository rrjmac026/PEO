<x-app-layout>

    @push('styles')
        @include('user.concrete-pouring._cp-styles')
        <style>
            .cp-checklist-check {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(220px,1fr));
                gap: 10px;
            }
            .cp-check-label {
                display: flex; align-items: center; gap: 10px;
                padding: 11px 14px;
                background: var(--cp-surface2);
                border: 1.5px solid var(--cp-border);
                border-radius: 8px;
                cursor: pointer;
                transition: border-color .18s, background .18s;
                font-size: 13px; color: var(--cp-text);
                user-select: none;
            }
            .cp-check-label input[type=checkbox] { display: none; }
            .cp-check-box {
                width: 18px; height: 18px; border-radius: 4px;
                border: 2px solid var(--cp-border);
                background: var(--cp-surface);
                flex-shrink: 0;
                display: flex; align-items: center; justify-content: center;
                transition: all .18s;
                font-size: 11px;
            }
            .cp-check-label:has(input:checked) {
                border-color: rgba(5,150,105,0.5);
                background: rgba(5,150,105,0.06);
            }
            .cp-check-label:has(input:checked) .cp-check-box {
                background: #059669; border-color: #059669; color: white;
            }
            .cp-section-title {
                font-size: 16px; font-weight: 700; color: var(--cp-text);
                margin-bottom: 4px;
            }
            .cp-section-sub { font-size: 13px; color: var(--cp-muted); margin-bottom: 20px; }
            .cp-form-grid { display: grid; gap: 16px; }
            .cp-form-two { grid-template-columns: 1fr 1fr; }
            @media(max-width:600px){ .cp-form-two { grid-template-columns: 1fr; } }
            .cp-form-section {
                border-bottom: 1px solid var(--cp-border);
                padding-bottom: 28px; margin-bottom: 28px;
            }
            .cp-form-section:last-child { border-bottom: none; }
        </style>
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                New Concrete Pouring Request
            </h2>
            <a href="{{ route('user.concrete-pouring.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="py-10 cp-wrap">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-700 dark:text-red-300">
                    <p class="font-semibold mb-1">Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="cp-card">
                <div class="cp-card-head">
                    <div class="cp-card-head-icon cyan"><i class="fas fa-fill-drip"></i></div>
                    <span class="cp-card-title">Concrete Pouring Request Form</span>
                </div>
                <div class="cp-card-body">
                    <form action="{{ route('user.concrete-pouring.store') }}" method="POST" novalidate>
                        @csrf

                        {{-- ── Section 1: Project Info ── --}}
                        <div class="cp-form-section">
                            <p class="cp-section-title">Project Information</p>
                            <p class="cp-section-sub">Basic details about the project and structure.</p>

                            {{-- Linked Work Request (optional) --}}
                            @if($approvedWorkRequests->count())
                                <div class="cp-form-grid mb-4">
                                    <div>
                                        <label class="cp-label">Link to Approved Work Request <span style="color:var(--cp-muted)">(optional)</span></label>
                                        <select name="work_request_id" class="cp-select">
                                            <option value="">— Select Work Request —</option>
                                            @foreach($approvedWorkRequests as $wr)
                                                <option value="{{ $wr->id }}"
                                                    {{ (old('work_request_id') == $wr->id || $workRequest?->id == $wr->id) ? 'selected' : '' }}>
                                                    #{{ str_pad($wr->id,6,'0',STR_PAD_LEFT) }} — {{ $wr->name_of_project }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="cp-form-grid cp-form-two">
                                <div>
                                    <label class="cp-label">Project Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="project_name"
                                           value="{{ old('project_name', $workRequest?->name_of_project) }}"
                                           class="cp-input @error('project_name') border-red-500 @enderror"
                                           placeholder="e.g. Davao-Cotabato Road">
                                    @error('project_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="cp-label">Location <span class="text-red-500">*</span></label>
                                    <input type="text" name="location"
                                           value="{{ old('location', $workRequest?->project_location) }}"
                                           class="cp-input @error('location') border-red-500 @enderror"
                                           placeholder="e.g. Sta. 12+300">
                                    @error('location')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="cp-label">Contractor <span class="text-red-500">*</span></label>
                                    <input type="text" name="contractor"
                                           value="{{ old('contractor', $workRequest?->contractor_name ?? Auth::user()->name) }}"
                                           class="cp-input @error('contractor') border-red-500 @enderror">
                                    @error('contractor')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="cp-label">Part of Structure <span class="text-red-500">*</span></label>
                                    <input type="text" name="part_of_structure"
                                           value="{{ old('part_of_structure') }}"
                                           class="cp-input @error('part_of_structure') border-red-500 @enderror"
                                           placeholder="e.g. Box Culvert Wing Wall">
                                    @error('part_of_structure')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="cp-label">Estimated Volume (m³) <span class="text-red-500">*</span></label>
                                    <input type="number" name="estimated_volume" step="0.01" min="0" max="9999.99"
                                           value="{{ old('estimated_volume') }}"
                                           class="cp-input @error('estimated_volume') border-red-500 @enderror"
                                           placeholder="0.00">
                                    @error('estimated_volume')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="cp-label">Station / Limits / Section</label>
                                    <input type="text" name="station_limits_section"
                                           value="{{ old('station_limits_section') }}"
                                           class="cp-input" placeholder="e.g. Sta. 12+300 to 12+450">
                                </div>
                                <div>
                                    <label class="cp-label">Pouring Date & Time <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" name="pouring_datetime"
                                           value="{{ old('pouring_datetime') }}"
                                           class="cp-input @error('pouring_datetime') border-red-500 @enderror">
                                    @error('pouring_datetime')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 2: Checklist ── --}}
                        <div class="cp-form-section">
                            <p class="cp-section-title">Pre-Pouring Checklist</p>
                            <p class="cp-section-sub">Check all items that have been verified and are ready.</p>

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

                            <div class="cp-checklist-check">
                                @foreach($checklistItems as $field => $label)
                                    <label class="cp-check-label">
                                        <input type="checkbox" name="{{ $field }}" value="1"
                                               {{ old($field) ? 'checked' : '' }}>
                                        <span class="cp-check-box"><i class="fas fa-check"></i></span>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('user.concrete-pouring.index') }}"
                               class="px-5 py-2.5 bg-gray-500 text-white text-sm font-semibold rounded-lg hover:bg-gray-600 transition">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="px-6 py-2.5 bg-cyan-600 text-white text-sm font-semibold rounded-lg hover:bg-cyan-700 transition inline-flex items-center gap-2">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>