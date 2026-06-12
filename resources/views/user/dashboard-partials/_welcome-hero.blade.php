{{-- ============================================================
     Welcome Hero Banner
     ============================================================ --}}
<div class="relative overflow-hidden shadow-xl sm:rounded-2xl
            bg-gradient-to-r from-orange-500 via-orange-600 to-orange-500
            dark:from-orange-950 dark:via-orange-900 dark:to-orange-950
            dark:border dark:border-orange-800/40">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 dark:bg-white/5 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16 pointer-events-none"></div>
    <div class="absolute top-1/2 right-1/4 w-24 h-24 bg-orange-300/20 dark:bg-orange-600/10 rounded-full blur-2xl pointer-events-none"></div>
    <div class="relative px-6 py-12 sm:px-12 z-10">
        <h1 class="text-3xl sm:text-4xl font-black text-white dark:text-orange-50 mb-2 tracking-tight">
            Welcome back, {{ Auth::user()->name }}! 👋
        </h1>
        <p class="text-orange-100 dark:text-orange-300/80 text-lg font-medium">
            Here's what's happening with your work requests today
        </p>
    </div>
</div>