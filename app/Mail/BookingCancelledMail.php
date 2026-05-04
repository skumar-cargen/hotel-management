<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public Domain $domain,
    ) {}

    public function envelope(): Envelope
    {
        $fromEmail = $this->domain->smtp_username ?: $this->domain->email;
        $fromName = $this->domain->name;

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: "Your booking has been cancelled — {$this->booking->reference_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-cancelled',
            with: [
                'booking' => $this->booking,
                'domain' => $this->domain,
                'domainName' => $this->domain->name,
                'domainAddress' => $this->domain->address,
                'domainPhone' => $this->domain->phone,
                'domainEmail' => $this->domain->email,
            ],
        );
    }
}
