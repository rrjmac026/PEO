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
        .cp-btn-secondary:hover { background: var(--cp-border); }
        .cp-btn-blue   { background: #2563eb; border-color: #2563eb; color: #fff; }
        .cp-btn-blue:hover { background: #1d4ed8; }
        .cp-btn-purple { background: #7c3aed; border-color: #7c3aed; color: #fff; }
        .cp-btn-purple:hover { background: #6d28d9; }
    </style>
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="cp-page-title">
                    Final Decision
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

    <div class="py-12 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Summary card --}}
        <div class="cp-panel cp-panel-body mb-6" style="font-size: 14px; space-y: 8px;">
            <div class="flex justify-between pb-3 border-b" style="border-color: var(--cp-border);">
                <span style="color: var(--cp-muted);">Reference No.</span>
                <span style="font-weight: 500; color: var(--cp-text);">{{ $concretePouring->reference_number ?? '—' }}</span>
            </div>
            <div class="flex justify-between py-3 border-b" style="border-color: var(--cp-border);">
                <span style="color: var(--cp-muted);">Project</span>
                <span style="font-weight: 500; color: var(--cp-text);">{{ $concretePouring->project_name }}</span>
            </div>
            <div class="flex justify-between py-3 border-b" style="border-color: var(--cp-border);">
                <span style="color: var(--cp-muted);">Contractor</span>
                <span style="color: var(--cp-text);">{{ $concretePouring->contractor }}</span>
            </div>
            <div class="flex justify-between py-3 border-b" style="border-color: var(--cp-border);">
                <span style="color: var(--cp-muted);">Location</span>
                <span style="color: var(--cp-text);">{{ $concretePouring->location }}</span>
            </div>
            <div class="flex justify-between py-3 border-b" style="border-color: var(--cp-border);">
                <span style="color: var(--cp-muted);">Checklist progress</span>
                <span style="color: var(--cp-text);">{{ $concretePouring->checklist_progress }}%</span>
            </div>
            <div class="flex justify-between pt-3">
                <span style="color: var(--cp-muted);">All reviewers completed</span>
                <span style="color: #16a34a; font-weight: 500;">✓ Yes</span>
            </div>
        </div>

        <form action="{{ route('admin.concrete-pouring.store-decision', $concretePouring) }}" method="POST"
              class="cp-panel cp-panel-body space-y-6">
            @csrf

            {{-- Decision radio --}}
            <div>
                <p style="font-size: 14px; font-weight: 500; color: var(--cp-text); margin-bottom: 12px;">
                    Decision <span style="color: #ef4444;">*</span>
                </p>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="decision" value="approved"
                               {{ old('decision') === 'approved' ? 'checked' : '' }}
                               class="accent-green-600">
                        <span style="font-size: 14px; font-weight: 500; color: #16a34a;">✓ Approve</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="decision" value="disapproved"
                               {{ old('decision') === 'disapproved' ? 'checked' : '' }}
                               class="accent-red-600">
                        <span style="font-size: 14px; font-weight: 500; color: #dc2626;">✗ Disapprove</span>
                    </label>
                </div>
                @error('decision')
                    <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remarks --}}
            <div>
                <label for="approval_remarks"
                       style="display: block; font-size: 14px; font-weight: 500; color: var(--cp-text); margin-bottom: 4px;">
                    Remarks
                    <span style="color: var(--cp-muted); font-weight: normal; font-size: 13px;">(required if disapproving)</span>
                </label>
                <textarea id="approval_remarks" name="approval_remarks" rows="4"
                          class="cp-input"
                          placeholder="Add any notes or reasons for your decision...">{{ old('approval_remarks') }}</textarea>
                @error('approval_remarks')
                    <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4" style="border-top: 1px solid var(--cp-border);">
                <a href="{{ route('admin.concrete-pouring.show', $concretePouring) }}"
                   class="cp-btn cp-btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="cp-btn cp-btn-blue">
                    <i class="fas fa-check"></i> Submit Decision
                </button>
            </div>
        </form>
    </div>
</x-app-layout>