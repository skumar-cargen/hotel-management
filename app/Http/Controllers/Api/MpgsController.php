<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpgsController extends Controller
{
    use ApiResponses;

    protected string $baseUrl;

    public function __construct()
    {
        $gateway = config('mpgs.gateway_url');
        $version = config('mpgs.api_version');
        $merchant = config('mpgs.merchant_id');

        $this->baseUrl = "{$gateway}/api/rest/version/{$version}/merchant/{$merchant}";
    }

    /**
     * Create an MPGS checkout session.
     *
     * POST /api/v1/payments/create-session
     */
    public function createSession(Request $request): JsonResponse
    {
        $request->validate([
            'booking_reference' => 'required|string',
        ]);

        $domain = $this->domain();

        $booking = Booking::where('reference_number', $request->booking_reference)
            ->where('domain_id', $domain->id)
            ->first();

        if (! $booking) {
            return $this->errorResponse('Booking not found.', 404);
        }

        if ($booking->status !== 'pending') {
            return $this->errorResponse('Booking is not in a payable state.', 422);
        }

        // Build frontend URLs using the domain's URL
        $frontendUrl = rtrim($domain->domain, '/');
        if (! str_starts_with($frontendUrl, 'http')) {
            $frontendUrl = "https://{$frontendUrl}";
        }

        $currency = $booking->currency ?: ($domain->default_currency ?: 'AED');

        $payload = [
            'apiOperation' => 'INITIATE_CHECKOUT',
            'interaction' => [
                'operation' => 'PURCHASE',
                'returnUrl' => "{$frontendUrl}/booking/{$booking->reference_number}",
                'cancelUrl' => "{$frontendUrl}/checkout",
                'merchant' => [
                    'name' => $domain->name,
                ],
            ],
            'order' => [
                'id' => $booking->reference_number,
                'amount' => number_format((float) $booking->total_amount, 2, '.', ''),
                'currency' => $currency,
                'description' => "Hotel Booking {$booking->reference_number}",
            ],
        ];

        Log::info('MPGS Create Session Request', [
            'booking' => $booking->reference_number,
            'url' => "{$this->baseUrl}/session",
            'payload' => $payload,
        ]);

        try {
            $response = Http::withBasicAuth(
                config('mpgs.api_username'),
                config('mpgs.api_password')
            )->post("{$this->baseUrl}/session", $payload);

            $body = $response->json();

            Log::info('MPGS Create Session Response', [
                'booking' => $booking->reference_number,
                'status' => $response->status(),
                'body' => $body,
            ]);

            if (! $response->successful()) {
                return $this->errorResponse(
                    $body['error']['explanation'] ?? 'Failed to create payment session.',
                    422
                );
            }

            $sessionId = $body['session']['id'] ?? null;
            $successIndicator = $body['successIndicator'] ?? null;

            if (! $sessionId) {
                return $this->errorResponse('Invalid MPGS response: missing session ID.', 422);
            }

            // Create a payment record with initiated status
            Payment::create([
                'booking_id' => $booking->id,
                'transaction_id' => $sessionId,
                'gateway' => 'mpgs',
                'amount' => $booking->total_amount,
                'currency' => $currency,
                'status' => 'initiated',
                'gateway_response' => $body,
            ]);

            return $this->successResponse([
                'session_id' => $sessionId,
                'success_indicator' => $successIndicator,
                'order_id' => $booking->reference_number,
                'amount' => (float) $booking->total_amount,
                'currency' => $currency,
            ]);

        } catch (\Exception $e) {
            Log::error('MPGS Create Session Error', [
                'booking' => $booking->reference_number,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Payment gateway error. Please try again.', 500);
        }
    }

    /**
     * Verify payment after checkout completes.
     *
     * POST /api/v1/payments/verify
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'booking_reference' => 'required|string',
            'result_indicator' => 'required|string',
        ]);

        $domain = $this->domain();

        $booking = Booking::where('reference_number', $request->booking_reference)
            ->where('domain_id', $domain->id)
            ->first();

        if (! $booking) {
            return $this->errorResponse('Booking not found.', 404);
        }

        Log::info('MPGS Verify Request', [
            'booking' => $booking->reference_number,
            'result_indicator' => $request->result_indicator,
        ]);

        try {
            $response = Http::withBasicAuth(
                config('mpgs.api_username'),
                config('mpgs.api_password')
            )->get("{$this->baseUrl}/order/{$booking->reference_number}");

            $body = $response->json();

            Log::info('MPGS Verify Response', [
                'booking' => $booking->reference_number,
                'status' => $response->status(),
                'body' => $body,
            ]);

            if (! $response->successful()) {
                return $this->errorResponse(
                    $body['error']['explanation'] ?? 'Failed to verify payment.',
                    422
                );
            }

            $orderStatus = $body['result'] ?? null;
            $transactionId = $this->extractTransactionId($body);

            // Find the initiated payment record
            $payment = Payment::where('booking_id', $booking->id)
                ->where('gateway', 'mpgs')
                ->latest()
                ->first();

            if ($orderStatus === 'SUCCESS') {
                // Update booking status
                $booking->update([
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                ]);

                // Update payment record
                if ($payment) {
                    $payment->update([
                        'transaction_id' => $transactionId ?: $payment->transaction_id,
                        'status' => 'completed',
                        'paid_at' => now(),
                        'payment_method' => $body['sourceOfFunds']['provided']['card']['brand'] ?? 'card',
                        'gateway_response' => $body,
                    ]);
                }

                return $this->successResponse([
                    'status' => 'paid',
                    'booking_reference' => $booking->reference_number,
                    'transaction_id' => $transactionId,
                ]);
            }

            // Payment failed
            if ($payment) {
                $payment->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'failure_reason' => $body['result'] ?? 'Payment not successful',
                    'gateway_response' => $body,
                ]);
            }

            return $this->successResponse([
                'status' => 'failed',
                'booking_reference' => $booking->reference_number,
                'transaction_id' => $transactionId,
            ]);

        } catch (\Exception $e) {
            Log::error('MPGS Verify Error', [
                'booking' => $booking->reference_number,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Payment verification failed. Please try again.', 500);
        }
    }

    /**
     * Extract transaction ID from MPGS order response.
     */
    private function extractTransactionId(array $body): ?string
    {
        // MPGS nests transactions under the order response
        $transactions = $body['transaction'] ?? [];

        if (is_array($transactions)) {
            foreach ($transactions as $txn) {
                if (isset($txn['transaction']['id'])) {
                    return $txn['transaction']['id'];
                }
            }
        }

        return null;
    }
}
