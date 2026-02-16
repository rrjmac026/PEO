<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-orange-500 via-orange-600 to-orange-500 dark:from-orange-900 dark:via-orange-800 dark:to-orange-900 overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="relative px-6 py-12 sm:px-12">
                    <!-- Decorative background elements -->
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
                    
                    <div class="relative z-10">
                        <h1 class="text-3xl sm:text-4xl font-black text-white mb-2 tracking-tight">
                            Welcome back, {{ Auth::user()->name }}! 👋
                        </h1>
                        <p class="text-orange-100 text-lg font-medium">
                            Here's what's happening with your system today
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Total Work Requests -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md sm:rounded-xl hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Total Work Requests</p>
                                <p class="text-3xl font-black text-gray-900 dark:text-white">
                                    {{ $totalRequests }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Active in system</p>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900 dark:to-blue-800 rounded-xl flex items-center justify-center">
                                <i class="fas fa-file-contract text-blue-600 dark:text-blue-300 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-3 bg-blue-50 dark:bg-blue-900/20 border-t border-blue-100 dark:border-blue-900">
                        <a href="{{ route('admin.work-requests.index') }}" class="text-blue-600 dark:text-blue-400 text-sm font-semibold hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-2">
                            View All <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Total Employees -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md sm:rounded-xl hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Total Employees</p>
                                <p class="text-3xl font-black text-gray-900 dark:text-white">
                                    {{ $totalEmployees }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Registered</p>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-emerald-100 to-emerald-50 dark:from-emerald-900 dark:to-emerald-800 rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-emerald-600 dark:text-emerald-300 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-3 bg-emerald-50 dark:bg-emerald-900/20 border-t border-emerald-100 dark:border-emerald-900">
                        <a href="#" class="text-emerald-600 dark:text-emerald-400 text-sm font-semibold hover:text-emerald-700 dark:hover:text-emerald-300 flex items-center gap-2">
                            Manage Employees <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md sm:rounded-xl hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">System Users</p>
                                <p class="text-3xl font-black text-gray-900 dark:text-white">
                                    {{ $totalUsers }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Active accounts</p>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-50 dark:from-purple-900 dark:to-purple-800 rounded-xl flex items-center justify-center">
                                <i class="fas fa-user-shield text-purple-600 dark:text-purple-300 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-3 bg-purple-50 dark:bg-purple-900/20 border-t border-purple-100 dark:border-purple-900">
                        <a href="{{ route('admin.users.index') }}" class="text-purple-600 dark:text-purple-400 text-sm font-semibold hover:text-purple-700 dark:hover:text-purple-300 flex items-center gap-2">
                            Manage Users <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-lightning-bolt text-orange-500"></i>
                    Quick Actions
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Create Work Request -->
                    <a href="{{ route('admin.work-requests.create') }}" 
                       class="group relative bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 hover:translate-y-[-4px] border border-gray-100 dark:border-gray-700">
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-100/0 to-orange-50/0 group-hover:from-orange-100/50 group-hover:to-orange-50/50 dark:from-orange-900/0 dark:to-orange-800/0 dark:group-hover:from-orange-900/20 dark:group-hover:to-orange-800/20 rounded-xl transition-colors duration-300"></div>
                        <div class="relative z-10">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-900 dark:to-orange-800 rounded-lg flex items-center justify-center mb-4 group-hover:shadow-md transition-shadow">
                                <i class="fas fa-file-medical text-orange-600 dark:text-orange-300 text-lg"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">New Work Request</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Create a new work request</p>
                        </div>
                    </a>

                    <!-- Import Employees -->
                    <a href="#" 
                       class="group relative bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 hover:translate-y-[-4px] border border-gray-100 dark:border-gray-700">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-100/0 to-emerald-50/0 group-hover:from-emerald-100/50 group-hover:to-emerald-50/50 dark:from-emerald-900/0 dark:to-emerald-800/0 dark:group-hover:from-emerald-900/20 dark:group-hover:to-emerald-800/20 rounded-xl transition-colors duration-300"></div>
                        <div class="relative z-10">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-50 dark:from-emerald-900 dark:to-emerald-800 rounded-lg flex items-center justify-center mb-4 group-hover:shadow-md transition-shadow">
                                <i class="fas fa-upload text-emerald-600 dark:text-emerald-300 text-lg"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Import Employees</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Upload employee data</p>
                        </div>
                    </a>

                    <!-- View Reports -->
                    <a href="#" 
                       class="group relative bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 hover:translate-y-[-4px] border border-gray-100 dark:border-gray-700">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-100/0 to-blue-50/0 group-hover:from-blue-100/50 group-hover:to-blue-50/50 dark:from-blue-900/0 dark:to-blue-800/0 dark:group-hover:from-blue-900/20 dark:group-hover:to-blue-800/20 rounded-xl transition-colors duration-300"></div>
                        <div class="relative z-10">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900 dark:to-blue-800 rounded-lg flex items-center justify-center mb-4 group-hover:shadow-md transition-shadow">
                                <i class="fas fa-chart-bar text-blue-600 dark:text-blue-300 text-lg"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">View Reports</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">System analytics & reports</p>
                        </div>
                    </a>

                    <!-- Settings -->
                    <a href="#" 
                       class="group relative bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 hover:translate-y-[-4px] border border-gray-100 dark:border-gray-700">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-100/0 to-purple-50/0 group-hover:from-purple-100/50 group-hover:to-purple-50/50 dark:from-purple-900/0 dark:to-purple-800/0 dark:group-hover:from-purple-900/20 dark:group-hover:to-purple-800/20 rounded-xl transition-colors duration-300"></div>
                        <div class="relative z-10">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-50 dark:from-purple-900 dark:to-purple-800 rounded-lg flex items-center justify-center mb-4 group-hover:shadow-md transition-shadow">
                                <i class="fas fa-sliders-h text-purple-600 dark:text-purple-300 text-lg"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Settings</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Configure system settings</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-history text-orange-500"></i>
                    Recent Activity
                </h2>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md sm:rounded-xl">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Activity Item 1 -->
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-900 dark:to-orange-800 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-file-circle-plus text-orange-600 dark:text-orange-300"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">New system initialized</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Admin dashboard activated for Provincial Engineering Office</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Today at {{ now()->format('g:i A') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Item 2 -->
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-gradient-to-br from-emerald-100 to-emerald-50 dark:from-emerald-900 dark:to-emerald-800 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-users text-emerald-600 dark:text-emerald-300"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Employee module activated</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ready to manage employee records and information</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Today at {{ now()->format('g:i A') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Item 3 -->
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900 dark:to-blue-800 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check-circle text-blue-600 dark:text-blue-300"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">System configuration completed</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">All required modules have been configured and are ready for use</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Today at {{ now()->format('g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer with View More -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700">
                        <button class="text-orange-600 dark:text-orange-400 text-sm font-semibold hover:text-orange-700 dark:hover:text-orange-300 flex items-center gap-2">
                            View All Activity <i class="fas fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Footer Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Last Updated</p>
                    <p class="text-gray-900 dark:text-white font-semibold">{{ now()->format('F j, Y \a\t g:i A') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">System Status</p>
                    <div class="flex items-center justify-center gap-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <p class="text-gray-900 dark:text-white font-semibold">Operational</p>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Current Version</p>
                    <p class="text-gray-900 dark:text-white font-semibold">v{{ config('app.version', '1.0.0') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
