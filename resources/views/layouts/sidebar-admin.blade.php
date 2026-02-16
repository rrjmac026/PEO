<!-- Admin Sidebar Menu Items -->
<nav class="space-y-2 p-4">
    <a href="{{ route('admin.dashboard') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
        <i class="fas fa-chart-line w-5"></i>
        <span class="font-medium">Dashboard</span>
    </a>

    <a href="{{ route('admin.users.index') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <i class="fas fa-users w-5"></i>
        <span class="font-medium">Users</span>
    </a>
    <a href="{{ route('admin.employees.index') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <i class="fas fa-id-badge w-5"></i>
        <span class="font-medium">Employees</span>
    </a>
    <a href="{{ route('admin.work-requests.index') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <i class="fas fa-file-contract w-5"></i>
        <span class="font-medium">Work Request</span>
    </a>

    <a href="{{ route('admin.work-request-logs.index') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <i class="fas fa-history w-5"></i>
        <span class="font-medium">Work Request Logs</span>



    <a href="#" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <i class="fas fa-sliders-h w-5"></i>
        <span class="font-medium">Settings</span>
    </a>
</nav>
