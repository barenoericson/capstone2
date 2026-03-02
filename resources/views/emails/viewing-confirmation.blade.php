@extends('emails.layout')

@section('title', 'Viewing Confirmed')

@section('content')
    <h2 style="margin:0 0 16px;font-size:20px;color:#100c08;">Viewing Confirmed!</h2>

    <p style="margin:0 0 20px;font-size:15px;color:#374151;line-height:1.6;">
        @if($recipientRole === 'buyer')
            Great news! Your viewing request has been confirmed. Here are the details:
        @else
            A viewing has been confirmed for your property. Here are the details:
        @endif
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#fefce8;border:1px solid #fde68a;border-radius:8px;padding:20px;margin-bottom:24px;">
        <tr>
            <td style="padding:8px 16px;">
                <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">Property</p>
                <p style="margin:0;font-size:16px;font-weight:600;color:#100c08;">{{ $viewing->property->title }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 16px;">
                <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">Location</p>
                <p style="margin:0;font-size:15px;color:#100c08;">{{ $viewing->property->city ?? 'N/A' }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 16px;">
                <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">Date</p>
                <p style="margin:0;font-size:15px;color:#100c08;">{{ $viewing->viewing_date->format('l, F j, Y') }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 16px;">
                <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">Time</p>
                <p style="margin:0;font-size:15px;color:#100c08;">{{ \Carbon\Carbon::parse($viewing->viewing_time)->format('g:i A') }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 16px;">
                <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">
                    @if($recipientRole === 'buyer') Agent @else Buyer @endif
                </p>
                <p style="margin:0;font-size:15px;color:#100c08;">
                    @if($recipientRole === 'buyer')
                        {{ $viewing->agent->user->name ?? 'N/A' }}
                    @else
                        {{ $viewing->buyer->name ?? 'N/A' }}
                    @endif
                </p>
            </td>
        </tr>
    </table>

    @if($viewing->buyer_notes)
        <p style="margin:0 0 24px;font-size:14px;color:#6b7280;line-height:1.5;">
            <strong>Notes:</strong> {{ $viewing->buyer_notes }}
        </p>
    @endif

    <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
        <tr>
            <td style="background:#FFD700;border-radius:8px;">
                <a href="{{ config('app.url') }}/viewings" style="display:inline-block;padding:12px 32px;font-size:15px;font-weight:600;color:#100c08;text-decoration:none;">
                    View Details
                </a>
            </td>
        </tr>
    </table>
@endsection
