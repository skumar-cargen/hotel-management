<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentGateway;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MashreqPaymentService
{
    private const DEFAULT_CURRENCY = 'AED';

    protected string $merchantId;

    protected string $apiKey;

    protected string $apiSecret;

    protected string $baseUrl;

    protected bool $sandbox;

    public function __construct()
    {
        $this->merchantId = config('mashreq.merchant_id', '');
        $this->apiKey = config('mashreq.api_key', '');
        $this->apiSecret = config('mashreq.api_secret', '');
        $this->sandbox = config('mashreq.sandbox', true);
        $this->baseUrl = $this->sandbox
            ? config('mashreq.sandbox_url', 'https://test-gateway.mashreqbank.com/api/v1')
            : config('mashreq.production_url', 'https://gateway.mashreqbank.com/api/v1');
    }

    /**
     * Initiate a payment session and return the redirect URL.
     */
    public function initiatePayment(Booking $booking): array
    {
        $orderId = $booking->reference_number.'-'.Str::random(6);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'transaction_id' => $orderId,
            'payment_method' => PaymentMethod::Card,
            'gateway' => PaymentGateway::Mashreq,
            'amount' => $booking->total_amount,
            'currency' => self::DEFAULT_CURRENCY,
            'status' => PaymentStatus::Initiated,
        ]);

        if ($this->sandbox) {
            // In sandbox mode, return a simulated payment URL
            return [
                'success' => true,
                'payment_id' => $payment->id,
                'redirect_url' => route('api.payment.callback', [
                    'order_id' => $orderId,
                    'status' => 'success',
                    'sandbox' => 1,
                ]),
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.'/payments/initiate', [
                'merchant_id' => $this->merchantId,
                'order_id' => $orderId,
                'amount' => number_format($booking->total_amount, 2, '.', ''),
                'currency' => self::DEFAULT_CURRENCY,
                'description' => "Booking {$booking->reference_number}",
                'return_url' => route('api.payment.callback'),
                'cancel_url' => config('app.frontend_url').'/booking/'.$booking->reference_number,
                'customer' => [
                    'name' => $booking->guest_full_name,
                    'email' => $booking->guest_email,
                    'phone' => $booking->guest_phone,
                ],
            ]);

            $data = $response->json();

            $payment->update([
                'status' => PaymentStatus::Processing,
                'gateway_response' => $data,
            ]);

            if ($response->successful() && isset($data['redirect_url'])) {
                return [
                    'success' => true,
                    'payment_id' => $payment->id,
                    'redirect_url' => $data['redirect_url'],
                ];
            }

            $payment->update([
                'status' => PaymentStatus::Failed,
                'failure_reason' => $data['message'] ?? 'Gateway error',
                'failed_at' => now(),
            ]);

            return [
                'success' => false,
                'error' => $data['message'] ?? 'Payment initiation failed',
            ];
        } catch (\Throwable $e) {
            Log::error('Mashreq payment initiation failed', [
                'booking' => $booking->reference_number,
                'error' => $e->getMessage(),
            ]);

            $payment->update([
                'status' => PaymentStatus::Failed,
                'failure_reason' => $e->getMessage(),
                'failed_at' => now(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment service unavailable. Please try again.',
            ];
        }
    }

    /**
     * Handle payment callback from gateway.
     */
    public function handleCallback(array $callbackData): array
    {
        $orderId = $callbackData['order_id'] ?? null;
        $status = $callbackData['status'] ?? 'failed';

        $payment = Payment::where('transaction_id', $orderId)->first();

        if (! $payment) {
            Log::warning('Payment callback for unknown order', [
                'order_id' => $callbackData['order_id'] ?? 'unknown',
                'status' => $callbackData['status'] ?? 'unknown',
            ]);

            return ['success' => false, 'error' => 'Payment not found'];
        }

        $booking = $payment->booking;

        // Sandbox mode simulation
        if ($this->sandbox && ($callbackData['sandbox'] ?? false)) {
            $payment->update([
                'status' => PaymentStatus::Completed,
                'paid_at' => now(),
                'gateway_response' => array_merge(
                    $payment->gateway_response ?? [],
                    ['callback' => $callbackData, 'sandbox' => true]
                ),
            ]);

            $booking->update([
                'status' => BookingStatus::Paid,
                'confirmed_at' => now(),
            ]);

            return [
                'success' => true,
                'booking' => $booking,
                'payment' => $payment,
            ];
        }

        // Verify transaction with gateway
        $verified = $this->verifyTransaction($orderId);

        if ($verified && $status === 'success') {
            $payment->update([
                'status' => PaymentStatus::Completed,
                'paid_at' => now(),
                'gateway_response' => array_merge(
                    $payment->gateway_response ?? [],
                    ['callback' => $callbackData]
                ),
            ]);

            $booking->update([
                'status' => BookingStatus::Paid,
                'confirmed_at' => now(),
            ]);

            return [
                'success' => true,
                'booking' => $booking,
                'payment' => $payment,
            ];
        }

        $payment->update([
            'status' => PaymentStatus::Failed,
            'failed_at' => now(),
            'failure_reason' => $callbackData['message'] ?? 'Payment verification failed',
            'gateway_response' => array_merge(
                $payment->gateway_response ?? [],
                ['callback' => $callbackData]
            ),
        ]);

        return [
            'success' => false,
            'booking' => $booking,
            'error' => 'Payment was not successful',
        ];
    }

    /**
     * Verify a transaction with the gateway.
     */
    public function verifyTransaction(string $orderId): bool
    {
        if ($this->sandbox) {
            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
            ])->get($this->baseUrl.'/payments/verify/'.$orderId);

            $data = $response->json();

            return $response->successful() && ($data['status'] ?? '') === 'completed';
        } catch (\Throwable $e) {
            Log::error('Payment verification failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Process a refund.
     */
    public function processRefund(Payment $payment, ?float $amount = null): array
    {
        $refundAmount = $amount ?? $payment->amount;

        if ($this->sandbox) {
            $refundTxnId = 'REF-'.Str::random(10);
            $payment->update([
                'status' => $amount && $amount < $payment->amount ? 'partially_refunded' : PaymentStatus::Refunded,
                'refund_amount' => $refundAmount,
                'refund_transaction_id' => $refundTxnId,
                'refunded_at' => now(),
            ]);

            $payment->booking->update(['status' => BookingStatus::Refunded]);

            return ['success' => true, 'refund_transaction_id' => $refundTxnId];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.'/payments/refund', [
                'merchant_id' => $this->merchantId,
                'transaction_id' => $payment->transaction_id,
                'amount' => number_format($refundAmount, 2, '.', ''),
                'currency' => self::DEFAULT_CURRENCY,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['refund_id'])) {
                $payment->update([
                    'status' => $amount && $amount < $payment->amount ? 'partially_refunded' : PaymentStatus::Refunded,
                    'refund_amount' => $refundAmount,
                    'refund_transaction_id' => $data['refund_id'],
                    'refunded_at' => now(),
                    'gateway_response' => array_merge($payment->gateway_response ?? [], ['refund' => $data]),
                ]);

                $payment->booking->update(['status' => BookingStatus::Refunded]);

                return ['success' => true, 'refund_transaction_id' => $data['refund_id']];
            }

            return ['success' => false, 'error' => $data['message'] ?? 'Refund failed'];
        } catch (\Throwable $e) {
            Log::error('Refund processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => 'Refund service unavailable'];
        }
    }
}
