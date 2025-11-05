<?php

use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\delete;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('guests are redirected to login page when trying to delete user', function () {
    $userToDelete = User::factory()->for($this->workshop)->create();

    delete(route('users.destroy', $userToDelete))->assertRedirect(route('login'));
});

test('owner can delete user in same workshop', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $userToDelete = User::factory()->for($this->workshop)->create();
    $userToDelete->assignRole('Mechanic');

    $response = actingAs($owner)
        ->delete(route('users.destroy', $userToDelete));

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', 'Użytkownik został usunięty pomyślnie');

    assertSoftDeleted('users', [
        'id' => $userToDelete->id,
    ]);
});

test('office can delete user in same workshop', function () {
    $office = User::factory()->for($this->workshop)->create();
    $office->assignRole('Office');

    $userToDelete = User::factory()->for($this->workshop)->create();
    $userToDelete->assignRole('Mechanic');

    $response = actingAs($office)
        ->delete(route('users.destroy', $userToDelete));

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', 'Użytkownik został usunięty pomyślnie');

    assertSoftDeleted('users', [
        'id' => $userToDelete->id,
    ]);
});

test('mechanic cannot delete user', function () {
    $mechanic = User::factory()->for($this->workshop)->create();
    $mechanic->assignRole('Mechanic');

    $userToDelete = User::factory()->for($this->workshop)->create();
    $userToDelete->assignRole('Office');

    actingAs($mechanic)
        ->delete(route('users.destroy', $userToDelete))
        ->assertForbidden();
});

test('user cannot delete user from different workshop', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $anotherWorkshop = Workshop::factory()->create();
    $userFromAnotherWorkshop = User::factory()->for($anotherWorkshop)->create();
    $userFromAnotherWorkshop->assignRole('Office');

    actingAs($owner)
        ->delete(route('users.destroy', $userFromAnotherWorkshop))
        ->assertForbidden();
});

test('user cannot delete own account', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $response = actingAs($owner)
        ->delete(route('users.destroy', $owner));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Nie można usunąć własnego konta');

    expect(User::find($owner->id))->not->toBeNull();
});

test('trying to delete non-existent user returns 404', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->delete(route('users.destroy', 99999))
        ->assertNotFound();
});
