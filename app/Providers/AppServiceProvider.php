<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer([
            'layouts.sidebar-admin',
            'layouts.sidebar-reviewer',
            'layouts.sidebar-user',
        ], function ($view) {
            if (Auth::check()) {
                $base = Notification::where('user_id', Auth::id())->where('is_read', false);
                $view->with([
                    'sidebarUnreadTotal' => (clone $base)->count(),
                    'sidebarUnreadWR'    => (clone $base)->where('type', 'work_request')->count(),
                    'sidebarUnreadCP'    => (clone $base)->where('type', 'concrete_pouring')->count(),
                ]);
            }
        });
    }
}