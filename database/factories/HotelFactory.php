<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hotel>
 */
class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Hotel',
            'slug' => fake()->unique()->slug(3),
            'location_id' => Location::factory(),
            'star_rating' => fake()->numberBetween(1, 5),
            'description' => fake()->paragraph(),
            'short_description' => fake()->sentence(),
            'address' => fake()->address(),
            'is_active' => true,
            'is_featured' => false,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
