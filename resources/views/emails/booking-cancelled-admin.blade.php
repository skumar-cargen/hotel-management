<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Cancelled — Admin Notification</title>
</head>
<body style="margin:0;padding:0;background:#f4f5f8;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f5f8;padding:32px 16px;">
        <tr><td align="center">
            <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
                {{-- Header --}}
                <tr><td style="background:#0f172a;color:#ffffff;padding:24px 32px;">
                    <p style="margin:0 0 4px;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;opacity:0.7;">Admin Notification</p>
                    <h1 style="margin:0;font-size:20px;font-weight:700;">Booking Cancelled by Customer</h1>
                    <p style="margin:6px 0 0;font-size:13px;opacity:0.85;">{{ $domain->name }} ({{ $domain->slug }})</p>
                </td></tr>

                {{-- Action Required --}}
                <tr><td style="padding:20px 32px 0;">
                    <div style="background:#fef2f2;border-left:4px solid #dc2626;padding:14px 16px;border-radius:6px;font-size:13.5px;color:#991b1b;line-height:1.6;">
                        <strong>Action required:</strong> If this booking was paid, please review the payment record and process the refund / settlement manually via the payment gateway.
                    </div>
                </td></tr>

                {{-- Booking Info --}}
                <tr><td style="padding:20px 32px;">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #e5e7eb;border-radius:8px;">
                        <tr><td style="padding:12px 16px;background:#f9fafb;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Booking Details</td></tr>
                        <tr><td style="padding:14px 16px;font-size:13.5px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="6">
                                <tr><td style="color:#6b7280;width:35%;">Reference</td><td style="font-weight:700;font-family:monospace;">{{ $booking->reference_number }}</td></tr>
                                <tr><td style="color:#6b7280;">Status</td><td><span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:4px;font-size:12px;font-weight:600;">CANCELLED</span></td></tr>
                                <tr><td style="color:#6b7280;">Hotel</td><td style="font-weight:600;">{{ optional($booking->hotel)->name ?? '—' }}</td></tr>
                                <tr><td style="color:#6b7280;">Room Type</td><td style="font-weight:600;">{{ optional($booking->roomType)->name ?? '—' }}</td></tr>
                                <tr><td style="color:#6b7280;">Check-in / Check-out</td><td style="font-weight:600;">{{ $booking->check_in_date?->format('M d, Y') }} → {{ $booking->check_out_date?->format('M d, Y') }} ({{ $booking->num_nights }} nights)</td></tr>
                                <tr><td style="color:#6b7280;">Total Amount</td><td style="font-weight:700;color:#0f172a;">{{ $booking->currency }} {{ number_format((float) $booking->total_amount, 2) }}</td></tr>
                                <tr><td style="color:#6b7280;">Cancelled At</td><td style="font-weight:600;">{{ $booking->cancelled_at?->format('M d, Y h:i A') }}</td></tr>
                                @if($booking->cancellation_reason)
                                <tr><td style="color:#6b7280;vertical-align:top;">Reason</td><td>{{ $booking->cancellation_reason }}</td></tr>
                                @endif
                            </table>
                        </td></tr>
                    </table>
                </td></tr>

                {{-- Customer Info --}}
                <tr><td style="padding:0 32px 20px;">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #e5e7eb;border-radius:8px;">
                        <tr><td style="padding:12px 16px;background:#f9fafb;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Customer</td></tr>
                        <tr><td style="padding:14px 16px;font-size:13.5px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="6">
                                <tr><td style="color:#6b7280;width:35%;">Name</td><td style="font-weight:600;">{{ $booking->guest_first_name }} {{ $booking->guest_last_name }}</td></tr>
                                <tr><td style="color:#6b7280;">Email</td><td>{{ $booking->guest_email }}</td></tr>
                                <tr><td style="color:#6b7280;">Phone</td><td>{{ $booking->guest_phone }}</td></tr>
                                @if($booking->guest_nationality)
                                <tr><td style="color:#6b7280;">Nationality</td><td>{{ $booking->guest_nationality }}</td></tr>
                                @endif
                            </table>
                        </td></tr>
                    </table>
                </td></tr>

                {{-- Payment Info --}}
                @if($booking->payments && $booking->payments->isNotEmpty())
                <tr><td style="padding:0 32px 24px;">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #e5e7eb;border-radius:8px;">
                        <tr><td style="padding:12px 16px;background:#f9fafb;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Payments on this booking</td></tr>
                        <tr><td style="padding:14px 16px;font-size:13px;">
                            @foreach($booking->payments as $p)
                                @php
                                    $gatewayLabel = $p->gateway instanceof \BackedEnum ? $p->gateway->value : (string) $p->gateway;
                                    $statusLabel = $p->status instanceof \BackedEnum ? $p->status->value : (string) $p->status;
                                @endphp
                                <div style="padding:8px 0;border-bottom:1px dashed #e5e7eb;">
                                    <strong>{{ strtoupper($gatewayLabel) }}</strong> — {{ $p->currency }} {{ number_format((float) $p->amount, 2) }}
                                    — Status: <code>{{ $statusLabel }}</code>
                                    @if($p->transaction_id)<br><span style="color:#6b7280;font-size:12px;">Txn: {{ $p->transaction_id }}</span>@endif
                                </div>
                            @endforeach
                        </td></tr>
                    </table>
                </td></tr>
                @endif

                {{-- Footer --}}
                <tr><td style="background:#f9fafb;padding:14px 32px;border-top:1px solid #e5e7eb;">
                    <p style="margin:0;font-size:11.5px;color:#9ca3af;">Sent automatically by {{ $domain->name }} system.</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
