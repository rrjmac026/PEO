<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create New Work Request') }}
            </h2>
            <a href="{{ route('user.work-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    @push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* =============================================
           DARK MODE TOKENS  (default / .dark parent)
           ============================================= */
        :root {
            --wr-bg:       #0f1117;
            --wr-surface:  #181c27;
            --wr-surface2: #1e2335;
            --wr-border:   #2a3050;
            --wr-accent:   #4f8dff;
            --wr-accent2:  #00d4aa;
            --wr-accent3:  #ff6b6b;
            --wr-text:     #e8eaf6;
            --wr-muted:    #7c85a8;
            --wr-label:    #a8b3d8;
            --wr-error:    #ff6b6b;
            --wr-focus-bg: #1a1f30;
            --wr-glow-1:   rgba(79,141,255,0.05);
            --wr-glow-2:   rgba(0,212,170,0.04);
            --wr-radius:   12px;
            --wr-radius-sm:8px;
        }

        /* =============================================
           LIGHT MODE TOKENS  (html without .dark)
           ============================================= */
        html:not(.dark) {
            --wr-bg:       #f1f5f9;
            --wr-surface:  #ffffff;
            --wr-surface2: #f8fafc;
            --wr-border:   #cbd5e1;
            --wr-accent:   #2563eb;
            --wr-accent2:  #059669;
            --wr-accent3:  #dc2626;
            --wr-text:     #0f172a;
            --wr-muted:    #64748b;
            --wr-label:    #475569;
            --wr-error:    #dc2626;
            --wr-focus-bg: #eff6ff;
            --wr-glow-1:   rgba(37,99,235,0.04);
            --wr-glow-2:   rgba(5,150,105,0.03);
        }

        .wr-wrap { font-family: 'Inter', sans-serif; }

        /* Progress bar */
        .wr-progress-bar {
            background: var(--wr-surface);
            border-radius: var(--wr-radius);
            padding: 14px 20px;
            margin-bottom: 24px;
            border: 1px solid var(--wr-border);
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .wr-progress-inner { display: flex; align-items: center; }
        .wr-step-item {
            display: flex; align-items: center; gap: 8px;
            flex: 1; cursor: pointer; position: relative;
        }
        .wr-step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 36px; right: 0; top: 50%;
            transform: translateY(-50%);
            height: 1px;
            background: var(--wr-border);
            z-index: 0;
            transition: background 0.4s;
        }
        .wr-step-item.done:not(:last-child)::after { background: var(--wr-accent); }
        .wr-step-num {
            width: 30px; height: 30px;
            border-radius: 50%;
            border: 2px solid var(--wr-border);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
            color: var(--wr-muted);
            background: var(--wr-bg);
            transition: all 0.3s;
            flex-shrink: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            position: relative; z-index: 1;
        }
        .wr-step-item.active .wr-step-num {
            background: var(--wr-accent);
            border-color: var(--wr-accent);
            color: white;
            box-shadow: 0 0 16px rgba(79,141,255,0.4);
        }
        .wr-step-item.done .wr-step-num {
            background: var(--wr-accent2);
            border-color: var(--wr-accent2);
            color: white;
        }
        .wr-step-label {
            font-size: 12px; font-weight: 500;
            color: var(--wr-muted);
            white-space: nowrap;
            transition: color 0.3s;
            font-family: 'Inter', sans-serif;
        }
        .wr-step-item.active .wr-step-label { color: var(--wr-accent); }
        .wr-step-item.done  .wr-step-label { color: var(--wr-accent2); }
        @media (max-width: 500px) { .wr-step-label { display: none; } }

        /* Main card */
        .wr-card {
            background: var(--wr-surface);
            border: 1px solid var(--wr-border);
            border-radius: var(--wr-radius);
            overflow: hidden;
            position: relative;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .wr-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background:
                radial-gradient(ellipse 60% 40% at 20% 10%, var(--wr-glow-1) 0%, transparent 60%),
                radial-gradient(ellipse 50% 30% at 80% 80%, var(--wr-glow-2) 0%, transparent 60%);
            pointer-events: none;
        }
        .wr-card-body { padding: 2rem; position: relative; z-index: 1; }

        /* Panels */
        .wr-panel { display: none; animation: wrFadeSlide 0.35s ease both; }
        .wr-panel.active { display: block; }
        @keyframes wrFadeSlide {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Panel tags */
        .wr-panel-tag {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 600;
            letter-spacing: 0.2px;
            color: var(--wr-accent);
            background: rgba(79,141,255,0.1);
            border: 1px solid rgba(79,141,255,0.25);
            padding: 5px 14px; border-radius: 20px; margin-bottom: 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-transform: none;
        }
        html:not(.dark) .wr-panel-tag {
            background: rgba(37,99,235,0.08);
            border-color: rgba(37,99,235,0.2);
        }
        .wr-panel-tag.green {
            color: var(--wr-accent2);
            background: rgba(0,212,170,0.1); border-color: rgba(0,212,170,0.25);
        }
        html:not(.dark) .wr-panel-tag.green {
            background: rgba(5,150,105,0.08); border-color: rgba(5,150,105,0.2);
        }
        .wr-panel-tag.orange {
            color: #f59e0b;
            background: rgba(245,158,11,0.1); border-color: rgba(245,158,11,0.25);
        }
        html:not(.dark) .wr-panel-tag.orange {
            color: #d97706;
            background: rgba(217,119,6,0.08); border-color: rgba(217,119,6,0.2);
        }
        .wr-panel-tag.purple {
            color: #a78bfa;
            background: rgba(167,139,250,0.1); border-color: rgba(167,139,250,0.25);
        }
        html:not(.dark) .wr-panel-tag.purple {
            color: #7c3aed;
            background: rgba(124,58,237,0.08); border-color: rgba(124,58,237,0.2);
        }

        .wr-panel-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 22px; font-weight: 700;
            color: var(--wr-text);
            letter-spacing: -0.3px; margin-bottom: 4px;
            line-height: 1.3;
        }
        .wr-panel-sub { font-size: 14px; color: var(--wr-muted); margin-bottom: 1.75rem; line-height: 1.6; font-weight: 400; }

        /* Fields */
        .wr-fields { display: grid; gap: 16px; }
        .wr-two-col { grid-template-columns: 1fr 1fr; }
        @media (max-width: 560px) { .wr-two-col { grid-template-columns: 1fr; } }
        .wr-field { display: flex; flex-direction: column; gap: 6px; }

        .wr-label {
            font-size: 13px; font-weight: 600;
            letter-spacing: 0.1px;
            color: var(--wr-label);
            display: flex; align-items: center; gap: 5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-transform: none;
        }
        .wr-req { color: var(--wr-accent3); font-size: 14px; }

        .wr-input-wrap { position: relative; }
        .wr-icon {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%);
            color: var(--wr-muted);
            font-style: normal; pointer-events: none;
            font-size: 14px; transition: color 0.2s; z-index: 1;
        }
        .wr-input-wrap.textarea-wrap .wr-icon { top: 14px; transform: none; }

        .wr-input-wrap input,
        .wr-input-wrap select,
        .wr-input-wrap textarea {
            width: 100%;
            background: var(--wr-surface2);
            border: 1.5px solid var(--wr-border);
            border-radius: var(--wr-radius-sm);
            color: var(--wr-text);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            line-height: 1.5;
            padding: 11px 14px 11px 36px;
            outline: none;
            transition: all 0.2s;
            appearance: none; -webkit-appearance: none;
        }
        .wr-input-wrap.no-icon input,
        .wr-input-wrap.no-icon select,
        .wr-input-wrap.no-icon textarea { padding-left: 14px; }

        .wr-input-wrap:focus-within input,
        .wr-input-wrap:focus-within select,
        .wr-input-wrap:focus-within textarea {
            border-color: var(--wr-accent);
            background: var(--wr-focus-bg);
            box-shadow: 0 0 0 3px rgba(79,141,255,0.12);
        }
        html:not(.dark) .wr-input-wrap:focus-within input,
        html:not(.dark) .wr-input-wrap:focus-within select,
        html:not(.dark) .wr-input-wrap:focus-within textarea {
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .wr-input-wrap:focus-within .wr-icon { color: var(--wr-accent); }

        .wr-input-wrap input[readonly] {
            color: var(--wr-muted); cursor: not-allowed; border-style: dashed;
        }
        .wr-input-wrap select option { background: var(--wr-surface2); color: var(--wr-text); }
        .wr-input-wrap textarea { resize: vertical; min-height: 90px; }

        .wr-input-wrap input.wr-error,
        .wr-input-wrap select.wr-error,
        .wr-input-wrap textarea.wr-error {
            border-color: var(--wr-error) !important;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.1) !important;
        }

        .wr-readonly-badge {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
            color: var(--wr-muted);
            background: var(--wr-surface);
            border: 1px solid var(--wr-border);
            border-radius: 4px; padding: 2px 7px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .wr-field-hint { font-size: 11px; color: var(--wr-muted); font-style: italic; }
        .wr-err-msg {
            font-size: 11px; color: var(--wr-error);
            display: none; align-items: center; gap: 4px;
        }
        .wr-err-msg.show { display: flex; }

        /* Status options */
        .wr-status-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 8px;
        }
        .wr-status-opt {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 12px;
            border: 1.5px solid var(--wr-border);
            border-radius: var(--wr-radius-sm);
            cursor: pointer; transition: all 0.2s;
            background: var(--wr-surface2);
        }
        .wr-status-opt:hover { border-color: var(--wr-accent); background: rgba(79,141,255,0.06); }
        html:not(.dark) .wr-status-opt:hover { background: rgba(37,99,235,0.05); }
        .wr-status-opt.selected { border-color: var(--wr-accent); background: rgba(79,141,255,0.12); }
        html:not(.dark) .wr-status-opt.selected { background: rgba(37,99,235,0.08); }
        .wr-status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .wr-status-opt-label { font-size: 13px; font-weight: 500; color: var(--wr-text); }
        .wr-status-opt input[type=radio] { display: none; }

        /* Nav */
        .wr-nav {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 2rem; padding-top: 1.5rem;
            border-top: 1px solid var(--wr-border);
        }

        .wr-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 22px; border: none;
            border-radius: var(--wr-radius-sm);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 700;
            letter-spacing: 0.1px;
            text-transform: none;
            cursor: pointer; transition: all 0.22s; text-decoration: none;
        }
        .wr-btn-ghost {
            background: transparent;
            border: 1.5px solid var(--wr-border);
            color: var(--wr-muted);
        }
        .wr-btn-ghost:hover { border-color: var(--wr-accent); color: var(--wr-accent); background: rgba(79,141,255,0.06); }
        html:not(.dark) .wr-btn-ghost:hover { background: rgba(37,99,235,0.05); }

        .wr-btn-primary {
            background: var(--wr-accent); color: white;
            box-shadow: 0 4px 18px rgba(79,141,255,0.3);
        }
        .wr-btn-primary:hover { filter: brightness(1.1); transform: translateY(-1px); }
        html:not(.dark) .wr-btn-primary { box-shadow: 0 4px 18px rgba(37,99,235,0.25); }

        .wr-btn-success {
            background: var(--wr-accent2); color: #0f1117;
            box-shadow: 0 4px 18px rgba(0,212,170,0.3);
            font-weight: 800;
        }
        html:not(.dark) .wr-btn-success { color: white; box-shadow: 0 4px 18px rgba(5,150,105,0.25); }
        .wr-btn-success:hover { filter: brightness(1.08); transform: translateY(-1px); }
        .wr-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none !important; }

        /* Summary */
        .wr-summary-grid { display: grid; gap: 12px; }
        .wr-summary-section {
            background: var(--wr-surface2);
            border: 1px solid var(--wr-border);
            border-radius: var(--wr-radius-sm);
            overflow: hidden;
        }
        .wr-summary-head {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 16px;
            background: rgba(0,0,0,0.03);
            border-bottom: 1px solid var(--wr-border);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px; font-weight: 700;
            letter-spacing: 0.1px;
            color: var(--wr-label);
        }
        html:not(.dark) .wr-summary-head { background: rgba(0,0,0,0.02); }
        .wr-summary-rows { padding: 12px 16px; display: grid; gap: 10px; }
        .wr-summary-row {
            display: flex; justify-content: space-between;
            align-items: flex-start; gap: 12px;
        }
        .wr-summary-key {
            font-size: 12px; font-weight: 600;
            color: var(--wr-muted); min-width: 130px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .wr-summary-val { font-size: 14px; color: var(--wr-text); text-align: right; word-break: break-word; font-weight: 400; line-height: 1.5; }
        .wr-summary-val.empty { color: var(--wr-muted); font-style: italic; }

        /* Float save */
        .wr-float-save {
            position: fixed; bottom: 20px; right: 20px;
            display: flex; align-items: center; gap: 8px;
            background: var(--wr-surface);
            border: 1px solid var(--wr-border);
            border-radius: 20px; padding: 7px 14px;
            font-size: 11px; color: var(--wr-muted);
            z-index: 9999; opacity: 0; transform: translateY(8px);
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        .wr-float-save.show { opacity: 1; transform: translateY(0); }
        .wr-float-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--wr-accent2);
            animation: wrBlink 1.5s ease infinite;
        }
        @keyframes wrBlink { 0%,100%{opacity:1;} 50%{opacity:0.3;} }
    </style>
    @endpush

    <div class="py-8 wr-wrap">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Progress Steps --}}
            <div class="wr-progress-bar mb-6">
                <div class="wr-progress-inner">
                    <div class="wr-step-item active" id="step-1" onclick="wrGoToStep(1)">
                        <div class="wr-step-num">1</div>
                        <span class="wr-step-label">Project Info</span>
                    </div>
                    <div class="wr-step-item" id="step-2" onclick="wrGoToStep(2)">
                        <div class="wr-step-num">2</div>
                        <span class="wr-step-label">Request Details</span>
                    </div>
                    <div class="wr-step-item" id="step-3" onclick="wrGoToStep(3)">
                        <div class="wr-step-num">3</div>
                        <span class="wr-step-label">Pay Items</span>
                    </div>
                    <div class="wr-step-item" id="step-4" onclick="wrGoToStep(4)">
                        <div class="wr-step-num">4</div>
                        <span class="wr-step-label">Review & Submit</span>
                    </div>
                </div>
            </div>

            {{-- Main Card --}}
            <div class="wr-card">
                <div class="wr-card-body">

                    <form id="wr-form" action="{{ route('user.work-requests.store') }}" method="POST" novalidate>
                        @csrf

                        {{-- STEP 1: Project Info --}}
                        <div class="wr-panel active" id="panel-1">
                            <div class="wr-panel-tag">📁 Step 1 of 4</div>
                            <h2 class="wr-panel-title">Project Information</h2>
                            <p class="wr-panel-sub">Tell us about the project where the work will take place.</p>

                            <div class="wr-fields">
                                <div class="wr-field">
                                    <label class="wr-label" for="name_of_project">
                                        Project Name <span class="wr-req">*</span>
                                    </label>
                                    <div class="wr-input-wrap">
                                        <span class="wr-icon">🏗</span>
                                        <input type="text" id="name_of_project" name="name_of_project"
                                            value="{{ old('name_of_project') }}"
                                            placeholder="e.g., Highway Expansion Phase 2"
                                            class="{{ $errors->has('name_of_project') ? 'wr-error' : '' }}">
                                    </div>
                                    <p class="wr-err-msg {{ $errors->has('name_of_project') ? 'show' : '' }}" id="err-name_of_project">
                                        ⚠ {{ $errors->first('name_of_project', 'Project name is required.') }}
                                    </p>
                                </div>

                                <div class="wr-field">
                                    <label class="wr-label" for="project_location">
                                        Project Location <span class="wr-req">*</span>
                                    </label>
                                    <div class="wr-input-wrap">
                                        <span class="wr-icon">📍</span>
                                        <input type="text" id="project_location" name="project_location"
                                            value="{{ old('project_location') }}"
                                            placeholder="e.g., Davao City, Zone 3"
                                            class="{{ $errors->has('project_location') ? 'wr-error' : '' }}">
                                    </div>
                                    <p class="wr-err-msg {{ $errors->has('project_location') ? 'show' : '' }}" id="err-project_location">
                                        ⚠ {{ $errors->first('project_location', 'Project location is required.') }}
                                    </p>
                                </div>

                                <div class="wr-field">
                                    <label class="wr-label" for="for_office">
                                        For Office
                                        <span style="color:var(--wr-muted);font-weight:400;text-transform:none;letter-spacing:0;font-family:'Inter',sans-serif;">(optional)</span>
                                    </label>
                                    <div class="wr-input-wrap">
                                        <span class="wr-icon">🏢</span>
                                        <input type="text" id="for_office" name="for_office"
                                            value="{{ old('for_office') }}"
                                            placeholder="e.g., Engineering Department">
                                    </div>
                                </div>

                                <div class="wr-field">
                                    <label class="wr-label" for="from_requester">
                                        From / Requester
                                        <span style="color:var(--wr-muted);font-weight:400;text-transform:none;letter-spacing:0;font-family:'Inter',sans-serif;">(optional)</span>
                                    </label>
                                    <div class="wr-input-wrap">
                                        <span class="wr-icon">👤</span>
                                        <input type="text" id="from_requester" name="from_requester"
                                            value="{{ old('from_requester') }}"
                                            placeholder="e.g., Project Manager">
                                    </div>
                                </div>
                            </div>

                            <div class="wr-nav">
                                <span></span>
                                <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(1)">
                                    Continue →
                                </button>
                            </div>
                        </div>

                        {{-- STEP 2: Request Details --}}
                        <div class="wr-panel" id="panel-2">
                            <div class="wr-panel-tag green">📋 Step 2 of 4</div>
                            <h2 class="wr-panel-title">Request Details</h2>
                            <p class="wr-panel-sub">Describe the work being requested and schedule it.</p>

                            <div class="wr-fields">
                                <div class="wr-field">
                                    <label class="wr-label" for="requested_by">
                                        Requested By <span class="wr-req">*</span>
                                    </label>
                                    <div class="wr-input-wrap">
                                        <span class="wr-icon">👤</span>
                                        <input type="text" id="requested_by" name="requested_by"
                                            value="{{ old('requested_by', Auth::user()->name) }}" readonly>
                                        <span class="wr-readonly-badge">Auto</span>
                                    </div>
                                    <p class="wr-field-hint">Automatically filled with your account name.</p>
                                </div>

                                <div class="wr-fields wr-two-col">
                                    <div class="wr-field">
                                        <label class="wr-label" for="requested_work_start_date">
                                            Start Date <span class="wr-req">*</span>
                                        </label>
                                        <div class="wr-input-wrap">
                                            <span class="wr-icon">📅</span>
                                            <input type="date" id="requested_work_start_date" name="requested_work_start_date"
                                                value="{{ old('requested_work_start_date') }}"
                                                class="{{ $errors->has('requested_work_start_date') ? 'wr-error' : '' }}">
                                        </div>
                                        <p class="wr-err-msg {{ $errors->has('requested_work_start_date') ? 'show' : '' }}" id="err-requested_work_start_date">
                                            ⚠ {{ $errors->first('requested_work_start_date', 'Start date is required.') }}
                                        </p>
                                    </div>
                                    <div class="wr-field">
                                        <label class="wr-label" for="requested_work_start_time">Start Time</label>
                                        <div class="wr-input-wrap">
                                            <span class="wr-icon">🕐</span>
                                            <input type="time" id="requested_work_start_time" name="requested_work_start_time"
                                                value="{{ old('requested_work_start_time') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="wr-field">
                                    <label class="wr-label" for="description_of_work_requested">
                                        Description of Work <span class="wr-req">*</span>
                                    </label>
                                    <div class="wr-input-wrap textarea-wrap">
                                        <span class="wr-icon">📝</span>
                                        <textarea id="description_of_work_requested" name="description_of_work_requested" rows="5"
                                            placeholder="Provide a detailed description of the work to be performed..."
                                            class="{{ $errors->has('description_of_work_requested') ? 'wr-error' : '' }}">{{ old('description_of_work_requested') }}</textarea>
                                    </div>
                                    <p class="wr-err-msg {{ $errors->has('description_of_work_requested') ? 'show' : '' }}" id="err-description_of_work_requested">
                                        ⚠ {{ $errors->first('description_of_work_requested', 'Work description is required.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="wr-nav">
                                <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(2)">← Back</button>
                                <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(2)">Continue →</button>
                            </div>
                        </div>

                        {{-- STEP 3: Pay Items & Submission --}}
                        <div class="wr-panel" id="panel-3">
                            <div class="wr-panel-tag orange">⚙️ Step 3 of 4</div>
                            <h2 class="wr-panel-title">Pay Item & Submission</h2>
                            <p class="wr-panel-sub">Specify the pay items, equipment, and submission details.</p>

                            <div class="wr-fields">
                                <div class="wr-fields wr-two-col">
                                    <div class="wr-field">
                                        <label class="wr-label" for="item_no">Item Number</label>
                                        <div class="wr-input-wrap">
                                            <span class="wr-icon">#</span>
                                            <input type="text" id="item_no" name="item_no"
                                                value="{{ old('item_no') }}" placeholder="e.g., A-101">
                                        </div>
                                    </div>
                                    <div class="wr-field">
                                        <label class="wr-label" for="equipment_to_be_used">Equipment to be Used</label>
                                        <div class="wr-input-wrap">
                                            <span class="wr-icon">🚧</span>
                                            <input type="text" id="equipment_to_be_used" name="equipment_to_be_used"
                                                value="{{ old('equipment_to_be_used') }}" placeholder="e.g., Excavator, Roller">
                                        </div>
                                    </div>
                                </div>

                                <div class="wr-field">
                                    <label class="wr-label" for="description">Item Description</label>
                                    <div class="wr-input-wrap textarea-wrap">
                                        <span class="wr-icon">📄</span>
                                        <textarea id="description" name="description" rows="3"
                                            placeholder="Brief description of the pay item...">{{ old('description') }}</textarea>
                                    </div>
                                </div>

                                <div class="wr-fields wr-two-col">
                                    <div class="wr-field">
                                        <label class="wr-label" for="estimated_quantity">Estimated Quantity</label>
                                        <div class="wr-input-wrap">
                                            <span class="wr-icon">🔢</span>
                                            <input type="number" id="estimated_quantity" name="estimated_quantity"
                                                step="0.01" min="0" placeholder="0.00"
                                                value="{{ old('estimated_quantity') }}">
                                        </div>
                                    </div>
                                    <div class="wr-field">
                                        <label class="wr-label" for="unit">Unit</label>
                                        <div class="wr-input-wrap">
                                            <span class="wr-icon">📐</span>
                                            <input type="text" id="unit" name="unit"
                                                value="{{ old('unit') }}" placeholder="m, kg, hrs, cu.m">
                                        </div>
                                    </div>
                                </div>

                                <div class="wr-field">
                                    <label class="wr-label" for="contractor_name">Contractor Name</label>
                                    <div class="wr-input-wrap">
                                        <span class="wr-icon">🏛</span>
                                        <input type="text" id="contractor_name" name="contractor_name"
                                            value="{{ old('contractor_name') }}" placeholder="e.g., XYZ Construction Corp.">
                                    </div>
                                </div>

                                <div class="wr-field">
                                    <label class="wr-label">Status <span class="wr-req">*</span></label>
                                    <div class="wr-status-options" id="wr-status-options">
                                        @foreach(\App\Models\WorkRequest::getStatuses() as $statusOption)
                                            @php
                                                $colors = [
                                                    'pending'     => '#ffaa4f',
                                                    'in_progress' => '#4f8dff',
                                                    'completed'   => '#00d4aa',
                                                    'cancelled'   => '#ff6b6b',
                                                ];
                                                $dotColor = $colors[$statusOption] ?? '#7c85a8';
                                                $isSelected = old('status', 'pending') === $statusOption;
                                            @endphp
                                            <label class="wr-status-opt {{ $isSelected ? 'selected' : '' }}" data-val="{{ $statusOption }}">
                                                <input type="radio" name="status" value="{{ $statusOption }}" {{ $isSelected ? 'checked' : '' }}>
                                                <span class="wr-status-dot" style="background:{{ $dotColor }};"></span>
                                                <span class="wr-status-opt-label">{{ ucfirst(str_replace('_', ' ', $statusOption)) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="wr-err-msg {{ $errors->has('status') ? 'show' : '' }}" id="err-status">
                                        ⚠ {{ $errors->first('status', 'Please select a status.') }}
                                    </p>
                                </div>

                                <div class="wr-field">
                                    <label class="wr-label" for="notes">Additional Notes</label>
                                    <div class="wr-input-wrap textarea-wrap">
                                        <span class="wr-icon">💬</span>
                                        <textarea id="notes" name="notes" rows="3"
                                            placeholder="Any additional information or special instructions...">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="wr-nav">
                                <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(3)">← Back</button>
                                <button type="button" class="wr-btn wr-btn-primary" onclick="wrNextStep(3)">Review →</button>
                            </div>
                        </div>

                        {{-- STEP 4: Review & Submit --}}
                        <div class="wr-panel" id="panel-4">
                            <div class="wr-panel-tag purple">✅ Step 4 of 4</div>
                            <h2 class="wr-panel-title">Review & Submit</h2>
                            <p class="wr-panel-sub">Double-check your details before submitting the work request.</p>

                            <div class="wr-summary-grid" id="wr-summary-grid">
                                {{-- Filled by JS --}}
                            </div>

                            <div class="wr-nav">
                                <button type="button" class="wr-btn wr-btn-ghost" onclick="wrPrevStep(4)">← Edit</button>
                                <button type="submit" class="wr-btn wr-btn-success" id="wr-submit-btn">
                                    🚀 Submit Request
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating auto-save indicator --}}
    <div class="wr-float-save" id="wr-float-save">
        <span class="wr-float-dot"></span>
        Draft saved
    </div>

    <script>
        let wrCurrentStep = 1;
        const wrTotalSteps = 4;

        // If there are server-side validation errors, jump to the right panel
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', () => {
                const errFields = {
                    1: ['name_of_project','project_location'],
                    2: ['requested_work_start_date','description_of_work_requested'],
                    3: ['status'],
                };
                const errorKeys = @json($errors->keys());
                for (let step = 1; step <= 3; step++) {
                    if (errFields[step].some(f => errorKeys.includes(f))) {
                        wrShowStep(step);
                        break;
                    }
                }
            });
        @endif

        function wrGoToStep(n) {
            if (n >= wrCurrentStep) return;
            wrShowStep(n);
        }

        function wrNextStep(from) {
            if (!wrValidateStep(from)) return;
            if (from === wrTotalSteps - 1) wrBuildSummary();
            wrShowStep(from + 1);
        }

        function wrPrevStep(from) {
            wrShowStep(from - 1);
        }

        function wrShowStep(n) {
            document.querySelectorAll('.wr-panel').forEach(p => p.classList.remove('active'));
            document.getElementById(`panel-${n}`).classList.add('active');
            wrCurrentStep = n;
            wrUpdateIndicators();
            window.scrollTo({ top: 0, behavior: 'smooth' });
            wrAutoSave();
        }

        function wrUpdateIndicators() {
            for (let i = 1; i <= wrTotalSteps; i++) {
                const el = document.getElementById(`step-${i}`);
                el.classList.remove('active', 'done');
                if (i < wrCurrentStep) el.classList.add('done');
                else if (i === wrCurrentStep) el.classList.add('active');
                const num = el.querySelector('.wr-step-num');
                num.textContent = i < wrCurrentStep ? '✓' : i;
            }
        }

        function wrValidateStep(step) {
            const required = {
                1: ['name_of_project', 'project_location'],
                2: ['requested_work_start_date', 'description_of_work_requested'],
                3: [],
            };
            if (!required[step]) return true;
            let ok = true;
            required[step].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.classList.remove('wr-error'); }
                const err = document.getElementById(`err-${id}`);
                if (err) err.classList.remove('show');
            });
            required[step].forEach(id => {
                const el = document.getElementById(id);
                if (!el || !el.value.trim()) {
                    if (el) el.classList.add('wr-error');
                    const err = document.getElementById(`err-${id}`);
                    if (err) err.classList.add('show');
                    ok = false;
                }
            });
            return ok;
        }

        // Status toggle
        document.querySelectorAll('.wr-status-opt').forEach(opt => {
            opt.addEventListener('click', () => {
                document.querySelectorAll('.wr-status-opt').forEach(o => o.classList.remove('selected'));
                opt.classList.add('selected');
            });
        });

        function wrGetVal(id) {
            const el = document.getElementById(id);
            return el ? el.value.trim() : '';
        }
        function wrGetStatus() {
            const checked = document.querySelector('.wr-status-opt.selected');
            return checked ? checked.querySelector('.wr-status-opt-label').textContent : '—';
        }

        function wrBuildSummary() {
            const sections = [
                {
                    icon: '📁', title: 'Project Information',
                    rows: [
                        { k: 'Project Name', v: wrGetVal('name_of_project') },
                        { k: 'Location', v: wrGetVal('project_location') },
                        { k: 'For Office', v: wrGetVal('for_office') || '—' },
                        { k: 'From Requester', v: wrGetVal('from_requester') || '—' },
                    ]
                },
                {
                    icon: '📋', title: 'Request Details',
                    rows: [
                        { k: 'Requested By', v: wrGetVal('requested_by') },
                        { k: 'Start Date', v: wrGetVal('requested_work_start_date') || '—' },
                        { k: 'Start Time', v: wrGetVal('requested_work_start_time') || '—' },
                        { k: 'Description', v: wrGetVal('description_of_work_requested') },
                    ]
                },
                {
                    icon: '⚙️', title: 'Pay Item & Submission',
                    rows: [
                        { k: 'Item No.', v: wrGetVal('item_no') || '—' },
                        { k: 'Item Description', v: wrGetVal('description') || '—' },
                        { k: 'Equipment', v: wrGetVal('equipment_to_be_used') || '—' },
                        { k: 'Quantity', v: wrGetVal('estimated_quantity') ? `${wrGetVal('estimated_quantity')} ${wrGetVal('unit')}` : '—' },
                        { k: 'Contractor', v: wrGetVal('contractor_name') || '—' },
                        { k: 'Status', v: wrGetStatus() },
                        { k: 'Notes', v: wrGetVal('notes') || '—' },
                    ]
                }
            ];

            document.getElementById('wr-summary-grid').innerHTML = sections.map(sec => `
                <div class="wr-summary-section">
                    <div class="wr-summary-head">${sec.icon} ${sec.title}</div>
                    <div class="wr-summary-rows">
                        ${sec.rows.map(r => `
                            <div class="wr-summary-row">
                                <span class="wr-summary-key">${r.k}</span>
                                <span class="wr-summary-val ${r.v === '—' ? 'empty' : ''}">${r.v}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `).join('');
        }

        // Auto-save indicator
        let wrSaveTimer;
        function wrAutoSave() {
            clearTimeout(wrSaveTimer);
            const ind = document.getElementById('wr-float-save');
            ind.classList.add('show');
            wrSaveTimer = setTimeout(() => ind.classList.remove('show'), 2000);
        }
        document.querySelectorAll('#wr-form input, #wr-form textarea, #wr-form select').forEach(el => {
            el.addEventListener('input', () => {
                clearTimeout(el._wrt);
                el._wrt = setTimeout(wrAutoSave, 800);
            });
        });

        // Min date
        document.addEventListener('DOMContentLoaded', () => {
            const dateEl = document.getElementById('requested_work_start_date');
            if (dateEl) dateEl.min = new Date().toISOString().split('T')[0];
        });
    </script>
</x-app-layout>