<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password — Provincial Engineering Office</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --orange:       #E05A00;
            --orange-dark:  #B84A00;
            --orange-light: #FF8C38;
            --cream:        #FFF8F0;
            --warm-white:   #FFFCF9;
            --stone:        #2C1E12;
            --stone-mid:    #6B4F3A;
            --stone-light:  #A07858;
            --gray-soft:    #F5EDE4;
            --error:        #D0190F;
            --success-bg:   rgba(39,146,106,0.08);
            --success-border: rgba(39,146,106,0.22);
            --success-text: #1A6640;
        }

        html, body {
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
            background: var(--cream);
            color: var(--stone);
        }

        /* ══════════════════════════
           LEFT PANEL
        ══════════════════════════ */
        .left-panel {
            background: var(--stone);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        .left-panel .glow {
            position: absolute;
            width: 480px; height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(224,90,0,0.17) 0%, transparent 70%);
            bottom: -110px; right: -100px;
            pointer-events: none;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 520px; height: 520px;
            border-radius: 50%;
            border: 1px solid rgba(224,90,0,0.14);
            bottom: -130px; right: -130px;
            pointer-events: none;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 360px; height: 360px;
            border-radius: 50%;
            border: 1px solid rgba(224,90,0,0.08);
            bottom: -50px; right: -50px;
            pointer-events: none;
        }

        .panel-top {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
            animation: fade-up 0.50s ease forwards;
            opacity: 0;
        }

        .panel-logo {
            width: 48px; height: 48px;
            object-fit: contain;
            border-radius: 10px;
            filter: drop-shadow(0 4px 12px rgba(224,90,0,0.35));
        }

        .panel-logo-text h2 {
            font-size: 0.95rem;
            font-weight: 800;
            color: rgba(255,255,255,0.90);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .panel-logo-text p {
            font-size: 0.68rem;
            color: rgba(255,255,255,0.38);
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .panel-middle {
            position: relative;
            z-index: 1;
            animation: fade-up 0.50s 0.10s ease forwards;
            opacity: 0;
        }

        .panel-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 12px;
            border-radius: 100px;
            background: rgba(224,90,0,0.14);
            border: 1px solid rgba(224,90,0,0.24);
            color: var(--orange-light);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .badge-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--orange-light);
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.35; transform: scale(0.72); }
        }

        .panel-headline {
            font-size: clamp(1.9rem, 2.6vw, 2.75rem);
            font-weight: 800;
            color: white;
            line-height: 1.12;
            letter-spacing: -0.035em;
            margin-bottom: 18px;
        }

        .panel-headline span { color: var(--orange-light); }

        .panel-desc {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.46);
            line-height: 1.72;
            max-width: 340px;
            font-weight: 400;
            margin-bottom: 36px;
        }

        /* How it works */
        .how-it-works {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .hiw-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .hiw-icon {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: rgba(224,90,0,0.14);
            border: 1px solid rgba(224,90,0,0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--orange-light);
            font-size: 0.82rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .hiw-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: rgba(255,255,255,0.80);
            line-height: 1.2;
            margin-bottom: 3px;
        }

        .hiw-sub {
            font-size: 0.76rem;
            color: rgba(255,255,255,0.32);
            font-weight: 400;
            line-height: 1.5;
        }

        .panel-bottom {
            position: relative;
            z-index: 1;
            animation: fade-up 0.50s 0.18s ease forwards;
            opacity: 0;
        }

        .panel-quote {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.28);
            font-style: italic;
            line-height: 1.65;
            border-left: 2px solid rgba(224,90,0,0.28);
            padding-left: 14px;
        }

        /* ══════════════════════════
           RIGHT PANEL
        ══════════════════════════ */
        .right-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        .right-panel::before {
            content: '';
            position: absolute;
            width: 360px; height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(224,90,0,0.06) 0%, transparent 70%);
            top: -80px; right: -80px;
            pointer-events: none;
        }

        .right-panel::after {
            content: '';
            position: absolute;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(201,122,0,0.05) 0%, transparent 70%);
            bottom: -60px; left: -60px;
            pointer-events: none;
        }

        .form-wrap {
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 1;
            animation: fade-up 0.50s 0.08s ease forwards;
            opacity: 0;
        }

        @keyframes fade-up {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Icon header */
        .form-icon-wrap {
            width: 60px; height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--orange), var(--orange-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.4rem;
            margin-bottom: 22px;
            box-shadow: 0 6px 20px rgba(224,90,0,0.28);
        }

        .form-header { margin-bottom: 28px; }

        .form-eyebrow {
            font-size: 0.70rem;
            font-weight: 700;
            color: var(--orange);
            letter-spacing: 0.13em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .form-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--stone);
            letter-spacing: -0.035em;
            line-height: 1.15;
            margin-bottom: 8px;
        }

        .form-sub {
            font-size: 0.875rem;
            color: var(--stone-light);
            font-weight: 400;
            line-height: 1.58;
        }

        /* Session status — success */
        .session-status {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 24px;
            animation: fade-up 0.35s ease forwards;
        }

        .session-status-icon {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: rgba(39,146,106,0.14);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--success-text);
            font-size: 0.80rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .session-status-text {
            font-size: 0.85rem;
            color: var(--success-text);
            font-weight: 500;
            line-height: 1.55;
        }

        .session-status-text strong {
            display: block;
            font-weight: 700;
            margin-bottom: 2px;
            font-size: 0.88rem;
        }

        /* Field */
        .field-group { margin-bottom: 18px; }

        .field-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--stone-mid);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .field-input-wrap { position: relative; }

        .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #C4A898;
            font-size: 0.82rem;
            pointer-events: none;
            transition: color 0.18s;
        }

        .field-input-wrap:focus-within .field-icon { color: var(--orange); }

        .field-input {
            width: 100%;
            padding: 12px 16px 12px 40px;
            border-radius: 10px;
            border: 1.5px solid #E8D9CC;
            background: var(--warm-white);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.925rem;
            font-weight: 500;
            color: var(--stone);
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
            -webkit-appearance: none;
        }

        .field-input::placeholder { color: #C4A898; font-weight: 400; }

        .field-input:focus {
            border-color: var(--orange);
            background: white;
            box-shadow: 0 0 0 3px rgba(224,90,0,0.11);
        }

        .field-error {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.80rem;
            color: var(--error);
            font-weight: 500;
            margin-top: 6px;
        }

        /* Submit */
        .btn-send {
            width: 100%;
            padding: 14px;
            border-radius: 11px;
            border: none;
            background: var(--orange);
            color: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.975rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: background 0.18s, transform 0.12s, box-shadow 0.18s;
            box-shadow: 0 4px 20px rgba(224,90,0,0.30);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            margin-top: 24px;
            position: relative;
            overflow: hidden;
        }

        .btn-send::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.10) 0%, transparent 55%);
            pointer-events: none;
        }

        .btn-send:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 26px rgba(224,90,0,0.38);
        }

        .btn-send:active { transform: translateY(0); }

        /* Footer */
        .form-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(224,90,0,0.12), transparent);
            margin: 24px 0;
        }

        .form-footer {
            text-align: center;
            font-size: 0.85rem;
            color: var(--stone-light);
            font-weight: 500;
        }

        .form-footer a {
            color: var(--orange);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.15s;
        }
        .form-footer a:hover { color: var(--orange-dark); text-decoration: underline; }

        /* Responsive */
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .left-panel { display: none; }
            .right-panel { padding: 40px 24px; }
        }
    </style>
</head>
<body>

    <!-- ═══ LEFT PANEL ═══ -->
    <div class="left-panel">
        <div class="glow"></div>

        <div class="panel-top">
            <img src="{{ asset('assets/app_logo.PNG') }}" alt="PEO Logo" class="panel-logo">
            <div class="panel-logo-text">
                <h2>Provincial Engineering Office</h2>
                <p>Malaybalay City, Bukidnon</p>
            </div>
        </div>

        <div class="panel-middle">
            <div class="panel-badge">
                <div class="badge-dot"></div>
                Account Recovery
            </div>
            <h1 class="panel-headline">
                Forgot Your<br>
                <span>Password?</span><br>
                No Problem.
            </h1>
            <p class="panel-desc">
                We'll send a secure reset link straight to your inbox. Just enter your email and follow the steps.
            </p>

            <div class="how-it-works">
                <div class="hiw-item">
                    <div class="hiw-icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="hiw-title">Enter your email</div>
                        <div class="hiw-sub">Provide the address linked to your PEO account</div>
                    </div>
                </div>
                <div class="hiw-item">
                    <div class="hiw-icon"><i class="fas fa-inbox"></i></div>
                    <div>
                        <div class="hiw-title">Check your inbox</div>
                        <div class="hiw-sub">We'll send a secure reset link within a few minutes</div>
                    </div>
                </div>
                <div class="hiw-item">
                    <div class="hiw-icon"><i class="fas fa-key"></i></div>
                    <div>
                        <div class="hiw-title">Set a new password</div>
                        <div class="hiw-sub">Click the link and choose a strong new password</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-bottom">
            <p class="panel-quote">
                "Committed to quality engineering services and sustainable infrastructure development for the people of Bukidnon."
            </p>
        </div>
    </div>

    <!-- ═══ RIGHT PANEL ═══ -->
    <div class="right-panel">
        <div class="form-wrap">

            <div class="form-icon-wrap">
                <i class="fas fa-paper-plane"></i>
            </div>

            <div class="form-header">
                <div class="form-eyebrow">Password Recovery</div>
                <h2 class="form-title">Forgot Password</h2>
                <p class="form-sub">Enter your registered email address and we'll send you a link to reset your password.</p>
            </div>

            {{-- Session Status --}}
            @if(session('status'))
                <div class="session-status">
                    <div class="session-status-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="session-status-text">
                        <strong>Link sent!</strong>
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="field-group">
                    <label class="field-label" for="email">Email Address</label>
                    <div class="field-input-wrap">
                        <i class="fas fa-envelope field-icon"></i>
                        <input
                            id="email"
                            class="field-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="you@example.com"
                        >
                    </div>
                    @error('email')
                        <div class="field-error">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn-send">
                    <i class="fas fa-paper-plane"></i>
                    Send Reset Link
                </button>
            </form>

            <div class="form-divider"></div>
            <div class="form-footer">
                Remember your password?
                <a href="{{ route('login') }}">Back to Sign In</a>
            </div>

        </div>
    </div>

</body>
</html>