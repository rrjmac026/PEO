<!-- sidebar.blade.php -->
<div x-data class="h-full">
    <!-- Backdrop for mobile -->
    <div x-show="$store.sidebar.isOpen && window.innerWidth < 1024" x-cloak
        class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
        @click="$store.sidebar.toggle()">
    </div>

    <!-- Sidebar -->
    <aside 
        x-show="$store.sidebar.isOpen"
        x-cloak
        x-transition:enter="transform transition-transform duration-300 ease-in-out"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition-transform duration-300 ease-in-out"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        @click.outside="$store.sidebar.isOpen = false"
        @click.capture="if ($event.target.closest('a') && window.innerWidth < 1024) $store.sidebar.isOpen = false"
        :class="{
            'fixed': window.innerWidth < 1024,
            'absolute lg:fixed': window.innerWidth >= 1024
        }"
        class="top-16 left-0 h-[calc(100vh-4rem)] w-72 max-w-[85vw] sm:max-w-72 border-r shadow-xl z-40 flex flex-col">
        
        <!-- Main content wrapper with scrolling -->
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

        <!-- Footer (not affected by scroll) -->
        <div class="p-3 sm:p-4 flex-shrink-0">
            <div class="p-3 sm:p-4 rounded-xl shadow-lg bg-[#FFEEF2]">
                <div class="flex flex-col items-center justify-center gap-2">
                    <div class="flex items-center gap-2 text-[#FF92C2]">
                        <img src="{{ asset('assets/app_logo.PNG') }}" alt="GAS Logo" 
                         class="w-8 h-8 object-contain">
                        <span class="text-xs sm:text-sm font-medium">GAS v1.0</span>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>