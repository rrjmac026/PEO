<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature Submitted</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #7c3aed; padding: 28px 32px; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
        .header p  { color: rgba(255,255,255,0.85); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .body p { color: #374151; font-size: 14px; line-height: 1.6; margin: 0 0 16px; }
        .detail-box { background: #faf5ff; border: 1px solid #ddd6fe; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #ede9fe; font-size: 13px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; font-weight: 600; }
        .detail-value { color: #0f172a; text-align: right; max-width: 60%; }
        .role-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #ede9fe; color: #6d28d9; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 28px; background: #7c3aed; color: #fff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; }
        .footer { background: #f8fafc; padding: 20px 32px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>✍️ Reviewer Signed Off</h1>
        <p>A reviewer has submitted their signature for a concrete pouring request.</p>
    </div>
    <div class="body">
        <p>Hello Admin,</p>
        <p>
            <strong>{{ $submittedByName }}</strong>
            (<span class="role-badge">{{ $submittedByRole }}</span>)
            has submitted their review and signature for the following concrete pouring request.
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
                <span class="detail-label">Current Step</span>
                <span class="detail-value">{{ $concretePouring->current_step_label }}</span>
            </div>
        </div>

        <a href="{{ url('/admin/concrete-pouring/' . $concretePouring->id) }}" class="btn">
            View Request →
        </a>
    </div>
    <div class="footer">
        This is an automated message from the Work Request Management System.<br>
        Province of Bukidnon — Provincial Engineers Office
    </div>
</div>
</body>
</html>
