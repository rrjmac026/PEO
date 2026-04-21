<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concrete Pouring Approved</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #059669; padding: 28px 32px; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
        .header p  { color: rgba(255,255,255,0.85); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .body p { color: #374151; font-size: 14px; line-height: 1.6; margin: 0 0 16px; }
        .detail-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #dcfce7; font-size: 13px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; font-weight: 600; }
        .detail-value { color: #0f172a; text-align: right; max-width: 60%; }
        .approved-banner { background: #dcfce7; border: 1px solid #86efac; border-radius: 8px; padding: 16px 20px; margin: 20px 0; text-align: center; }
        .approved-banner h2 { color: #166534; margin: 0 0 6px; font-size: 18px; }
        .approved-banner p { color: #166534; margin: 0; font-size: 13px; }
        .remarks-box { background: #f8fafc; border-left: 4px solid #059669; border-radius: 4px; padding: 14px 16px; margin: 16px 0; font-size: 13px; color: #374151; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 28px; background: #059669; color: #fff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; }
        .footer { background: #f8fafc; padding: 20px 32px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>✅ Concrete Pouring Request Approved</h1>
        <p>The Provincial Engineer has approved this concrete pouring request.</p>
    </div>
    <div class="body">
        <p>Hello,</p>

        <div class="approved-banner">
            <h2>✅ APPROVED</h2>
            <p>Your concrete pouring request has been officially approved.</p>
        </div>

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
                <span class="detail-label">Approved By</span>
                <span class="detail-value">
                    {{ $concretePouring->approver?->name ?? '—' }}
                    (Provincial Engineer)
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Approved Date</span>
                <span class="detail-value">
                    {{ $concretePouring->approved_date?->format('M d, Y') ?? now()->format('M d, Y') }}
                </span>
            </div>
        </div>

        @if($concretePouring->approval_remarks)
            <p style="font-weight:600; color:#0f172a; margin-bottom:6px;">Remarks from Provincial Engineer:</p>
            <div class="remarks-box">
                {{ $concretePouring->approval_remarks }}
            </div>
        @endif

        <p>You may now proceed with the concrete pouring as scheduled.</p>

        <a href="{{ url('/user/concrete-pouring/' . $concretePouring->id) }}" class="btn">
            View Approved Request →
        </a>
    </div>
    <div class="footer">
        This is an automated message from the Work Request Management System.<br>
        Province of Bukidnon — Provincial Engineers Office
    </div>
</div>
</body>
</html>
