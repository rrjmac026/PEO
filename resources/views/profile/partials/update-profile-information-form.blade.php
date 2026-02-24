<section class="profile-form-group">
    <header class="profile-header">
        <h2>{{ __('Profile Information') }}</h2>
        <p>{{ __("Update your account's profile information and email address.") }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block font-medium text-sm mb-2" style="color: var(--profile-text);">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="profile-input mt-1 block w-full px-3 py-2" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            @if($errors->has('name'))
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                    @foreach($errors->get('name') as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <label for="email" class="block font-medium text-sm mb-2" style="color: var(--profile-text);">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="profile-input mt-1 block w-full px-3 py-2" :value="old('email', $user->email)" required autocomplete="username" />
            @if($errors->has('email'))
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                    @foreach($errors->get('email') as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                </div>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2" style="color: var(--profile-text);">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm hover:opacity-70 transition" style="color: #3b82f6;">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="profile-btn-save">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
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
