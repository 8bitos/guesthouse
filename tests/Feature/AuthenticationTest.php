<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate with correct credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
        'role' => 'pelanggan',
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/');
});

test('admins can authenticate and redirect to admin dashboard', function () {
    $user = User::factory()->create([
        'email' => 'admin-test@example.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/admin/dashboard');
});

test('users cannot authenticate with invalid password', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can register a new account', function () {
    $response = $this->post('/register', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'phone' => '+6281234567',
        'address' => 'Test Street',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/');

    $this->assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
        'role' => 'pelanggan',
        'phone' => '+6281234567',
        'address' => 'Test Street',
    ]);
});

test('users can log out', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
