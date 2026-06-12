{{-- resources/views/user/dashboard-partials/_status-alerts.blade.php --}}
@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();

    // ── Work Requests ─────────────────────────────────────────────────────────
    // Approved WRs not yet acknowledged (approved in last 7 days)
    $approvedWrs = \App\Models\WorkRequest::where('contractor_name', $user->name)
        ->where('status', \App\Models\WorkRequest::STATUS_APPROVED)
        ->where('accepted_date', '>=', now()->subDays(7))
        ->orWhere(function($q) use ($user) {
            $q->where('contractor_name', $user->name)
              ->where('status', \App\Models\WorkRequest::STATUS_APPROVED)
              ->whereNull('accepted_date');
        })
        ->latest()
        ->get();

    $rejectedWrs = \App\Models\WorkRequest::where('contractor_name', $user->name)
        ->where('status', \App\Models\WorkRequest::STATUS_REJECTED)
        ->where('updated_at', '>=', now()->subDays(7))
        ->latest()
        ->get();

    $inReviewWrs = \App\Models\WorkRequest::where('contractor_name', $user->name)
        ->whereIn('status', [
            \App\Models\WorkRequest::STATUS_IN_REVIEW,
            \App\Models\WorkRequest::STATUS_ASSIGNED,
        ])
        ->latest()
        ->get();

    // ── Concrete Pourings ─────────────────────────────────────────────────────
    $approvedCps = \App\Models\ConcretePouring::where('requested_by_user_id', $user->id)
        ->where('status', 'approved')
        ->where('updated_at', '>=', now()->subDays(7))
        ->latest()
        ->get();

    $disapprovedCps = \App\Models\ConcretePouring::where('requested_by_user_id', $user->id)
        ->where('status', 'disapproved')
        ->where('updated_at', '>=', now()->subDays(7))
        ->latest()
        ->get();

    $pendingCps = \App\Models\ConcretePouring::where('requested_by_user_id', $user->id)
        ->where('status', 'requested')
        ->whereNotNull('current_review_step')
        ->latest()
        ->get();

    $unreadMemos = \App\Models\MemoRecipient::with('memo')
        ->where('user_id', $user->id)
        ->whereNull('read_at')
        ->whereHas('memo', fn($q) => $q->where('status', 'sent'))
        ->latest()
        ->get();
    $latestUnreadMemo = $unreadMemos->first()?->memo;

    $hasAlerts = $approvedWrs->isNotEmpty()
        || $rejectedWrs->isNotEmpty()
        || $inReviewWrs->isNotEmpty()
        || $approvedCps->isNotEmpty()
        || $disapprovedCps->isNotEmpty()
        || $pendingCps->isNotEmpty()
        || $unreadMemos->isNotEmpty();
@endphp

@if($hasAlerts)
<div class="space-y-3" id="user-status-alerts">

    {{-- ── Unread Memos Alert ─────────────────────────────────────────────── --}}
        @if($unreadMemos->isNotEmpty())
        <div id="alert-user-memo" style="border-radius: 12px; overflow: hidden; border: 1px solid #bfdbfe; background: #eff6ff; position: relative;">
            <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #2563eb, #3b82f6);"></div>
            <div style="padding: 14px 16px 14px 24px; display: flex; align-items: flex-start; gap: 14px;">
                {{-- Icon --}}
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #dbeafe; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;">
                    <i class="fas fa-envelope-open-text" style="color: #2563eb; font-size: 16px;"></i>
                    <span style="position: absolute; top: -3px; right: -3px; width: 10px; height: 10px; border-radius: 50%; background: #ef4444; border: 2px solid #eff6ff; animation: alert-pulse 1.8s ease-in-out infinite;"></span>
                </div>
                {{-- Body --}}
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                        <p style="font-size: 14px; font-weight: 700; color: #1e40af; margin: 0;">
                            You have {{ $unreadMemos->count() }} unread {{ Str::plural('memo', $unreadMemos->count()) }}
                        </p>
                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 10px; background: #2563eb; color: #fff; font-size: 11px; font-weight: 800;">
                            {{ $unreadMemos->count() }}
                        </span>
                    </div>
                    @if($latestUnreadMemo)
                    <p style="font-size: 13px; color: #1d4ed8; margin: 0 0 10px;">
                        Latest: <span style="font-weight: 600;">{{ Str::limit($latestUnreadMemo->subject, 60) }}</span>
                        <span style="font-weight: 400; opacity: 0.75;">· {{ $latestUnreadMemo->created_at->diffForHumans() }}</span>
                    </p>
                    @endif
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <a href="{{ route('user.memos.index') }}"
                        style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #2563eb; color: #fff; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                        onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                            <i class="fas fa-envelope" style="font-size: 10px;"></i>
                            View Memos
                        </a>
                        @if($latestUnreadMemo)
                        <a href="{{ route('user.memos.show', $latestUnreadMemo) }}"
                        style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                        onmouseover="this.style.background='#bfdbfe'" onmouseout="this.style.background='#dbeafe'">
                            <i class="fas fa-eye" style="font-size: 10px;"></i>
                            Read Latest
                        </a>
                        @endif
                    </div>
                </div>
                {{-- Dismiss --}}
                <button onclick="dismissAlert('alert-user-memo')"
                        style="flex-shrink: 0; width: 28px; height: 28px; border-radius: 7px; background: none; border: none; cursor: pointer; color: #3b82f6; display: flex; align-items: center; justify-content: center; transition: background 0.15s;"
                        onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='transparent'"
                        title="Dismiss">
                    <i class="fas fa-times" style="font-size: 12px;"></i>
                </button>
            </div>
        </div>
        @endif

    {{-- ── Work Request: Approved ─────────────────────────────────────────── --}}
    @if($approvedWrs->isNotEmpty())
    <div id="alert-wr-approved" style="border-radius: 12px; overflow: hidden; border: 1px solid #bbf7d0; background: #f0fdf4; position: relative;">
        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #16a34a, #22c55e);"></div>
        <div style="padding: 14px 16px 14px 24px; display: flex; align-items: flex-start; gap: 14px;">
            {{-- Icon --}}
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #dcfce7; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;">
                <i class="fas fa-circle-check" style="color: #16a34a; font-size: 16px;"></i>
                <span style="position: absolute; top: -3px; right: -3px; width: 10px; height: 10px; border-radius: 50%; background: #16a34a; border: 2px solid #f0fdf4;"></span>
            </div>
            {{-- Body --}}
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                    <p style="font-size: 14px; font-weight: 700; color: #14532d; margin: 0;">
                        {{ $approvedWrs->count() }} work {{ Str::plural('request', $approvedWrs->count()) }} approved! 🎉
                    </p>
                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 10px; background: #16a34a; color: #fff; font-size: 11px; font-weight: 800;">
                        {{ $approvedWrs->count() }}
                    </span>
                </div>
                <p style="font-size: 13px; color: #15803d; margin: 0 0 10px;">
                    Latest: <span style="font-weight: 600;">{{ Str::limit($approvedWrs->first()->name_of_project, 55) }}</span>
                    <span style="font-weight: 400; opacity: 0.75;">· {{ $approvedWrs->first()->updated_at->diffForHumans() }}</span>
                </p>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('user.work-requests.index') }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #16a34a; color: #fff; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                        <i class="fas fa-list" style="font-size: 10px;"></i>
                        View Requests
                    </a>
                    <a href="{{ route('user.work-requests.show', $approvedWrs->first()) }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #dcfce7; color: #14532d; border: 1px solid #bbf7d0; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#bbf7d0'" onmouseout="this.style.background='#dcfce7'">
                        <i class="fas fa-eye" style="font-size: 10px;"></i>
                        View Latest
                    </a>
                </div>
            </div>
            {{-- Dismiss --}}
            <button onclick="dismissAlert('alert-wr-approved')"
                    style="flex-shrink: 0; width: 28px; height: 28px; border-radius: 7px; background: none; border: none; cursor: pointer; color: #22c55e; display: flex; align-items: center; justify-content: center; transition: background 0.15s;"
                    onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='transparent'"
                    title="Dismiss">
                <i class="fas fa-times" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ── Work Request: Rejected ──────────────────────────────────────────── --}}
    @if($rejectedWrs->isNotEmpty())
    <div id="alert-wr-rejected" style="border-radius: 12px; overflow: hidden; border: 1px solid #fecaca; background: #fff1f2; position: relative;">
        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #dc2626, #ef4444);"></div>
        <div style="padding: 14px 16px 14px 24px; display: flex; align-items: flex-start; gap: 14px;">
            {{-- Icon --}}
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #fee2e2; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;">
                <i class="fas fa-circle-xmark" style="color: #dc2626; font-size: 16px;"></i>
                <span style="position: absolute; top: -3px; right: -3px; width: 10px; height: 10px; border-radius: 50%; background: #ef4444; border: 2px solid #fff1f2; animation: alert-pulse 1.8s ease-in-out infinite;"></span>
            </div>
            {{-- Body --}}
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                    <p style="font-size: 14px; font-weight: 700; color: #7f1d1d; margin: 0;">
                        {{ $rejectedWrs->count() }} work {{ Str::plural('request', $rejectedWrs->count()) }} rejected
                    </p>
                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 10px; background: #dc2626; color: #fff; font-size: 11px; font-weight: 800;">
                        {{ $rejectedWrs->count() }}
                    </span>
                </div>
                <p style="font-size: 13px; color: #991b1b; margin: 0 0 10px;">
                    Latest: <span style="font-weight: 600;">{{ Str::limit($rejectedWrs->first()->name_of_project, 55) }}</span>
                    @if($rejectedWrs->first()->approved_recommendation_action)
                        <span style="font-weight: 400; opacity: 0.8;"> — "{{ Str::limit($rejectedWrs->first()->approved_recommendation_action, 60) }}"</span>
                    @endif
                </p>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('user.work-requests.show', $rejectedWrs->first()) }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #dc2626; color: #fff; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                        <i class="fas fa-eye" style="font-size: 10px;"></i>
                        View Details
                    </a>
                    <a href="{{ route('user.work-requests.create') }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #fee2e2; color: #7f1d1d; border: 1px solid #fecaca; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                        <i class="fas fa-rotate-right" style="font-size: 10px;"></i>
                        Resubmit
                    </a>
                </div>
            </div>
            <button onclick="dismissAlert('alert-wr-rejected')"
                    style="flex-shrink: 0; width: 28px; height: 28px; border-radius: 7px; background: none; border: none; cursor: pointer; color: #ef4444; display: flex; align-items: center; justify-content: center; transition: background 0.15s;"
                    onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='transparent'"
                    title="Dismiss">
                <i class="fas fa-times" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ── Work Request: In Review ─────────────────────────────────────────── --}}
    @if($inReviewWrs->isNotEmpty())
    <div id="alert-wr-in-review" style="border-radius: 12px; overflow: hidden; border: 1px solid #bfdbfe; background: #eff6ff; position: relative;">
        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #2563eb, #3b82f6);"></div>
        <div style="padding: 14px 16px 14px 24px; display: flex; align-items: flex-start; gap: 14px;">
            {{-- Icon --}}
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #dbeafe; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;">
                <i class="fas fa-spinner" style="color: #2563eb; font-size: 16px; animation: spin-slow 2s linear infinite;"></i>
            </div>
            {{-- Body --}}
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                    <p style="font-size: 14px; font-weight: 700; color: #1e40af; margin: 0;">
                        {{ $inReviewWrs->count() }} work {{ Str::plural('request', $inReviewWrs->count()) }} currently under review
                    </p>
                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 10px; background: #2563eb; color: #fff; font-size: 11px; font-weight: 800;">
                        {{ $inReviewWrs->count() }}
                    </span>
                </div>
                <p style="font-size: 13px; color: #1d4ed8; margin: 0 0 10px;">
                    Latest: <span style="font-weight: 600;">{{ Str::limit($inReviewWrs->first()->name_of_project, 55) }}</span>
                    <span style="font-weight: 400; opacity: 0.75;">
                        · Step: {{ ucfirst(str_replace('_', ' ', $inReviewWrs->first()->current_review_step ?? 'assigned')) }}
                    </span>
                </p>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('user.work-requests.index') }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #2563eb; color: #fff; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                        <i class="fas fa-list" style="font-size: 10px;"></i>
                        Track Progress
                    </a>
                </div>
            </div>
            <button onclick="dismissAlert('alert-wr-in-review')"
                    style="flex-shrink: 0; width: 28px; height: 28px; border-radius: 7px; background: none; border: none; cursor: pointer; color: #3b82f6; display: flex; align-items: center; justify-content: center; transition: background 0.15s;"
                    onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='transparent'"
                    title="Dismiss">
                <i class="fas fa-times" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ── Concrete Pouring: Approved ─────────────────────────────────────── --}}
    @if($approvedCps->isNotEmpty())
    <div id="alert-cp-approved" style="border-radius: 12px; overflow: hidden; border: 1px solid #a7f3d0; background: #ecfdf5; position: relative;">
        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #059669, #10b981);"></div>
        <div style="padding: 14px 16px 14px 24px; display: flex; align-items: flex-start; gap: 14px;">
            {{-- Icon --}}
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #d1fae5; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;">
                <i class="fas fa-hard-hat" style="color: #059669; font-size: 16px;"></i>
                <span style="position: absolute; top: -3px; right: -3px; width: 10px; height: 10px; border-radius: 50%; background: #059669; border: 2px solid #ecfdf5;"></span>
            </div>
            {{-- Body --}}
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                    <p style="font-size: 14px; font-weight: 700; color: #064e3b; margin: 0;">
                        {{ $approvedCps->count() }} concrete {{ Str::plural('pouring', $approvedCps->count()) }} approved! 🎉
                    </p>
                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 10px; background: #059669; color: #fff; font-size: 11px; font-weight: 800;">
                        {{ $approvedCps->count() }}
                    </span>
                </div>
                <p style="font-size: 13px; color: #047857; margin: 0 0 10px;">
                    Latest: <span style="font-weight: 600;">{{ Str::limit($approvedCps->first()->project_name, 55) }}</span>
                    <span style="font-weight: 400; opacity: 0.75;">· {{ $approvedCps->first()->updated_at->diffForHumans() }}</span>
                </p>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('user.concrete-pouring.show', $approvedCps->first()) }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #059669; color: #fff; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                        <i class="fas fa-eye" style="font-size: 10px;"></i>
                        View Details
                    </a>
                </div>
            </div>
            <button onclick="dismissAlert('alert-cp-approved')"
                    style="flex-shrink: 0; width: 28px; height: 28px; border-radius: 7px; background: none; border: none; cursor: pointer; color: #10b981; display: flex; align-items: center; justify-content: center; transition: background 0.15s;"
                    onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='transparent'"
                    title="Dismiss">
                <i class="fas fa-times" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ── Concrete Pouring: Disapproved ──────────────────────────────────── --}}
    @if($disapprovedCps->isNotEmpty())
    <div id="alert-cp-disapproved" style="border-radius: 12px; overflow: hidden; border: 1px solid #fecaca; background: #fff1f2; position: relative;">
        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #dc2626, #ef4444);"></div>
        <div style="padding: 14px 16px 14px 24px; display: flex; align-items: flex-start; gap: 14px;">
            {{-- Icon --}}
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #fee2e2; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;">
                <i class="fas fa-hard-hat" style="color: #dc2626; font-size: 16px;"></i>
                <span style="position: absolute; top: -3px; right: -3px; width: 10px; height: 10px; border-radius: 50%; background: #ef4444; border: 2px solid #fff1f2; animation: alert-pulse 1.8s ease-in-out infinite;"></span>
            </div>
            {{-- Body --}}
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                    <p style="font-size: 14px; font-weight: 700; color: #7f1d1d; margin: 0;">
                        {{ $disapprovedCps->count() }} concrete {{ Str::plural('pouring', $disapprovedCps->count()) }} disapproved
                    </p>
                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 10px; background: #dc2626; color: #fff; font-size: 11px; font-weight: 800;">
                        {{ $disapprovedCps->count() }}
                    </span>
                </div>
                <p style="font-size: 13px; color: #991b1b; margin: 0 0 10px;">
                    Latest: <span style="font-weight: 600;">{{ Str::limit($disapprovedCps->first()->project_name, 55) }}</span>
                    @if($disapprovedCps->first()->approval_remarks)
                        <span style="font-weight: 400; opacity: 0.8;"> — "{{ Str::limit($disapprovedCps->first()->approval_remarks, 60) }}"</span>
                    @endif
                </p>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('user.concrete-pouring.show', $disapprovedCps->first()) }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #dc2626; color: #fff; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                        <i class="fas fa-eye" style="font-size: 10px;"></i>
                        View Details
                    </a>
                    <a href="{{ route('user.concrete-pouring.create') }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #fee2e2; color: #7f1d1d; border: 1px solid #fecaca; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                        <i class="fas fa-rotate-right" style="font-size: 10px;"></i>
                        Resubmit
                    </a>
                </div>
            </div>
            <button onclick="dismissAlert('alert-cp-disapproved')"
                    style="flex-shrink: 0; width: 28px; height: 28px; border-radius: 7px; background: none; border: none; cursor: pointer; color: #ef4444; display: flex; align-items: center; justify-content: center; transition: background 0.15s;"
                    onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='transparent'"
                    title="Dismiss">
                <i class="fas fa-times" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ── Concrete Pouring: In Review ─────────────────────────────────────── --}}
    @if($pendingCps->isNotEmpty())
    <div id="alert-cp-in-review" style="border-radius: 12px; overflow: hidden; border: 1px solid #fde68a; background: #fffbeb; position: relative;">
        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #d97706, #f59e0b);"></div>
        <div style="padding: 14px 16px 14px 24px; display: flex; align-items: flex-start; gap: 14px;">
            {{-- Icon --}}
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #fef3c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-hard-hat" style="color: #d97706; font-size: 16px; animation: spin-slow 3s linear infinite;"></i>
            </div>
            {{-- Body --}}
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                    <p style="font-size: 14px; font-weight: 700; color: #92400e; margin: 0;">
                        {{ $pendingCps->count() }} concrete {{ Str::plural('pouring', $pendingCps->count()) }} under review
                    </p>
                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 10px; background: #d97706; color: #fff; font-size: 11px; font-weight: 800;">
                        {{ $pendingCps->count() }}
                    </span>
                </div>
                <p style="font-size: 13px; color: #b45309; margin: 0 0 10px;">
                    Latest: <span style="font-weight: 600;">{{ Str::limit($pendingCps->first()->project_name, 55) }}</span>
                    <span style="font-weight: 400; opacity: 0.75;">
                        · Step: {{ ucfirst(str_replace('_', ' ', $pendingCps->first()->current_review_step ?? 'assigned')) }}
                    </span>
                </p>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('user.concrete-pouring.index') }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #d97706; color: #fff; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#b45309'" onmouseout="this.style.background='#d97706'">
                        <i class="fas fa-list" style="font-size: 10px;"></i>
                        Track Progress
                    </a>
                </div>
            </div>
            <button onclick="dismissAlert('alert-cp-in-review')"
                    style="flex-shrink: 0; width: 28px; height: 28px; border-radius: 7px; background: none; border: none; cursor: pointer; color: #f59e0b; display: flex; align-items: center; justify-content: center; transition: background 0.15s;"
                    onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'"
                    title="Dismiss">
                <i class="fas fa-times" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>
    @endif

</div>

<style>
    @keyframes alert-pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.5; transform: scale(0.75); }
    }
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }
    /* Dark mode */
    .dark #alert-wr-approved     { background: rgba(22,163,74,0.10) !important; border-color: rgba(34,197,94,0.25) !important; }
    .dark #alert-wr-approved p   { color: #86efac !important; }
    .dark #alert-wr-rejected     { background: rgba(220,38,38,0.10) !important; border-color: rgba(239,68,68,0.25) !important; }
    .dark #alert-wr-rejected p   { color: #fca5a5 !important; }
    .dark #alert-wr-in-review    { background: rgba(37,99,235,0.10) !important; border-color: rgba(59,130,246,0.25) !important; }
    .dark #alert-wr-in-review p  { color: #93c5fd !important; }
    .dark #alert-cp-approved     { background: rgba(5,150,105,0.10) !important; border-color: rgba(16,185,129,0.25) !important; }
    .dark #alert-cp-approved p   { color: #6ee7b7 !important; }
    .dark #alert-cp-disapproved  { background: rgba(220,38,38,0.10) !important; border-color: rgba(239,68,68,0.25) !important; }
    .dark #alert-cp-disapproved p{ color: #fca5a5 !important; }
    .dark #alert-cp-in-review    { background: rgba(217,119,6,0.10) !important; border-color: rgba(245,158,11,0.25) !important; }
    .dark #alert-user-memo    { background: rgba(37,99,235,0.10) !important; border-color: rgba(59,130,246,0.25) !important; }
    .dark #alert-user-memo p  { color: #93c5fd !important; }
    .dark #alert-cp-in-review p  { color: #fcd34d !important; }
</style>

<script>
    // Re-use the same dismissAlert function from action-alerts if present,
    // or define it here as a fallback
    if (typeof dismissAlert === 'undefined') {
        function dismissAlert(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.style.transition = 'opacity 0.25s ease, transform 0.25s ease, max-height 0.3s ease, margin 0.3s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-4px)';
            el.style.maxHeight = el.offsetHeight + 'px';
            setTimeout(() => {
                el.style.maxHeight = '0';
                el.style.marginBottom = '0';
                el.style.overflow = 'hidden';
            }, 200);
            setTimeout(() => el.remove(), 500);
            sessionStorage.setItem('dismissed_' + id, '1');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        ['alert-user-memo', 'alert-wr-approved', 'alert-wr-rejected','alert-wr-approved', 'alert-wr-rejected', 'alert-wr-in-review',
         'alert-cp-approved', 'alert-cp-disapproved', 'alert-cp-in-review'].forEach(id => {
            if (sessionStorage.getItem('dismissed_' + id)) {
                const el = document.getElementById(id);
                if (el) el.remove();
            }
        });
    });
</script>
@endif