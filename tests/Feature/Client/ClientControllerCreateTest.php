<?php

use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

// ============================================================================
// CREATE TESTS
// ============================================================================

test('guests are redirected to login when accessing create', function () {
    get(route('clients.create'))
        ->assertRedirect(route('login'));
});

test('owner can access client create page', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('clients.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('clients/create'));
});

test('office can access client create page', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    actingAs($user)
        ->get(route('clients.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('clients/create'));
});

test('mechanic cannot access client create page', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->get(route('clients.create'))
        ->assertForbidden();
});

test('user without role cannot access client create page', function () {
    $user = User::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('clients.create'))
        ->assertForbidden();
});
