<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'user@guesthouse.com')->first();
        $rooms = Room::all();

        if ($user && $rooms->isNotEmpty()) {
            // Seeding a confirmed booking
            Booking::create([
                'invoice_no' => 'BGH-202605-1001',
                'user_id' => $user->id,
                'room_id' => $rooms->first()->id, // Family Suite 1
                'guest_name' => $user->name,
                'guest_email' => $user->email,
                'guest_phone' => $user->phone ?? '081234567890',
                'guest_country' => 'Indonesia',
                'special_requests' => 'Quiet room please',
                'check_in' => '2026-06-12',
                'check_out' => '2026-06-15',
                'nights' => 3,
                'guests' => 2,
                'subtotal' => 2730000,
                'discount' => 0,
                'tax' => 273000,
                'total_price' => 3003000,
                'payment_method' => 'Transfer Bank',
                'payment_proof' => 'proofs/dummy-proof.jpg',
                'status' => 'confirmed',
            ]);

            // Seeding a pending booking
            Booking::create([
                'invoice_no' => 'BGH-202605-1002',
                'user_id' => $user->id,
                'room_id' => $rooms->skip(2)->first()->id, // Suite 3
                'guest_name' => $user->name,
                'guest_email' => $user->email,
                'guest_phone' => $user->phone ?? '081234567890',
                'guest_country' => 'Indonesia',
                'special_requests' => null,
                'check_in' => '2026-06-18',
                'check_out' => '2026-06-19',
                'nights' => 1,
                'guests' => 3,
                'subtotal' => 1200000,
                'discount' => 120000, // 10% off
                'tax' => 108000,
                'total_price' => 1188000,
                'payment_method' => 'Transfer Bank',
                'payment_proof' => 'proofs/dummy-proof.jpg',
                'status' => 'pending',
            ]);
        }
    }
}
