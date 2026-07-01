<?php

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('revenue and status check on multi room checkout', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pelanggan']);

    $room1 = Room::factory()->create(['status' => 'dipesan', 'price' => 1000000]);
    $room2 = Room::factory()->create(['status' => 'dipesan', 'price' => 800000]);

    // Parent booking (Room 1)
    $parent = Booking::create([
        'user_id' => $user->id,
        'room_id' => $room1->id,
        'invoice_no' => 'INV-PARENT-123',
        'guest_name' => 'John Parent',
        'guest_email' => 'john.parent@example.com',
        'guest_phone' => '08123456789',
        'guest_country' => 'Indonesia',
        'check_in' => '2026-06-10',
        'check_out' => '2026-06-12',
        'nights' => 2,
        'guests' => 2,
        'subtotal' => 2000000,
        'tax' => 200000,
        'total_price' => 2200000,
        'status' => 'checked_in',
    ]);

    // Child booking (Room 2)
    $child = Booking::create([
        'parent_id' => $parent->id,
        'user_id' => $user->id,
        'room_id' => $room2->id,
        'invoice_no' => 'INV-PARENT-123',
        'guest_name' => 'John Parent',
        'guest_email' => 'john.parent@example.com',
        'guest_phone' => '08123456789',
        'guest_country' => 'Indonesia',
        'check_in' => '2026-06-10',
        'check_out' => '2026-06-12',
        'nights' => 2,
        'guests' => 2,
        'subtotal' => 1600000,
        'tax' => 160000,
        'total_price' => 1760000,
        'status' => 'checked_in',
    ]);

    // Calculate initial revenue (sum of checked_in and completed)
    $initialRevenue = Booking::whereIn('status', ['checked_in', 'completed'])->sum('total_price');
    expect($initialRevenue)->toEqual(2200000 + 1760000); // 3960000

    // Act: Admin checks out the parent booking
    $response = $this->actingAs($admin)->post(route('admin.bookings.checkout', $parent));
    $response->assertRedirect();

    // Assert parent is completed, and room is vacant (tersedia)
    expect($parent->fresh()->status)->toBe('completed');
    expect($room1->fresh()->status)->toBe('tersedia');

    // Assert child booking and room statuses after checkout are completed/vacant
    $childFresh = $child->fresh();
    $room2Fresh = $room2->fresh();

    expect($childFresh->status)->toBe('completed');
    expect($room2Fresh->status)->toBe('tersedia');

    // Calculate final revenue
    $finalRevenue = Booking::whereIn('status', ['checked_in', 'completed'])->sum('total_price');
    expect($finalRevenue)->toEqual($initialRevenue);
});
