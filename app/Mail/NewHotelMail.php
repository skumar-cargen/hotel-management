<?php

namespace App\Mail;

use App\Models\Domain;
use App\Models\Hotel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewHotelMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Hotel $hotel,
        public Domain $domain,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                $this->domain->email ?? config('mail.from.address'),
                $this->domain->name ?? config('mail.from.name'),
            ),
            subject: 'New Property: ' . $this->hotel->name . ' - ' . $this->domain->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.new-hotel',
            with: [
                'hotel' => $this->hotel->load(['location', 'amenities']),
                'domain' => $this->domain,
            ],
        );
    }
}
