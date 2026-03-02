@extends('emails.layout')

@section('title', 'Reset Your Password')

@section('content')
    <h2 style="margin:0 0 16px;font-size:20px;font-weight:700;color:#100c08;">
        Reset Your Password
    </h2>

    <p style="margin:0 0 12px;font-size:15px;color:#374151;line-height:1.6;">
        Hi {{ $userName }},
    </p>

    <p style="margin:0 0 20px;font-size:15px;color:#374151;line-height:1.6;">
        We received a request to reset your password for your RealtyLinkPH account. Click the button below to set a new password.
    </p>

    <div style="text-align:center;margin:28px 0;">
        <a href="{{ $resetUrl }}"
           style="display:inline-block;padding:14px 32px;background:#FFD700;color:#100c08;text-decoration:none;border-radius:10px;font-weight:700;font-size:15px;">
            Reset Password
        </a>
    </div>

    <p style="margin:0 0 12px;font-size:13px;color:#6b7280;line-height:1.6;">
        This password reset link will expire in <strong>60 minutes</strong>.
    </p>

    <p style="margin:0 0 12px;font-size:13px;color:#6b7280;line-height:1.6;">
        If you didn't request a password reset, no action is needed. Your account is safe.
    </p>

    <div style="margin-top:24px;padding-top:16px;border-top:1px solid #e5e7eb;">
        <p style="margin:0;font-size:12px;color:#9ca3af;line-height:1.5;">
            If the button doesn't work, copy and paste this URL into your browser:<br>
            <a href="{{ $resetUrl }}" style="color:#FFD700;word-break:break-all;">{{ $resetUrl }}</a>
        </p>
    </div>
@endsection
