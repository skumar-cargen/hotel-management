<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            // General
            ['name' => 'Free WiFi', 'icon' => 'bx-wifi', 'category' => 'General'],
            ['name' => 'Air Conditioning', 'icon' => 'bx-wind', 'category' => 'General'],
            ['name' => 'Elevator', 'icon' => 'bx-up-arrow-alt', 'category' => 'General'],
            ['name' => '24/7 Reception', 'icon' => 'bx-time', 'category' => 'General'],
            ['name' => 'Concierge', 'icon' => 'bx-user-voice', 'category' => 'General'],
            ['name' => 'Luggage Storage', 'icon' => 'bx-briefcase', 'category' => 'General'],

            // Recreation
            ['name' => 'Swimming Pool', 'icon' => 'bx-swim', 'category' => 'Recreation'],
            ['name' => 'Gym / Fitness Center', 'icon' => 'bx-dumbbell', 'category' => 'Recreation'],
            ['name' => 'Spa & Wellness', 'icon' => 'bx-spa', 'category' => 'Recreation'],
            ['name' => 'Beach Access', 'icon' => 'bx-sun', 'category' => 'Recreation'],
            ['name' => 'Kids Club', 'icon' => 'bx-child', 'category' => 'Recreation'],
            ['name' => 'Garden', 'icon' => 'bx-leaf', 'category' => 'Recreation'],

            // Dining
            ['name' => 'Restaurant', 'icon' => 'bx-restaurant', 'category' => 'Dining'],
            ['name' => 'Room Service', 'icon' => 'bx-food-menu', 'category' => 'Dining'],
            ['name' => 'Bar / Lounge', 'icon' => 'bx-drink', 'category' => 'Dining'],
            ['name' => 'Breakfast Included', 'icon' => 'bx-coffee', 'category' => 'Dining'],

            // Transport
            ['name' => 'Free Parking', 'icon' => 'bx-car', 'category' => 'Transport'],
            ['name' => 'Valet Parking', 'icon' => 'bx-key', 'category' => 'Transport'],
            ['name' => 'Airport Shuttle', 'icon' => 'bx-bus', 'category' => 'Transport'],

            // Business
            ['name' => 'Business Center', 'icon' => 'bx-briefcase-alt-2', 'category' => 'Business'],
            ['name' => 'Meeting Rooms', 'icon' => 'bx-group', 'category' => 'Business'],

            // Room Features
            ['name' => 'Balcony', 'icon' => 'bx-door-open', 'category' => 'Room'],
            ['name' => 'Kitchen / Kitchenette', 'icon' => 'bx-fridge', 'category' => 'Room'],
            ['name' => 'Washing Machine', 'icon' => 'bx-recycle', 'category' => 'Room'],
            ['name' => 'City View', 'icon' => 'bx-buildings', 'category' => 'Room'],
            ['name' => 'Sea View', 'icon' => 'bx-water', 'category' => 'Room'],
            ['name' => 'Safe Box', 'icon' => 'bx-lock', 'category' => 'Room'],
            ['name' => 'Flat Screen TV', 'icon' => 'bx-tv', 'category' => 'Room'],
        ];

        foreach ($amenities as $index => $amenity) {
            Amenity::firstOrCreate(
                ['slug' => Str::slug($amenity['name'])],
                array_merge($amenity, [
                    'is_active' => true,
                    'sort_order' => $index,
                ])
            );
        }
    }
}
