<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create Work Request') }}
            </h2>
            <a href="{{ route('admin.work-requests.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    @include('admin.work-requests.partials.styles')

    <div class="py-8 wr-wrap">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @include('admin.work-requests.partials.progress')

            <div class="wr-card">
                <div class="wr-card-body">
                    <form id="wr-form" action="{{ route('admin.work-requests.store') }}" method="POST" novalidate>
                        @csrf

                        @include('admin.work-requests.partials.step-1')
                        @include('admin.work-requests.partials.step-2')
                        @include('admin.work-requests.partials.step-3')
                        @include('admin.work-requests.partials.step-4')
                        @include('admin.work-requests.partials.step-5')
                        @include('admin.work-requests.partials.step-6')
                        @include('admin.work-requests.partials.step-7')

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="wr-float-save" id="wr-float-save">
        <span class="wr-float-dot"></span>
        Draft saved
    </div>

    @include('admin.work-requests.partials.scripts')

</x-app-layout>