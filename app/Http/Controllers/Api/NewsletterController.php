<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    use ApiResponses;

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $domain = $this->domain();

        $subscriber = NewsletterSubscriber::where('domain_id', $domain->id)
            ->where('email', $request->email)
            ->first();

        if ($subscriber) {
            if ($subscriber->is_active) {
                return $this->successResponse([
                    'message' => 'You are already subscribed to our newsletter.',
                ]);
            }

            $subscriber->update([
                'is_active' => true,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'ip_address' => $request->ip(),
            ]);

            return $this->successResponse([
                'message' => 'Welcome back! You have been re-subscribed to our newsletter.',
            ]);
        }

        NewsletterSubscriber::create([
            'domain_id' => $domain->id,
            'email' => $request->email,
            'ip_address' => $request->ip(),
        ]);

        return $this->successResponse([
            'message' => 'You have been subscribed to our newsletter successfully.',
        ], 201);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $domain = $this->domain();

        $subscriber = NewsletterSubscriber::where('domain_id', $domain->id)
            ->where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (! $subscriber) {
            return $this->errorResponse('Subscription not found.', 404);
        }

        $subscriber->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);

        return $this->successResponse([
            'message' => 'You have been unsubscribed from our newsletter.',
        ]);
    }
}
