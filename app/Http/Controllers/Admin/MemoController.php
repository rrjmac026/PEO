<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\MemoMail;
use App\Models\Memo;
use App\Models\MemoRecipient;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MemoController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Memo::with('sender')
            ->withCount('memoRecipients')
            ->orderByDesc('created_at');

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $memos = $query->paginate(15)->withQueryString();

        return view('admin.memos.index', [
            'memos'  => $memos,
            'types'  => Memo::types(),
            'stats'  => [
                'total'     => Memo::count(),
                'sent'      => Memo::sent()->count(),
                'draft'     => Memo::draft()->count(),
                'scheduled' => Memo::scheduled()->count(),
            ],
        ]);
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create()
    {
        $users       = User::orderBy('name')->get();
        $departments = \App\Models\Employee::distinct()->pluck('department')->sort()->values();
        $roles       = ['admin', 'contractor', 'resident_engineer', 'provincial_engineer', 'mtqa'];

        return view('admin.memos.create', compact('users', 'departments', 'roles'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'               => 'required|in:' . implode(',', array_keys(Memo::types())),
            'subject'            => 'required|string|max:255',
            'body'               => 'required|string',
            'recipient_scope'    => 'required|in:all,by_role,by_department,specific',
            'target_roles'       => 'nullable|array',
            'target_roles.*'     => 'string',
            'target_departments' => 'nullable|array',
            'target_departments.*'=> 'string',
            'specific_user_ids'  => 'nullable|array',
            'specific_user_ids.*'=> 'exists:users,id',
            'scheduled_at'       => 'nullable|date|after:now',
            'attachments.*'      => 'nullable|file|max:10240',
            'action'             => 'required|in:draft,send,schedule',
        ]);

        DB::beginTransaction();

        try {
            // Handle file uploads
            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $attachmentPaths[] = $file->store('memos/attachments', 'public');
                }
            }

            $status = match ($data['action']) {
                'send'     => Memo::STATUS_SENT,
                'schedule' => Memo::STATUS_SCHEDULED,
                default    => Memo::STATUS_DRAFT,
            };

            $memo = Memo::create([
                'type'               => $data['type'],
                'subject'            => $data['subject'],
                'body'               => $data['body'],
                'sent_by_user_id'    => Auth::id(),
                'status'             => $status,
                'scheduled_at'       => $data['action'] === 'schedule' ? $data['scheduled_at'] : null,
                'sent_at'            => $data['action'] === 'send' ? now() : null,
                'recipient_scope'    => $data['recipient_scope'],
                'target_roles'       => $data['target_roles'] ?? null,
                'target_departments' => $data['target_departments'] ?? null,
                'attachments'        => $attachmentPaths ?: null,
            ]);

            // Resolve recipient user IDs
            $userIds = $this->resolveRecipientIds($memo, $data);

            // Create memo_recipients rows
            $rows = array_map(fn ($uid) => [
                'memo_id'    => $memo->id,
                'user_id'    => $uid,
                'created_at' => now(),
                'updated_at' => now(),
            ], $userIds);

            MemoRecipient::insert($rows);

            // If sending now, dispatch emails & in-app notifications
            if ($status === Memo::STATUS_SENT) {
                $this->dispatchNotifications($memo, $userIds);
            }

            DB::commit();

            $msg = match ($status) {
                Memo::STATUS_SENT      => "Memo sent to " . count($userIds) . " recipient(s).",
                Memo::STATUS_SCHEDULED => "Memo scheduled for {$memo->scheduled_at->format('M d, Y g:i A')}.",
                default                => "Memo saved as draft.",
            };

            return redirect()->route('admin.memos.show', $memo)
                ->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to save memo: ' . $e->getMessage());
        }
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Memo $memo)
    {
        $memo->load([
            'sender',
            'memoRecipients.user.employee',
        ]);

        return view('admin.memos.show', compact('memo'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit(Memo $memo)
    {
        abort_if($memo->status === Memo::STATUS_SENT, 403, 'Sent memos cannot be edited.');

        $users       = User::orderBy('name')->get();
        $departments = \App\Models\Employee::distinct()->pluck('department')->sort()->values();
        $roles       = ['admin', 'contractor', 'resident_engineer', 'provincial_engineer', 'mtqa'];
        $existingRecipientIds = $memo->memoRecipients()->pluck('user_id')->toArray();

        return view('admin.memos.edit', compact('memo', 'users', 'departments', 'roles', 'existingRecipientIds'));
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(Request $request, Memo $memo)
    {
        abort_if($memo->status === Memo::STATUS_SENT, 403, 'Sent memos cannot be edited.');

        $data = $request->validate([
            'type'               => 'required|in:' . implode(',', array_keys(Memo::types())),
            'subject'            => 'required|string|max:255',
            'body'               => 'required|string',
            'recipient_scope'    => 'required|in:all,by_role,by_department,specific',
            'target_roles'       => 'nullable|array',
            'target_roles.*'     => 'string',
            'target_departments' => 'nullable|array',
            'target_departments.*'=> 'string',
            'specific_user_ids'  => 'nullable|array',
            'specific_user_ids.*'=> 'exists:users,id',
            'scheduled_at'       => 'nullable|date|after:now',
            'attachments.*'      => 'nullable|file|max:10240',
            'action'             => 'required|in:draft,send,schedule',
        ]);

        DB::beginTransaction();

        try {
            // Handle new file uploads
            $attachmentPaths = $memo->attachments ?? [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $attachmentPaths[] = $file->store('memos/attachments', 'public');
                }
            }

            $status = match ($data['action']) {
                'send'     => Memo::STATUS_SENT,
                'schedule' => Memo::STATUS_SCHEDULED,
                default    => Memo::STATUS_DRAFT,
            };

            $memo->update([
                'type'               => $data['type'],
                'subject'            => $data['subject'],
                'body'               => $data['body'],
                'status'             => $status,
                'scheduled_at'       => $data['action'] === 'schedule' ? $data['scheduled_at'] : null,
                'sent_at'            => $data['action'] === 'send' ? now() : null,
                'recipient_scope'    => $data['recipient_scope'],
                'target_roles'       => $data['target_roles'] ?? null,
                'target_departments' => $data['target_departments'] ?? null,
                'attachments'        => $attachmentPaths ?: null,
            ]);

            // Rebuild recipients
            $memo->memoRecipients()->delete();
            $userIds = $this->resolveRecipientIds($memo, $data);
            $rows = array_map(fn ($uid) => [
                'memo_id'    => $memo->id,
                'user_id'    => $uid,
                'created_at' => now(),
                'updated_at' => now(),
            ], $userIds);
            MemoRecipient::insert($rows);

            if ($status === Memo::STATUS_SENT) {
                $this->dispatchNotifications($memo, $userIds);
            }

            DB::commit();

            return redirect()->route('admin.memos.show', $memo)
                ->with('success', 'Memo updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update memo: ' . $e->getMessage());
        }
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Memo $memo)
    {
        $memo->delete();
        return redirect()->route('admin.memos.index')
            ->with('success', 'Memo deleted.');
    }

    // ── Send Now (from draft/scheduled) ──────────────────────────────────────

    public function sendNow(Memo $memo)
    {
        abort_if($memo->status === Memo::STATUS_SENT, 403, 'Memo already sent.');

        $userIds = $memo->memoRecipients()->pluck('user_id')->toArray();

        $memo->update(['status' => Memo::STATUS_SENT, 'sent_at' => now()]);

        $this->dispatchNotifications($memo, $userIds);

        return redirect()->route('admin.memos.show', $memo)
            ->with('success', "Memo sent to " . count($userIds) . " recipient(s).");
    }

    // ── Cancel scheduled ─────────────────────────────────────────────────────

    public function cancel(Memo $memo)
    {
        abort_if($memo->status !== Memo::STATUS_SCHEDULED, 403, 'Only scheduled memos can be cancelled.');

        $memo->update(['status' => Memo::STATUS_CANCELLED]);

        return redirect()->route('admin.memos.show', $memo)
            ->with('success', 'Memo cancelled.');
    }

    // ── Remove attachment ─────────────────────────────────────────────────────

    public function removeAttachment(Request $request, Memo $memo)
    {
        $path        = $request->input('path');
        $attachments = collect($memo->attachments ?? [])
            ->reject(fn ($p) => $p === $path)
            ->values()
            ->toArray();

        Storage::disk('public')->delete($path);
        $memo->update(['attachments' => $attachments]);

        return back()->with('success', 'Attachment removed.');
    }

    // ── Mark read (for recipient) ─────────────────────────────────────────────

    public function markRead(Memo $memo)
    {
        MemoRecipient::where('memo_id', $memo->id)
            ->where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function resolveRecipientIds(Memo $memo, array $data): array
    {
        return match ($data['recipient_scope']) {
            Memo::SCOPE_ALL => User::pluck('id')->toArray(),

            Memo::SCOPE_BY_ROLE => User::whereIn('role', $data['target_roles'] ?? [])
                ->pluck('id')->toArray(),

            Memo::SCOPE_BY_DEPT => User::whereHas('employee', fn ($q) =>
                    $q->whereIn('department', $data['target_departments'] ?? [])
                )->pluck('id')->toArray(),

            default => array_map('intval', $data['specific_user_ids'] ?? []),
        };
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function dispatchNotifications(Memo $memo, array $userIds): void
    {
        $recipients = User::whereIn('id', $userIds)->get();

        $reviewerRoles = [
            'site_inspector', 'surveyor', 'resident_engineer',
            'provincial_engineer', 'mtqa', 'engineeriii', 'engineeriv',
        ];

        foreach ($recipients as $user) {
            $link = match(true) {
                $user->role === 'admin'               => route('admin.memos.show', $memo),
                $user->role === 'contractor'          => route('user.memos.show', $memo),
                in_array($user->role, $reviewerRoles) => \Illuminate\Support\Facades\Route::has('reviewer.memos.show')
                                                            ? route('reviewer.memos.show', $memo)
                                                            : route('admin.memos.show', $memo),
                default                               => route('user.memos.show', $memo),
            };

            Notification::send(
                [$user->id],
                'memo',
                "[{$memo->type_label}] {$memo->subject}",
                "You have received a new memo from {$memo->sender->name}.",
                $link,
                $memo
            );

            try {
                // Use send() instead of queue() so exceptions are catchable here.
                // If you need async, switch to a dedicated SendMemoMail job that
                // handles its own retry/failure logging.
                Mail::to($user->email)->send(new MemoMail($memo, $user));

                MemoRecipient::where('memo_id', $memo->id)
                    ->where('user_id', $user->id)
                    ->update(['email_sent_at' => now()]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("MemoMail failed for user {$user->id}: " . $e->getMessage());

                MemoRecipient::where('memo_id', $memo->id)
                    ->where('user_id', $user->id)
                    ->update(['email_failed' => true]);
            }
        }
    }
}