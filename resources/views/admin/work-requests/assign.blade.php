{{-- resources/views/admin/work-requests/assign.blade.php --}}
<x-app-layout>

    @push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @endpush
    @include('admin.work-requests.partials._assign_styles')

    <div class="py-8 wr-wrap max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Page Header Card --}}
        <div class="wr-header-card">
            <div>
                <div class="wr-header-title">
                    Assign Reviewers
                    <span style="font-weight:400; color:var(--wr-muted);">—</span>
                    {{ $workRequest->name_of_project }}
                </div>
                <div class="wr-header-sub">{{ $workRequest->project_location }}</div>
            </div>
            <a href="{{ route('admin.work-requests.show', $workRequest) }}" class="wr-back-btn">
                ← Back to Request
            </a>
        </div>

        {{-- Contractor pre-selected RE notice --}}
        @if ($contractorPreselectedRe)
            <div class="wr-contractor-hint">
                <span class="wr-contractor-hint-icon">⚠️</span>
                <div class="wr-contractor-hint-body">
                    <strong>Contractor's preferred Resident Engineer:</strong>
                    {{ $contractorPreselectedRe->name }} — pre-filled in step 3 below.
                    You may keep or change this selection.
                    <br>
                    <span class="warn-strong">All other reviewers must still be assigned</span>
                    before the pipeline can start. No steps are skipped automatically.
                </div>
            </div>
        @endif

        {{-- Info Banner --}}
        <div class="wr-info-banner">
            <span class="wr-info-icon">📋</span>
            <div class="wr-info-text">
                <strong>How this works:</strong> Reviewers are notified in pipeline order. Assign all required roles — leave a slot blank only if that role is not needed for this request.
                <div class="wr-pipeline">
                    <span class="wr-pipeline-step regular">Site Inspector</span>
                    <span class="wr-pipeline-arrow">→</span>
                    <span class="wr-pipeline-step regular">Surveyor</span>
                    <span class="wr-pipeline-arrow">→</span>
                    <span class="wr-pipeline-step regular">Resident Engineer</span>
                    <span class="wr-pipeline-arrow">→</span>
                    <span class="wr-pipeline-step regular">MTQA</span>
                    <span class="wr-pipeline-arrow">→</span>
                    <span class="wr-pipeline-step regular">Engineer IV</span>
                    <span class="wr-pipeline-arrow">→</span>
                    <span class="wr-pipeline-step regular">Engineer III</span>
                    <span class="wr-pipeline-arrow">→</span>
                    <span class="wr-pipeline-step final">Provincial Engineer</span>
                </div>
                <div class="wr-note-badge">
                    📌 The <strong>Provincial Engineer</strong> makes the final approve/reject decision.
                    After approval, <strong>MTQA</strong> is notified to print the document.
                </div>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="wr-error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Main Form Card --}}
        <div class="wr-card">
            <form action="{{ route('admin.work-requests.assign', $workRequest) }}" method="POST">
                @csrf

                {{-- Section Label --}}
                <div class="px-6 pt-5 pb-0">
                    <div class="wr-section-label">Reviewer Assignment Pipeline</div>
                </div>

                @php
                    $slots = [
                        ['label' => 'Site Inspector',     'name' => 'assigned_site_inspector_id',     'users' => $siteInspectors,      'step' => 1, 'final' => false],
                        ['label' => 'Surveyor',           'name' => 'assigned_surveyor_id',            'users' => $surveyors,           'step' => 2, 'final' => false],
                        ['label' => 'Resident Engineer',  'name' => 'assigned_resident_engineer_id',   'users' => $residentEngineers,   'step' => 3, 'final' => false],
                        ['label' => 'MTQA',               'name' => 'assigned_mtqa_id',                'users' => $mtqas,               'step' => 4, 'final' => false],
                        ['label' => 'Engineer IV',        'name' => 'assigned_engineer_iv_id',         'users' => $engineersIv,         'step' => 5, 'final' => false],
                        ['label' => 'Engineer III',       'name' => 'assigned_engineer_iii_id',        'users' => $engineersIii,        'step' => 6, 'final' => false],
                        ['label' => 'Provincial Engineer','name' => 'assigned_provincial_engineer_id', 'users' => $provincialEngineers, 'step' => 7, 'final' => true],
                    ];
                @endphp

                <div class="wr-slot-list">
                    @foreach ($slots as $slot)
                        @php
                            $isPreselectedRe = $slot['name'] === 'assigned_resident_engineer_id'
                                               && $contractorPreselectedRe;
                        @endphp

                        <div class="wr-slot {{ $isPreselectedRe ? 'preselected' : '' }}">

                            {{-- Step Badge --}}
                            <div class="wr-step-badge {{ $slot['final'] ? 'final' : ($isPreselectedRe ? 'preselected-badge' : 'regular') }}">
                                {{ $slot['step'] }}
                            </div>

                            {{-- Label --}}
                            <div class="wr-slot-label-col">
                                <div class="wr-slot-label-name {{ $slot['final'] ? 'final-label' : '' }}">
                                    {{ $slot['label'] }}
                                    @if ($slot['final'])
                                        <span class="wr-final-pill">Final Decision</span>
                                    @endif
                                </div>
                                <div class="wr-slot-label-hint">
                                    @if ($slot['final'])
                                        Approves or rejects the request
                                    @elseif ($isPreselectedRe)
                                        Pre-selected by contractor — confirm or change
                                    @else
                                        Leave blank to skip this step
                                    @endif
                                </div>
                                @if ($isPreselectedRe)
                                    <span class="wr-preselected-tag">🔖 Contractor's choice</span>
                                @endif
                            </div>

                            {{-- Select --}}
                            <div class="wr-slot-select-col">
                                <div class="wr-slot-select-wrap {{ $slot['final'] ? 'final-select' : ($isPreselectedRe ? 'preselected-select' : '') }}">
                                    <select name="{{ $slot['name'] }}" id="{{ $slot['name'] }}">
                                        <option value="">— Skip this step —</option>
                                        @foreach ($slot['users'] as $u)
                                            <option value="{{ $u->id }}"
                                                {{ old($slot['name'], $workRequest->{$slot['name']}) == $u->id ? 'selected' : '' }}>
                                                {{ $u->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error($slot['name'])
                                    <div class="wr-field-error">⚠ {{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- Footer Note --}}
                <div class="wr-footer-note">
                    <div class="wr-footer-note-icon">ℹ️</div>
                    <div class="wr-footer-note-text">
                        <strong>Admin assigns reviewers only.</strong>
                        The <strong>Provincial Engineer</strong> makes the final approve/reject decision.
                        After approval, <strong>MTQA</strong> is notified and can print the document.
                    </div>
                </div>

                {{-- Actions --}}
                <div class="wr-actions">
                    <a href="{{ route('admin.work-requests.show', $workRequest) }}" class="wr-btn wr-btn-ghost">
                        ✕ Cancel
                    </a>
                    <button type="submit" class="wr-btn wr-btn-primary">
                        <i class="fas fa-user-check"></i>
                        Save Assignments & Start Review
                    </button>
                </div>

            </form>
        </div>

    </div>
</x-app-layout>