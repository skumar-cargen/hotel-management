<?php

namespace App\Mail;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        $domain = $this->booking->domain;

        return new Envelope(
            from: new Address(
                $domain?->email ?? config('mail.from.address'),
                $domain?->name ?? config('mail.from.name'),
            ),
            subject: 'Booking Confirmation - ' . $this->booking->reference_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'booking' => $this->booking->load(['hotel', 'roomType', 'domain']),
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.booking-confirmation', [
            'booking' => $this->booking->load(['hotel', 'roomType', 'domain']),
        ]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'booking-' . $this->booking->reference_number . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
