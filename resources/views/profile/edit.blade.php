<x-app-layout>
    @push('styles')
    <style>
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

        .profile-page-header {
            background: var(--profile-surface);
            border: 1px solid var(--profile-border);
            border-radius: 12px;
            padding: 16px 24px;
            box-shadow: var(--profile-shadow);
        }
        .profile-page-header h2 { color: var(--profile-text); margin: 0; }

        .profile-card {
            background: var(--profile-surface);
            border: 1px solid var(--profile-border);
            border-radius: 12px;
            box-shadow: var(--profile-shadow);
            overflow: hidden;
        }

        .profile-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--profile-text);
            margin-bottom: 8px;
        }
        .profile-header p { font-size: 14px; color: var(--profile-muted); }
        .profile-form-group { color: var(--profile-text); }

        .profile-input {
            background: var(--profile-surface);
            color: var(--profile-text);
            border: 1px solid var(--profile-border);
            border-radius: 6px;
        }
        .profile-input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .dark .profile-input:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(96,165,250,0.15);
        }

        .profile-btn-save {
            background: #1f2937; color: white; border: none;
            border-radius: 6px; padding: 8px 16px;
            font-size: 12px; font-weight: 600;
            cursor: pointer; transition: background 0.2s;
        }
        .profile-btn-save:hover { background: #111827; }
        .dark .profile-btn-save { background: #e5e7eb; color: #1f2937; }
        .dark .profile-btn-save:hover { background: #f3f4f6; }

        .profile-btn-delete {
            background: #dc2626; color: white; border: none;
            border-radius: 6px; padding: 8px 16px;
            font-size: 12px; font-weight: 600;
            cursor: pointer; transition: background 0.2s;
        }
        .profile-btn-delete:hover { background: #b91c1c; }
        .dark .profile-btn-delete { background: #ef4444; }
        .dark .profile-btn-delete:hover { background: #dc2626; }

        /* ── Tabs ── */
        .profile-tabs {
            display: flex;
            gap: 4px;
            border-bottom: 2px solid var(--profile-border);
            padding: 0 24px;
            background: var(--profile-surface2);
        }
        .profile-tab-btn {
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 600;
            color: var(--profile-muted);
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            cursor: pointer;
            transition: color 0.2s, border-color 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .profile-tab-btn:hover { color: var(--profile-text); }
        .profile-tab-btn.active { color: #3b82f6; border-bottom-color: #3b82f6; }
        .dark .profile-tab-btn.active { color: #60a5fa; border-bottom-color: #60a5fa; }

        .profile-tab-panel { display: none; }
        .profile-tab-panel.active { display: block; }
    </style>
    @endpush

    {{-- Page Header --}}
    <div class="profile-page-header mx-4 sm:mx-6 lg:mx-8 mt-6">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Profile') }}</h2>
    </div>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="profile-card">

                {{-- Tab Headers --}}
                <div class="profile-tabs">
                    <button class="profile-tab-btn active" data-tab="account">
                        <i class="fas fa-user"></i> Account
                    </button>
                    <button class="profile-tab-btn" data-tab="security">
                        <i class="fas fa-lock"></i> Security
                    </button>
                    @if(auth()->user()->role !== 'admin')
                        <button class="profile-tab-btn" data-tab="employee">
                            <i class="fas fa-id-badge"></i> Employee Info
                        </button>
                    @endif
                    <button class="profile-tab-btn" data-tab="danger">
                        <i class="fas fa-triangle-exclamation"></i> Danger Zone
                    </button>
                </div>

                {{-- Tab: Account --}}
                <div class="profile-tab-panel active p-6 sm:p-8" id="tab-account">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- Tab: Security --}}
                <div class="profile-tab-panel p-6 sm:p-8" id="tab-security">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                {{-- Tab: Employee Info --}}
                @if(auth()->user()->role !== 'admin')
                    <div class="profile-tab-panel p-6 sm:p-8" id="tab-employee">
                        <div class="max-w-xl">
                            @include('profile.partials.update-employee-form')
                        </div>
                    </div>
                @endif

                {{-- Tab: Danger Zone --}}
                <div class="profile-tab-panel p-6 sm:p-8" id="tab-danger">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs   = document.querySelectorAll('.profile-tab-btn');
            const panels = document.querySelectorAll('.profile-tab-panel');

            // Auto-switch to employee tab if that form was just saved
            const status = @json(session('status'));
            if (status === 'employee-updated') {
                switchTab('employee');
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', () => switchTab(tab.dataset.tab));
            });

            function switchTab(name) {
                tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === name));
                panels.forEach(p => p.classList.toggle('active', p.id === `tab-${name}`));
            }
        });
    </script>
    @endpush
</x-app-layout>