<?php

use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('admin can export booking reports to XLS and apply filters', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $room1 = Room::factory()->create(['name' => 'Deluxe Room']);
    $room2 = Room::factory()->create(['name' => 'Suite Room']);

    // Create mock bookings
    Booking::create([
        'user_id' => User::factory()->create(['role' => 'pelanggan'])->id,
        'room_id' => $room1->id,
        'invoice_no' => 'INV-001',
        'guest_name' => 'John Doe',
        'guest_email' => 'john@example.com',
        'guest_phone' => '08123456789',
        'guest_country' => 'Indonesia',
        'check_in' => '2026-06-10',
        'check_out' => '2026-06-12',
        'nights' => 2,
        'adults' => 2,
        'children' => 0,
        'subtotal' => 1000000,
        'tax' => 100000,
        'total_price' => 1100000,
        'status' => 'pending',
    ]);

    Booking::create([
        'user_id' => User::factory()->create(['role' => 'pelanggan'])->id,
        'room_id' => $room2->id,
        'invoice_no' => 'INV-002',
        'guest_name' => 'Alice Smith',
        'guest_email' => 'alice@example.com',
        'guest_phone' => '087777777',
        'guest_country' => 'Singapore',
        'check_in' => '2026-07-20',
        'check_out' => '2026-07-22',
        'nights' => 2,
        'adults' => 1,
        'children' => 1,
        'subtotal' => 2000000,
        'tax' => 200000,
        'total_price' => 2200000,
        'status' => 'confirmed',
    ]);

    // 1. Export without filters
    $response = $this->actingAs($admin)->get(route('admin.reports.export'));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=utf-8');

    $disposition = $response->headers->get('Content-Disposition');
    expect($disposition)->toStartWith('attachment; filename=reservations_report_')
        ->toEndWith('.xls');

    $content = $response->streamedContent();
    expect($content)->toContain('Invoice No')
        ->toContain('INV-001')
        ->toContain('John Doe')
        ->toContain('INV-002')
        ->toContain('Alice Smith');

    // 2. Filter by status
    $responseFilteredStatus = $this->actingAs($admin)->get(route('admin.reports.export', [
        'status' => 'confirmed',
    ]));
    $contentFilteredStatus = $responseFilteredStatus->streamedContent();
    expect($contentFilteredStatus)->toContain('INV-002')
        ->toContain('Alice Smith')
        ->not->toContain('INV-001');

    // 3. Filter by room
    $responseFilteredRoom = $this->actingAs($admin)->get(route('admin.reports.export', [
        'room_id' => $room1->id,
    ]));
    $contentFilteredRoom = $responseFilteredRoom->streamedContent();
    expect($contentFilteredRoom)->toContain('INV-001')
        ->toContain('John Doe')
        ->not->toContain('INV-002');

    // 4. Filter by date range
    $responseFilteredDate = $this->actingAs($admin)->get(route('admin.reports.export', [
        'date_type' => 'check_in',
        'start_date' => '2026-06-01',
        'end_date' => '2026-06-30',
    ]));
    $contentFilteredDate = $responseFilteredDate->streamedContent();
    expect($contentFilteredDate)->toContain('INV-001')
        ->not->toContain('INV-002');
});

test('admin can view users list, edit form, and update customer profile', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create([
        'role' => 'pelanggan',
        'name' => 'Old Customer Name',
        'phone' => '08111111',
        'address' => 'Old Address',
    ]);

    // Index
    $response = $this->actingAs($admin)->get(route('admin.users.index'));
    $response->assertStatus(200);
    $response->assertSee('Old Customer Name');

    // Edit
    $response = $this->actingAs($admin)->get(route('admin.users.edit', $customer));
    $response->assertStatus(200);

    // Update
    $response = $this->actingAs($admin)->put(route('admin.users.update', $customer), [
        'name' => 'New Customer Name',
        'email' => $customer->email,
        'phone' => '08222222',
        'address' => 'New Address',
    ]);
    $response->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'id' => $customer->id,
        'name' => 'New Customer Name',
        'phone' => '08222222',
        'address' => 'New Address',
    ]);
});

test('admin can delete customer profile', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'pelanggan']);

    $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $customer));
    $response->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseMissing('users', ['id' => $customer->id]);
});

test('admin can manage bookings: view, edit, update, approve, reject, cancel', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $room = Room::factory()->create();
    $booking = Booking::create([
        'user_id' => User::factory()->create(['role' => 'pelanggan'])->id,
        'room_id' => $room->id,
        'invoice_no' => 'INV-002',
        'guest_name' => 'Bob Smith',
        'guest_email' => 'bob@example.com',
        'guest_phone' => '08987654321',
        'guest_country' => 'USA',
        'check_in' => '2026-06-15',
        'check_out' => '2026-06-16',
        'nights' => 1,
        'adults' => 1,
        'children' => 0,
        'subtotal' => 500000,
        'tax' => 50000,
        'total_price' => 550000,
        'status' => 'pending',
    ]);

    // View index & edit
    $this->actingAs($admin)->get(route('admin.bookings.index'))->assertStatus(200)->assertSee('INV-002');
    $this->actingAs($admin)->get(route('admin.bookings.edit', $booking))->assertStatus(200);

    // Update
    $response = $this->actingAs($admin)->put(route('admin.bookings.update', $booking), [
        'guest_name' => 'Bob Smith Updated',
        'guest_email' => 'bob_updated@example.com',
        'guest_phone' => '089999999',
        'guest_country' => 'Canada',
        'status' => 'pending',
    ]);
    $response->assertRedirect(route('admin.bookings.index'));
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'guest_name' => 'Bob Smith Updated',
        'guest_email' => 'bob_updated@example.com',
    ]);

    // Approve booking
    $this->actingAs($admin)->post(route('admin.bookings.approve', $booking))->assertRedirect();
    $this->assertEquals('confirmed', $booking->fresh()->status);

    // Reject booking
    $this->actingAs($admin)->post(route('admin.bookings.reject', $booking))->assertRedirect();
    $this->assertEquals('rejected', $booking->fresh()->status);

    // Cancel booking
    $this->actingAs($admin)->post(route('admin.bookings.cancel', $booking))->assertRedirect();
    $this->assertEquals('cancelled', $booking->fresh()->status);
});

test('customer can submit complaints and admin can resolve them', function () {
    $customer = User::factory()->create(['role' => 'pelanggan']);
    $admin = User::factory()->create(['role' => 'admin']);
    $room = Room::factory()->create();
    $booking = Booking::create([
        'user_id' => $customer->id,
        'room_id' => $room->id,
        'invoice_no' => 'INV-003',
        'guest_name' => 'Alice Cooper',
        'guest_email' => 'alice@example.com',
        'guest_phone' => '087777777',
        'guest_country' => 'UK',
        'check_in' => '2026-06-20',
        'check_out' => '2026-06-22',
        'nights' => 2,
        'adults' => 2,
        'children' => 0,
        'subtotal' => 800000,
        'tax' => 80000,
        'total_price' => 880000,
        'status' => 'confirmed',
    ]);

    // Customer submits a complaint
    $response = $this->actingAs($customer)->post(route('complaints.store'), [
        'booking_id' => $booking->id,
        'subject' => 'AC is leaking water',
        'description' => 'Water is dripping from the AC onto the floor.',
    ]);
    $response->assertRedirect();

    $this->assertDatabaseHas('complaints', [
        'user_id' => $customer->id,
        'booking_id' => $booking->id,
        'subject' => 'AC is leaking water',
        'status' => 'pending',
    ]);

    $complaint = Complaint::where('subject', 'AC is leaking water')->first();

    // Admin views complaints index and show page
    $this->actingAs($admin)->get(route('admin.complaints.index'))->assertStatus(200)->assertSee('AC is leaking water');
    $this->actingAs($admin)->get(route('admin.complaints.show', $complaint))->assertStatus(200)->assertSee('Water is dripping');

    // Admin updates complaint resolution
    $response = $this->actingAs($admin)->put(route('admin.complaints.update', $complaint), [
        'resolution' => 'Sent AC technician to fix the leak.',
        'status' => 'resolved',
    ]);
    $response->assertRedirect(route('admin.complaints.index'));

    $this->assertDatabaseHas('complaints', [
        'id' => $complaint->id,
        'resolution' => 'Sent AC technician to fix the leak.',
        'status' => 'resolved',
    ]);
});
