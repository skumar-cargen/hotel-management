<?php

namespace App\Mail;

use App\Models\BlogPost;
use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBlogPostMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BlogPost $blogPost,
        public Domain $domain,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                $this->domain->email ?? config('mail.from.address'),
                $this->domain->name ?? config('mail.from.name'),
            ),
            subject: 'New Article: ' . $this->blogPost->title . ' - ' . $this->domain->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.new-blog-post',
            with: [
                'blogPost' => $this->blogPost->load('category'),
                'domain' => $this->domain,
            ],
        );
    }
}
