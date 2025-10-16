<?php

use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->workshop = Workshop::factory()->create();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('unauthenticated users are redirected to login', function () {
    get(route('clients.create'))
        ->assertRedirect(route('login'));
});

test('authenticated user with Owner role can access client create page', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('clients.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('clients/create'));
});

test('authenticated user with Office role can access client create page', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Office');

    actingAs($user)
        ->get(route('clients.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('clients/create'));
});

test('authenticated user with Mechanic role cannot access client create page', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Mechanic');

    actingAs($user)
        ->get(route('clients.create'))
        ->assertForbidden();
});

test('authenticated user without any role cannot access client create page', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);

    actingAs($user)
        ->get(route('clients.create'))
        ->assertForbidden();
});
