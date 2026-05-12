@extends('layouts.app')

@section('title', 'New Memo')

@push('styles')
<style>
    :root {
        --mo-surface:   #ffffff;
        --mo-surface2:  #f8fafc;
        --mo-border:    #e2e8f0;
        --mo-text:      #0f172a;
        --mo-text-sec:  #334155;
        --mo-muted:     #64748b;
        --mo-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
    }
    .dark {
        --mo-surface:   #1a1f2e;
        --mo-surface2:  #1e2335;
        --mo-border:    #2a3050;
        --mo-text:      #e8eaf6;
        --mo-text-sec:  #c5cae9;
        --mo-muted:     #7c85a8;
        --mo-shadow:    0 1px 4px rgba(0,0,0,0.35);
    }

    .mo-page-title { font-size: 26px; font-weight: 800; color: var(--mo-text); line-height: 1.2; }
    .mo-page-sub   { font-size: 14px; color: var(--mo-muted); margin-top: 4px; }

    /* ── Breadcrumb ── */
    .mo-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--mo-muted); margin-bottom: 20px; }
    .mo-breadcrumb a { color: var(--mo-muted); text-decoration: none; }
    .mo-breadcrumb a:hover { color: #ea580c; }
    .mo-breadcrumb .sep { opacity: .4; }

    /* ── Card ── */
    .mo-card {
        background: var(--mo-surface);
        border: 1px solid var(--mo-border);
        border-radius: 14px;
        box-shadow: var(--mo-shadow);
        overflow: hidden;
    }
    .mo-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--mo-border);
        background: var(--mo-surface2);
        display: flex; align-items: center; gap: 12px;
    }
    .mo-card-header-icon {
        width: 38px; height: 38px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; background: rgba(234,88,12,.12); color: #ea580c;
        flex-shrink: 0;
    }
    .dark .mo-card-header-icon { background: rgba(251,146,60,.15); color: #fb923c; }
    .mo-card-title { font-size: 15px; font-weight: 700; color: var(--mo-text); }
    .mo-card-subtitle { font-size: 12px; color: var(--mo-muted); margin-top: 1px; }
    .mo-card-body { padding: 24px; }

    /* ── Form elements ── */
    .mo-form-group { margin-bottom: 20px; }
    .mo-label {
        display: block; font-size: 13px; font-weight: 600;
        color: var(--mo-text-sec); margin-bottom: 7px;
    }
    .mo-label .required { color: #ef4444; margin-left: 2px; }
    .mo-input, .mo-select, .mo-textarea {
        width: 100%;
        background: var(--mo-surface);
        border: 1px solid var(--mo-border);
        border-radius: 9px;
        padding: 10px 14px;
        font-size: 14px; color: var(--mo-text);
        box-shadow: var(--mo-shadow);
        transition: border-color .15s, box-shadow .15s; outline: none;
        font-family: inherit;
    }
    .mo-input::placeholder, .mo-textarea::placeholder { color: var(--mo-muted); }
    .mo-input:focus, .mo-select:focus, .mo-textarea:focus {
        border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,.10);
    }
    .dark .mo-input:focus, .dark .mo-select:focus, .dark .mo-textarea:focus {
        border-color: #fb923c; box-shadow: 0 0 0 3px rgba(251,146,60,.10);
    }
    .mo-textarea { resize: vertical; min-height: 240px; line-height: 1.6; }
    .mo-input.is-invalid, .mo-select.is-invalid, .mo-textarea.is-invalid {
        border-color: #ef4444;
    }
    .mo-error { font-size: 12px; color: #ef4444; margin-top: 5px; }
    .mo-hint  { font-size: 12px; color: var(--mo-muted); margin-top: 5px; }

    /* ── Scope radio cards ── */
    .mo-scope-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 10px;
    }
    .mo-scope-card {
        border: 2px solid var(--mo-border);
        border-radius: 10px;
        padding: 14px;
        cursor: pointer;
        transition: border-color .15s, background .15s;
        display: flex; align-items: flex-start; gap: 10px;
    }
    .mo-scope-card:hover { border-color: #fb923c; background: rgba(234,88,12,.04); }
    .mo-scope-card.selected { border-color: #ea580c; background: rgba(234,88,12,.06); }
    .dark .mo-scope-card.selected { border-color: #fb923c; background: rgba(251,146,60,.1); }
    .mo-scope-card input[type="radio"] { margin-top: 2px; accent-color: #ea580c; }
    .mo-scope-card-label { font-size: 13px; font-weight: 600; color: var(--mo-text-sec); }
    .mo-scope-card-desc  { font-size: 11px; color: var(--mo-muted); margin-top: 2px; }

    /* ── Checkbox / role / dept grids ── */
    .mo-check-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 8px;
    }
    .mo-check-item {
        display: flex; align-items: center; gap: 8px;
        padding: 9px 12px; border-radius: 8px;
        border: 1px solid var(--mo-border);
        background: var(--mo-surface2);
        cursor: pointer; font-size: 13px; color: var(--mo-text-sec);
        transition: border-color .12s, background .12s;
    }
    .mo-check-item:hover { border-color: #fb923c; }
    .mo-check-item input[type="checkbox"] { accent-color: #ea580c; }

    /* ── Recipient select panel ── */
    .mo-scope-panel { display: none; }
    .mo-scope-panel.active { display: block; }

    /* ── File upload ── */
    .mo-file-drop {
        border: 2px dashed var(--mo-border);
        border-radius: 10px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: border-color .15s, background .15s;
        position: relative;
    }
    .mo-file-drop:hover { border-color: #ea580c; background: rgba(234,88,12,.03); }
    .mo-file-drop input[type="file"] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer;
    }
    .mo-file-drop i { font-size: 28px; color: var(--mo-muted); margin-bottom: 8px; display: block; }
    .mo-file-drop-label { font-size: 13px; color: var(--mo-muted); }
    .mo-file-drop-label span { color: #ea580c; font-weight: 600; }

    /* ── Action bar ── */
    .mo-action-bar {
        padding: 18px 24px;
        border-top: 1px solid var(--mo-border);
        background: var(--mo-surface2);
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        flex-wrap: wrap;
    }
    .mo-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 9px;
        font-size: 13px; font-weight: 600; border: 1px solid; cursor: pointer;
        transition: all .15s; text-decoration: none; white-space: nowrap;
        font-family: inherit;
    }
    .mo-btn-orange { background: #ea580c; border-color: #ea580c; color: #fff; }
    .mo-btn-orange:hover { background: #c2410c; border-color: #c2410c; color: #fff; }
    .dark .mo-btn-orange { background: #f97316; border-color: #f97316; }
    .mo-btn-blue   { background: #2563eb; border-color: #2563eb; color: #fff; }
    .mo-btn-blue:hover { background: #1d4ed8; border-color: #1d4ed8; }
    .mo-btn-secondary { background: var(--mo-surface2); border-color: var(--mo-border); color: var(--mo-text-sec); }
    .mo-btn-secondary:hover { border-color: var(--mo-muted); }
    .mo-btn-gray  { background: #475569; border-color: #475569; color: #fff; }
    .mo-btn-gray:hover { background: #334155; }

    /* ── Schedule datetime toggle ── */
    #schedule-section { display: none; }

    /* ── File list ── */
    .mo-file-list { margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px; }
    .mo-file-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 10px; border-radius: 6px;
        background: var(--mo-surface2); border: 1px solid var(--mo-border);
        font-size: 12px; color: var(--mo-text-sec);
    }
    .mo-file-chip button { background: none; border: none; cursor: pointer; color: #ef4444; padding: 0; font-size: 11px; }
</style>
@endpush

@section('content')

    <!-- ── Breadcrumb ── -->
    <div class="mo-breadcrumb">
        <a href="{{ route('admin.memos.index') }}"><i class="fas fa-envelope mr-1"></i>Memos</a>
        <span class="sep">/</span>
        <span>New Memo</span>
    </div>

    <!-- ── Page Header ── -->
    <div class="mb-6">
        <h1 class="mo-page-title">Compose Memo</h1>
        <p class="mo-page-sub">Write and deliver internal communications to your team</p>
    </div>

    <form action="{{ route('admin.memos.store') }}" method="POST" enctype="multipart/form-data" id="memo-form">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <!-- ── LEFT: Main compose area ── -->
            <div class="xl:col-span-2 space-y-6">

                <!-- Memo Details -->
                <div class="mo-card">
                    <div class="mo-card-header">
                        <div class="mo-card-header-icon"><i class="fas fa-pen-nib"></i></div>
                        <div>
                            <div class="mo-card-title">Memo Details</div>
                            <div class="mo-card-subtitle">Type, subject and body</div>
                        </div>
                    </div>
                    <div class="mo-card-body">

                        <!-- Type -->
                        <div class="mo-form-group">
                            <label class="mo-label">Memo Type <span class="required">*</span></label>
                            <select name="type" class="mo-select {{ $errors->has('type') ? 'is-invalid' : '' }}" required>
                                <option value="">— Select type —</option>
                                @foreach (\App\Models\Memo::types() as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type') <p class="mo-error">{{ $message }}</p> @enderror
                        </div>

                        <!-- Subject -->
                        <div class="mo-form-group">
                            <label class="mo-label">Subject <span class="required">*</span></label>
                            <input type="text" name="subject"
                                   value="{{ old('subject') }}"
                                   placeholder="Brief and descriptive subject line…"
                                   class="mo-input {{ $errors->has('subject') ? 'is-invalid' : '' }}" required>
                            @error('subject') <p class="mo-error">{{ $message }}</p> @enderror
                        </div>

                        <!-- Body -->
                        <div class="mo-form-group" style="margin-bottom: 0;">
                            <label class="mo-label">Message Body <span class="required">*</span></label>
                            <textarea name="body"
                                      placeholder="Write your memo content here…"
                                      class="mo-textarea {{ $errors->has('body') ? 'is-invalid' : '' }}"
                                      required>{{ old('body') }}</textarea>
                            @error('body') <p class="mo-error">{{ $message }}</p> @enderror
                        </div>

                    </div>
                </div>

                <!-- Attachments -->
                <div class="mo-card">
                    <div class="mo-card-header">
                        <div class="mo-card-header-icon"><i class="fas fa-paperclip"></i></div>
                        <div>
                            <div class="mo-card-title">Attachments</div>
                            <div class="mo-card-subtitle">Up to 10 MB per file</div>
                        </div>
                    </div>
                    <div class="mo-card-body">
                        <div class="mo-file-drop" id="file-drop-zone">
                            <input type="file" name="attachments[]" multiple accept="*/*" id="attachment-input">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div class="mo-file-drop-label">
                                Drag & drop files here or <span>browse</span>
                            </div>
                            <p style="font-size:11px; color: var(--mo-muted); margin-top:6px;">Max 10 MB per file · Any format</p>
                        </div>
                        <div class="mo-file-list" id="file-list"></div>
                        @error('attachments.*') <p class="mo-error">{{ $message }}</p> @enderror
                    </div>
                </div>

            </div>

            <!-- ── RIGHT: Recipients & Options ── -->
            <div class="space-y-6">

                <!-- Recipients -->
                <div class="mo-card">
                    <div class="mo-card-header">
                        <div class="mo-card-header-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="mo-card-title">Recipients</div>
                            <div class="mo-card-subtitle">Who receives this memo</div>
                        </div>
                    </div>
                    <div class="mo-card-body">

                        <!-- Scope selector -->
                        <div class="mo-form-group">
                            <label class="mo-label">Recipient Scope <span class="required">*</span></label>
                            <div class="mo-scope-grid">

                                <label class="mo-scope-card {{ old('recipient_scope','specific') === 'all' ? 'selected' : '' }}">
                                    <input type="radio" name="recipient_scope" value="all"
                                           {{ old('recipient_scope') === 'all' ? 'checked' : '' }}
                                           onchange="switchScope(this.value)">
                                    <div>
                                        <div class="mo-scope-card-label"><i class="fas fa-globe mr-1"></i>All Users</div>
                                        <div class="mo-scope-card-desc">Every active user</div>
                                    </div>
                                </label>

                                <label class="mo-scope-card {{ old('recipient_scope') === 'by_role' ? 'selected' : '' }}">
                                    <input type="radio" name="recipient_scope" value="by_role"
                                           {{ old('recipient_scope') === 'by_role' ? 'checked' : '' }}
                                           onchange="switchScope(this.value)">
                                    <div>
                                        <div class="mo-scope-card-label"><i class="fas fa-user-tag mr-1"></i>By Role</div>
                                        <div class="mo-scope-card-desc">Filter by user role</div>
                                    </div>
                                </label>

                                <label class="mo-scope-card {{ old('recipient_scope') === 'by_department' ? 'selected' : '' }}">
                                    <input type="radio" name="recipient_scope" value="by_department"
                                           {{ old('recipient_scope') === 'by_department' ? 'checked' : '' }}
                                           onchange="switchScope(this.value)">
                                    <div>
                                        <div class="mo-scope-card-label"><i class="fas fa-building mr-1"></i>By Dept</div>
                                        <div class="mo-scope-card-desc">Filter by department</div>
                                    </div>
                                </label>

                                <label class="mo-scope-card {{ old('recipient_scope','specific') === 'specific' ? 'selected' : '' }}">
                                    <input type="radio" name="recipient_scope" value="specific"
                                           {{ (old('recipient_scope', 'specific') === 'specific') ? 'checked' : '' }}
                                           onchange="switchScope(this.value)">
                                    <div>
                                        <div class="mo-scope-card-label"><i class="fas fa-user-check mr-1"></i>Specific</div>
                                        <div class="mo-scope-card-desc">Pick individual users</div>
                                    </div>
                                </label>

                            </div>
                            @error('recipient_scope') <p class="mo-error">{{ $message }}</p> @enderror
                        </div>

                        <!-- By Role panel -->
                        <div id="panel-by_role" class="mo-scope-panel {{ old('recipient_scope') === 'by_role' ? 'active' : '' }}">
                            <label class="mo-label">Select Roles</label>
                            <div class="mo-check-grid">
                                @foreach ($roles as $role)
                                    <label class="mo-check-item">
                                        <input type="checkbox" name="target_roles[]" value="{{ $role }}"
                                               {{ in_array($role, old('target_roles', [])) ? 'checked' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $role)) }}
                                    </label>
                                @endforeach
                            </div>
                            @error('target_roles') <p class="mo-error">{{ $message }}</p> @enderror
                        </div>

                        <!-- By Department panel -->
                        <div id="panel-by_department" class="mo-scope-panel {{ old('recipient_scope') === 'by_department' ? 'active' : '' }}">
                            <label class="mo-label">Select Departments</label>
                            <div class="mo-check-grid">
                                @foreach ($departments as $dept)
                                    <label class="mo-check-item">
                                        <input type="checkbox" name="target_departments[]" value="{{ $dept }}"
                                               {{ in_array($dept, old('target_departments', [])) ? 'checked' : '' }}>
                                        {{ $dept }}
                                    </label>
                                @endforeach
                            </div>
                            @error('target_departments') <p class="mo-error">{{ $message }}</p> @enderror
                        </div>

                        <!-- Specific users panel -->
                        <div id="panel-specific" class="mo-scope-panel {{ (old('recipient_scope', 'specific') === 'specific') ? 'active' : '' }}">
                            <label class="mo-label">Select Users</label>
                            <input type="text" id="user-search"
                                   placeholder="Search users…"
                                   class="mo-input" style="margin-bottom: 10px;">
                            <div style="max-height: 220px; overflow-y: auto; border: 1px solid var(--mo-border); border-radius: 8px; padding: 6px;">
                                @foreach ($users as $user)
                                    <label class="mo-check-item" style="margin-bottom: 4px;" data-name="{{ strtolower($user->name) }}">
                                        <input type="checkbox" name="specific_user_ids[]" value="{{ $user->id }}"
                                               {{ in_array($user->id, old('specific_user_ids', [])) ? 'checked' : '' }}>
                                        <span>
                                            {{ $user->name }}
                                            <span style="font-size:11px; color: var(--mo-muted);">({{ $user->role }})</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('specific_user_ids') <p class="mo-error">{{ $message }}</p> @enderror
                        </div>

                    </div>
                </div>

                <!-- Schedule -->
                <div class="mo-card">
                    <div class="mo-card-header">
                        <div class="mo-card-header-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div>
                            <div class="mo-card-title">Schedule (Optional)</div>
                            <div class="mo-card-subtitle">Send at a future date &amp; time</div>
                        </div>
                    </div>
                    <div class="mo-card-body">
                        <label class="mo-check-item" style="margin-bottom: 12px;" id="schedule-toggle-label">
                            <input type="checkbox" id="schedule-toggle" onchange="toggleSchedule(this.checked)">
                            Enable scheduled delivery
                        </label>
                        <div id="schedule-section">
                            <label class="mo-label">Send At <span class="required">*</span></label>
                            <input type="datetime-local" name="scheduled_at"
                                   value="{{ old('scheduled_at') }}"
                                   class="mo-input {{ $errors->has('scheduled_at') ? 'is-invalid' : '' }}"
                                   min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                            @error('scheduled_at') <p class="mo-error">{{ $message }}</p> @enderror
                            <p class="mo-hint">Must be at least 5 minutes from now.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ── Action Bar ── -->
        <div class="mo-card" style="margin-top: 24px;">
            <div class="mo-action-bar">
                <a href="{{ route('admin.memos.index') }}" class="mo-btn mo-btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <div class="flex gap-3 flex-wrap">
                    <button type="submit" name="action" value="draft" class="mo-btn mo-btn-gray">
                        <i class="fas fa-save"></i> Save as Draft
                    </button>
                    <button type="submit" name="action" value="schedule" class="mo-btn mo-btn-blue" id="schedule-btn" style="display:none;">
                        <i class="fas fa-clock"></i> Schedule Memo
                    </button>
                    <button type="submit" name="action" value="send" class="mo-btn mo-btn-orange" id="send-btn">
                        <i class="fas fa-paper-plane"></i> Send Now
                    </button>
                </div>
            </div>
        </div>

    </form>

@endsection

@push('scripts')
<script>
    // ── Scope panel switcher ──
    function switchScope(val) {
        document.querySelectorAll('.mo-scope-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.mo-scope-card').forEach(c => c.classList.remove('selected'));
        const panel = document.getElementById('panel-' + val);
        if (panel) panel.classList.add('active');
        const checked = document.querySelector(`input[name="recipient_scope"][value="${val}"]`);
        if (checked) checked.closest('.mo-scope-card').classList.add('selected');
    }

    // Init on load
    const initScope = document.querySelector('input[name="recipient_scope"]:checked');
    if (initScope) switchScope(initScope.value);

    // ── Schedule toggle ──
    function toggleSchedule(on) {
        document.getElementById('schedule-section').style.display = on ? 'block' : 'none';
        document.getElementById('schedule-btn').style.display = on ? 'inline-flex' : 'none';
        document.getElementById('send-btn').style.display = on ? 'none' : 'inline-flex';
        const input = document.querySelector('input[name="scheduled_at"]');
        input.required = on;
    }

    // If old input had scheduled_at show the toggle
    @if (old('action') === 'schedule' || old('scheduled_at'))
        document.getElementById('schedule-toggle').checked = true;
        toggleSchedule(true);
    @endif

    // ── User search filter ──
    document.getElementById('user-search').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#panel-specific [data-name]').forEach(el => {
            el.style.display = el.dataset.name.includes(q) ? '' : 'none';
        });
    });

    // ── File list display ──
    document.getElementById('attachment-input').addEventListener('change', function () {
        const list = document.getElementById('file-list');
        list.innerHTML = '';
        Array.from(this.files).forEach(f => {
            const chip = document.createElement('div');
            chip.className = 'mo-file-chip';
            chip.innerHTML = `<i class="fas fa-file"></i> ${f.name} <span style="color:var(--mo-muted)">(${(f.size/1024/1024).toFixed(2)} MB)</span>`;
            list.appendChild(chip);
        });
    });
</script>
@endpush