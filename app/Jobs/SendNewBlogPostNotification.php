<?php

namespace App\Jobs;

use App\Mail\NewBlogPostMail;
use App\Models\BlogPost;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewBlogPostNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 120;

    public function __construct(public BlogPost $blogPost)
    {
        $this->onQueue('newsletters');
    }

    public function handle(): void
    {
        $this->blogPost->loadMissing(['category', 'domains']);

        $totalSent = 0;

        foreach ($this->blogPost->domains as $domain) {
            if (! $domain->email || ! $domain->is_active) {
                continue;
            }

            NewsletterSubscriber::where('domain_id', $domain->id)
                ->where('is_active', true)
                ->chunkById(50, function ($subscribers) use ($domain, &$totalSent) {
                    foreach ($subscribers as $subscriber) {
                        Mail::to($subscriber->email)
                            ->send(new NewBlogPostMail($this->blogPost, $domain));
                        $totalSent++;
                    }
                });
        }

        Log::info('Blog post newsletter sent', [
            'post_id' => $this->blogPost->id,
            'title' => $this->blogPost->title,
            'total_sent' => $totalSent,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send blog post newsletter', [
            'post_id' => $this->blogPost->id,
            'title' => $this->blogPost->title,
            'error' => $exception->getMessage(),
        ]);
    }
}
