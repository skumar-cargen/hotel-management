@extends('emails.layouts.base', [
    'domainName' => $booking->domain?->name ?? 'Dubai Apartments',
    'domainEmail' => $booking->domain?->email ?? '',
    'domainPhone' => $booking->domain?->phone ?? '',
    'domainAddress' => $booking->domain?->address ?? '',
])

@section('subject', 'Booking Confirmation - ' . ($booking->reference_number ?? ''))

@section('preheader', 'Your booking ' . ($booking->reference_number ?? '') . ' at ' . ($booking->hotel?->name ?? 'our hotel') . ' is confirmed.')

@section('content')
    {{-- Greeting --}}
    <p style="margin: 0 0 16px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 1.6; color: #1a1d29;">
        Dear {{ $booking->guest_first_name ?? 'Guest' }},
    </p>

    {{-- Confirmation message --}}
    <p style="margin: 0 0 24px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 1.6; color: #1a1d29;">
        Thank you for your booking! We are pleased to confirm your reservation. Please find your booking details below.
    </p>

    {{-- Booking Reference (ticket style) --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
        <tr>
            <td style="background-color: #667eea; padding: 20px 24px; border-radius: 8px; text-align: center;">
                <p style="margin: 0 0 4px 0; font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: 600; color: #c7d2fe; text-transform: uppercase; letter-spacing: 1.5px;">
                    Booking Reference
                </p>
                <p style="margin: 0; font-family: 'Courier New', Courier, monospace; font-size: 28px; font-weight: 700; color: #ffffff; letter-spacing: 2px;">
                    {{ $booking->reference_number ?? 'N/A' }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Hotel Info --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px;">
        <tr>
            <td style="background-color: #f9fafb; padding: 20px 24px; border-radius: 8px; border: 1px solid #e5e7eb;">
                <h3 style="margin: 0 0 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: 700; color: #1a1d29;">
                    {{ $booking->hotel?->name ?? 'Hotel' }}
                </h3>
                @if($booking->hotel?->star_rating)
                    <p style="margin: 0 0 6px 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #667eea; font-weight: 600;">
                        {{ $booking->hotel->star_rating }}-Star Hotel
                    </p>
                @endif
                @if($booking->hotel?->address)
                    <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #6b7280;">
                        {{ $booking->hotel->address }}
                    </p>
                @endif
            </td>
        </tr>
    </table>

    {{-- Stay Details --}}
    <h3 style="margin: 0 0 12px 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: 700; color: #1a1d29; border-bottom: 2px solid #667eea; padding-bottom: 8px;">
        Stay Details
    </h3>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280; width: 40%;">
                Check-in
            </td>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; font-weight: 600; text-align: right;">
                {{ $booking->check_in_date?->format('M d, Y') ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">
                Check-out
            </td>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; font-weight: 600; text-align: right;">
                {{ $booking->check_out_date?->format('M d, Y') ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">
                Nights
            </td>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; font-weight: 600; text-align: right;">
                {{ $booking->num_nights ?? 0 }}
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">
                Rooms
            </td>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; font-weight: 600; text-align: right;">
                {{ $booking->num_rooms ?? 1 }}
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">
                Guests
            </td>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; font-weight: 600; text-align: right;">
                {{ $booking->num_adults ?? 0 }} {{ ($booking->num_adults ?? 0) === 1 ? 'adult' : 'adults' }}@if(($booking->num_children ?? 0) > 0), {{ $booking->num_children }} {{ $booking->num_children === 1 ? 'child' : 'children' }}@endif
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">
                Room Type
            </td>
            <td style="padding: 10px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; font-weight: 600; text-align: right;">
                {{ $booking->roomType?->name ?? 'N/A' }}
            </td>
        </tr>
    </table>

    {{-- Price Breakdown --}}
    @php
        $currency = $booking->currency ?? 'AED';
    @endphp

    <h3 style="margin: 0 0 12px 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: 700; color: #1a1d29; border-bottom: 2px solid #667eea; padding-bottom: 8px;">
        Price Breakdown
    </h3>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280; width: 55%;">
                Room Price / Night
            </td>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; text-align: right;">
                {{ $currency }} {{ number_format($booking->room_price_per_night ?? 0, 2) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">
                Subtotal
            </td>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; text-align: right;">
                {{ $currency }} {{ number_format($booking->subtotal ?? 0, 2) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">
                Tax ({{ number_format($booking->tax_percentage ?? 0, 2) }}%)
            </td>
            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; text-align: right;">
                {{ $currency }} {{ number_format($booking->tax_amount ?? 0, 2) }}
            </td>
        </tr>
        @if(($booking->tourism_fee ?? 0) > 0)
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">
                    Tourism Fee
                </td>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; text-align: right;">
                    {{ $currency }} {{ number_format($booking->tourism_fee, 2) }}
                </td>
            </tr>
        @endif
        @if(($booking->service_charge ?? 0) > 0)
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280;">
                    Service Charge
                </td>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; text-align: right;">
                    {{ $currency }} {{ number_format($booking->service_charge, 2) }}
                </td>
            </tr>
        @endif
        {{-- Total row --}}
        <tr>
            <td style="padding: 14px 0 10px 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: 700; color: #1a1d29;">
                Total
            </td>
            <td style="padding: 14px 0 10px 0; font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: 700; color: #667eea; text-align: right;">
                {{ $currency }} {{ number_format($booking->total_amount ?? 0, 2) }}
            </td>
        </tr>
    </table>

    {{-- Special Requests --}}
    @if(!empty($booking->special_requests))
        <h3 style="margin: 0 0 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: 700; color: #1a1d29; border-bottom: 2px solid #667eea; padding-bottom: 8px;">
            Special Requests
        </h3>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
            <tr>
                <td style="background-color: #fefce8; padding: 14px 16px; border-radius: 6px; border: 1px solid #fde68a; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.6; color: #92400e;">
                    {{ $booking->special_requests }}
                </td>
            </tr>
        </table>
    @endif

    {{-- Status note --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
        <tr>
            <td style="background-color: #eff6ff; padding: 14px 16px; border-radius: 6px; border: 1px solid #bfdbfe; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.6; color: #1e40af;">
                Your booking is currently <strong>{{ $booking->status?->label() ?? ($booking->status ?? 'pending') }}</strong>.
                You will receive further updates as your booking status changes.
            </td>
        </tr>
    </table>

    {{-- CTA Button --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="border-radius: 6px; background-color: #667eea;">
                            <a href="#view-booking" style="display: inline-block; padding: 14px 32px; font-family: Arial, Helvetica, sans-serif; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 6px; border: 1px solid #667eea;">
                                View Booking Details
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- PDF note --}}
    <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.6; color: #6b7280; text-align: center;">
        A detailed PDF of your booking is attached to this email.
    </p>
@endsection
