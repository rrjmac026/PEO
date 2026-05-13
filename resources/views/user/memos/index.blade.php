<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Memos
            @if($unreadCount > 0)
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ $unreadCount }} unread
                </span>
            @endif
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Filters --}}
        <form method="GET" class="flex gap-3 mb-6">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search memos…"
                class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">

            <select name="type" class="rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500">
                <option value="">All Types</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit"
                class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                Filter
            </button>
            @if(request('search') || request('type'))
                <a href="{{ route('user.memos.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">
                    Clear
                </a>
            @endif
        </form>

        {{-- Memo list --}}
        <div class="space-y-3">
            @forelse($memos as $memo)
                @php $pivot = $memo->memoRecipients->first(); @endphp
                <a href="{{ route('user.memos.show', $memo) }}"
                   class="flex items-start gap-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100
                          hover:shadow-md transition {{ is_null($pivot?->read_at) ? 'border-l-4 border-l-indigo-500' : '' }}">

                    {{-- Icon --}}
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-{{ $memo->type_color }}-100
                                flex items-center justify-center text-{{ $memo->type_color }}-600">
                        <i class="fa-solid {{ $memo->type_icon }}"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ $memo->subject }}
                                @if(is_null($pivot?->read_at))
                                    <span class="ml-1 inline-block w-2 h-2 rounded-full bg-indigo-500"></span>
                                @endif
                            </p>
                            <span class="text-xs text-gray-400 whitespace-nowrap">
                                {{ $memo->sent_at?->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            From: {{ $memo->sender?->name }} &middot;
                            <span class="capitalize">{{ $memo->type_label }}</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1 line-clamp-1">
                            {!! strip_tags($memo->body) !!}
                        </p>
                    </div>
                </a>
            @empty
                <div class="text-center py-16 text-gray-400">
                    <i class="fa-solid fa-envelope-open text-4xl mb-3"></i>
                    <p>No memos yet.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $memos->links() }}</div>
    </div>
</x-app-layout>