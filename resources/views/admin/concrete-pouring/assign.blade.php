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
        .cp-input { width: 100%; background: var(--cp-surface2); border: 1px solid var(--cp-border); border-radius: 8px; padding: 8px 14px; font-size: 14px; color: var(--cp-text); outline: none; }
        .cp-input:focus { border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,.12); }
        .cp-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid; cursor: pointer; transition: all .15s; text-decoration: none; white-space: nowrap; }
        .cp-btn-dark { background: #1e293b; border-color: #1e293b; color: #fff; }
        .cp-btn-dark:hover { background: #334155; }
        .dark .cp-btn-dark { background: #e2e8f0; border-color: #e2e8f0; color: #0f172a; }
        .cp-btn-secondary { background: var(--cp-surface2); border-color: var(--cp-border); color: var(--cp-text-sec); }
        .cp-btn-secondary:hover { background: var(--cp-border); }
        .cp-btn-blue   { background: #2563eb; border-color: #2563eb; color: #fff; }
        .cp-btn-blue:hover { background: #1d4ed8; }
        .cp-alert { display: flex; align-items: flex-start; justify-content: space-between; padding: 12px 16px; border-radius: 10px; border: 1px solid; margin-bottom: 16px; font-size: 14px; }
        .cp-alert.info { background: #f0f9ff; border-color: #93c5fd; color: #1e40af; }
        .dark .cp-alert.info { background: rgba(59,130,246,.12); border-color: rgba(96,165,250,.3); color: #60a5fa; }
        .cp-alert.error { background: #fef2f2; border-color: #fca5a5; color: #991b1b; }
        .dark .cp-alert.error { background: rgba(248,113,113,.12); border-color: rgba(248,113,113,.3); color: #f87171; }
    </style>
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="cp-page-title">
                    Assign Reviewers
                </h2>
                <p class="cp-page-sub">
                    {{ $concretePouring->project_name }}
                </p>
            </div>
            <a href="{{ route('admin.concrete-pouring.show', $concretePouring) }}"
               class="cp-btn cp-btn-dark">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="cp-alert info mb-6">
            <div>
                <strong>How this works:</strong> Reviewers are notified <em>in order</em>.
                Leave a slot blank to skip that step. The pipeline is:
                <strong>MTQA → Resident Engineer → Provincial Engineer → Admin final decision</strong>.
            </div>
        </div>

        @if($errors->any())
            <div class="cp-alert error mb-6">
                <div>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.concrete-pouring.assign', $concretePouring) }}" method="POST"
              class="cp-panel">
            @csrf

            @php
                $slots = [
                    [
                        'label' => 'ME/MTQA',
                        'name'  => 'me_mtqa_user_id',
                        'users' => $mtqas,
                        'step'  => 1,
                        'current' => $concretePouring->me_mtqa_user_id,
                    ],
                    [
                        'label' => 'Resident Engineer',
                        'name'  => 'resident_engineer_user_id',
                        'users' => $residentEngineers,
                        'step'  => 2,
                        'current' => $concretePouring->resident_engineer_user_id,
                    ],
                    [
                        'label' => 'Provincial Engineer',
                        'name'  => 'noted_by_user_id',
                        'users' => $provincialEngineers,
                        'step'  => 3,
                        'current' => $concretePouring->noted_by_user_id,
                    ],
                ];
            @endphp

            @foreach($slots as $slot)
                <div class="flex items-center gap-4 px-6 py-4 {{ !$loop->last ? 'border-b' : '' }}" style="border-color: var(--cp-border);">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #ede9fe; color: #7c3aed; font-size: 12px; font-weight: bold; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        {{ $slot['step'] }}
                    </div>
                    <div style="width: 192px; flex-shrink: 0;">
                        <label for="{{ $slot['name'] }}" style="display: block; font-size: 14px; font-weight: 500; color: var(--cp-text);">
                            {{ $slot['label'] }}
                        </label>
                        <span style="font-size: 12px; color: var(--cp-muted);">Leave blank to skip</span>
                    </div>
                    <div style="flex: 1;">
                        <select name="{{ $slot['name'] }}" id="{{ $slot['name'] }}"
                                class="cp-input">
                            <option value="">— Skip this step —</option>
                            @foreach($slot['users'] as $u)
                                <option value="{{ $u->id }}"
                                    {{ old($slot['name'], $slot['current']) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                        @error($slot['name'])
                            <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endforeach

            {{-- Step 4: Admin final (automatic) --}}
            <div class="flex items-center gap-4 px-6 py-4" style="background: var(--cp-surface2); border-radius: 0 0 12px 12px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: #dbeafe; color: #2563eb; font-size: 12px; font-weight: bold; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    4
                </div>
                <div style="width: 192px; flex-shrink: 0;">
                    <p style="font-size: 14px; font-weight: 500; color: var(--cp-text); margin: 0;">Admin Final Decision</p>
                    <span style="font-size: 12px; color: var(--cp-muted);">Always last</span>
                </div>
                <div style="flex: 1; font-size: 14px; color: var(--cp-muted); font-style: italic;">
                    Automatically triggered after all reviewers complete their steps
                </div>
            </div>

            <div class="px-6 py-4 flex justify-end gap-3" style="border-top: 1px solid var(--cp-border);">
                <a href="{{ route('admin.concrete-pouring.show', $concretePouring) }}"
                   class="cp-btn cp-btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="cp-btn cp-btn-blue">
                    <i class="fas fa-check"></i> Save Assignments & Start
                </button>
            </div>
        </form>
    </div>
</x-app-layout>