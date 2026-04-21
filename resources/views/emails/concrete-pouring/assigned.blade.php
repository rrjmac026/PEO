<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concrete Pouring Request Assigned</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: {{ $isFirst ? '#0891b2' : '#475569' }}; padding: 28px 32px; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
        .header p  { color: rgba(255,255,255,0.85); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .body p { color: #374151; font-size: 14px; line-height: 1.6; margin: 0 0 16px; }
        .detail-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0f2fe; font-size: 13px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; font-weight: 600; }
        .detail-value { color: #0f172a; text-align: right; max-width: 60%; }
        .role-badge { display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700;
                      background: {{ $isFirst ? '#cffafe' : '#f1f5f9' }};
                      color: {{ $isFirst ? '#0e7490' : '#475569' }}; }
        .action-box { background: #fefce8; border: 1px solid #fde68a; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .action-box p { color: #92400e; margin: 0; font-size: 13px; font-weight: 600; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 28px; background: #0891b2; color: #fff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; }
        .pipeline { margin: 20px 0; }
        .pipeline-step { display: flex; align-items: center; gap: 10px; padding: 8px 0; font-size: 13px; color: #64748b; }
        .pipeline-step.active { color: #0e7490; font-weight: 700; }
        .pipeline-dot { width: 10px; height: 10px; border-radius: 50%; background: #cbd5e1; flex-shrink: 0; }
        .pipeline-step.active .pipeline-dot { background: #0891b2; }
        .pipeline-step.done .pipeline-dot { background: #10b981; }
        .pipeline-step.done { color: #059669; }
        .footer { background: #f8fafc; padding: 20px 32px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        @if($isFirst)
            <h1>🏗️ Action Required: Concrete Pouring Review</h1>
            <p>You have been assigned as the first reviewer for this request.</p>
        @else
            <h1>📋 Heads Up: You're in the Review Queue</h1>
            <p>You have been assigned as a reviewer. You'll be notified when it's your turn.</p>
        @endif
    </div>
    <div class="body">
        <p>Hello,</p>
        <p>
            You have been assigned as the
            <span class="role-badge">{{ $role }}</span>
            reviewer for the following concrete pouring request.
        </p>

        <div class="detail-box">
            <div class="detail-row">
                <span class="detail-label">Reference No.</span>
                <span class="detail-value">{{ $concretePouring->reference_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Project</span>
                <span class="detail-value">{{ $concretePouring->project_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Location</span>
                <span class="detail-value">{{ $concretePouring->location }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Contractor</span>
                <span class="detail-value">{{ $concretePouring->contractor }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Part of Structure</span>
                <span class="detail-value">{{ $concretePouring->part_of_structure }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Estimated Volume</span>
                <span class="detail-value">{{ $concretePouring->estimated_volume }} cu.m.</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Pouring Date & Time</span>
                <span class="detail-value">{{ $concretePouring->pouring_datetime?->format('M d, Y h:i A') }}</span>
            </div>
        </div>

        {{-- Review pipeline --}}
        <p style="font-weight:600; color:#0f172a; margin-bottom:8px;">Review Pipeline:</p>
        <div class="pipeline">
            @php
                $steps = [
                    'mtqa'                => 'ME/MTQA',
                    'resident_engineer'   => 'Resident Engineer',
                    'provincial_engineer' => 'Provincial Engineer (Final Decision)',
                ];
                $stepKeys  = array_keys($steps);
                $roleMap   = [
                    'ME/MTQA'                          => 'mtqa',
                    'Resident Engineer'                => 'resident_engineer',
                    'Provincial Engineer (Final Decision)' => 'provincial_engineer',
                ];
                $currentStepKey = $roleMap[$role] ?? null;
                $currentIdx = array_search($currentStepKey, $stepKeys);
            @endphp
            @foreach($steps as $key => $label)
                @php
                    $idx = array_search($key, $stepKeys);
                    $cls = $idx < $currentIdx ? 'done' : ($key === $currentStepKey ? 'active' : '');
                @endphp
                <div class="pipeline-step {{ $cls }}">
                    <div class="pipeline-dot"></div>
                    {{ $label }}
                    @if($key === $currentStepKey) ← You @endif
                </div>
            @endforeach
        </div>

        @if($isFirst)
            <div class="action-box">
                <p>⚡ It is currently your turn to review this request. Please log in and submit your findings.</p>
            </div>
            <a href="{{ url('/reviewer/concrete-pouring/' . $concretePouring->id) }}" class="btn">
                Review Now →
            </a>
        @else
            <p style="color:#64748b; font-size:13px;">
                You will receive another email when it is your turn to review.
            </p>
            <a href="{{ url('/reviewer/concrete-pouring/' . $concretePouring->id) }}" class="btn"
               style="background:#475569;">
                View Request →
            </a>
        @endif
    </div>
    <div class="footer">
        This is an automated message from the Work Request Management System.<br>
        Province of Bukidnon — Provincial Engineers Office
    </div>
</div>
</body>
</html>
