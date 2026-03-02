@extends('emails.layout')

@section('title', 'Schedule Change Proposed')

@section('content')
    <h2 style="margin:0 0 16px;font-size:20px;color:#100c08;">New Schedule Proposed</h2>

    <p style="margin:0 0 20px;font-size:15px;color:#374151;line-height:1.6;">
        @if($recipientRole === 'buyer')
            The agent has proposed a new schedule for your viewing of <strong>{{ $viewing->property->title }}</strong>.
        @else
            The buyer has proposed a new schedule for viewing <strong>{{ $viewing->property->title }}</strong>.
        @endif
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:20px;margin-bottom:24px;">
        <tr>
            <td style="padding:8px 16px;">
                <p style="margin:0 0 4px;font-size:12px;font-weight:600;color:#2563eb;text-transform:uppercase;letter-spacing:0.5px;">Proposed Schedule</p>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 16px;">
                <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">Date</p>
                <p style="margin:0;font-size:15px;color:#100c08;">{{ $negotiation->proposed_date->format('l, F j, Y') }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 16px;">
                <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">Time</p>
                <p style="margin:0;font-size:15px;color:#100c08;">{{ \Carbon\Carbon::parse($negotiation->proposed_time)->format('g:i A') }}</p>
            </td>
        </tr>
        @if($negotiation->note)
            <tr>
                <td style="padding:8px 16px;">
                    <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">Note</p>
                    <p style="margin:0;font-size:15px;color:#100c08;font-style:italic;">"{{ $negotiation->note }}"</p>
                </td>
            </tr>
        @endif
    </table>

    <p style="margin:0 0 24px;font-size:14px;color:#6b7280;line-height:1.5;">
        You can accept this schedule, propose a different time, or reject the viewing request.
    </p>

    <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
        <tr>
            <td style="background:#FFD700;border-radius:8px;">
                <a href="{{ config('app.url') }}/viewings" style="display:inline-block;padding:12px 32px;font-size:15px;font-weight:600;color:#100c08;text-decoration:none;">
                    Review Proposal
                </a>
            </td>
        </tr>
    </table>
@endsection
