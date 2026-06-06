<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('now', '+1 month');
        $nights = rand(1, 4);
        $checkOut = clone $checkIn;
        $checkOut->modify('+'.$nights.' days');
        $subtotal = 1000000 * $nights;
        $tax = $subtotal * 0.1;
        $totalPrice = $subtotal + $tax;

        return [
            'invoice_no' => 'BGH-'.fake()->dateTime()->format('Ymd').'-'.fake()->unique()->numberBetween(1000, 9999),
            'user_id' => User::factory(),
            'room_id' => Room::factory(),
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'guest_phone' => fake()->phoneNumber(),
            'guest_country' => fake()->country(),
            'special_requests' => fake()->optional()->sentence(),
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'nights' => $nights,
            'adults' => rand(1, 2),
            'children' => rand(0, 1),
            'subtotal' => $subtotal,
            'discount' => 0,
            'tax' => $tax,
            'total_price' => $totalPrice,
            'payment_method' => 'Transfer Bank',
            'payment_proof' => null,
            'status' => fake()->randomElement(['pending', 'confirmed', 'rejected']),
        ];
    }
}
