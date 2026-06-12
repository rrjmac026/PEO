<x-app-layout>
    @push('styles')
        @include('user.work-requests.edit-partials._edit-styles')
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Work Request') }}
            </h2>
            <a href="{{ route('user.work-requests.show', $workRequest) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 wre-wrap">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="wre-card">
                <div class="p-6" style="color: var(--wr-text);">
                    <form action="{{ route('user.work-requests.update', $workRequest) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-6">
                            @include('user.work-requests.edit-partials._section-1-project-info')
                            @include('user.work-requests.edit-partials._section-2-request-details')
                            @include('user.work-requests.edit-partials._section-3-pay-items')
                            @include('user.work-requests.edit-partials._section-4-submission')

                            {{-- Action Buttons --}}
                            <div class="flex justify-end gap-4">
                                <a href="{{ route('user.work-requests.show', $workRequest) }}"
                                    class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Update Request') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('user.work-requests.edit-partials._edit-scripts')
    @endpush
</x-app-layout>