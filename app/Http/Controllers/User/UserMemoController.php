<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Memo;
use App\Models\MemoRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMemoController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Memo::whereHas('memoRecipients', fn ($q) => $q->where('user_id', $userId))
            ->with(['sender', 'memoRecipients' => fn ($q) => $q->where('user_id', $userId)])
            ->where('status', Memo::STATUS_SENT)
            ->orderByDesc('sent_at');

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        $memos = $query->paginate(15)->withQueryString();

        // Unread count for badge
        $unreadCount = MemoRecipient::where('user_id', $userId)
            ->whereNull('read_at')
            ->whereHas('memo', fn ($q) => $q->where('status', Memo::STATUS_SENT))
            ->count();

        return view('user.memos.index', [
            'memos'       => $memos,
            'types'       => Memo::types(),
            'unreadCount' => $unreadCount,
        ]);
    }

    public function show(Memo $memo)
    {
        $userId = Auth::id();

        // Gate: ensure this user is actually a recipient
        $recipient = MemoRecipient::where('memo_id', $memo->id)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Auto mark-read on open
        $recipient->markRead();

        $memo->load('sender');

        return view('user.memos.show', compact('memo', 'recipient'));
    }

    public function markRead(Memo $memo)
    {
        MemoRecipient::where('memo_id', $memo->id)
            ->where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }
}