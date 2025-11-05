<?php

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('guests are redirected to login when attempting to store vehicle', function () {
    $client = Client::factory()->for($this->workshop)->create();

    post(route('vehicles.store'), [
        'client_id' => $client->id,
        'make' => 'Toyota',
        'model' => 'Corolla',
        'year' => 2020,
        'vin' => '1HGBH41JXMN109186',
        'registration_number' => 'ABC123',
    ])->assertRedirect(route('login'));
});

test('user without proper role cannot store vehicle', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertForbidden();
});

test('owner can store a vehicle', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertRedirect(route('vehicles.index'))
        ->assertSessionHas('success', 'Pojazd został dodany');

    assertDatabaseHas('vehicles', [
        'workshop_id' => $this->workshop->id,
        'client_id' => $client->id,
        'make' => 'Toyota',
        'model' => 'Corolla',
        'year' => 2020,
        'vin' => '1HGBH41JXMN109186',
        'registration_number' => 'ABC123',
    ]);
});

test('office can store a vehicle', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => 2021,
            'vin' => '2HGBH41JXMN109187',
            'registration_number' => 'XYZ789',
        ])
        ->assertRedirect(route('vehicles.index'))
        ->assertSessionHas('success', 'Pojazd został dodany');

    assertDatabaseHas('vehicles', [
        'workshop_id' => $this->workshop->id,
        'client_id' => $client->id,
        'make' => 'Honda',
        'model' => 'Civic',
        'year' => 2021,
        'vin' => '2HGBH41JXMN109187',
        'registration_number' => 'XYZ789',
    ]);
});

test('storing vehicle validates client_id is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('vehicles.store'), [
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('client_id');
});

test('storing vehicle validates client_id must exist in workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherClient = Client::factory()->for($otherWorkshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $otherClient->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('client_id');
});

test('storing vehicle validates make is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'model' => 'Corolla',
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('make');
});

test('storing vehicle validates model is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('model');
});

test('storing vehicle validates year is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('year');
});

test('storing vehicle validates year must be integer', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 'not-a-number',
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('year');
});

test('storing vehicle validates year minimum is 1900', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 1899,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('year');
});

test('storing vehicle validates year maximum is 2100', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2101,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('year');
});

test('storing vehicle validates vin is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('vin');
});

test('storing vehicle validates vin must be unique within workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    Vehicle::factory()->for($client)->for($this->workshop)->create(['vin' => '1HGBH41JXMN109186']);

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('vin');
});

test('storing vehicle allows same vin in different workshops', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherClient = Client::factory()->for($otherWorkshop)->create();
    Vehicle::factory()->for($otherClient)->for($otherWorkshop)->create(['vin' => '1HGBH41JXMN109186']);

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertRedirect(route('vehicles.index'))
        ->assertSessionHas('success', 'Pojazd został dodany');

    assertDatabaseHas('vehicles', [
        'workshop_id' => $this->workshop->id,
        'vin' => '1HGBH41JXMN109186',
    ]);
});

test('storing vehicle validates registration_number is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
        ])
        ->assertSessionHasErrors('registration_number');
});

test('storing vehicle validates make max length is 255', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => str_repeat('a', 256),
            'model' => 'Corolla',
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('make');
});

test('storing vehicle validates model max length is 255', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => str_repeat('a', 256),
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('model');
});

test('storing vehicle validates vin max length is 17', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'vin' => str_repeat('a', 18),
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('vin');
});

test('storing vehicle validates registration_number max length is 20', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('vehicles.store'), [
            'client_id' => $client->id,
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => str_repeat('a', 21),
        ])
        ->assertSessionHasErrors('registration_number');
});

test('vehicle store validation returns polish error messages', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user);

    $response = postJson(route('vehicles.store'), []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'client_id' => 'Pole klient jest wymagane.',
            'make' => 'Pole marka jest wymagane.',
            'model' => 'Pole model jest wymagane.',
            'year' => 'Pole rok jest wymagane.',
            'vin' => 'Pole VIN jest wymagane.',
            'registration_number' => 'Pole numer rejestracyjny jest wymagane.',
        ]);
});

test('vehicle year validation returns polish error messages', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');
    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user);

    $response = postJson(route('vehicles.store'), [
        'client_id' => $client->id,
        'make' => 'Toyota',
        'model' => 'Corolla',
        'year' => 1800,
        'vin' => '1HGBH41JXMN109186',
        'registration_number' => 'ABC123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'year' => 'Rok produkcji musi być większy lub równy 1900.',
        ]);
});

test('vehicle client exists validation returns polish error message', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user);

    $response = postJson(route('vehicles.store'), [
        'client_id' => 99999,
        'make' => 'Toyota',
        'model' => 'Corolla',
        'year' => 2020,
        'vin' => '1HGBH41JXMN109186',
        'registration_number' => 'ABC123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'client_id' => 'Wybrany klient nie istnieje.',
        ]);
});
