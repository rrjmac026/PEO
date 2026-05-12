@php
    $sidebarUnreadTotal = \App\Models\Notification::where('user_id', Auth::id())
        ->where('is_read', false)->count();

    $sidebarUnreadWR = \App\Models\Notification::where('user_id', Auth::id())
        ->where('is_read', false)->where('type', 'work_request')->count();

    $sidebarUnreadCP = \App\Models\Notification::where('user_id', Auth::id())
        ->where('is_read', false)->where('type', 'concrete_pouring')->count();
@endphp