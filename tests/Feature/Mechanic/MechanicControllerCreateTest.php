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
    get(route('mechanics.create'))
        ->assertRedirect(route('login'));
});

test('owner can access mechanic create page', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('mechanics.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('mechanics/create'));
});

test('office can access mechanic create page', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    actingAs($user)
        ->get(route('mechanics.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('mechanics/create'));
});

test('mechanic cannot access mechanic create page', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->get(route('mechanics.create'))
        ->assertForbidden();
});

test('user without role cannot access mechanic create page', function () {
    $user = User::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('mechanics.create'))
        ->assertForbidden();
});
