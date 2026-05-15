<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $memo->subject }}</title>
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

    /* ── Header ── */
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

    /* ── Accent bar ── */
    .accent-bar {
        height: 4px;
        background: linear-gradient(90deg, #E05A00, #FF8C38);
    }

    /* ── Badge row ── */
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
        background: rgba(224,90,0,0.10);
        color: #E05A00;
        border: 1px solid rgba(224,90,0,0.25);
    }
    .badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    /* ── Body ── */
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
        margin-bottom: 20px;
    }

    /* ── Meta table (From / To / Date) ── */
    .meta-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 28px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #F0E0D0;
    }
    .meta-table tr:nth-child(even) td { background: #FFF8F2; }
    .meta-table td {
        padding: 11px 16px;
        font-size: 13px;
        border-bottom: 1px solid #F0E0D0;
        vertical-align: top;
    }
    .meta-table tr:last-child td { border-bottom: none; }
    .meta-table .lbl {
        color: #A07858;
        font-weight: 600;
        width: 30%;
        white-space: nowrap;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.05em;
    }
    .meta-table .val {
        color: #3D2B1A;
        font-weight: 500;
    }

    /* ── Memo body text ── */
    .memo-body {
        background: #FFF8F2;
        border: 1px solid #F0E0D0;
        border-radius: 12px;
        padding: 20px 22px;
        margin-bottom: 28px;
        font-size: 14px;
        color: #3D2B1A;
        line-height: 1.75;
    }

    /* ── Attachments ── */
    .attachments-box {
        background: #FFF8F2;
        border-left: 4px solid #E05A00;
        border-radius: 0 8px 8px 0;
        padding: 14px 18px;
        margin-bottom: 28px;
    }
    .attachments-box .attach-label {
        font-size: 11px;
        font-weight: 700;
        color: #E05A00;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 8px;
    }
    .attachments-box ul {
        padding-left: 18px;
        margin: 0 0 6px;
    }
    .attachments-box ul li {
        font-size: 13px;
        color: #3D2B1A;
        line-height: 1.65;
    }
    .attachments-box .attach-note {
        font-size: 11px;
        color: #A07858;
        margin-top: 6px;
    }

    /* ── Footer ── */
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
    .email-footer strong {
        color: rgba(255,255,255,0.65);
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
        <span class="badge">
            <span class="badge-dot"></span>
            {{ $memo->type_label ?? $memo->type }}
        </span>
    </div>

    {{-- BODY --}}
    <div class="email-body">

        <h2 class="email-title">{{ $memo->subject }}</h2>

        {{-- Meta --}}
        <table class="meta-table">
            <tr>
                <td class="lbl">From</td>
                <td class="val">{{ $memo->sender->name ?? 'Admin' }}</td>
            </tr>
            <tr>
                <td class="lbl">To</td>
                <td class="val">{{ $recipient->name }}</td>
            </tr>
            <tr>
                <td class="lbl">Date</td>
                <td class="val">
                    {{ $memo->sent_at?->format('F d, Y g:i A') ?? now()->format('F d, Y g:i A') }}
                </td>
            </tr>
        </table>

        {{-- Memo body --}}
        <div class="memo-body">
            {!! nl2br(e($memo->body)) !!}
        </div>

        {{-- Attachments --}}
        @if (!empty($memo->attachments))
            <div class="attachments-box">
                <div class="attach-label">📎 Attachments ({{ count($memo->attachments) }})</div>
                <ul>
                    @foreach ($memo->attachments as $path)
                        <li>{{ basename($path) }}</li>
                    @endforeach
                </ul>
                <p class="attach-note">Files are attached to this email.</p>
            </div>
        @endif

    </div>

    {{-- FOOTER --}}
    <div class="email-footer">
        <p>
            This is an official memo from<br>
            <strong>Provincial Engineering Office Work Request Management System</strong>
        </p>
        <p style="margin-top: 8px;">Please do not reply directly to this email.</p>
    </div>

</div>
</body>
</html>