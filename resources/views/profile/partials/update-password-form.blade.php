<section class="profile-form-group">
    <header class="profile-header">
        <h2>{{ __('Update Password') }}</h2>
        <p>{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block font-medium text-sm mb-2" style="color: var(--profile-text);">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="profile-input mt-1 block w-full px-3 py-2" autocomplete="current-password" />
            @if($errors->updatePassword->has('current_password'))
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                    @foreach($errors->updatePassword->get('current_password') as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="block font-medium text-sm mb-2" style="color: var(--profile-text);">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="profile-input mt-1 block w-full px-3 py-2" autocomplete="new-password" />
            @if($errors->updatePassword->has('password'))
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                    @foreach($errors->updatePassword->get('password') as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block font-medium text-sm mb-2" style="color: var(--profile-text);">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="profile-input mt-1 block w-full px-3 py-2" autocomplete="new-password" />
            @if($errors->updatePassword->has('password_confirmation'))
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                    @foreach($errors->updatePassword->get('password_confirmation') as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="profile-btn-save">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="profile-status-message"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
