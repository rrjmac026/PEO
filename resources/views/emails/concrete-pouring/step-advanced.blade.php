<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Review Turn</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #0891b2; padding: 28px 32px; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
        .header p  { color: rgba(255,255,255,0.85); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .body p { color: #374151; font-size: 14px; line-height: 1.6; margin: 0 0 16px; }
        .detail-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0f2fe; font-size: 13px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; font-weight: 600; }
        .detail-value { color: #0f172a; text-align: right; max-width: 60%; }
        .action-box { background: #fefce8; border: 1px solid #fde68a; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .action-box p { color: #92400e; margin: 0; font-size: 13px; font-weight: 600; }
        .completed-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #dcfce7; color: #166534; }
        .next-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #cffafe; color: #0e7490; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 28px; background: #0891b2; color: #fff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; }
        .footer { background: #f8fafc; padding: 20px 32px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>⚡ It's Your Turn to Review</h1>
        <p>The previous reviewer has completed their step. Action required from you.</p>
    </div>
    <div class="body">
        <p>Hello,</p>
        <p>
            <strong>{{ $completedByName }}</strong> has completed the
            <span class="completed-badge">{{ $completedStep }}</span>
            review step. The request has now been forwarded to you as
            <span class="next-badge">{{ $nextStepLabel }}</span>.
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
            <div class="detail-row">
                <span class="detail-label">Your Step</span>
                <span class="detail-value"><span class="next-badge">{{ $nextStepLabel }}</span></span>
            </div>
        </div>

        <div class="action-box">
            <p>⚡ Please log in to review and submit your findings or signature for this request.</p>
        </div>

        <a href="{{ url('/reviewer/concrete-pouring/' . $concretePouring->id) }}" class="btn">
            Review Now →
        </a>
    </div>
    <div class="footer">
        This is an automated message from the Work Request Management System.<br>
        Province of Bukidnon — Provincial Engineers Office
    </div>
</div>
</body>
</html>
