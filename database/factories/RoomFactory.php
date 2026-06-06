<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word().' Room',
            'type' => fake()->randomElement(['Standard Double Room', 'Deluxe Double Room', 'Budget Double Room', 'Superior King Room']),
            'price' => fake()->randomElement([650000, 910000, 1040000, 1200000]),
            'capacity' => rand(2, 4),
            'description' => fake()->sentence(),
            'status' => 'tersedia',
            'image' => null,
            'allow_breakfast' => true,
            'allow_extra_bed' => true,
            'allow_late_checkout' => true,
        ];
    }
}
