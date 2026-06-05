<?php

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

test('guests cannot access rooms management', function () {
    $this->get(route('admin.rooms.index'))->assertRedirect('/login');
    $this->get(route('admin.rooms.create'))->assertRedirect('/login');
    $this->post(route('admin.rooms.store'), [])->assertRedirect('/login');
});

test('regular users cannot access rooms management', function () {
    $user = User::factory()->create(['role' => 'pelanggan']);

    $this->actingAs($user)->get(route('admin.rooms.index'))->assertStatus(403);
    $this->actingAs($user)->get(route('admin.rooms.create'))->assertStatus(403);
    $this->actingAs($user)->post(route('admin.rooms.store'), [])->assertStatus(403);
});

test('admins can view rooms index and create form', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('admin.rooms.index'))->assertStatus(200);
    $this->actingAs($admin)->get(route('admin.rooms.create'))->assertStatus(200);
});

test('admins can store a new room with image upload', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => 'admin']);
    $file = UploadedFile::fake()->image('room_photo.jpg');

    $response = $this->actingAs($admin)->post(route('admin.rooms.store'), [
        'name' => 'Presidents Villa',
        'type' => 'Villa',
        'price' => 2500000,
        'capacity' => 6,
        'description' => 'A grand villa with valley views.',
        'status' => 'tersedia',
        'image' => $file,
    ]);

    $response->assertRedirect(route('admin.rooms.index'));
    $this->assertDatabaseHas('rooms', [
        'name' => 'Presidents Villa',
        'price' => 2500000,
    ]);

    $room = Room::where('name', 'Presidents Villa')->first();
    $this->assertNotNull($room->image);
    Storage::disk('public')->assertExists($room->image);
});

test('admins can edit and update room details', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => 'admin']);
    $room = Room::create([
        'name' => 'Standard Cabin',
        'type' => 'Standard',
        'price' => 500000,
        'capacity' => 2,
        'description' => 'Cozy cabin.',
        'status' => 'tersedia',
    ]);

    $response = $this->actingAs($admin)->put(route('admin.rooms.update', $room), [
        'name' => 'Standard Cabin Updated',
        'type' => 'Standard',
        'price' => 550000,
        'capacity' => 2,
        'description' => 'Updated cozy cabin.',
        'status' => 'perbaikan',
    ]);

    $response->assertRedirect(route('admin.rooms.index'));
    $this->assertDatabaseHas('rooms', [
        'id' => $room->id,
        'name' => 'Standard Cabin Updated',
        'price' => 550000,
        'status' => 'perbaikan',
    ]);
});

test('admins can delete a room', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => 'admin']);
    $imagePath = Storage::disk('public')->putFile('rooms', UploadedFile::fake()->image('temp.jpg'));

    $room = Room::create([
        'name' => 'Room to Delete',
        'type' => 'Standard',
        'price' => 400000,
        'capacity' => 2,
        'status' => 'tersedia',
        'image' => $imagePath,
    ]);

    Storage::disk('public')->assertExists($imagePath);

    $response = $this->actingAs($admin)->delete(route('admin.rooms.destroy', $room));

    $response->assertRedirect(route('admin.rooms.index'));
    $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    Storage::disk('public')->assertMissing($imagePath);
});
