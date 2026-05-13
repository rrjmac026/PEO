<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $memo->subject }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 620px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #1e3a5f; color: #ffffff; padding: 28px 32px; }
        .header .badge { display: inline-block; background: rgba(255,255,255,0.2); color: #fff; font-size: 11px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 4px 10px; border-radius: 4px; margin-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; line-height: 1.4; }
        .meta { background: #f0f4f8; padding: 16px 32px; border-bottom: 1px solid #dde3ea; font-size: 13px; color: #555; }
        .meta span { margin-right: 24px; }
        .meta strong { color: #1e3a5f; }
        .body { padding: 32px; color: #333; font-size: 15px; line-height: 1.7; }
        .body p { margin: 0 0 16px; }
        .attachments { margin: 24px 32px; padding: 16px; background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 6px; }
        .attachments p { margin: 0 0 8px; font-size: 13px; color: #666; font-weight: bold; }
        .attachments ul { margin: 0; padding-left: 20px; font-size: 13px; color: #444; }
        .footer { background: #f0f4f8; padding: 20px 32px; font-size: 12px; color: #888; border-top: 1px solid #dde3ea; text-align: center; }
    </style>
</head>
<body>
    <div class="wrapper">
        {{-- Header --}}
        <div class="header">
            <div class="badge">{{ $memo->type_label ?? $memo->type }}</div>
            <h1>{{ $memo->subject }}</h1>
        </div>

        {{-- Meta --}}
        <div class="meta">
            <span><strong>From:</strong> {{ $memo->sender->name ?? 'Admin' }}</span>
            <span><strong>Date:</strong> {{ $memo->sent_at?->format('F d, Y g:i A') ?? now()->format('F d, Y g:i A') }}</span>
            <span><strong>To:</strong> {{ $recipient->name }}</span>
        </div>

        {{-- Body --}}
        <div class="body">
            {!! nl2br(e($memo->body)) !!}
        </div>

        {{-- Attachments notice --}}
        @if (!empty($memo->attachments))
            <div class="attachments">
                <p>📎 Attachments ({{ count($memo->attachments) }})</p>
                <ul>
                    @foreach ($memo->attachments as $path)
                        <li>{{ basename($path) }}</li>
                    @endforeach
                </ul>
                <p style="margin-top:8px; font-size:12px; color:#888;">Files are attached to this email.</p>
            </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            This is an official memo from <strong>{{ config('app.name') }}</strong>.<br>
            Please do not reply directly to this email.
        </div>
    </div>
</body>
</html>