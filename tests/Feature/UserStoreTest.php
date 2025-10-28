<?php

use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

const VALID_USER_DATA = [
    'name' => 'Jan Kowalski',
    'email' => 'jan.kowalski@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 'Mechanic',
];

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('guests are redirected to login page when trying to store user', function () {
    post(route('users.store'), VALID_USER_DATA)->assertRedirect(route('login'));
});

test('owner can store user', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $response = actingAs($owner)
        ->post(route('users.store'), VALID_USER_DATA);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', 'Użytkownik został dodany');

    assertDatabaseHas('users', [
        'workshop_id' => $this->workshop->id,
        'name' => 'Jan Kowalski',
        'email' => 'jan.kowalski@example.com',
    ]);

    $createdUser = User::where('email', 'jan.kowalski@example.com')->first();
    expect($createdUser)->not->toBeNull();
    expect($createdUser->hasRole('Mechanic'))->toBeTrue();
});

test('office can store user', function () {
    $office = User::factory()->for($this->workshop)->create();
    $office->assignRole('Office');

    $response = actingAs($office)
        ->post(route('users.store'), VALID_USER_DATA);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', 'Użytkownik został dodany');

    assertDatabaseHas('users', [
        'workshop_id' => $this->workshop->id,
        'name' => 'Jan Kowalski',
        'email' => 'jan.kowalski@example.com',
    ]);
});

test('mechanic cannot store user', function () {
    $mechanic = User::factory()->for($this->workshop)->create();
    $mechanic->assignRole('Mechanic');

    actingAs($mechanic)
        ->post(route('users.store'), VALID_USER_DATA)
        ->assertForbidden();
});

test('store validation fails when name is missing', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->post(route('users.store'), array_merge(VALID_USER_DATA, ['name' => '']))
        ->assertInvalid(['name']);
});

test('store validation fails when email is missing', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->post(route('users.store'), array_merge(VALID_USER_DATA, ['email' => '']))
        ->assertInvalid(['email']);
});

test('store validation fails when email is invalid', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->post(route('users.store'), array_merge(VALID_USER_DATA, ['email' => 'invalid-email']))
        ->assertInvalid(['email']);
});

test('store validation fails when password is missing', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->post(route('users.store'), array_merge(VALID_USER_DATA, ['password' => '']))
        ->assertInvalid(['password']);
});

test('store validation fails when password is too short', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->post(route('users.store'), array_merge(VALID_USER_DATA, [
            'password' => '123',
            'password_confirmation' => '123'
        ]))
        ->assertInvalid(['password']);
});

test('store validation fails when password confirmation does not match', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->post(route('users.store'), array_merge(VALID_USER_DATA, [
            'password' => 'password123',
            'password_confirmation' => 'different123'
        ]))
        ->assertInvalid(['password']);
});

test('store validation fails when role is missing', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->post(route('users.store'), array_merge(VALID_USER_DATA, ['role' => '']))
        ->assertInvalid(['role']);
});

test('store validation fails when role is invalid', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->post(route('users.store'), array_merge(VALID_USER_DATA, ['role' => 'InvalidRole']))
        ->assertInvalid(['role']);
});

test('store validation fails when email is not unique', function () {
    User::factory()->for($this->workshop)->create(['email' => 'existing@example.com']);

    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->post(route('users.store'), array_merge(VALID_USER_DATA, ['email' => 'existing@example.com']))
        ->assertInvalid(['email']);
});
