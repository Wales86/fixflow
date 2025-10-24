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

test('guests are redirected to the login page', function () {
    get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users without proper role are forbidden from dashboard', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});

test('owner can view dashboard', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/index')
        );
});

test('office can view dashboard', function () {
    $officeRole = Role::firstOrCreate(['name' => 'Office']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($officeRole);

    actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/index')
        );
});
