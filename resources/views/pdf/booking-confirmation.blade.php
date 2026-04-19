<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Booking Confirmation - {{ $booking->reference_number ?? '' }}</title>
    <style>
        /* DomPDF-compatible styles */
        @page {
            size: A4;
            margin: 20mm 15mm 20mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1a1d29;
            line-height: 1.5;
            background-color: #ffffff;
        }

        /* Header */
        .header {
            width: 100%;
            border-bottom: 2px solid #667eea;
            padding-bottom: 12px;
            margin-bottom: 0;
        }

        .header-table {
            width: 100%;
        }

        .header-left {
            text-align: left;
            vertical-align: bottom;
        }

        .header-right {
            text-align: right;
            vertical-align: bottom;
        }

        .domain-name {
            font-size: 20px;
            font-weight: 700;
            color: #1a1d3a;
        }

        .doc-title {
            font-size: 16px;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-date {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* Reference bar */
        .reference-bar {
            background-color: #667eea;
            color: #ffffff;
            padding: 14px 20px;
            margin: 16px 0;
            text-align: center;
        }

        .reference-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #c7d2fe;
            margin-bottom: 4px;
        }

        .reference-number {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 3px;
            font-family: 'Courier New', Courier, monospace;
        }

        /* Status badge */
        .status-bar {
            text-align: center;
            margin-bottom: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid #e5e7eb;
            color: #1a1d29;
            background-color: #f3f4f6;
        }

        /* Section titles */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a1d29;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 12px;
            background-color: #f3f4f6;
            border-left: 3px solid #667eea;
            margin-bottom: 12px;
            margin-top: 20px;
        }

        /* Info table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .info-table td {
            padding: 6px 12px;
            font-size: 12px;
            vertical-align: top;
        }

        .info-label {
            color: #6b7280;
            width: 35%;
            font-weight: 400;
        }

        .info-value {
            color: #1a1d29;
            font-weight: 500;
        }

        /* Two-column layout */
        .two-col-table {
            width: 100%;
            border-collapse: collapse;
        }

        .two-col-table td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .two-col-table td:first-child {
            padding-right: 10px;
        }

        .two-col-table td:last-child {
            padding-left: 10px;
        }

        /* Price table */
        .price-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .price-table td {
            padding: 8px 12px;
            font-size: 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .price-table .price-label {
            color: #6b7280;
            text-align: left;
        }

        .price-table .price-value {
            color: #1a1d29;
            text-align: right;
            font-weight: 500;
        }

        .price-table .total-row td {
            border-top: 2px solid #1a1d29;
            border-bottom: none;
            padding-top: 10px;
            font-weight: 700;
            font-size: 14px;
        }

        .price-table .total-row .price-value {
            color: #667eea;
            font-size: 16px;
        }

        /* Special requests */
        .special-requests {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            padding: 10px 14px;
            font-size: 12px;
            color: #92400e;
            margin-top: 8px;
            line-height: 1.6;
        }

        /* Terms section */
        .terms {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
        }

        .terms p {
            font-size: 10px;
            color: #9ca3af;
            line-height: 1.6;
            margin-bottom: 4px;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 2px solid #667eea;
            text-align: center;
        }

        .footer p {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 2px;
        }

        .footer .footer-domain {
            font-size: 12px;
            font-weight: 700;
            color: #1a1d3a;
        }
    </style>
</head>
<body>

    @php
        $currency = $booking->currency ?? 'AED';
        $domainName = $booking->domain?->name ?? 'Dubai Apartments';
        $domainEmail = $booking->domain?->email ?? '';
        $domainPhone = $booking->domain?->phone ?? '';
        $domainAddress = $booking->domain?->address ?? '';
    @endphp

    {{-- Header --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <div class="domain-name">{{ $domainName }}</div>
                </td>
                <td class="header-right">
                    <div class="doc-title">Booking Confirmation</div>
                    <div class="doc-date">Date: {{ $booking->booked_at?->format('M d, Y') ?? $booking->created_at?->format('M d, Y') ?? now()->format('M d, Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Booking Reference Bar --}}
    <div class="reference-bar">
        <div class="reference-label">Booking Reference</div>
        <div class="reference-number">{{ $booking->reference_number ?? 'N/A' }}</div>
    </div>

    {{-- Status --}}
    <div class="status-bar">
        <span class="status-badge">
            Status: {{ $booking->status?->label() ?? ($booking->status ?? 'Pending') }}
        </span>
    </div>

    {{-- Guest Information --}}
    <div class="section-title">Guest Information</div>
    <table class="two-col-table">
        <tr>
            <td>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Full Name</td>
                        <td class="info-value">{{ $booking->guest_first_name ?? '' }} {{ $booking->guest_last_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Email</td>
                        <td class="info-value">{{ $booking->guest_email ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Phone</td>
                        <td class="info-value">{{ $booking->guest_phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Nationality</td>
                        <td class="info-value">{{ $booking->guest_nationality ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Hotel & Room Details --}}
    <div class="section-title">Hotel &amp; Room Details</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Hotel</td>
            <td class="info-value">{{ $booking->hotel?->name ?? 'N/A' }}</td>
        </tr>
        @if($booking->hotel?->star_rating)
            <tr>
                <td class="info-label">Star Rating</td>
                <td class="info-value">{{ $booking->hotel->star_rating }}-Star Hotel</td>
            </tr>
        @endif
        <tr>
            <td class="info-label">Room Type</td>
            <td class="info-value">{{ $booking->roomType?->name ?? 'N/A' }}</td>
        </tr>
        @if($booking->hotel?->address)
            <tr>
                <td class="info-label">Address</td>
                <td class="info-value">{{ $booking->hotel->address }}</td>
            </tr>
        @endif
    </table>

    {{-- Stay Details --}}
    <div class="section-title">Stay Details</div>
    <table class="two-col-table">
        <tr>
            <td>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Check-in</td>
                        <td class="info-value">{{ $booking->check_in_date?->format('M d, Y') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Check-out</td>
                        <td class="info-value">{{ $booking->check_out_date?->format('M d, Y') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Nights</td>
                        <td class="info-value">{{ $booking->num_nights ?? 0 }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Rooms</td>
                        <td class="info-value">{{ $booking->num_rooms ?? 1 }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Adults</td>
                        <td class="info-value">{{ $booking->num_adults ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Children</td>
                        <td class="info-value">{{ $booking->num_children ?? 0 }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Price Breakdown --}}
    <div class="section-title">Price Breakdown</div>
    <table class="price-table">
        <tr>
            <td class="price-label">Room Price / Night</td>
            <td class="price-value">{{ $currency }} {{ number_format($booking->room_price_per_night ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="price-label">Subtotal</td>
            <td class="price-value">{{ $currency }} {{ number_format($booking->subtotal ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="price-label">Tax ({{ number_format($booking->tax_percentage ?? 0, 2) }}%)</td>
            <td class="price-value">{{ $currency }} {{ number_format($booking->tax_amount ?? 0, 2) }}</td>
        </tr>
        @if(($booking->tourism_fee ?? 0) > 0)
            <tr>
                <td class="price-label">Tourism Fee</td>
                <td class="price-value">{{ $currency }} {{ number_format($booking->tourism_fee, 2) }}</td>
            </tr>
        @endif
        @if(($booking->service_charge ?? 0) > 0)
            <tr>
                <td class="price-label">Service Charge</td>
                <td class="price-value">{{ $currency }} {{ number_format($booking->service_charge, 2) }}</td>
            </tr>
        @endif
        <tr class="total-row">
            <td class="price-label">Total Amount</td>
            <td class="price-value">{{ $currency }} {{ number_format($booking->total_amount ?? 0, 2) }}</td>
        </tr>
    </table>

    {{-- Special Requests --}}
    @if(!empty($booking->special_requests))
        <div class="section-title">Special Requests</div>
        <div class="special-requests">
            {{ $booking->special_requests }}
        </div>
    @endif

    {{-- Terms / Notes --}}
    <div class="terms">
        <p>This is a system-generated document. For any queries, please contact {{ $domainEmail ?: $domainName }}.</p>
        <p>Please present this confirmation at the time of check-in along with a valid photo ID.</p>
        <p>Check-in and check-out times are subject to hotel policy.</p>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p class="footer-domain">{{ $domainName }}</p>
        @if(!empty($domainAddress))
            <p>{{ $domainAddress }}</p>
        @endif
        @if(!empty($domainPhone) || !empty($domainEmail))
            <p>
                @if(!empty($domainPhone))
                    Phone: {{ $domainPhone }}
                @endif
                @if(!empty($domainPhone) && !empty($domainEmail))
                    &nbsp;&bull;&nbsp;
                @endif
                @if(!empty($domainEmail))
                    Email: {{ $domainEmail }}
                @endif
            </p>
        @endif
    </div>

</body>
</html>
