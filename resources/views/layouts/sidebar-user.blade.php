<!-- User Sidebar Menu Items -->
<nav class="space-y-2 p-4">
    <a href="{{ route('user.dashboard') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('user.dashboard') ? 'bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
        <i class="fas fa-home w-5"></i>
        <span class="font-medium">Dashboard</span>
    </a>

    <a href="{{ route('user.work-requests.index') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <i class="fas fa-file-contract w-5"></i>
        <span class="font-medium">Work Request</span>
    </a>

    <a href="#" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <i class="fas fa-file-alt w-5"></i>
        <span class="font-medium">My Documents</span>
    </a>

    <a href="#" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <i class="fas fa-cog w-5"></i>
        <span class="font-medium">Settings</span>
    </a>
</nav>