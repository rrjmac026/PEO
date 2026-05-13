<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('user.memos.index') }}"
               class="text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800">{{ $memo->subject }}</h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Memo header --}}
            <div class="px-6 py-5 border-b border-gray-100 flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-{{ $memo->type_color }}-100
                            flex items-center justify-center text-{{ $memo->type_color }}-600 text-lg">
                    <i class="fa-solid {{ $memo->type_icon }}"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                        {{ $memo->reference_number }}
                    </p>
                    <h3 class="text-lg font-semibold text-gray-900 mt-0.5">{{ $memo->subject }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        From <span class="font-medium text-gray-700">{{ $memo->sender?->name }}</span>
                        &middot; {{ $memo->sent_at?->format('F d, Y g:i A') }}
                    </p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                             bg-{{ $memo->type_color }}-100 text-{{ $memo->type_color }}-700">
                    {{ $memo->type_label }}
                </span>
            </div>

            {{-- Body --}}
            <div class="px-6 py-6 prose prose-sm max-w-none text-gray-700">
                {!! $memo->body !!}
            </div>

            {{-- Attachments --}}
            @if(!empty($memo->attachments))
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Attachments</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($memo->attachments as $path)
                            <a href="{{ Storage::url($path) }}" target="_blank"
                               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200
                                      bg-white text-sm text-gray-700 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-paperclip text-gray-400"></i>
                                {{ basename($path) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Read receipt footer --}}
            <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 text-xs text-gray-400">
                @if($recipient->read_at)
                    <i class="fa-solid fa-check-double text-indigo-400 mr-1"></i>
                    Read {{ $recipient->read_at->format('F d, Y g:i A') }}
                @endif
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('user.memos.index') }}"
               class="text-sm text-indigo-600 hover:underline">
                &larr; Back to Memos
            </a>
        </div>
    </div>
</x-app-layout>