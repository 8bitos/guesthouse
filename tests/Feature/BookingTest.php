<?php

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('unauthenticated guests are redirected to login when accessing booking page', function () {
    $this->get('/booking')->assertRedirect('/login');
});

test('authenticated pelanggan gets guest details prefilled on booking page', function () {
    $user = User::factory()->create([
        'role' => 'pelanggan',
        'name' => 'Alice Margatroid',
        'email' => 'alice@example.com',
        'phone' => '+628123456789',
        'address' => 'Japan',
    ]);

    $response = $this->actingAs($user)->get('/booking');

    $response->assertSuccessful();
    $response->assertSee('Book Your Luxury Stay');
    $response->assertSee('id="check-in-input"', false);
    $response->assertSee('id="check-out-input"', false);
    $response->assertSee('value="Alice Margatroid"', false);
    $response->assertSee('value="alice@example.com"', false);
    $response->assertSee('value="+628123456789"', false);
    $response->assertSee('value="Japan"', false);
});

test('unauthenticated guests are redirected to login when checking availability', function () {
    $this->get('/booking/check-availability?check_in=2026-06-06&check_out=2026-06-07')->assertRedirect('/login');
});

test('authenticated pelanggan can check room availability and see overlapping bookings', function () {
    $user = User::factory()->create([
        'role' => 'pelanggan',
    ]);

    $room1 = Room::factory()->create(['status' => 'tersedia']);
    $room2 = Room::factory()->create(['status' => 'tersedia']);

    // Create a confirmed booking for room 1
    Booking::factory()->create([
        'room_id' => $room1->id,
        'check_in' => '2026-06-10',
        'check_out' => '2026-06-12',
        'status' => 'confirmed',
    ]);

    // Create a pending booking for room 2 (still counts as occupied)
    Booking::factory()->create([
        'room_id' => $room2->id,
        'check_in' => '2026-06-11',
        'check_out' => '2026-06-13',
        'status' => 'pending',
    ]);

    // Create a rejected booking (should NOT count as occupied)
    $room3 = Room::factory()->create(['status' => 'tersedia']);
    Booking::factory()->create([
        'room_id' => $room3->id,
        'check_in' => '2026-06-10',
        'check_out' => '2026-06-15',
        'status' => 'rejected',
    ]);

    // Test date range that overlaps with the bookings (e.g. 2026-06-11 to 2026-06-12)
    $response = $this->actingAs($user)->getJson('/booking/check-availability?check_in=2026-06-11&check_out=2026-06-12');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'booked_rooms' => [
            '*' => [
                'room_id',
                'check_out',
                'check_out_formatted',
            ],
        ],
    ]);

    $response->assertJsonFragment([
        'room_id' => $room1->id,
    ]);
    $response->assertJsonFragment([
        'room_id' => $room2->id,
    ]);
    $response->assertJsonMissing([
        'room_id' => $room3->id,
    ]);
});
