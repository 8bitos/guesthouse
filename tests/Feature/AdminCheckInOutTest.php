<?php

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('admin can check in a guest and update room status to occupied', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $room = Room::factory()->create(['status' => 'tersedia']);

    $booking = Booking::create([
        'user_id' => User::factory()->create(['role' => 'pelanggan'])->id,
        'room_id' => $room->id,
        'invoice_no' => 'INV-CHECKIN-TEST',
        'guest_name' => 'John Checkin',
        'guest_email' => 'john.checkin@example.com',
        'guest_phone' => '08123456789',
        'guest_country' => 'Indonesia',
        'check_in' => '2026-06-10',
        'check_out' => '2026-06-12',
        'nights' => 2,
        'guests' => 2,
        'subtotal' => 1000000,
        'tax' => 100000,
        'total_price' => 1100000,
        'status' => 'confirmed',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.bookings.checkin', $booking));

    $response->assertRedirect();

    // Verify booking status remains confirmed
    expect($booking->fresh()->status)->toBe('confirmed');

    // Verify room status is now dipesan (occupied)
    expect($room->fresh()->status)->toBe('dipesan');
});

test('admin can check out a guest and update room status to vacant', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $room = Room::factory()->create(['status' => 'dipesan']);

    $booking = Booking::create([
        'user_id' => User::factory()->create(['role' => 'pelanggan'])->id,
        'room_id' => $room->id,
        'invoice_no' => 'INV-CHECKOUT-TEST',
        'guest_name' => 'John Checkout',
        'guest_email' => 'john.checkout@example.com',
        'guest_phone' => '08123456789',
        'guest_country' => 'Indonesia',
        'check_in' => '2026-06-10',
        'check_out' => '2026-06-12',
        'nights' => 2,
        'guests' => 2,
        'subtotal' => 1000000,
        'tax' => 100000,
        'total_price' => 1100000,
        'status' => 'confirmed',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.bookings.checkout', $booking));

    $response->assertRedirect();

    // Verify booking status is completed
    expect($booking->fresh()->status)->toBe('completed');

    // Verify room status is now tersedia (vacant)
    expect($room->fresh()->status)->toBe('tersedia');
});

test('non-admin cannot check in or check out a guest', function () {
    $user = User::factory()->create(['role' => 'pelanggan']);
    $room = Room::factory()->create(['status' => 'tersedia']);

    $booking = Booking::create([
        'user_id' => $user->id,
        'room_id' => $room->id,
        'invoice_no' => 'INV-NONADMIN-TEST',
        'guest_name' => 'John NonAdmin',
        'guest_email' => 'john.nonadmin@example.com',
        'guest_phone' => '08123456789',
        'guest_country' => 'Indonesia',
        'check_in' => '2026-06-10',
        'check_out' => '2026-06-12',
        'nights' => 2,
        'guests' => 2,
        'subtotal' => 1000000,
        'tax' => 100000,
        'total_price' => 1100000,
        'status' => 'confirmed',
    ]);

    $responseCheckin = $this->actingAs($user)->post(route('admin.bookings.checkin', $booking));
    $responseCheckin->assertStatus(403);

    // Verify room remains tersedia
    expect($room->fresh()->status)->toBe('tersedia');

    // Set room to dipesan to test checkout block
    $room->update(['status' => 'dipesan']);

    $responseCheckout = $this->actingAs($user)->post(route('admin.bookings.checkout', $booking));
    $responseCheckout->assertStatus(403);

    // Verify room remains dipesan
    expect($room->fresh()->status)->toBe('dipesan');
});
