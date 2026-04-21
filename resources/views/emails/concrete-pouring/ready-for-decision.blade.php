<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Decision Required</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #0d9488; padding: 28px 32px; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
        .header p  { color: rgba(255,255,255,0.85); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .body p { color: #374151; font-size: 14px; line-height: 1.6; margin: 0 0 16px; }
        .detail-box { background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #ccfbf1; font-size: 13px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; font-weight: 600; }
        .detail-value { color: #0f172a; text-align: right; max-width: 60%; }
        .review-summary { margin: 20px 0; }
        .review-item { display: flex; align-items: center; gap: 10px; padding: 8px 0; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
        .review-item:last-child { border-bottom: none; }
        .check { color: #10b981; font-weight: 700; }
        .review-role { color: #64748b; font-weight: 600; min-width: 160px; }
        .review-name { color: #0f172a; }
        .action-box { background: #fefce8; border: 1px solid #fde68a; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .action-box p { color: #92400e; margin: 0; font-size: 13px; font-weight: 600; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 28px; background: #0d9488; color: #fff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; }
        .footer { background: #f8fafc; padding: 20px 32px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>⚖️ Final Decision Required</h1>
        <p>All reviewers have completed their steps. Your final decision is needed.</p>
    </div>
    <div class="body">
        <p>Hello Provincial Engineer,</p>
        <p>
            All assigned reviewers have completed their steps for the following concrete pouring request.
            Your <strong>final approval or disapproval</strong> is now required.
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

        {{-- Review summary --}}
        <p style="font-weight:600; color:#0f172a; margin-bottom:8px;">Completed Reviews:</p>
        <div class="review-summary">
            @if($concretePouring->meMtqaChecker)
                <div class="review-item">
                    <span class="check">✓</span>
                    <span class="review-role">ME/MTQA</span>
                    <span class="review-name">{{ $concretePouring->meMtqaChecker->name }}</span>
                </div>
            @endif
            @if($concretePouring->residentEngineer)
                <div class="review-item">
                    <span class="check">✓</span>
                    <span class="review-role">Resident Engineer</span>
                    <span class="review-name">{{ $concretePouring->residentEngineer->name }}</span>
                </div>
            @endif
        </div>

        <div class="action-box">
            <p>⚡ Please log in to review all findings and submit your final decision (Approve or Disapprove).</p>
        </div>

        <a href="{{ url('/reviewer/concrete-pouring/' . $concretePouring->id) }}" class="btn">
            Submit Final Decision →
        </a>
    </div>
    <div class="footer">
        This is an automated message from the Work Request Management System.<br>
        Province of Bukidnon — Provincial Engineers Office
    </div>
</div>
</body>
</html>
