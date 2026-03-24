<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $emailTitle ?? 'Provincial Engineering Office' }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #F5EDE4;
        color: #3D2B1A;
        padding: 32px 16px;
    }
    .wrapper {
        max-width: 620px;
        margin: 0 auto;
    }
    /* Header */
    .email-header {
        background: #3D2B1A;
        border-radius: 16px 16px 0 0;
        padding: 28px 36px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .email-header img {
        width: 52px;
        height: 52px;
        object-fit: contain;
        border-radius: 10px;
    }
    .header-text h1 {
        font-size: 15px;
        font-weight: 700;
        color: #ffffff;
        line-height: 1.2;
    }
    .header-text p {
        font-size: 11px;
        color: rgba(255,255,255,0.55);
        margin-top: 3px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    /* Accent bar */
    .accent-bar {
        height: 4px;
        background: linear-gradient(90deg, #E05A00, #FF8C38);
    }
    /* Badge */
    .badge-row {
        background: #fff;
        padding: 20px 36px 0;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 14px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }
    .badge.orange { background: rgba(224,90,0,0.10); color: #E05A00; border: 1px solid rgba(224,90,0,0.25); }
    .badge.green  { background: rgba(45,158,107,0.10); color: #1F7A52; border: 1px solid rgba(45,158,107,0.25); }
    .badge.red    { background: rgba(185,28,28,0.10); color: #B91C1C; border: 1px solid rgba(185,28,28,0.25); }
    .badge-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: currentColor;
    }
    /* Body */
    .email-body {
        background: #ffffff;
        padding: 28px 36px 36px;
    }
    .email-title {
        font-size: 22px;
        font-weight: 800;
        color: #3D2B1A;
        letter-spacing: -0.02em;
        line-height: 1.25;
        margin-bottom: 12px;
    }
    .email-intro {
        font-size: 14px;
        color: #6B4F3A;
        line-height: 1.7;
        margin-bottom: 28px;
    }
    /* Info table */
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 28px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #F0E0D0;
    }
    .info-table tr:nth-child(even) td { background: #FFF8F2; }
    .info-table td {
        padding: 12px 16px;
        font-size: 13px;
        border-bottom: 1px solid #F0E0D0;
        vertical-align: top;
    }
    .info-table tr:last-child td { border-bottom: none; }
    .info-table .lbl {
        color: #A07858;
        font-weight: 600;
        width: 38%;
        white-space: nowrap;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.05em;
    }
    .info-table .val {
        color: #3D2B1A;
        font-weight: 500;
    }
    /* Remarks box */
    .remarks-box {
        background: #FFF8F2;
        border-left: 4px solid #E05A00;
        border-radius: 0 8px 8px 0;
        padding: 14px 18px;
        margin-bottom: 28px;
    }
    .remarks-box .remarks-label {
        font-size: 11px;
        font-weight: 700;
        color: #E05A00;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 6px;
    }
    .remarks-box p {
        font-size: 13px;
        color: #3D2B1A;
        line-height: 1.65;
    }
    /* CTA Button */
    .cta-wrap { text-align: center; margin-bottom: 8px; }
    .cta-btn {
        display: inline-block;
        padding: 14px 36px;
        background: #E05A00;
        color: #ffffff !important;
        text-decoration: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.01em;
        box-shadow: 0 4px 16px rgba(224,90,0,0.30);
    }
    .cta-btn.secondary {
        background: #3D2B1A;
        box-shadow: 0 4px 16px rgba(61,43,26,0.20);
    }
    /* Divider */
    .divider {
        height: 1px;
        background: #F0E0D0;
        margin: 24px 0;
    }
    /* Footer */
    .email-footer {
        background: #3D2B1A;
        border-radius: 0 0 16px 16px;
        padding: 22px 36px;
        text-align: center;
    }
    .email-footer p {
        font-size: 11px;
        color: rgba(255,255,255,0.40);
        line-height: 1.65;
    }
    .email-footer a {
        color: rgba(255,140,56,0.80);
        text-decoration: none;
    }
    /* Step pill */
    .step-pill {
        display: inline-block;
        background: rgba(224,90,0,0.10);
        color: #B84A00;
        border: 1px solid rgba(224,90,0,0.20);
        border-radius: 6px;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 600;
    }
    /* Decision highlight */
    .decision-approved {
        background: rgba(45,158,107,0.08);
        border: 1px solid rgba(45,158,107,0.20);
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 24px;
        text-align: center;
    }
    .decision-approved .big-label {
        font-size: 18px;
        font-weight: 800;
        color: #1F7A52;
    }
    .decision-rejected {
        background: rgba(185,28,28,0.07);
        border: 1px solid rgba(185,28,28,0.18);
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 24px;
        text-align: center;
    }
    .decision-rejected .big-label {
        font-size: 18px;
        font-weight: 800;
        color: #B91C1C;
    }
</style>
</head>
<body>
<div class="wrapper">

    {{-- HEADER --}}
    <div class="email-header">
        <img src="{{ $message->embed(public_path('assets/app_logo.PNG')) }}" alt="PEO Logo">
        <div class="header-text">
            <h1>Provincial Engineering Office</h1>
            <p>Malaybalay City, Bukidnon</p>
        </div>
    </div>
    <div class="accent-bar"></div>

    {{-- BADGE ROW --}}
    <div class="badge-row">
        <span class="badge {{ $badgeClass ?? 'orange' }}">
            <span class="badge-dot"></span>
            {{ $badgeText ?? 'Work Request' }}
        </span>
    </div>

    {{-- BODY --}}
    <div class="email-body">
        @yield('content')
    </div>

    {{-- FOOTER --}}
    <div class="email-footer">
        <p>
            This is an automated notification from the<br>
            <strong style="color:rgba(255,255,255,0.65)">Provincial Engineering Office — Malaybalay City, Bukidnon</strong>
        </p>
        <p style="margin-top:8px;">Please do not reply to this email.</p>
    </div>

</div>
</body>
</html>