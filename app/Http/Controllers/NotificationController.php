<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Return the authenticated user's notifications as JSON (for the bell).
     */
    public function index()
    {
        $notifications = Notification::forUser(Auth::id())
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'created_at' => $n->created_at->diffForHumans(),
                'is_read'    => $n->is_read,
                'link'       => $n->link,
            ]);

        return response()->json($notifications);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Notification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark ALL notifications for the user as read.
     */
    public function markAllRead()
    {
        Notification::forUser(Auth::id())
            ->unread()
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Return unread count only (for polling).
     */
    public function unreadCount()
    {
        $count = Notification::forUser(Auth::id())->unread()->count();

        return response()->json(['count' => $count]);
    }

    public function page(Request $request)
    {
        $user       = Auth::user();
        $filter     = $request->input('filter', 'all');
        $typeFilter = $request->input('type', '');

        $base = Notification::forUser($user->id)->latest();

        $totalCount  = (clone $base)->count();
        $unreadCount = (clone $base)->unread()->count();
        $readCount   = $totalCount - $unreadCount;
        $wrCount     = (clone $base)->where('type', 'work_request')->count();
        $cpCount     = (clone $base)->where('type', 'concrete_pouring')->count();

        $query = Notification::forUser($user->id)->latest();

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }

        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }

        $notifications = $query->paginate(20)->withQueryString();

        $grouped = $notifications->getCollection()->groupBy(function ($n) {
            if ($n->created_at->isToday())     return 'Today';
            if ($n->created_at->isYesterday()) return 'Yesterday';
            return $n->created_at->format('F j, Y');
        });

        $reviewerRoles = ['site_inspector','surveyor','resident_engineer','provincial_engineer','mtqa','engineeriii','engineeriv'];

        if ($user->role === 'admin') {
            $dashRoute = route('admin.dashboard');
        } elseif ($user->role === 'contractor') {
            $dashRoute = route('user.dashboard');
        } elseif (in_array($user->role, $reviewerRoles)) {
            $dashRoute = route('reviewer.dashboard');
        } else {
            $dashRoute = '/';
        }

        return view('notifications.index', compact(
            'notifications', 'grouped', 'filter', 'typeFilter',
            'totalCount', 'unreadCount', 'readCount',
            'wrCount', 'cpCount', 'dashRoute'
        ));
    }
}