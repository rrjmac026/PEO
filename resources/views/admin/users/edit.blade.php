<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit User') }} - {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Name Field --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Full Name') }}
                                <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $user->name) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm @error('name') is-invalid border-red-500 @enderror"
                                placeholder="Enter full name"
                                required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email Field --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Email Address') }}
                                <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email', $user->email) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm @error('email') is-invalid border-red-500 @enderror"
                                placeholder="Enter email address"
                                required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Role Field --}}
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Role') }}
                                <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="role" 
                                name="role"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm @error('role') is-invalid border-red-500 @enderror"
                                required>
                                <option value="">{{ __('Select a role') }}</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>{{ __('User') }}</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Section --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Change Password (Optional)') }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                {{ __('Leave blank if you dont want to change the password') }}
                            </p>

                            {{-- New Password Field --}}
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('New Password') }}
                                </label>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm @error('password') is-invalid border-red-500 @enderror"
                                    placeholder="Enter new password">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm Password Field --}}
                            <div class="mt-4">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Confirm Password') }}
                                </label>
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm"
                                    placeholder="Confirm new password">
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="flex gap-3 pt-6">
                            <button 
                                type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-orange-600 dark:bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-orange-600 focus:bg-orange-700 dark:focus:bg-orange-600 active:bg-orange-900 dark:active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i>
                                {{ __('Update User') }}
                            </button>
                            <a 
                                href="{{ route('admin.users.index') }}" 
                                class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Additional Info --}}
            <div class="mt-6 bg-amber-50 dark:bg-amber-900 border-l-4 border-amber-500 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-amber-500 dark:text-amber-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-amber-700 dark:text-amber-300">
                            <strong>{{ __('User Created:') }}</strong> {{ $user->created_at->format('F d, Y \a\t H:i A') }}
                        </p>
                        @if($user->updated_at != $user->created_at)
                            <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                                <strong>{{ __('Last Updated:') }}</strong> {{ $user->updated_at->format('F d, Y \a\t H:i A') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
