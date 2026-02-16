<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('User Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="inline-flex items-center px-4 py-2 bg-orange-600 dark:bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-orange-600 focus:bg-orange-700 dark:focus:bg-orange-600 active:bg-orange-900 dark:active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-edit mr-2"></i>
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('admin.users.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- User Profile Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- User Header --}}
                    <div class="flex items-start gap-6">
                        {{-- Avatar/Icon --}}
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-24 w-24 rounded-full bg-gradient-to-r from-orange-400 to-orange-600 text-white text-3xl font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        </div>

                        {{-- User Info --}}
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                {{ $user->name }}
                            </h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                {{ $user->email }}
                            </p>
                            <div class="mt-4">
                                @php
                                    $roleColors = [
                                        'admin' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'user' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                    ];
                                    $colorClass = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- User Information Details --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                        {{ __('Account Information') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Full Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Full Name') }}
                            </label>
                            <p class="mt-2 text-base text-gray-900 dark:text-gray-100 font-semibold">
                                {{ $user->name }}
                            </p>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Email Address') }}
                            </label>
                            <p class="mt-2 text-base text-gray-900 dark:text-gray-100 font-semibold break-all">
                                {{ $user->email }}
                            </p>
                        </div>

                        {{-- Role --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Role') }}
                            </label>
                            <p class="mt-2 text-base text-gray-900 dark:text-gray-100 font-semibold">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </p>
                        </div>

                        {{-- User ID --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('User ID') }}
                            </label>
                            <p class="mt-2 text-base text-gray-900 dark:text-gray-100 font-semibold">
                                #{{ $user->id }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timestamps --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Created Date --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Account Created') }}
                    </h3>
                    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $user->created_at->format('M d, Y') }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $user->created_at->format('h:i A') }}
                    </p>
                </div>

                {{-- Last Updated Date --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Last Updated') }}
                    </h3>
                    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $user->updated_at->format('M d, Y') }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $user->updated_at->format('h:i A') }}
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex gap-3">
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="inline-flex items-center px-6 py-3 bg-orange-600 dark:bg-orange-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-orange-600 focus:bg-orange-700 dark:focus:bg-orange-600 active:bg-orange-900 dark:active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-edit mr-2"></i>
                    {{ __('Edit User') }}
                </a>
                <form action="{{ route('admin.users.destroy', $user) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-900 dark:active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <i class="fas fa-trash mr-2"></i>
                        {{ __('Delete User') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
