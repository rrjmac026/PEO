<x-app-layout>
    @push('styles')
    <style>
        /* ══════════════════════════════════════════
           LIGHT MODE TOKENS (primary / default)
        ══════════════════════════════════════════ */
        :root {
            --profile-surface:   #ffffff;
            --profile-surface2:  #f8fafc;
            --profile-border:    #e2e8f0;
            --profile-text:      #0f172a;
            --profile-text-sec:  #334155;
            --profile-muted:     #64748b;
            --profile-shadow:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            --profile-shadow-lg: 0 4px 16px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
        }

        /* ══════════════════════════════════════════
           DARK MODE TOKENS (override on .dark)
        ══════════════════════════════════════════ */
        .dark {
            --profile-surface:   #1a1f2e;
            --profile-surface2:  #1e2335;
            --profile-border:    #2a3050;
            --profile-text:      #e8eaf6;
            --profile-text-sec:  #c5cae9;
            --profile-muted:     #7c85a8;
            --profile-shadow:    0 1px 4px rgba(0,0,0,0.35);
            --profile-shadow-lg: 0 4px 16px rgba(0,0,0,0.45);
        }

        /* ── Profile cards ── */
        .profile-card {
            background: var(--profile-surface);
            border: 1px solid var(--profile-border);
            border-radius: 12px;
            box-shadow: var(--profile-shadow);
            transition: box-shadow 0.25s ease;
        }
        .profile-card:hover { box-shadow: var(--profile-shadow-lg); }

        .profile-header {
            color: var(--profile-text);
        }

        .profile-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--profile-text);
            margin-bottom: 8px;
        }

        .profile-header p {
            font-size: 14px;
            color: var(--profile-muted);
        }

        .profile-form-group {
            color: var(--profile-text);
        }

        /* Input styling */
        .profile-input {
            background: var(--profile-surface);
            color: var(--profile-text);
            border: 1px solid var(--profile-border);
            border-radius: 6px;
        }

        .profile-input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .dark .profile-input:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.15);
        }

        /* Button styling */
        .profile-btn-save {
            background: #1f2937;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .profile-btn-save:hover {
            background: #111827;
        }

        .dark .profile-btn-save {
            background: #e5e7eb;
            color: #1f2937;
        }

        .dark .profile-btn-save:hover {
            background: #f3f4f6;
        }

        .profile-btn-delete {
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .profile-btn-delete:hover {
            background: #b91c1c;
        }

        .dark .profile-btn-delete {
            background: #ef4444;
        }

        .dark .profile-btn-delete:hover {
            background: #dc2626;
        }

        .profile-page-header {
            background: var(--profile-surface);
            border: 1px solid var(--profile-border);
            border-radius: 12px;
            padding: 16px 24px;
            box-shadow: var(--profile-shadow);
        }

        .profile-page-header h2 {
            color: var(--profile-text);
            margin: 0;
        }
    </style>
    @endpush


        <div class="profile-page-header">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Profile') }}
            </h2>
        </div>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="profile-card p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="profile-card p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="profile-card p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
