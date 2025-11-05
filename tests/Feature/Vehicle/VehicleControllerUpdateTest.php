<?php

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('guests cannot update vehicle', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    $this->put(route('vehicles.update', $vehicle), [
        'client_id' => $client->id,
        'make' => 'Updated',
        'model' => 'Model',
        'year' => 2021,
        'vin' => '1HGBH41JXMN109999',
        'registration_number' => 'ABC123',
    ])->assertRedirect(route('login'));
});

test('mechanic cannot update vehicle', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->put(route('vehicles.update', $vehicle), [
            'client_id' => $client->id,
            'make' => 'Updated',
            'model' => 'Model',
            'year' => 2021,
            'vin' => '1HGBH41JXMN109999',
            'registration_number' => 'ABC123',
        ])
        ->assertForbidden();
});

test('owner can update vehicle', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client1 = Client::factory()->for($this->workshop)->create();
    $client2 = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client1)->for($this->workshop)->create([
        'make' => 'Toyota',
        'model' => 'Corolla',
        'year' => 2020,
        'vin' => '1HGBH41JXMN109186',
        'registration_number' => 'ABC123',
    ]);

    actingAs($user)
        ->put(route('vehicles.update', $vehicle), [
            'client_id' => $client2->id,
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => 2021,
            'vin' => '2HGFC2F59KH123456',
            'registration_number' => 'XYZ789',
        ])
        ->assertRedirect(route('vehicles.show', $vehicle))
        ->assertSessionHas('success', 'Pojazd został zaktualizowany');

    $vehicle->refresh();
    expect($vehicle->client_id)->toBe($client2->id);
    expect($vehicle->make)->toBe('Honda');
    expect($vehicle->model)->toBe('Civic');
    expect($vehicle->year)->toBe(2021);
    expect($vehicle->vin)->toBe('2HGFC2F59KH123456');
    expect($vehicle->registration_number)->toBe('XYZ789');
});

test('office can update vehicle', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create([
        'make' => 'Toyota',
        'model' => 'Corolla',
    ]);

    actingAs($user)
        ->put(route('vehicles.update', $vehicle), [
            'client_id' => $client->id,
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => 2021,
            'vin' => $vehicle->vin,
            'registration_number' => $vehicle->registration_number,
        ])
        ->assertRedirect(route('vehicles.show', $vehicle))
        ->assertSessionHas('success');

    expect($vehicle->fresh()->make)->toBe('Honda');
});

test('updating vehicle validates client_id is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->put(route('vehicles.update', $vehicle), [
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => 2021,
            'vin' => '1HGBH41JXMN109999',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('client_id');
});

test('updating vehicle validates make is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->put(route('vehicles.update', $vehicle), [
            'client_id' => $client->id,
            'model' => 'Civic',
            'year' => 2021,
            'vin' => '1HGBH41JXMN109999',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('make');
});

test('updating vehicle validates vin must be unique within workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle1 = Vehicle::factory()->for($client)->for($this->workshop)->create([
        'vin' => '1HGBH41JXMN109186',
    ]);
    $vehicle2 = Vehicle::factory()->for($client)->for($this->workshop)->create([
        'vin' => '2HGFC2F59KH123456',
    ]);

    actingAs($user)
        ->put(route('vehicles.update', $vehicle2), [
            'client_id' => $client->id,
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => 2021,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => 'ABC123',
        ])
        ->assertSessionHasErrors('vin');
});

test('updating vehicle allows keeping same vin', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create([
        'vin' => '1HGBH41JXMN109186',
        'make' => 'Toyota',
    ]);

    actingAs($user)
        ->put(route('vehicles.update', $vehicle), [
            'client_id' => $client->id,
            'make' => 'Honda',
            'model' => $vehicle->model,
            'year' => $vehicle->year,
            'vin' => '1HGBH41JXMN109186',
            'registration_number' => $vehicle->registration_number,
        ])
        ->assertRedirect(route('vehicles.show', $vehicle))
        ->assertSessionHas('success');

    expect($vehicle->fresh()->make)->toBe('Honda');
    expect($vehicle->fresh()->vin)->toBe('1HGBH41JXMN109186');
});

test('updating non-existent vehicle returns 404', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->put(route('vehicles.update', 99999), [
            'client_id' => $client->id,
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => 2021,
            'vin' => '1HGBH41JXMN109999',
            'registration_number' => 'ABC123',
        ])
        ->assertNotFound();
});
