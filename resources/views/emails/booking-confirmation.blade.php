@extends('emails.layouts.base', [
    'domainName' => $booking->domain?->name ?? 'Abu Dhabi Hotels',
    'domainEmail' => $booking->domain?->email ?? '',
    'domainPhone' => $booking->domain?->phone ?? '',
    'domainAddress' => $booking->domain?->address ?? '',
])

@section('subject', 'Booking Confirmation - ' . ($booking->reference_number ?? ''))

@section('preheader', 'Your booking ' . ($booking->reference_number ?? '') . ' at ' . ($booking->hotel?->name ?? 'our hotel') . ' is confirmed.')

@section('content')
    @php
        $currency = $booking->currency ?? 'AED';
    @endphp

    {{-- Greeting --}}
    <p style="margin: 0 0 6px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #8B7355; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;">
        Welcome
    </p>
    <p style="margin: 0 0 20px 0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; line-height: 1.4; color: #0F1B2D; font-weight: 700;">
        Dear {{ $booking->guest_first_name ?? 'Guest' }},
    </p>

    <p style="margin: 0 0 28px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 1.7; color: #4a4a4a;">
        Thank you for choosing us. Your reservation has been received and we are delighted to confirm your upcoming stay. Please find your booking details below.
    </p>

    {{-- Booking Reference --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
        <tr>
            <td style="background-color: #0F1B2D; padding: 24px 28px; text-align: center;">
                <p style="margin: 0 0 6px 0; font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-weight: 600; color: #C8A97E; text-transform: uppercase; letter-spacing: 2.5px;">
                    Booking Reference
                </p>
                <p style="margin: 0; font-family: 'Courier New', Courier, monospace; font-size: 28px; font-weight: 700; color: #ffffff; letter-spacing: 3px;">
                    {{ $booking->reference_number ?? 'N/A' }}
                </p>
            </td>
        </tr>
        <tr>
            <td style="height: 3px; background-color: #C8A97E; font-size: 1px; line-height: 1px;">&nbsp;</td>
        </tr>
    </table>

    {{-- Hotel Info Card --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
        <tr>
            <td style="background-color: #F7F4EF; padding: 22px 24px; border: 1px solid #E8DFD1;">
                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #C8A97E; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;">
                    Your Hotel
                </p>
                <h3 style="margin: 6px 0 6px 0; font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: 700; color: #0F1B2D;">
                    {{ $booking->hotel?->name ?? 'Hotel' }}
                </h3>
                @if($booking->hotel?->star_rating)
                    <p style="margin: 0 0 4px 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #C8A97E; font-weight: 600;">
                        @for($i = 0; $i < $booking->hotel->star_rating; $i++)&#9733; @endfor
                    </p>
                @endif
                @if($booking->hotel?->address)
                    <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #6b7280;">
                        {{ $booking->hotel->address }}
                    </p>
                @endif
                @if($booking->roomType?->name)
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 12px;">
                        <tr>
                            <td style="border-top: 1px solid #E8DFD1; padding-top: 12px;">
                                <p style="margin: 0 0 2px 0; font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #8B7355; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">Room Type</p>
                                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #0F1B2D; font-weight: 600;">{{ $booking->roomType->name }}</p>
                            </td>
                        </tr>
                    </table>
                @endif
            </td>
        </tr>
    </table>

    {{-- Stay Details --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 8px;">
        <tr>
            <td style="padding-bottom: 14px;">
                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #C8A97E; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;">Stay Details</p>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 2px;"><tr><td style="border-top: 1px solid #E8DFD1; font-size: 1px; line-height: 1px;">&nbsp;</td></tr></table>
            </td>
        </tr>
    </table>

    {{-- Check-in / Check-out side by side --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
        <tr>
            <td width="48%" style="background-color: #F7F4EF; padding: 16px 18px; text-align: center; border: 1px solid #E8DFD1;">
                <p style="margin: 0 0 4px 0; font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #8B7355; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;">Check-in</p>
                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: 700; color: #0F1B2D;">
                    {{ $booking->check_in_date?->format('d M Y') ?? 'N/A' }}
                </p>
                <p style="margin: 4px 0 0 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #8B7355;">
                    {{ $booking->check_in_date?->format('l') ?? '' }}
                </p>
            </td>
            <td width="4%">&nbsp;</td>
            <td width="48%" style="background-color: #F7F4EF; padding: 16px 18px; text-align: center; border: 1px solid #E8DFD1;">
                <p style="margin: 0 0 4px 0; font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #8B7355; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;">Check-out</p>
                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: 700; color: #0F1B2D;">
                    {{ $booking->check_out_date?->format('d M Y') ?? 'N/A' }}
                </p>
                <p style="margin: 4px 0 0 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #8B7355;">
                    {{ $booking->check_out_date?->format('l') ?? '' }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Stay summary row --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
        <tr>
            <td width="25%" style="background-color: #FAFAF8; padding: 12px 8px; text-align: center; border: 1px solid #F0EDE8;">
                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: 700; color: #0F1B2D;">{{ $booking->num_nights ?? 0 }}</p>
                <p style="margin: 2px 0 0 0; font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #8B8B8B; text-transform: uppercase; letter-spacing: 1px;">{{ ($booking->num_nights ?? 0) === 1 ? 'Night' : 'Nights' }}</p>
            </td>
            <td width="25%" style="background-color: #FAFAF8; padding: 12px 8px; text-align: center; border: 1px solid #F0EDE8;">
                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: 700; color: #0F1B2D;">{{ $booking->num_rooms ?? 1 }}</p>
                <p style="margin: 2px 0 0 0; font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #8B8B8B; text-transform: uppercase; letter-spacing: 1px;">{{ ($booking->num_rooms ?? 1) === 1 ? 'Room' : 'Rooms' }}</p>
            </td>
            <td width="25%" style="background-color: #FAFAF8; padding: 12px 8px; text-align: center; border: 1px solid #F0EDE8;">
                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: 700; color: #0F1B2D;">{{ $booking->num_adults ?? 0 }}</p>
                <p style="margin: 2px 0 0 0; font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #8B8B8B; text-transform: uppercase; letter-spacing: 1px;">{{ ($booking->num_adults ?? 0) === 1 ? 'Adult' : 'Adults' }}</p>
            </td>
            <td width="25%" style="background-color: #FAFAF8; padding: 12px 8px; text-align: center; border: 1px solid #F0EDE8;">
                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: 700; color: #0F1B2D;">{{ $booking->num_children ?? 0 }}</p>
                <p style="margin: 2px 0 0 0; font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #8B8B8B; text-transform: uppercase; letter-spacing: 1px;">{{ ($booking->num_children ?? 0) === 1 ? 'Child' : 'Children' }}</p>
            </td>
        </tr>
    </table>

    {{-- Price Breakdown --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 8px;">
        <tr>
            <td style="padding-bottom: 14px;">
                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #C8A97E; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;">Price Breakdown</p>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 2px;"><tr><td style="border-top: 1px solid #E8DFD1; font-size: 1px; line-height: 1px;">&nbsp;</td></tr></table>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #F3F0EC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280; width: 55%;">Room Rate / Night</td>
            <td style="padding: 10px 0; border-bottom: 1px solid #F3F0EC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; text-align: right; font-weight: 500;">{{ $currency }} {{ number_format($booking->room_price_per_night ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #F3F0EC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">Subtotal</td>
            <td style="padding: 10px 0; border-bottom: 1px solid #F3F0EC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; text-align: right; font-weight: 500;">{{ $currency }} {{ number_format($booking->subtotal ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #F3F0EC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">Tax ({{ number_format($booking->tax_percentage ?? 0, 1) }}%)</td>
            <td style="padding: 10px 0; border-bottom: 1px solid #F3F0EC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; text-align: right; font-weight: 500;">{{ $currency }} {{ number_format($booking->tax_amount ?? 0, 2) }}</td>
        </tr>
        @if(($booking->tourism_fee ?? 0) > 0)
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #F3F0EC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">Tourism Fee</td>
                <td style="padding: 10px 0; border-bottom: 1px solid #F3F0EC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; text-align: right; font-weight: 500;">{{ $currency }} {{ number_format($booking->tourism_fee, 2) }}</td>
            </tr>
        @endif
        @if(($booking->service_charge ?? 0) > 0)
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #F3F0EC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">Service Charge</td>
                <td style="padding: 10px 0; border-bottom: 1px solid #F3F0EC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; text-align: right; font-weight: 500;">{{ $currency }} {{ number_format($booking->service_charge, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td style="padding: 14px 0; border-top: 2px solid #0F1B2D; font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: 700; color: #0F1B2D;">Total Amount</td>
            <td style="padding: 14px 0; border-top: 2px solid #0F1B2D; font-family: Arial, Helvetica, sans-serif; font-size: 22px; font-weight: 700; color: #C8A97E; text-align: right;">{{ $currency }} {{ number_format($booking->total_amount ?? 0, 2) }}</td>
        </tr>
    </table>

    {{-- Special Requests --}}
    @if(!empty($booking->special_requests))
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
            <tr>
                <td style="background-color: #FDF9F0; padding: 16px 20px; border: 1px solid #E8DFD1; border-left: 3px solid #C8A97E;">
                    <p style="margin: 0 0 6px 0; font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #8B7355; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">Special Requests</p>
                    <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.6; color: #5C4A2E;">{{ $booking->special_requests }}</p>
                </td>
            </tr>
        </table>
    @endif

    {{-- Status note --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
        <tr>
            <td style="background-color: #F7F4EF; padding: 16px 20px; border: 1px solid #E8DFD1;">
                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.6; color: #5C4A2E;">
                    Your booking is currently <strong style="color: #0F1B2D;">{{ $booking->status?->label() ?? ($booking->status ?? 'pending') }}</strong>.
                    You will receive further updates as your booking status changes.
                </p>
            </td>
        </tr>
    </table>

    {{-- Important notes --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
        <tr>
            <td style="padding: 16px 20px; border: 1px solid #E8DFD1; background-color: #FAFAF8;">
                <p style="margin: 0 0 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #8B7355; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">Important Information</p>
                <p style="margin: 0 0 4px 0; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #6b7280; line-height: 1.7;">&#8226; Please present this confirmation at check-in with a valid photo ID.</p>
                <p style="margin: 0 0 4px 0; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #6b7280; line-height: 1.7;">&#8226; Check-in/check-out times are subject to hotel policy.</p>
                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #6b7280; line-height: 1.7;">&#8226; For queries, please reply to this email or contact the hotel directly.</p>
            </td>
        </tr>
    </table>
@endsection
