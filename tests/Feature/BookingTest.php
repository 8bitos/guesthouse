<?php

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
