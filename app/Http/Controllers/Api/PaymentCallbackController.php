<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// use App\Services\MashreqPaymentService; // Temporarily disabled — Mashreq payment
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    use ApiResponses;

    // Temporarily disabled — Mashreq payment
    // public function __construct(
    //     protected MashreqPaymentService $paymentService,
    // ) {}

    // public function handle(Request $request)
    // {
    //     $result = $this->paymentService->handleCallback($request->all());
    //
    //     if (! $result['success']) {
    //         return $this->errorResponse($result['error'] ?? 'Payment failed.', 422);
    //     }
    //
    //     $booking = $result['booking'];
    //
    //     return $this->successResponse([
    //         'reference_number' => $booking->reference_number,
    //         'status' => $booking->status,
    //         'redirect_url' => config('app.frontend_url').'/booking/'.$booking->reference_number.'/confirmation',
    //     ]);
    // }
}
