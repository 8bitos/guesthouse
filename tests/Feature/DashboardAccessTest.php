<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('guests are redirected to login when accessing dashboards', function () {
    $this->get('/dashboard')->assertRedirect('/login');
    $this->get('/admin/dashboard')->assertRedirect('/login');
});

test('pelanggan can access user dashboard but not admin dashboard', function () {
    $user = User::factory()->create([
        'role' => 'pelanggan',
    ]);

    $this->actingAs($user)->get('/dashboard')->assertStatus(200);
    $this->actingAs($user)->get('/admin/dashboard')->assertStatus(403);
});

test('admin can access admin dashboard but not user dashboard', function () {
    $user = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->actingAs($user)->get('/admin/dashboard')->assertStatus(200);
    $this->actingAs($user)->get('/dashboard')->assertStatus(403);
});

test('authenticated pelanggan is redirected to home when visiting login or register screen', function () {
    $user = User::factory()->create([
        'role' => 'pelanggan',
    ]);

    $this->actingAs($user)->get('/login')->assertRedirect('/');
    $this->actingAs($user)->get('/register')->assertRedirect('/');
});

test('authenticated admin is redirected to admin dashboard when visiting login or register screen', function () {
    $user = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->actingAs($user)->get('/login')->assertRedirect('/admin/dashboard');
    $this->actingAs($user)->get('/register')->assertRedirect('/admin/dashboard');
});
