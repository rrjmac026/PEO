{{-- resources/views/emails/user/credentials.blade.php --}}
@php
    $emailTitle = 'Account Created — Your Login Credentials';
    $badgeClass = 'orange';
    $badgeText  = 'Account Created';
@endphp

@extends('emails.work-requests.layout')

@section('content')
<h2 class="email-title">Welcome, {{ $user->name }}!</h2>
<p class="email-intro">
    An account has been created for you on the
    <strong>Provincial Engineering Office Work Request Management System</strong>.
    Below are your login credentials — please keep them safe and confidential.
</p>

<table class="info-table">
    <tr>
        <td class="lbl">Full Name</td>
        <td class="val">{{ $user->name }}</td>
    </tr>
    <tr>
        <td class="lbl">Email Address</td>
        <td class="val">{{ $user->email }}</td>
    </tr>
    <tr>
        <td class="lbl">Password</td>
        <td class="val">
            <span style="
                font-family: monospace;
                font-size: 14px;
                font-weight: 700;
                background: #FFF8F2;
                border: 1px solid #F0E0D0;
                border-radius: 6px;
                padding: 3px 10px;
                letter-spacing: 0.05em;
                color: #3D2B1A;
            ">{{ $plainPassword }}</span>
        </td>
    </tr>
    <tr>
        <td class="lbl">Role</td>
        <td class="val">
            <span class="step-pill">
                {{ ucwords(str_replace('_', ' ', $user->role)) }}
            </span>
        </td>
    </tr>
</table>

<div class="remarks-box">
    <div class="remarks-label">🔒 Security Notice</div>
    <p>Please change your password immediately after your first login. Do not share these credentials with anyone.</p>
</div>

<div class="cta-wrap">
    <a href="{{ url('/login') }}" class="cta-btn">
        Log In Now →
    </a>
</div>
@endsection