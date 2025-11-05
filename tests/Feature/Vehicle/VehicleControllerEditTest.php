<?php

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
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

test('guests cannot access vehicle edit page', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    get(route('vehicles.edit', $vehicle))
        ->assertRedirect(route('login'));
});

test('owner can access vehicle edit page from their workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('vehicles.edit', $vehicle))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/edit')
            ->has('vehicle')
            ->where('vehicle.id', $vehicle->id)
            ->where('vehicle.client_id', $client->id)
            ->where('vehicle.make', $vehicle->make)
            ->where('vehicle.model', $vehicle->model)
            ->where('vehicle.year', $vehicle->year)
            ->where('vehicle.registration_number', $vehicle->registration_number)
            ->where('vehicle.vin', $vehicle->vin)
            ->has('clients')
        );
});

test('office user can access vehicle edit page from their workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('vehicles.edit', $vehicle))
        ->assertOk();
});

test('mechanic cannot access vehicle edit page', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('vehicles.edit', $vehicle))
        ->assertForbidden();
});

test('user cannot access vehicle edit page from another workshop', function () {
    $otherWorkshop = Workshop::factory()->create();
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($otherWorkshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($otherWorkshop)->create();

    expect($user->can('update', $vehicle))->toBeFalse();
});

test('vehicle edit page includes list of clients from same workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client1 = Client::factory()->for($this->workshop)->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
    $client2 = Client::factory()->for($this->workshop)->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
    ]);

    $vehicle = Vehicle::factory()->for($client1)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('vehicles.edit', $vehicle))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/edit')
            ->has('clients', 2)
            ->where('clients.0.id', $client1->id)
            ->has('clients.0.name')
        );
});

test('vehicle edit returns 404 for non-existent vehicle', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('vehicles.edit', 99999))
        ->assertNotFound();
});
