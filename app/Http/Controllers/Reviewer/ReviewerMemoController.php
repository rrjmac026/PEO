<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Memo;
use App\Models\MemoRecipient;
use Illuminate\Support\Facades\Auth;

class ReviewerMemoController extends Controller
{
    public function index()
    {
        $memos = Memo::whereHas('memoRecipients', fn ($q) =>
                $q->where('user_id', Auth::id())
            )
            ->with('sender')
            ->withCount('memoRecipients')
            ->where('status', Memo::STATUS_SENT)
            ->orderByDesc('sent_at')
            ->paginate(15);

        $unreadCount = MemoRecipient::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return view('reviewer.memos.index', compact('memos', 'unreadCount'));
    }

    public function show(Memo $memo)
    {
        // Ensure this reviewer is actually a recipient
        abort_unless(
            $memo->memoRecipients()->where('user_id', Auth::id())->exists(),
            403
        );

        $memo->load('sender', 'memoRecipients.user.employee');

        // Auto-mark as read on open
        MemoRecipient::where('memo_id', $memo->id)
            ->where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $recipient = $memo->memoRecipients()
            ->where('user_id', Auth::id())
            ->first();

        return view('reviewer.memos.show', compact('memo', 'recipient'));
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