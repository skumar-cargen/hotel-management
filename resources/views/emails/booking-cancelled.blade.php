<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Cancelled</title>
</head>
<body style="margin:0;padding:0;background:#f4f5f8;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f5f8;padding:32px 16px;">
        <tr><td align="center">
            <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
                {{-- Header --}}
                <tr><td style="background:#dc2626;color:#ffffff;padding:28px 32px;">
                    <h1 style="margin:0;font-size:22px;font-weight:700;">Booking Cancelled</h1>
                    <p style="margin:6px 0 0;font-size:13px;opacity:0.9;">Reference: <strong>{{ $booking->reference_number }}</strong></p>
                </td></tr>

                {{-- Body --}}
                <tr><td style="padding:28px 32px;">
                    <p style="margin:0 0 14px;font-size:15px;">Dear {{ $booking->guest_first_name }},</p>
                    <p style="margin:0 0 14px;font-size:14px;line-height:1.6;">
                        Your booking with <strong>{{ $domainName }}</strong> has been successfully cancelled. Below are the details for your reference.
                    </p>

                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:18px 0;border:1px solid #e5e7eb;border-radius:8px;">
                        <tr><td style="padding:12px 16px;background:#f9fafb;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Booking Summary</td></tr>
                        <tr><td style="padding:14px 16px;font-size:14px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="6" style="font-size:13.5px;">
                                <tr><td style="color:#6b7280;width:40%;">Hotel</td><td style="font-weight:600;">{{ optional($booking->hotel)->name ?? '—' }}</td></tr>
                                <tr><td style="color:#6b7280;">Room Type</td><td style="font-weight:600;">{{ optional($booking->roomType)->name ?? '—' }}</td></tr>
                                <tr><td style="color:#6b7280;">Check-in</td><td style="font-weight:600;">{{ $booking->check_in_date?->format('M d, Y') }}</td></tr>
                                <tr><td style="color:#6b7280;">Check-out</td><td style="font-weight:600;">{{ $booking->check_out_date?->format('M d, Y') }}</td></tr>
                                <tr><td style="color:#6b7280;">Nights</td><td style="font-weight:600;">{{ $booking->num_nights }}</td></tr>
                                <tr><td style="color:#6b7280;">Total Paid/Due</td><td style="font-weight:600;">{{ $booking->currency }} {{ number_format((float) $booking->total_amount, 2) }}</td></tr>
                                <tr><td style="color:#6b7280;">Cancelled At</td><td style="font-weight:600;">{{ $booking->cancelled_at?->format('M d, Y h:i A') }}</td></tr>
                                @if($booking->cancellation_reason)
                                <tr><td style="color:#6b7280;vertical-align:top;">Reason</td><td>{{ $booking->cancellation_reason }}</td></tr>
                                @endif
                            </table>
                        </td></tr>
                    </table>

                    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:14px 16px;margin:18px 0;font-size:13.5px;line-height:1.6;color:#92400e;">
                        <strong>Refund:</strong> If a payment was made for this booking, our team will review and process any applicable refund manually. You will be contacted directly regarding the settlement.
                    </div>

                    <p style="margin:16px 0 0;font-size:13.5px;line-height:1.6;color:#374151;">
                        For any questions, please contact us
                        @if($domainPhone) at <strong>{{ $domainPhone }}</strong>@endif
                        @if($domainEmail) or write to <a href="mailto:{{ $domainEmail }}" style="color:#2563eb;text-decoration:none;">{{ $domainEmail }}</a>@endif.
                    </p>

                    <p style="margin:18px 0 0;font-size:14px;">Warm regards,<br><strong>{{ $domainName }}</strong></p>
                </td></tr>

                {{-- Footer --}}
                <tr><td style="background:#f9fafb;padding:16px 32px;border-top:1px solid #e5e7eb;">
                    <p style="margin:0;font-size:11.5px;color:#9ca3af;line-height:1.5;">
                        This is an automated notification. Please do not reply directly to this email.
                        @if($domainAddress)<br>{{ $domainAddress }}@endif
                    </p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
