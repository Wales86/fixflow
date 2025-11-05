<?php

use App\Models\Mechanic;
use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\put;

const VALID_UPDATE_DATA = [
    'first_name' => 'Updated',
    'last_name' => 'Name',
    'is_active' => true,
];

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('guests are redirected to login when updating mechanic', function () {
    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    put(route('mechanics.update', $mechanic), VALID_UPDATE_DATA)
        ->assertRedirect(route('login'));
});

test('owner can update mechanic', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $mechanic = Mechanic::factory()->for($this->workshop)->create([
        'first_name' => 'Original',
        'last_name' => 'Name',
        'is_active' => true,
    ]);

    $response = actingAs($owner)
        ->put(route('mechanics.update', $mechanic), VALID_UPDATE_DATA);

    $response->assertRedirect(route('mechanics.index'));
    $response->assertSessionHas('success', 'Mechanik został zaktualizowany');

    assertDatabaseHas('mechanics', [
        'id' => $mechanic->id,
        'workshop_id' => $this->workshop->id,
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'is_active' => true,
    ]);
});

test('office can update mechanic', function () {
    $office = User::factory()->for($this->workshop)->create();
    $office->assignRole('Office');

    $mechanic = Mechanic::factory()->for($this->workshop)->create([
        'first_name' => 'Original',
        'last_name' => 'Name',
    ]);

    $response = actingAs($office)
        ->put(route('mechanics.update', $mechanic), VALID_UPDATE_DATA);

    $response->assertRedirect(route('mechanics.index'));
    $response->assertSessionHas('success', 'Mechanik został zaktualizowany');

    assertDatabaseHas('mechanics', [
        'id' => $mechanic->id,
        'first_name' => 'Updated',
        'last_name' => 'Name',
    ]);
});

test('mechanic cannot update mechanic', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    actingAs($user)
        ->put(route('mechanics.update', $mechanic), VALID_UPDATE_DATA)
        ->assertForbidden();
});

test('user without role cannot update mechanic', function () {
    $user = User::factory()->for($this->workshop)->create();

    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    actingAs($user)
        ->put(route('mechanics.update', $mechanic), VALID_UPDATE_DATA)
        ->assertForbidden();
});

test('mechanic from another workshop returns 404 due to global scope', function () {
    $anotherWorkshop = Workshop::factory()->create();
    $mechanicFromAnotherWorkshop = Mechanic::factory()->for($anotherWorkshop)->create();

    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->put(route('mechanics.update', $mechanicFromAnotherWorkshop), VALID_UPDATE_DATA)
        ->assertNotFound();
});

test('returns 404 when updating non-existent mechanic', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->put(route('mechanics.update', 99999), VALID_UPDATE_DATA)
        ->assertNotFound();
});

test('update validation fails when first_name is missing', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    $invalidData = VALID_UPDATE_DATA;
    unset($invalidData['first_name']);

    actingAs($owner)
        ->put(route('mechanics.update', $mechanic), $invalidData)
        ->assertSessionHasErrors(['first_name']);
});

test('update validation fails when last_name is missing', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    $invalidData = VALID_UPDATE_DATA;
    unset($invalidData['last_name']);

    actingAs($owner)
        ->put(route('mechanics.update', $mechanic), $invalidData)
        ->assertSessionHasErrors(['last_name']);
});

test('update validation fails when first_name exceeds max length', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    $invalidData = VALID_UPDATE_DATA;
    $invalidData['first_name'] = str_repeat('a', 256);

    actingAs($owner)
        ->put(route('mechanics.update', $mechanic), $invalidData)
        ->assertSessionHasErrors(['first_name']);
});

test('update validation fails when last_name exceeds max length', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    $invalidData = VALID_UPDATE_DATA;
    $invalidData['last_name'] = str_repeat('a', 256);

    actingAs($owner)
        ->put(route('mechanics.update', $mechanic), $invalidData)
        ->assertSessionHasErrors(['last_name']);
});

test('update validation fails when is_active is not boolean', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    $invalidData = VALID_UPDATE_DATA;
    $invalidData['is_active'] = 'invalid';

    actingAs($owner)
        ->put(route('mechanics.update', $mechanic), $invalidData)
        ->assertSessionHasErrors(['is_active']);
});

test('can update mechanic to inactive', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $mechanic = Mechanic::factory()->for($this->workshop)->create([
        'first_name' => 'Active',
        'last_name' => 'Mechanic',
        'is_active' => true,
    ]);

    $updateData = [
        'first_name' => 'Inactive',
        'last_name' => 'Mechanic',
        'is_active' => false,
    ];

    $response = actingAs($owner)
        ->put(route('mechanics.update', $mechanic), $updateData);

    $response->assertRedirect(route('mechanics.index'));

    assertDatabaseHas('mechanics', [
        'id' => $mechanic->id,
        'first_name' => 'Inactive',
        'last_name' => 'Mechanic',
        'is_active' => false,
    ]);
});

test('can update only names without changing is_active', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $mechanic = Mechanic::factory()->for($this->workshop)->create([
        'first_name' => 'Original',
        'last_name' => 'Name',
        'is_active' => false,
    ]);

    $updateData = [
        'first_name' => 'New',
        'last_name' => 'Name',
        'is_active' => false,
    ];

    actingAs($owner)
        ->put(route('mechanics.update', $mechanic), $updateData);

    assertDatabaseHas('mechanics', [
        'id' => $mechanic->id,
        'first_name' => 'New',
        'last_name' => 'Name',
        'is_active' => false,
    ]);
});
