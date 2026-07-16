<x-app-layout>

    @push('styles')
        @include('user.concrete-pouring._cp-styles')
        @include('user.concrete-pouring._cp-combobox-styles')
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

                            {{-- ── Contract Number Combobox (full width, above the grid) ── --}}
                            <div id="cpRefField" class="mb-4">
                                <label class="cp-label">
                                    Contract Number
                                    <span style="color:var(--cp-muted);font-weight:400;font-size:.8em;">
                                        (optional — auto-generated if blank)
                                    </span>
                                </label>

                                {{-- Trigger --}}
                                <div class="cp-ref-trigger" id="cpRefTrigger"
                                     onclick="cpRefToggle()" role="combobox"
                                     aria-haspopup="listbox" aria-expanded="false" tabindex="0"
                                     onkeydown="if(event.key==='Enter'||event.key===' ')cpRefToggle()">
                                    <span class="cp-ref-trigger-icon">#</span>
                                    <span id="cpRefDisplay" class="cp-ref-display cp-ref-placeholder">
                                        Select or search a contract number
                                    </span>
                                    <span style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
                                        <button type="button" id="cpRefClearBtn" class="cp-ref-clear-btn"
                                                onclick="cpRefClear(event)" title="Clear" style="display:none;">✕</button>
                                        <svg style="width:16px;height:16px;color:var(--cp-muted);transition:transform .2s;flex-shrink:0;"
                                             id="cpRefChevron" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                </div>

                                {{-- Dropdown --}}
                                <div class="cp-ref-dropdown" id="cpRefDropdown" role="listbox">
                                    <div class="cp-ref-search-wrap">
                                        <svg style="width:15px;height:15px;color:var(--cp-muted);flex-shrink:0;" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                                        </svg>
                                        <input type="text" id="cpRefSearchInput" class="cp-ref-search-input"
                                               placeholder="Search contract numbers…" autocomplete="off"
                                               oninput="cpRefFilter(this.value)" onkeydown="cpRefKey(event)">
                                    </div>
                                    <div class="cp-ref-list" id="cpRefList"></div>
                                    <div class="cp-ref-footer" onclick="cpRefEnableCustom()">
                                        <span style="font-size:16px;font-weight:700;line-height:1;">+</span>
                                        <span id="cpRefAddLabel">Enter a custom contract number</span>
                                    </div>
                                </div>

                                {{-- Custom free-text input --}}
                                <div class="cp-ref-custom-wrap" id="cpRefCustomWrap">
                                    <input type="text" id="cpRefCustomInput" class="cp-input"
                                           placeholder="e.g. CP-2026-0001" maxlength="50"
                                           oninput="cpRefOnCustomInput(this.value)">
                                </div>

                                {{-- Hidden field submitted with the form --}}
                                <input type="hidden" name="contract_number" id="cpRefHidden" value="{{ old('contract_number') }}">

                                @error('contract_number')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                                <p style="font-size:11px;color:var(--cp-muted);margin-top:5px;font-style:italic;">
                                    Pick from existing CP contract numbers, or type a new one. Leave blank to auto-generate.
                                </p>
                            </div>

                            {{-- ── Two-column grid for the rest ── --}}
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

                        {{-- ── Section 2: Assign Reviewers ── --}}
                        <div class="cp-form-section">
                            <p class="cp-section-title">Assign Reviewers</p>
                            <p class="cp-section-sub">
                                Select the engineers who will review this request. Leave blank to skip a step.
                                Pipeline: <strong>Resident Engineer → ME/MTQA → Provincial Engineer (Final Decision)</strong>.
                            </p>

                            @php
                                $reviewerSlots = [
                                    [
                                        'step'  => 1,
                                        'label' => 'Resident Engineer',
                                        'name'  => 'resident_engineer_user_id',
                                        'users' => $residentEngineers,
                                        'color' => '#dbeafe',
                                        'text'  => '#2563eb',
                                        'final' => false,
                                    ],
                                    [
                                        'step'  => 2,
                                        'label' => 'ME / MTQA',
                                        'name'  => 'me_mtqa_user_id',
                                        'users' => $mtqas,
                                        'color' => '#fef3c7',
                                        'text'  => '#d97706',
                                        'final' => false,
                                    ],
                                    [
                                        'step'  => 3,
                                        'label' => 'Provincial Engineer',
                                        'name'  => 'noted_by_user_id',
                                        'users' => $provincialEngineers,
                                        'color' => '#dcfce7',
                                        'text'  => '#16a34a',
                                        'final' => true,
                                    ],
                                ];
                            @endphp

                            <div style="border:1px solid var(--cp-border);border-radius:10px;overflow:hidden;">
                                @foreach($reviewerSlots as $slot)
                                    <div style="display:flex;align-items:center;gap:16px;padding:14px 18px;
                                                {{ !$loop->last ? 'border-bottom:1px solid var(--cp-border);' : '' }}">
                                        <div style="width:32px;height:32px;border-radius:50%;
                                                    background:{{ $slot['color'] }};color:{{ $slot['text'] }};
                                                    font-size:12px;font-weight:700;flex-shrink:0;
                                                    display:flex;align-items:center;justify-content:center;">
                                            {{ $slot['step'] }}
                                        </div>
                                        <div style="width:200px;flex-shrink:0;">
                                            <div style="font-size:14px;font-weight:500;color:var(--cp-text);">
                                                {{ $slot['label'] }}
                                                @if($slot['final'])
                                                    <span style="font-size:11px;background:#dcfce7;color:#16a34a;
                                                                 border-radius:20px;padding:1px 8px;margin-left:4px;font-weight:600;">
                                                        FINAL
                                                    </span>
                                                @endif
                                            </div>
                                            <div style="font-size:12px;color:var(--cp-muted);">Leave blank to skip</div>
                                        </div>
                                        <div style="flex:1;">
                                            <select name="{{ $slot['name'] }}" class="cp-select">
                                                <option value="">— Skip this step —</option>
                                                @foreach($slot['users'] as $u)
                                                    <option value="{{ $u->id }}"
                                                        {{ old($slot['name']) == $u->id ? 'selected' : '' }}>
                                                        {{ $u->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error($slot['name'])
                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                                <div style="padding:12px 18px;background:#f0fdf4;border-top:1px solid #bbf7d0;
                                            display:flex;align-items:flex-start;gap:10px;">
                                    <i class="fas fa-info-circle" style="color:#16a34a;margin-top:2px;flex-shrink:0;"></i>
                                    <p style="font-size:13px;color:#166534;margin:0;">
                                        The <strong>Provincial Engineer</strong> reviewer makes the final
                                        <strong>Approve</strong> or <strong>Disapprove</strong> decision.
                                        You must assign at least one reviewer.
                                    </p>
                                </div>
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

@push('scripts')
    @include('user.concrete-pouring._cp-create-script')
@endpush

</x-app-layout>