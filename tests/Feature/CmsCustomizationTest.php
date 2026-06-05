<?php

use App\Models\Facility;
use App\Models\Gallery;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

test('guests are redirected when trying to access cms management', function () {
    $this->get(route('admin.cms.about'))->assertRedirect('/login');
    $this->post(route('admin.cms.about'), [])->assertRedirect('/login');
    $this->get(route('admin.cms.facilities.index'))->assertRedirect('/login');
    $this->get(route('admin.cms.gallery.index'))->assertRedirect('/login');
});

test('pelanggan cannot access cms management', function () {
    $user = User::factory()->create(['role' => 'pelanggan']);

    $this->actingAs($user)->get(route('admin.cms.about'))->assertForbidden();
    $this->actingAs($user)->post(route('admin.cms.about'), [])->assertForbidden();
    $this->actingAs($user)->get(route('admin.cms.facilities.index'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.cms.gallery.index'))->assertForbidden();
});

test('admin can access and update about us settings', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('admin.cms.about'));
    $response->assertSuccessful();

    $response = $this->actingAs($admin)->post(route('admin.cms.about'), [
        'about_title' => 'New About Us',
        'about_desc' => 'New Description',
        'about_why_list' => "Reason 1\nReason 2",
        'about_vision' => 'New Vision Statement',
    ]);

    $response->assertRedirect();

    expect(Setting::getValue('about_title'))->toBe('New About Us');
    expect(Setting::getValue('about_desc'))->toBe('New Description');
    expect(Setting::getValue('about_why_list'))->toBe("Reason 1\nReason 2");
    expect(Setting::getValue('about_vision'))->toBe('New Vision Statement');
});

test('admin can perform CRUD operations on facilities', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // List
    $this->actingAs($admin)->get(route('admin.cms.facilities.index'))->assertSuccessful();

    // Create form
    $this->actingAs($admin)->get(route('admin.cms.facilities.create'))->assertSuccessful();

    // Store
    $response = $this->actingAs($admin)->post(route('admin.cms.facilities.store'), [
        'icon' => '🏊',
        'title' => 'Pool',
        'description' => 'Beautiful pool',
    ]);
    $response->assertRedirect(route('admin.cms.facilities.index'));
    $this->assertDatabaseHas('facilities', [
        'icon' => '🏊',
        'title' => 'Pool',
        'description' => 'Beautiful pool',
    ]);

    $facility = Facility::where('title', 'Pool')->first();

    // Edit form
    $this->actingAs($admin)->get(route('admin.cms.facilities.edit', $facility))->assertSuccessful();

    // Update
    $response = $this->actingAs($admin)->put(route('admin.cms.facilities.update', $facility), [
        'icon' => '🏊‍♂️',
        'title' => 'Infinity Pool',
        'description' => 'Beautiful infinity pool',
    ]);
    $response->assertRedirect(route('admin.cms.facilities.index'));
    $this->assertDatabaseHas('facilities', [
        'id' => $facility->id,
        'icon' => '🏊‍♂️',
        'title' => 'Infinity Pool',
        'description' => 'Beautiful infinity pool',
    ]);

    // Delete
    $response = $this->actingAs($admin)->delete(route('admin.cms.facilities.destroy', $facility));
    $response->assertRedirect(route('admin.cms.facilities.index'));
    $this->assertDatabaseMissing('facilities', ['id' => $facility->id]);
});

test('admin can upload and delete gallery photos', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => 'admin']);

    // List
    $this->actingAs($admin)->get(route('admin.cms.gallery.index'))->assertSuccessful();

    // Create form
    $this->actingAs($admin)->get(route('admin.cms.gallery.create'))->assertSuccessful();

    // Store photo
    $file = UploadedFile::fake()->image('gallery_photo.jpg');
    $response = $this->actingAs($admin)->post(route('admin.cms.gallery.store'), [
        'image' => $file,
        'caption' => 'Scenic View',
        'order_index' => 5,
    ]);

    $response->assertRedirect(route('admin.cms.gallery.index'));
    $this->assertDatabaseHas('galleries', [
        'caption' => 'Scenic View',
        'order_index' => 5,
    ]);

    $gallery = Gallery::where('caption', 'Scenic View')->first();
    $this->assertNotNull($gallery->image);
    Storage::disk('public')->assertExists($gallery->image);

    // Delete photo
    $response = $this->actingAs($admin)->delete(route('admin.cms.gallery.destroy', $gallery));
    $response->assertRedirect(route('admin.cms.gallery.index'));
    $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
    Storage::disk('public')->assertMissing($gallery->image);
});
