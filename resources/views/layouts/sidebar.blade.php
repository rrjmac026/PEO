<!-- sidebar.blade.php -->
<div x-data class="h-full">

    <!-- ── Mobile backdrop ── -->
    <div x-show="$store.sidebar.isOpen"
         x-cloak
         x-transition:enter="transition-opacity duration-300 ease-in-out"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-300 ease-in-out"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-30 bg-black/50 lg:hidden"
         @click="$store.sidebar.isOpen = false">
    </div>

    <!-- ── Sidebar ── -->
    <aside
        x-show="$store.sidebar.isOpen"
        x-cloak
        x-transition:enter="transform transition-transform duration-300 ease-in-out"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition-transform duration-300 ease-in-out"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed top-16 left-0 z-40 flex flex-col
               h-[calc(100vh-4rem)] w-72 max-w-[85vw]
               border-r border-gray-200 dark:border-gray-700/60
               bg-white dark:bg-[#1a1f2e]
               shadow-xl lg:shadow-md">

        <!-- ── Nav links (scrollable) ── -->
        <div class="flex-1 overflow-y-auto overflow-x-hidden"
             @click="if (($event.target.tagName === 'A' || $event.target.closest('a')) && window.innerWidth < 1024) {
                 $store.sidebar.isOpen = false
             }">
            @if(auth()->user()->role === 'user')
                @include('layouts.sidebar-user')
            @else
                @include('layouts.sidebar-admin')
            @endif
        </div>

        <!-- ── Footer pill ── -->
        <div class="p-3 flex-shrink-0">
            <div class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl
                        bg-orange-50  dark:bg-orange-950/40
                        border border-orange-100 dark:border-orange-900/50">
                <img src="{{ asset('assets/app_logo.PNG') }}"
                     alt="PEO Logo"
                     class="w-7 h-7 object-contain">
                <span class="text-xs font-semibold
                             text-orange-600 dark:text-orange-400
                             tracking-wide">
                    PEO v1.0
                </span>
            </div>
        </div>
    </aside>
</div>