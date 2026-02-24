<section class="profile-form-group space-y-6">
    <header class="profile-header">
        <h2>{{ __('Delete Account') }}</h2>
        <p>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="profile-btn-delete"
    >{{ __('Delete Account') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium" style="color: var(--profile-text);">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-4 text-sm" style="color: var(--profile-muted);">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <label for="password" value="{{ __('Password') }}" class="sr-only" />

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="profile-input mt-1 block w-3/4 px-3 py-2"
                    placeholder="{{ __('Password') }}"
                />

                @if($errors->userDeletion->has('password'))
                    <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                        @foreach($errors->userDeletion->get('password') as $message)
                            <p>{{ $message }}</p>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 rounded-md text-sm font-medium" style="background: var(--profile-surface2); color: var(--profile-text); border: 1px solid var(--profile-border);">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="profile-btn-delete">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
