<?php

namespace App\Observers;

use App\Jobs\SendBookingConfirmationEmail;
use App\Models\Booking;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        // Only send confirmation if we have a guest email
        if ($booking->guest_email) {
            SendBookingConfirmationEmail::dispatch($booking);
        }
    }
}
