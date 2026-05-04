<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBookingNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        $domain = $this->booking->domain;
        $domainLabel = $domain?->name ? " ({$domain->name})" : '';

        return new Envelope(
            subject: "New Booking{$domainLabel}: {$this->booking->reference_number}",
        );
    }

    public function content(): Content
    {
        $booking = $this->booking->loadMissing(['hotel', 'roomType', 'domain']);

        return new Content(
            view: 'emails.new-booking-notification',
            with: [
                'booking' => $booking,
                'domain' => $booking->domain,
                'domainName' => $booking->domain?->name ?? config('app.name'),
                'domainAddress' => $booking->domain?->address,
                'domainPhone' => $booking->domain?->phone,
                'domainEmail' => $booking->domain?->email,
            ],
        );
    }
}
