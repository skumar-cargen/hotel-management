<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomType>
 */
class RoomTypeFactory extends Factory
{
    protected $model = RoomType::class;

    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'name' => fake()->randomElement(['Standard', 'Deluxe', 'Suite', 'Premium']) . ' Room',
            'slug' => fake()->unique()->slug(3),
            'description' => fake()->paragraph(),
            'base_price' => fake()->randomFloat(2, 100, 2000),
            'max_guests' => fake()->numberBetween(1, 6),
            'max_adults' => fake()->numberBetween(1, 4),
            'max_children' => fake()->numberBetween(0, 3),
            'total_rooms' => fake()->numberBetween(1, 50),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
