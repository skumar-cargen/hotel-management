<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Booking Confirmation - {{ $booking->reference_number ?? '' }}</title>
    <style>
        @page { size: A4; margin: 12mm 14mm 12mm 14mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #2d2d2d; line-height: 1.4; }

        /* ── Header ── */
        .header { background-color: #0F1B2D; padding: 16px 22px 14px; }
        .header td { vertical-align: middle; }
        .brand { font-size: 18px; font-weight: 700; color: #fff; }
        .brand-sub { font-size: 7px; color: #C8A97E; text-transform: uppercase; letter-spacing: 3px; margin-top: 1px; }
        .doc-label { font-size: 8px; color: #C8A97E; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; text-align: right; }
        .doc-date { font-size: 9px; color: #7A8EA0; text-align: right; margin-top: 2px; }
        .gold-line { height: 2px; background: #C8A97E; }

        /* ── Reference bar ── */
        .ref-bar { background: #F7F4EF; padding: 10px 22px; border-bottom: 1px solid #E8DFD1; }
        .ref-bar td { vertical-align: middle; }
        .ref-label { font-size: 7px; color: #8B7355; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; }
        .ref-num { font-size: 17px; font-weight: 700; color: #0F1B2D; font-family: 'Courier New', monospace; letter-spacing: 2px; }
        .status { display: inline-block; padding: 3px 10px; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #0F1B2D; background: #E8DFD1; border: 1px solid #C8A97E; }

        /* ── Content ── */
        .content { padding: 14px 22px 10px; }
        .sec { margin-top: 12px; margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px solid #E8DFD1; }
        .sec-text { font-size: 8px; font-weight: 700; color: #C8A97E; text-transform: uppercase; letter-spacing: 2px; }

        /* ── Hotel card ── */
        .hotel { background: #0F1B2D; padding: 12px 16px; }
        .h-name { font-size: 14px; font-weight: 700; color: #fff; }
        .h-stars { color: #C8A97E; font-size: 10px; font-weight: 600; margin-top: 1px; }
        .h-addr { font-size: 9px; color: #7A8EA0; margin-top: 1px; }
        .h-room { margin-top: 8px; padding-top: 7px; border-top: 1px solid #1E2D42; }
        .h-room-label { font-size: 7px; color: #C8A97E; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; }
        .h-room-name { font-size: 11px; font-weight: 600; color: #fff; margin-top: 1px; }

        /* ── Date cards ── */
        .date-card { background: #F7F4EF; padding: 10px 12px; text-align: center; border: 1px solid #E8DFD1; }
        .dc-label { font-size: 7px; color: #8B7355; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; }
        .dc-val { font-size: 13px; font-weight: 700; color: #0F1B2D; margin-top: 2px; }
        .dc-day { font-size: 8px; color: #8B7355; margin-top: 1px; }

        /* ── Summary boxes ── */
        .sum-box td { text-align: center; padding: 7px 6px; background: #FAFAF8; border: 1px solid #F0EDE8; }
        .sb-val { font-size: 14px; font-weight: 700; color: #0F1B2D; }
        .sb-lbl { font-size: 7px; color: #8B8B8B; text-transform: uppercase; letter-spacing: 1px; margin-top: 1px; }

        /* ── Guest table ── */
        .g-table { width: 100%; border-collapse: collapse; }
        .g-table td { padding: 4px 0; font-size: 10px; }
        .g-label { color: #8B8B8B; width: 28%; }
        .g-value { color: #1a1d29; font-weight: 600; }

        /* ── Price table ── */
        .p-table { width: 100%; border-collapse: collapse; }
        .p-table td { padding: 6px 0; font-size: 10px; border-bottom: 1px solid #F3F0EC; }
        .p-label { color: #6b7280; }
        .p-value { text-align: right; color: #1a1d29; font-weight: 500; }
        .p-total td { border-top: 2px solid #0F1B2D; border-bottom: none; padding-top: 8px; }
        .p-total .p-label { font-size: 12px; font-weight: 700; color: #0F1B2D; }
        .p-total .p-value { font-size: 15px; font-weight: 700; color: #C8A97E; }

        /* ── Special requests ── */
        .special { background: #FDF9F0; border: 1px solid #E8DFD1; border-left: 3px solid #C8A97E; padding: 8px 12px; font-size: 10px; color: #5C4A2E; line-height: 1.6; margin-top: 6px; }

        /* ── Notes ── */
        .notes { margin-top: 12px; padding: 10px 14px; background: #F7F4EF; border: 1px solid #E8DFD1; }
        .notes-title { font-size: 7px; font-weight: 700; color: #8B7355; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px; }
        .notes p { font-size: 8px; color: #6B6B6B; line-height: 1.6; margin-bottom: 1px; }

        /* ── Footer ── */
        .footer { margin-top: 12px; padding: 12px 22px; background: #0F1B2D; text-align: center; }
        .footer p { font-size: 8px; color: #7A8EA0; line-height: 1.7; }
        .f-brand { font-size: 10px; font-weight: 700; color: #C8A97E; letter-spacing: 1px; }
        .f-div { width: 30px; height: 1px; background: #1E2D42; margin: 4px auto; }
    </style>
</head>
<body>

@php
    $currency = $booking->currency ?? 'AED';
    $dn = $booking->domain?->name ?? 'Abu Dhabi Hotels';
    $de = $booking->domain?->email ?? '';
    $dp = $booking->domain?->phone ?? '';
    $da = $booking->domain?->address ?? '';
@endphp

{{-- ═══ Header ═══ --}}
<div class="header">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td><div class="brand">{{ $dn }}</div><div class="brand-sub">Premium Hotel Booking</div></td>
            <td style="text-align: right;"><div class="doc-label">Booking Confirmation</div><div class="doc-date">{{ $booking->booked_at?->format('F d, Y') ?? now()->format('F d, Y') }}</div></td>
        </tr>
    </table>
</div>
<div class="gold-line"></div>

{{-- ═══ Reference ═══ --}}
<div class="ref-bar">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td><div class="ref-label">Booking Reference</div><div class="ref-num">{{ $booking->reference_number ?? 'N/A' }}</div></td>
            <td style="text-align: right;"><span class="status">{{ $booking->status?->label() ?? ($booking->status ?? 'Pending') }}</span></td>
        </tr>
    </table>
</div>

{{-- ═══ Content ═══ --}}
<div class="content">

    {{-- Hotel --}}
    <div class="hotel">
        <div class="h-name">{{ $booking->hotel?->name ?? 'N/A' }}</div>
        @if($booking->hotel?->star_rating)
            <div class="h-stars">@for($i = 0; $i < $booking->hotel->star_rating; $i++)&#9733; @endfor {{ $booking->hotel->star_rating }}-Star Hotel</div>
        @endif
        @if($booking->hotel?->address)<div class="h-addr">{{ $booking->hotel->address }}</div>@endif
        <div class="h-room">
            <div class="h-room-label">Room Type</div>
            <div class="h-room-name">{{ $booking->roomType?->name ?? 'N/A' }}</div>
        </div>
    </div>

    {{-- Stay --}}
    <div class="sec"><div class="sec-text">Stay Details</div></div>

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="48%"><div class="date-card"><div class="dc-label">Check-in</div><div class="dc-val">{{ $booking->check_in_date?->format('d M Y') ?? 'N/A' }}</div><div class="dc-day">{{ $booking->check_in_date?->format('l') ?? '' }}</div></div></td>
            <td width="4%"></td>
            <td width="48%"><div class="date-card"><div class="dc-label">Check-out</div><div class="dc-val">{{ $booking->check_out_date?->format('d M Y') ?? 'N/A' }}</div><div class="dc-day">{{ $booking->check_out_date?->format('l') ?? '' }}</div></div></td>
        </tr>
    </table>

    <table class="sum-box" width="100%" cellpadding="0" cellspacing="0" style="margin-top: 6px;">
        <tr>
            <td><div class="sb-val">{{ $booking->num_nights ?? 0 }}</div><div class="sb-lbl">{{ ($booking->num_nights ?? 0) === 1 ? 'Night' : 'Nights' }}</div></td>
            <td><div class="sb-val">{{ $booking->num_rooms ?? 1 }}</div><div class="sb-lbl">{{ ($booking->num_rooms ?? 1) === 1 ? 'Room' : 'Rooms' }}</div></td>
            <td><div class="sb-val">{{ $booking->num_adults ?? 0 }}</div><div class="sb-lbl">{{ ($booking->num_adults ?? 0) === 1 ? 'Adult' : 'Adults' }}</div></td>
            <td><div class="sb-val">{{ $booking->num_children ?? 0 }}</div><div class="sb-lbl">{{ ($booking->num_children ?? 0) === 1 ? 'Child' : 'Children' }}</div></td>
        </tr>
    </table>

    {{-- Guest + Price side by side --}}
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="48%" style="vertical-align: top;">
                <div class="sec"><div class="sec-text">Guest Information</div></div>
                <table class="g-table">
                    <tr><td class="g-label">Name</td><td class="g-value">{{ $booking->guest_first_name ?? '' }} {{ $booking->guest_last_name ?? '' }}</td></tr>
                    <tr><td class="g-label">Email</td><td class="g-value">{{ $booking->guest_email ?? 'N/A' }}</td></tr>
                    <tr><td class="g-label">Phone</td><td class="g-value">{{ $booking->guest_phone ?? 'N/A' }}</td></tr>
                    @if($booking->guest_nationality)<tr><td class="g-label">Nationality</td><td class="g-value">{{ $booking->guest_nationality }}</td></tr>@endif
                </table>
            </td>
            <td width="4%"></td>
            <td width="48%" style="vertical-align: top;">
                <div class="sec"><div class="sec-text">Price Breakdown</div></div>
                <table class="p-table">
                    <tr><td class="p-label">Rate / Night</td><td class="p-value">{{ $currency }} {{ number_format($booking->room_price_per_night ?? 0, 2) }}</td></tr>
                    <tr><td class="p-label">Subtotal</td><td class="p-value">{{ $currency }} {{ number_format($booking->subtotal ?? 0, 2) }}</td></tr>
                    <tr><td class="p-label">Tax ({{ number_format($booking->tax_percentage ?? 0, 1) }}%)</td><td class="p-value">{{ $currency }} {{ number_format($booking->tax_amount ?? 0, 2) }}</td></tr>
                    @if(($booking->tourism_fee ?? 0) > 0)
                        <tr><td class="p-label">Tourism Fee</td><td class="p-value">{{ $currency }} {{ number_format($booking->tourism_fee, 2) }}</td></tr>
                    @endif
                    @if(($booking->service_charge ?? 0) > 0)
                        <tr><td class="p-label">Service Charge</td><td class="p-value">{{ $currency }} {{ number_format($booking->service_charge, 2) }}</td></tr>
                    @endif
                    <tr class="p-total"><td class="p-label">Total</td><td class="p-value">{{ $currency }} {{ number_format($booking->total_amount ?? 0, 2) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Special Requests --}}
    @if(!empty($booking->special_requests))
        <div class="sec"><div class="sec-text">Special Requests</div></div>
        <div class="special">{{ $booking->special_requests }}</div>
    @endif

    {{-- Notes --}}
    <div class="notes">
        <div class="notes-title">Important Information</div>
        <p>&#8226; Present this confirmation at check-in with a valid photo ID.</p>
        <p>&#8226; Check-in and check-out times are subject to hotel policy.</p>
        <p>&#8226; Cancellation policies vary by rate and room type.</p>
    </div>
</div>

{{-- ═══ Footer ═══ --}}
<div class="footer">
    <p class="f-brand">{{ $dn }}</p>
    <div class="f-div"></div>
    @if(!empty($da))<p>{{ $da }}</p>@endif
    @if(!empty($dp) || !empty($de))
        <p>@if(!empty($dp))Phone: {{ $dp }}@endif @if(!empty($dp) && !empty($de)) | @endif @if(!empty($de))Email: {{ $de }}@endif</p>
    @endif
    <p style="margin-top: 3px; font-size: 7px; color: #556677;">&copy; {{ date('Y') }} {{ $dn }}. All rights reserved.</p>
</div>

</body>
</html>
