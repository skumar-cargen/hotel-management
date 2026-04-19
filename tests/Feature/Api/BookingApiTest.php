<?php

namespace Tests\Feature\Api;

use App\Models\Domain;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    protected Domain $domain;

    protected Hotel $hotel;

    protected RoomType $roomType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->domain = Domain::factory()->create();
        $location = Location::factory()->create();
        $this->hotel = Hotel::factory()->create([
            'location_id' => $location->id,
            'star_rating' => 4,
        ]);
        $this->roomType = RoomType::factory()->create([
            'hotel_id' => $this->hotel->id,
            'base_price' => 500.00,
        ]);

        // Attach hotel to domain
        $this->hotel->domains()->attach($this->domain->id, [
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_create_booking_requires_valid_data(): void
    {
        $response = $this->withHeaders([
            'X-Domain' => $this->domain->slug,
            'Accept' => 'application/json',
        ])->postJson('/api/v1/bookings', []);

        $response->assertStatus(422);
    }

    public function test_create_booking_validates_required_fields(): void
    {
        $response = $this->withHeaders([
            'X-Domain' => $this->domain->slug,
            'Accept' => 'application/json',
        ])->postJson('/api/v1/bookings', [
            'hotel_id' => $this->hotel->id,
            // Missing other required fields
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'room_type_id',
            'check_in',
            'check_out',
            'num_rooms',
            'num_adults',
            'guest_first_name',
            'guest_last_name',
            'guest_email',
            'guest_phone',
        ]);
    }

    public function test_create_booking_with_valid_data(): void
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(3)->format('Y-m-d');

        $response = $this->withHeaders([
            'X-Domain' => $this->domain->slug,
            'Accept' => 'application/json',
        ])->postJson('/api/v1/bookings', [
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $this->roomType->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'num_rooms' => 1,
            'num_adults' => 2,
            'guest_first_name' => 'John',
            'guest_last_name' => 'Doe',
            'guest_email' => 'john.doe@example.com',
            'guest_phone' => '+971501234567',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'data',
        ]);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('bookings', [
            'domain_id' => $this->domain->id,
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $this->roomType->id,
            'guest_email' => 'john.doe@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_create_booking_requires_domain_header(): void
    {
        $response = $this->postJson('/api/v1/bookings', [
            'hotel_id' => $this->hotel->id,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'X-Domain header is required.',
        ]);
    }

    public function test_create_booking_rejects_invalid_domain(): void
    {
        $response = $this->withHeaders([
            'X-Domain' => 'nonexistent-domain',
            'Accept' => 'application/json',
        ])->postJson('/api/v1/bookings', [
            'hotel_id' => $this->hotel->id,
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Domain not found or inactive.',
        ]);
    }
}
