<?php

use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\put;

const VALID_USER_UPDATE_DATA = [
    'name' => 'Updated Name',
    'email' => 'updated@example.com',
    'roles' => ['Office'],
];

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('guests are redirected to login page when trying to update user', function () {
    $userToUpdate = User::factory()->for($this->workshop)->create();

    put(route('users.update', $userToUpdate), VALID_USER_UPDATE_DATA)->assertRedirect(route('login'));
});

test('owner can update user in same workshop', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $userToUpdate = User::factory()->for($this->workshop)->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);
    $userToUpdate->assignRole('Mechanic');

    $response = actingAs($owner)
        ->put(route('users.update', $userToUpdate), VALID_USER_UPDATE_DATA);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', 'Użytkownik został zaktualizowany');

    assertDatabaseHas('users', [
        'id' => $userToUpdate->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    $userToUpdate->refresh();
    expect($userToUpdate->hasRole('Office'))->toBeTrue();
    expect($userToUpdate->hasRole('Mechanic'))->toBeFalse();
});

test('office can update user in same workshop', function () {
    $office = User::factory()->for($this->workshop)->create();
    $office->assignRole('Office');

    $userToUpdate = User::factory()->for($this->workshop)->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);
    $userToUpdate->assignRole('Mechanic');

    $response = actingAs($office)
        ->put(route('users.update', $userToUpdate), VALID_USER_UPDATE_DATA);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', 'Użytkownik został zaktualizowany');

    assertDatabaseHas('users', [
        'id' => $userToUpdate->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

test('mechanic cannot update user', function () {
    $mechanic = User::factory()->for($this->workshop)->create();
    $mechanic->assignRole('Mechanic');

    $userToUpdate = User::factory()->for($this->workshop)->create();
    $userToUpdate->assignRole('Office');

    actingAs($mechanic)
        ->put(route('users.update', $userToUpdate), VALID_USER_UPDATE_DATA)
        ->assertForbidden();
});

test('user cannot update user from different workshop', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $anotherWorkshop = Workshop::factory()->create();
    $userFromAnotherWorkshop = User::factory()->for($anotherWorkshop)->create();
    $userFromAnotherWorkshop->assignRole('Office');

    actingAs($owner)
        ->put(route('users.update', $userFromAnotherWorkshop), VALID_USER_UPDATE_DATA)
        ->assertForbidden();
});

test('user can update with multiple roles', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $userToUpdate = User::factory()->for($this->workshop)->create();
    $userToUpdate->assignRole('Mechanic');

    $data = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'roles' => ['Owner', 'Office'],
    ];

    actingAs($owner)
        ->put(route('users.update', $userToUpdate), $data)
        ->assertRedirect(route('users.index'));

    $userToUpdate->refresh();
    expect($userToUpdate->hasRole('Owner'))->toBeTrue();
    expect($userToUpdate->hasRole('Office'))->toBeTrue();
    expect($userToUpdate->hasRole('Mechanic'))->toBeFalse();
});

test('update validation fails when name is missing', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $userToUpdate = User::factory()->for($this->workshop)->create();
    $userToUpdate->assignRole('Mechanic');

    actingAs($owner)
        ->put(route('users.update', $userToUpdate), array_merge(VALID_USER_UPDATE_DATA, ['name' => '']))
        ->assertInvalid(['name']);
});

test('update validation fails when email is missing', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $userToUpdate = User::factory()->for($this->workshop)->create();
    $userToUpdate->assignRole('Mechanic');

    actingAs($owner)
        ->put(route('users.update', $userToUpdate), array_merge(VALID_USER_UPDATE_DATA, ['email' => '']))
        ->assertInvalid(['email']);
});

test('update validation fails when email is invalid', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $userToUpdate = User::factory()->for($this->workshop)->create();
    $userToUpdate->assignRole('Mechanic');

    actingAs($owner)
        ->put(route('users.update', $userToUpdate), array_merge(VALID_USER_UPDATE_DATA, ['email' => 'invalid-email']))
        ->assertInvalid(['email']);
});

test('update validation fails when roles is missing', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $userToUpdate = User::factory()->for($this->workshop)->create();
    $userToUpdate->assignRole('Mechanic');

    actingAs($owner)
        ->put(route('users.update', $userToUpdate), array_merge(VALID_USER_UPDATE_DATA, ['roles' => []]))
        ->assertInvalid(['roles']);
});

test('update validation fails when role is invalid', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $userToUpdate = User::factory()->for($this->workshop)->create();
    $userToUpdate->assignRole('Mechanic');

    actingAs($owner)
        ->put(route('users.update', $userToUpdate), array_merge(VALID_USER_UPDATE_DATA, ['roles' => ['InvalidRole']]))
        ->assertInvalid(['roles.0']);
});

test('update validation fails when email is not unique', function () {
    User::factory()->for($this->workshop)->create(['email' => 'existing@example.com']);

    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $userToUpdate = User::factory()->for($this->workshop)->create();
    $userToUpdate->assignRole('Mechanic');

    actingAs($owner)
        ->put(route('users.update', $userToUpdate), array_merge(VALID_USER_UPDATE_DATA, ['email' => 'existing@example.com']))
        ->assertInvalid(['email']);
});

test('update allows keeping same email', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $userToUpdate = User::factory()->for($this->workshop)->create([
        'name' => 'Old Name',
        'email' => 'same@example.com',
    ]);
    $userToUpdate->assignRole('Mechanic');

    $data = [
        'name' => 'Updated Name',
        'email' => 'same@example.com',
        'roles' => ['Office'],
    ];

    $response = actingAs($owner)
        ->put(route('users.update', $userToUpdate), $data);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', 'Użytkownik został zaktualizowany');

    assertDatabaseHas('users', [
        'id' => $userToUpdate->id,
        'name' => 'Updated Name',
        'email' => 'same@example.com',
    ]);
});
