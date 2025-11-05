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
    get(route('users.create'))->assertRedirect(route('login'));
});

test('users without proper permission are forbidden from viewing create form', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->get(route('users.create'))
        ->assertForbidden();
});

test('owner can view user create form', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('users.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('users/create')
            ->has('roles')
        );
});

test('office can view user create form', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    actingAs($user)
        ->get(route('users.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('users/create')
            ->has('roles')
        );
});

test('create form includes all available roles', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('users.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('roles', 3)
            ->where('roles.0.value', 'Owner')
            ->where('roles.0.label', 'Właściciel')
            ->where('roles.1.value', 'Office')
            ->where('roles.1.label', 'Biuro')
            ->where('roles.2.value', 'Mechanic')
            ->where('roles.2.label', 'Mechanik')
        );
});
