{{-- resources/views/reviewer/dashboard-partials/action-alerts.blade.php --}}
@php
    use App\Models\Notification;
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();

    // ── Unread Memos ──────────────────────────────────────────────────────────
    $unreadMemoCount = \App\Models\MemoRecipient::where('user_id', $user->id)
        ->whereNull('read_at')
        ->count();
    $latestMemo = \App\Models\MemoRecipient::with('memo')
        ->where('user_id', $user->id)
        ->whereNull('read_at')
        ->latest()
        ->first()?->memo;

    // ── Pending Work Requests (assigned to this reviewer) ────────────────────
    $pendingWrCount = \App\Models\WorkRequest::assignedToUser($user->id)->count();
    $latestWr = \App\Models\WorkRequest::assignedToUser($user->id)->latest()->first();

    // ── Pending Concrete Pourings (assigned to this reviewer) ────────────────
    $pendingCpCount = 0;
    $latestCp = null;

    if (in_array($user->role, ['mtqa', 'engineeriv'])) {
        $pendingCpCount = \App\Models\ConcretePouring::where('current_review_step', 'mtqa')
            ->where('me_mtqa_user_id', $user->id)
            ->where('status', 'requested')
            ->count();
        $latestCp = \App\Models\ConcretePouring::where('current_review_step', 'mtqa')
            ->where('me_mtqa_user_id', $user->id)
            ->where('status', 'requested')
            ->latest()->first();
    } elseif ($user->role === 'resident_engineer') {
        $pendingCpCount = \App\Models\ConcretePouring::where('current_review_step', 'resident_engineer')
            ->where('resident_engineer_user_id', $user->id)
            ->where('status', 'requested')
            ->count();
        $latestCp = \App\Models\ConcretePouring::where('current_review_step', 'resident_engineer')
            ->where('resident_engineer_user_id', $user->id)
            ->where('status', 'requested')
            ->latest()->first();
    } elseif (in_array($user->role, ['provincial_engineer', 'engineeriii'])) {
        $pendingCpCount = \App\Models\ConcretePouring::where('current_review_step', 'provincial_engineer')
            ->where('noted_by_user_id', $user->id)
            ->where('status', 'requested')
            ->count();
        $latestCp = \App\Models\ConcretePouring::where('current_review_step', 'provincial_engineer')
            ->where('noted_by_user_id', $user->id)
            ->where('status', 'requested')
            ->latest()->first();
    }
@endphp

@if($unreadMemoCount > 0 || $pendingWrCount > 0 || $pendingCpCount > 0)
<div class="space-y-3" id="dashboard-action-alerts">

    {{-- ── Unread Memos Alert ───────────────────────────────────────────── --}}
    @if($unreadMemoCount > 0)
    <div id="alert-memo"
         style="border-radius: 12px; overflow: hidden; border: 1px solid #bfdbfe; background: #eff6ff; position: relative;">
        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #2563eb, #3b82f6);"></div>
        <div style="padding: 14px 16px 14px 24px; display: flex; align-items: flex-start; gap: 14px;">
            {{-- Icon --}}
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #dbeafe; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-envelope-open-text" style="color: #2563eb; font-size: 16px;"></i>
            </div>
            {{-- Body --}}
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                    <p style="font-size: 14px; font-weight: 700; color: #1e40af; margin: 0;">
                        You have {{ $unreadMemoCount }} unread {{ Str::plural('memo', $unreadMemoCount) }}
                    </p>
                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 10px; background: #2563eb; color: #fff; font-size: 11px; font-weight: 800;">
                        {{ $unreadMemoCount }}
                    </span>
                </div>
                @if($latestMemo)
                <p style="font-size: 13px; color: #1d4ed8; margin: 0 0 10px;">
                    Latest: <span style="font-weight: 600;">{{ Str::limit($latestMemo->subject, 60) }}</span>
                    <span style="font-weight: 400; opacity: 0.75;">· {{ $latestMemo->created_at->diffForHumans() }}</span>
                </p>
                @endif
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('memos.index') ?? '#' }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #2563eb; color: #fff; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                        <i class="fas fa-envelope" style="font-size: 10px;"></i>
                        View Memos
                    </a>
                </div>
            </div>
            {{-- Dismiss --}}
            <button onclick="dismissAlert('alert-memo')"
                    style="flex-shrink: 0; width: 28px; height: 28px; border-radius: 7px; background: none; border: none; cursor: pointer; color: #3b82f6; display: flex; align-items: center; justify-content: center; transition: background 0.15s;"
                    onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='transparent'"
                    title="Dismiss">
                <i class="fas fa-times" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ── Pending Work Requests Alert ──────────────────────────────────── --}}
    @if($pendingWrCount > 0)
    <div id="alert-work-request"
         style="border-radius: 12px; overflow: hidden; border: 1px solid #fde68a; background: #fffbeb; position: relative;">
        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #d97706, #f59e0b);"></div>
        <div style="padding: 14px 16px 14px 24px; display: flex; align-items: flex-start; gap: 14px;">
            {{-- Icon --}}
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #fef3c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;">
                <i class="fas fa-file-signature" style="color: #d97706; font-size: 16px;"></i>
                {{-- Pulse dot --}}
                <span style="position: absolute; top: -3px; right: -3px; width: 10px; height: 10px; border-radius: 50%; background: #ef4444; border: 2px solid #fffbeb; animation: alert-pulse 1.8s ease-in-out infinite;"></span>
            </div>
            {{-- Body --}}
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                    <p style="font-size: 14px; font-weight: 700; color: #92400e; margin: 0;">
                        {{ $pendingWrCount }} work {{ Str::plural('request', $pendingWrCount) }} awaiting your action
                    </p>
                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 10px; background: #d97706; color: #fff; font-size: 11px; font-weight: 800;">
                        {{ $pendingWrCount }}
                    </span>
                </div>
                @if($latestWr)
                <p style="font-size: 13px; color: #b45309; margin: 0 0 10px;">
                    Latest: <span style="font-weight: 600;">{{ Str::limit($latestWr->name_of_project, 55) }}</span>
                    <span style="font-weight: 400; opacity: 0.75;">· {{ $latestWr->created_at->diffForHumans() }}</span>
                </p>
                @endif
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('reviewer.work-requests.index') }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #d97706; color: #fff; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#b45309'" onmouseout="this.style.background='#d97706'">
                        <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                        Review Now
                    </a>
                    @if($latestWr)
                    <a href="{{ route('reviewer.work-requests.show', $latestWr) }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'">
                        <i class="fas fa-eye" style="font-size: 10px;"></i>
                        View Latest
                    </a>
                    @endif
                </div>
            </div>
            {{-- Dismiss --}}
            <button onclick="dismissAlert('alert-work-request')"
                    style="flex-shrink: 0; width: 28px; height: 28px; border-radius: 7px; background: none; border: none; cursor: pointer; color: #f59e0b; display: flex; align-items: center; justify-content: center; transition: background 0.15s;"
                    onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'"
                    title="Dismiss">
                <i class="fas fa-times" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ── Pending Concrete Pouring Alert ───────────────────────────────── --}}
    @if($pendingCpCount > 0)
    <div id="alert-concrete-pouring"
         style="border-radius: 12px; overflow: hidden; border: 1px solid #bbf7d0; background: #f0fdf4; position: relative;">
        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #16a34a, #22c55e);"></div>
        <div style="padding: 14px 16px 14px 24px; display: flex; align-items: flex-start; gap: 14px;">
            {{-- Icon --}}
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #dcfce7; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;">
                <i class="fas fa-hard-hat" style="color: #16a34a; font-size: 16px;"></i>
                <span style="position: absolute; top: -3px; right: -3px; width: 10px; height: 10px; border-radius: 50%; background: #ef4444; border: 2px solid #f0fdf4; animation: alert-pulse 1.8s ease-in-out infinite 0.4s;"></span>
            </div>
            {{-- Body --}}
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                    <p style="font-size: 14px; font-weight: 700; color: #14532d; margin: 0;">
                        {{ $pendingCpCount }} concrete {{ Str::plural('pouring', $pendingCpCount) }} awaiting your review
                    </p>
                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 10px; background: #16a34a; color: #fff; font-size: 11px; font-weight: 800;">
                        {{ $pendingCpCount }}
                    </span>
                </div>
                @if($latestCp)
                <p style="font-size: 13px; color: #15803d; margin: 0 0 10px;">
                    Latest: <span style="font-weight: 600;">{{ Str::limit($latestCp->project_name, 55) }}</span>
                    <span style="font-weight: 400; opacity: 0.75;">· {{ $latestCp->created_at->diffForHumans() }}</span>
                </p>
                @endif
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('reviewer.concrete-pourings.index') ?? '#' }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #16a34a; color: #fff; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                        <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                        Review Now
                    </a>
                    @if($latestCp)
                    <a href="{{ route('reviewer.concrete-pourings.show', $latestCp) ?? '#' }}"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 7px; background: #dcfce7; color: #14532d; border: 1px solid #bbf7d0; font-size: 12px; font-weight: 700; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#bbf7d0'" onmouseout="this.style.background='#dcfce7'">
                        <i class="fas fa-eye" style="font-size: 10px;"></i>
                        View Latest
                    </a>
                    @endif
                </div>
            </div>
            {{-- Dismiss --}}
            <button onclick="dismissAlert('alert-concrete-pouring')"
                    style="flex-shrink: 0; width: 28px; height: 28px; border-radius: 7px; background: none; border: none; cursor: pointer; color: #22c55e; display: flex; align-items: center; justify-content: center; transition: background 0.15s;"
                    onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='transparent'"
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
    /* Dark mode overrides */
    .dark #alert-memo            { background: rgba(37,99,235,0.10) !important; border-color: rgba(59,130,246,0.25) !important; }
    .dark #alert-memo p          { color: #93c5fd !important; }
    .dark #alert-memo span[style*="color: #1d4ed8"] { color: #bfdbfe !important; }
    .dark #alert-work-request    { background: rgba(217,119,6,0.10) !important; border-color: rgba(245,158,11,0.25) !important; }
    .dark #alert-work-request p  { color: #fcd34d !important; }
    .dark #alert-concrete-pouring{ background: rgba(22,163,74,0.10) !important; border-color: rgba(34,197,94,0.25) !important; }
    .dark #alert-concrete-pouring p { color: #86efac !important; }
</style>

<script>
    function dismissAlert(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.style.transition = 'opacity 0.25s ease, transform 0.25s ease, max-height 0.3s ease, margin 0.3s ease, padding 0.3s ease';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-4px)';
        el.style.maxHeight = el.offsetHeight + 'px';
        setTimeout(() => {
            el.style.maxHeight = '0';
            el.style.marginBottom = '0';
            el.style.overflow = 'hidden';
        }, 200);
        setTimeout(() => el.remove(), 500);

        // Persist dismissal in sessionStorage so it stays gone on soft navigations
        sessionStorage.setItem('dismissed_' + id, '1');
    }

    // Re-apply dismissals on page load
    document.addEventListener('DOMContentLoaded', () => {
        ['alert-memo', 'alert-work-request', 'alert-concrete-pouring'].forEach(id => {
            if (sessionStorage.getItem('dismissed_' + id)) {
                const el = document.getElementById(id);
                if (el) el.remove();
            }
        });
    });
</script>
@endif