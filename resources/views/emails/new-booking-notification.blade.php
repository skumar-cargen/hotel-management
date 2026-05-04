@extends('emails.layouts.base')

@section('subject', 'New Booking - ' . $booking->reference_number)
@section('preheader', 'A new booking has been received' . ($domain?->name ? ' on ' . $domain->name : ''))

@section('content')
    <h2 style="margin: 0 0 16px 0; font-family: Arial, Helvetica, sans-serif; font-size: 22px; font-weight: 700; color: #0F1B2D;">
        New Booking Received
    </h2>

    <p style="margin: 0 0 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 1.6; color: #1a1d29;">
        Reference: <strong>{{ $booking->reference_number }}</strong>
    </p>
    <p style="margin: 0 0 20px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.6; color: #556677;">
        Status: <span style="text-transform: capitalize;">{{ $booking->status?->value ?? $booking->status }}</span>
        @if($domain)&nbsp;&middot;&nbsp;Domain: {{ $domain->name }}@endif
    </p>

    <h3 style="margin: 24px 0 10px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 700; color: #0F1B2D; text-transform: uppercase; letter-spacing: 0.05em;">
        Guest Details
    </h3>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; margin: 0 0 20px 0;">
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677; width: 35%;">Name</td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; font-weight: 600;">
                {{ trim($booking->guest_first_name . ' ' . $booking->guest_last_name) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">Email</td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">
                <a href="mailto:{{ $booking->guest_email }}" style="color: #0F1B2D; text-decoration: none;">{{ $booking->guest_email }}</a>
            </td>
        </tr>
        @if($booking->guest_phone)
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">Phone</td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">{{ $booking->guest_phone }}</td>
        </tr>
        @endif
        @if($booking->guest_nationality)
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">Nationality</td>
            <td style="padding: 10px 12px; background-color: #ffffff; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">{{ $booking->guest_nationality }}</td>
        </tr>
        @endif
    </table>

    <h3 style="margin: 24px 0 10px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 700; color: #0F1B2D; text-transform: uppercase; letter-spacing: 0.05em;">
        Booking Details
    </h3>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; margin: 0 0 20px 0;">
        @if($booking->hotel)
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677; width: 35%;">Hotel</td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; font-weight: 600;">{{ $booking->hotel->name }}</td>
        </tr>
        @endif
        @if($booking->roomType)
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">Room Type</td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">{{ $booking->roomType->name }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">Check-in</td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">{{ $booking->check_in_date?->format('d M Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">Check-out</td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">{{ $booking->check_out_date?->format('d M Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">Nights</td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">{{ $booking->num_nights }}</td>
        </tr>
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">Guests</td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">
                {{ $booking->num_adults }} adult{{ $booking->num_adults > 1 ? 's' : '' }}@if($booking->num_children), {{ $booking->num_children }} child{{ $booking->num_children > 1 ? 'ren' : '' }}@endif &middot; {{ $booking->num_rooms }} room{{ $booking->num_rooms > 1 ? 's' : '' }}
            </td>
        </tr>
        @if($booking->special_requests)
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677; vertical-align: top;">Special Requests</td>
            <td style="padding: 10px 12px; background-color: #ffffff; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; line-height: 1.5;">{{ $booking->special_requests }}</td>
        </tr>
        @endif
    </table>

    <h3 style="margin: 24px 0 10px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 700; color: #0F1B2D; text-transform: uppercase; letter-spacing: 0.05em;">
        Total
    </h3>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; margin: 0 0 20px 0;">
        <tr>
            <td style="padding: 14px 16px; background-color: #0F1B2D; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #C8A97E; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">Total Amount</td>
            <td style="padding: 14px 16px; background-color: #0F1B2D; font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #ffffff; font-weight: 700; text-align: right;">
                {{ $booking->currency }} {{ number_format((float) $booking->total_amount, 2) }}
            </td>
        </tr>
    </table>

    <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.6; color: #556677;">
        Booked at: {{ $booking->created_at?->format('d M Y, H:i') }}<br>
        This is an automated notification from the booking system.
    </p>
@endsection
