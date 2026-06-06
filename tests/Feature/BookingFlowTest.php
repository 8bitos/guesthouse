<?php

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

test('authenticated pelanggan can submit a booking request with payment proof', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => 'pelanggan',
    ]);

    $room = Room::factory()->create([
        'status' => 'tersedia',
        'price' => 1000000,
    ]);

    $file = UploadedFile::fake()->image('payment_receipt.jpg');

    $response = $this->actingAs($user)->post('/booking', [
        'room_id' => $room->id,
        'guest_name' => 'Bob Builder',
        'guest_email' => 'bob@example.com',
        'guest_phone' => '081234567890',
        'guest_country' => 'Indonesia',
        'special_requests' => 'High floor please',
        'check_in' => now()->format('Y-m-d'),
        'check_out' => now()->addDay()->format('Y-m-d'),
        'nights' => 1,
        'adults' => 2,
        'children' => 0,
        'subtotal' => 1000000,
        'discount' => 0,
        'tax' => 100000,
        'total_price' => 1100000,
        'payment_proof' => $file,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    $this->assertDatabaseHas('bookings', [
        'guest_name' => 'Bob Builder',
        'total_price' => 1100000,
        'status' => 'pending',
    ]);

    $booking = Booking::first();
    Storage::disk('public')->assertExists($booking->payment_proof);
});

test('admin can approve a pending booking', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $booking = Booking::factory()->create([
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/approve");

    $response->assertRedirect();
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'confirmed',
    ]);
});

test('admin can reject a pending booking', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $booking = Booking::factory()->create([
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/reject");

    $response->assertRedirect();
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'rejected',
    ]);
});
