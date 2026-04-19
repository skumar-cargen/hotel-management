<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Domain;
use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('+1 day', '+30 days');
        $checkOut = fake()->dateTimeBetween($checkIn, '+35 days');
        $numNights = max(1, (int) (new \DateTime($checkOut->format('Y-m-d')))->diff(new \DateTime($checkIn->format('Y-m-d')))->days);
        $pricePerNight = fake()->randomFloat(2, 200, 1500);
        $subtotal = round($pricePerNight * $numNights, 2);
        $taxAmount = round($subtotal * 0.05, 2);
        $tourismFee = round(10 * $numNights, 2);
        $totalAmount = round($subtotal + $taxAmount + $tourismFee, 2);

        return [
            'reference_number' => 'BK-' . strtoupper(Str::random(8)),
            'domain_id' => Domain::factory(),
            'hotel_id' => Hotel::factory(),
            'room_type_id' => RoomType::factory(),
            'guest_first_name' => fake()->firstName(),
            'guest_last_name' => fake()->lastName(),
            'guest_email' => fake()->safeEmail(),
            'guest_phone' => fake()->phoneNumber(),
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'num_nights' => $numNights,
            'num_adults' => fake()->numberBetween(1, 4),
            'num_children' => 0,
            'num_rooms' => 1,
            'room_price_per_night' => $pricePerNight,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'tax_percentage' => 5.00,
            'tourism_fee' => $tourismFee,
            'total_amount' => $totalAmount,
            'currency' => 'AED',
            'status' => 'pending',
            'booked_at' => now(),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Changed plans',
        ]);
    }
}
