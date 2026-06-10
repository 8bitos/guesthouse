<?php

use App\Mail\BookingApproved;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
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
        'guests' => 2,
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

test('admin can approve a pending booking and send an email to the guest', function () {
    Mail::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $booking = Booking::factory()->create([
        'status' => 'pending',
        'guest_email' => 'guest@example.com',
    ]);

    $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/approve");

    $response->assertRedirect();
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'confirmed',
    ]);

    Mail::assertSent(BookingApproved::class, function ($mail) use ($booking) {
        return $mail->hasTo($booking->guest_email) &&
               $mail->booking->id === $booking->id;
    });
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

test('authenticated pelanggan can submit a booking request with extras', function () {
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
        'include_breakfast' => true,
        'include_extra_bed' => true,
        'late_checkout' => true,
        'check_in' => now()->format('Y-m-d'),
        'check_out' => now()->addDay()->format('Y-m-d'),
        'nights' => 1,
        'guests' => 2,
        'subtotal' => 1000000,
        'discount' => 0,
        'tax' => 100000,
        'total_price' => 1100000,
        'payment_proof' => $file,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'booking' => [
            'include_breakfast' => true,
            'include_extra_bed' => true,
            'late_checkout' => true,
        ],
    ]);

    $this->assertDatabaseHas('bookings', [
        'guest_name' => 'Bob Builder',
        'include_breakfast' => true,
        'include_extra_bed' => true,
        'late_checkout' => true,
    ]);
});

test('admin can access booking page but cannot complete checkout', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->get('/booking');
    $response->assertStatus(200);

    // Try to checkout
    Storage::fake('public');
    $room = Room::factory()->create([
        'status' => 'tersedia',
        'price' => 1000000,
    ]);
    $file = UploadedFile::fake()->image('payment_receipt.jpg');

    $responseCheckout = $this->actingAs($admin)->post('/booking', [
        'room_id' => $room->id,
        'guest_name' => 'Admin Guest',
        'guest_email' => 'admin@example.com',
        'guest_phone' => '081234567890',
        'guest_country' => 'Indonesia',
        'check_in' => now()->format('Y-m-d'),
        'check_out' => now()->addDay()->format('Y-m-d'),
        'nights' => 1,
        'guests' => 2,
        'subtotal' => 1000000,
        'discount' => 0,
        'tax' => 100000,
        'total_price' => 1100000,
        'payment_proof' => $file,
    ]);

    $responseCheckout->assertStatus(403);
    $responseCheckout->assertJson([
        'error' => 'You cannot perform checkout because you are logged in as an admin.',
    ]);
});

test('authenticated pelanggan can book multiple rooms in a single checkout transaction', function () {
    Storage::fake('public');
    Mail::fake();

    $user = User::factory()->create(['role' => 'pelanggan']);

    $room1 = Room::factory()->create(['status' => 'tersedia', 'price' => 1000000]);
    $room2 = Room::factory()->create(['status' => 'tersedia', 'price' => 1500000]);

    $file = UploadedFile::fake()->image('payment_receipt.jpg');

    $response = $this->actingAs($user)->post('/booking', [
        'guest_name' => 'John Multi',
        'guest_email' => 'john.multi@example.com',
        'guest_phone' => '081234567890',
        'guest_country' => 'Indonesia',
        'special_requests' => 'None',
        'check_in' => now()->format('Y-m-d'),
        'check_out' => now()->addDay()->format('Y-m-d'),
        'nights' => 1,
        'payment_proof' => $file,
        'rooms' => [
            [
                'room_id' => $room1->id,
                'guests' => 2,
                'include_breakfast' => true,
                'include_extra_bed' => false,
                'late_checkout' => false,
                'subtotal' => 1000000,
                'discount' => 0,
                'tax' => 100000,
                'total_price' => 1100000,
            ],
            [
                'room_id' => $room2->id,
                'guests' => 3,
                'include_breakfast' => false,
                'include_extra_bed' => true,
                'late_checkout' => true,
                'subtotal' => 1500000,
                'discount' => 0,
                'tax' => 150000,
                'total_price' => 1650000,
            ],
        ],
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    // Check parent and child bookings in database
    $bookings = Booking::orderBy('id', 'asc')->get();
    expect($bookings)->toHaveCount(2);

    $parent = $bookings->first();
    $child = $bookings->last();

    expect($parent->parent_id)->toBeNull();
    expect($child->parent_id)->toBe($parent->id);
    expect($parent->invoice_no)->toBe($child->invoice_no);
    expect($parent->guest_name)->toBe('John Multi');
    expect($child->guest_name)->toBe('John Multi');

    // Test Admin Status Cascade Approval
    $admin = User::factory()->create(['role' => 'admin']);
    $approveResponse = $this->actingAs($admin)->post("/admin/bookings/{$parent->id}/approve");
    $approveResponse->assertRedirect();

    // Verify both are now confirmed
    expect($parent->fresh()->status)->toBe('confirmed');
    expect($child->fresh()->status)->toBe('confirmed');

    Mail::assertSent(BookingApproved::class);
});

test('authenticated pelanggan can book multiple rooms with custom check-in check-out dates per room', function () {
    Storage::fake('public');
    Mail::fake();

    $user = User::factory()->create(['role' => 'pelanggan']);

    $room1 = Room::factory()->create(['status' => 'tersedia', 'price' => 1000000]);
    $room2 = Room::factory()->create(['status' => 'tersedia', 'price' => 1500000]);

    $file = UploadedFile::fake()->image('payment_receipt.jpg');

    $customCheckIn1 = now()->addDays(2)->format('Y-m-d');
    $customCheckOut1 = now()->addDays(4)->format('Y-m-d'); // 2 nights
    $customCheckIn2 = now()->addDays(3)->format('Y-m-d');
    $customCheckOut2 = now()->addDays(5)->format('Y-m-d'); // 2 nights

    $response = $this->actingAs($user)->post('/booking', [
        'guest_name' => 'John Custom Date',
        'guest_email' => 'john.custom@example.com',
        'guest_phone' => '081234567890',
        'guest_country' => 'Indonesia',
        'special_requests' => 'None',
        'check_in' => now()->format('Y-m-d'),
        'check_out' => now()->addDay()->format('Y-m-d'),
        'nights' => 1,
        'payment_proof' => $file,
        'rooms' => [
            [
                'room_id' => $room1->id,
                'guests' => 2,
                'include_breakfast' => true,
                'include_extra_bed' => false,
                'late_checkout' => false,
                'subtotal' => 2000000,
                'discount' => 0,
                'tax' => 200000,
                'total_price' => 2200000,
                'check_in' => $customCheckIn1,
                'check_out' => $customCheckOut1,
                'nights' => 2,
            ],
            [
                'room_id' => $room2->id,
                'guests' => 3,
                'include_breakfast' => false,
                'include_extra_bed' => true,
                'late_checkout' => true,
                'subtotal' => 3000000,
                'discount' => 0,
                'tax' => 300000,
                'total_price' => 3300000,
                'check_in' => $customCheckIn2,
                'check_out' => $customCheckOut2,
                'nights' => 2,
            ],
        ],
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    $bookings = Booking::orderBy('id', 'asc')->get();
    expect($bookings)->toHaveCount(2);

    $parent = $bookings->first();
    $child = $bookings->last();

    expect($parent->check_in)->toBe($customCheckIn1);
    expect($parent->check_out)->toBe($customCheckOut1);
    expect($parent->nights)->toBe(2);

    expect($child->check_in)->toBe($customCheckIn2);
    expect($child->check_out)->toBe($customCheckOut2);
    expect($child->nights)->toBe(2);
});
