<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(LazilyRefreshDatabase::class);

test('guests cannot access profile page', function () {
    $this->get(route('profile.edit'))->assertRedirect('/login');
    $this->post(route('profile.update'), [])->assertRedirect('/login');
});

test('authenticated users can access profile page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertStatus(200);
    $response->assertSee($user->name);
    $response->assertSee($user->email);
});

test('authenticated users can update their profile information', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'phone' => '+6212345',
        'address' => 'Old Address',
    ]);

    $response = $this->actingAs($user)->post(route('profile.update'), [
        'name' => 'New Name',
        'country_code' => '+62',
        'phone' => '67890',
        'address' => 'New Address',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $user->refresh();
    expect($user->name)->toBe('New Name');
    expect($user->phone)->toBe('+6267890');
    expect($user->address)->toBe('New Address');
});

test('authenticated users can change their password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword'),
    ]);

    $response = $this->actingAs($user)->post(route('profile.update'), [
        'name' => $user->name,
        'country_code' => '+62',
        'current_password' => 'oldpassword',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $user->refresh();
    expect(Hash::check('newpassword', $user->password))->toBeTrue();
});

test('password change fails if current password is incorrect', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword'),
    ]);

    $response = $this->actingAs($user)->post(route('profile.update'), [
        'name' => $user->name,
        'country_code' => '+62',
        'current_password' => 'wrongpassword',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors(['current_password']);

    $user->refresh();
    expect(Hash::check('oldpassword', $user->password))->toBeTrue();
});
