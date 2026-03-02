@extends('emails.layout')

@section('title', 'Verify Your Email')

@section('content')
    <h2 style="margin:0 0 16px;font-size:20px;font-weight:700;color:#100c08;">
        Welcome to RealtyLinkPH!
    </h2>

    <p style="margin:0 0 12px;font-size:15px;color:#374151;line-height:1.6;">
        Hi {{ $userName }},
    </p>

    <p style="margin:0 0 20px;font-size:15px;color:#374151;line-height:1.6;">
        Thank you for creating an account. Please verify your email address by clicking the button below.
    </p>

    <div style="text-align:center;margin:28px 0;">
        <a href="{{ $verifyUrl }}"
           style="display:inline-block;padding:14px 32px;background:#FFD700;color:#100c08;text-decoration:none;border-radius:10px;font-weight:700;font-size:15px;">
            Verify Email Address
        </a>
    </div>

    <p style="margin:0 0 12px;font-size:13px;color:#6b7280;line-height:1.6;">
        If you didn't create an account on RealtyLinkPH, you can safely ignore this email.
    </p>

    <div style="margin-top:24px;padding-top:16px;border-top:1px solid #e5e7eb;">
        <p style="margin:0;font-size:12px;color:#9ca3af;line-height:1.5;">
            If the button doesn't work, copy and paste this URL into your browser:<br>
            <a href="{{ $verifyUrl }}" style="color:#FFD700;word-break:break-all;">{{ $verifyUrl }}</a>
        </p>
    </div>
@endsection
