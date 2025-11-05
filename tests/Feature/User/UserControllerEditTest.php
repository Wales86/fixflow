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

test('guests are redirected to login page when trying to edit user', function () {
    $targetUser = User::factory()->for($this->workshop)->create();

    get(route('users.edit', $targetUser))->assertRedirect(route('login'));
});

test('owner can access edit page for user in the same workshop', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $targetUser = User::factory()->for($this->workshop)->create();
    $targetUser->assignRole('Mechanic');

    $response = actingAs($owner)
        ->get(route('users.edit', $targetUser));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('users/edit')
        ->has('user', fn ($user) => $user
            ->where('id', $targetUser->id)
            ->where('name', $targetUser->name)
            ->where('email', $targetUser->email)
            ->has('roles')
            ->etc()
        )
        ->has('roles')
    );
});

test('office can access edit page for user in the same workshop', function () {
    $office = User::factory()->for($this->workshop)->create();
    $office->assignRole('Office');

    $targetUser = User::factory()->for($this->workshop)->create();
    $targetUser->assignRole('Mechanic');

    $response = actingAs($office)
        ->get(route('users.edit', $targetUser));

    $response->assertOk();
});

test('mechanic cannot access edit page', function () {
    $mechanic = User::factory()->for($this->workshop)->create();
    $mechanic->assignRole('Mechanic');

    $targetUser = User::factory()->for($this->workshop)->create();
    $targetUser->assignRole('Office');

    actingAs($mechanic)
        ->get(route('users.edit', $targetUser))
        ->assertForbidden();
});

test('user cannot edit user from different workshop', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $targetUser = User::factory()->for($otherWorkshop)->create();
    $targetUser->assignRole('Mechanic');

    actingAs($owner)
        ->get(route('users.edit', $targetUser))
        ->assertForbidden();
});

test('returns 404 when user does not exist', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->get(route('users.edit', 99999))
        ->assertNotFound();
});

test('edit page contains all available roles', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $targetUser = User::factory()->for($this->workshop)->create();
    $targetUser->assignRole('Mechanic');

    $response = actingAs($owner)
        ->get(route('users.edit', $targetUser));

    $response->assertInertia(fn ($page) => $page
        ->has('roles', 3)
        ->has('roles.0', fn ($role) => $role
            ->has('value')
            ->has('label')
        )
    );
});

test('user data includes roles in the response', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $targetUser = User::factory()->for($this->workshop)->create();
    $targetUser->assignRole('Mechanic');
    $targetUser->assignRole('Office');

    $response = actingAs($owner)
        ->get(route('users.edit', $targetUser));

    $response->assertInertia(fn ($page) => $page
        ->has('user.roles', 2)
    );
});
