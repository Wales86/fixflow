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

// ============================================================================
// SHOW TESTS
// ============================================================================

test('guests are redirected to login when accessing show', function () {
    $client = Client::factory()->for($this->workshop)->create();

    get(route('clients.show', $client))
        ->assertRedirect(route('login'));
});

test('authenticated users without proper role cannot access show', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('clients.show', $client))
        ->assertForbidden();
});

test('owner can view client details', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('clients.show', $client))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clients/show')
            ->has('client')
            ->has('vehicles')
            ->where('client.id', $client->id)
            ->where('client.first_name', $client->first_name)
            ->where('client.last_name', $client->last_name)
            ->where('client.phone_number', $client->phone_number)
            ->where('client.email', $client->email)
            ->where('client.address_street', $client->address_street)
            ->where('client.address_city', $client->address_city)
            ->where('client.address_postal_code', $client->address_postal_code)
            ->where('client.address_country', $client->address_country)
            ->has('client.created_at')
        );
});

test('office can view client details', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('clients.show', $client))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clients/show')
            ->has('client')
            ->has('vehicles')
            ->where('client.id', $client->id)
        );
});

test('accessing non-existent client show page returns 404', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('clients.show', 99999))
        ->assertNotFound();
});

test('client show includes all client vehicles', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicles = Vehicle::factory()->for($client)->for($this->workshop)->count(3)->create();

    actingAs($user)
        ->get(route('clients.show', $client))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clients/show')
            ->has('vehicles', 3)
            ->has('vehicles.0', fn ($vehicle) => $vehicle
                ->has('id')
                ->has('make')
                ->has('model')
                ->has('year')
                ->has('registration_number')
                ->has('vin')
                ->has('repair_orders_count')
                ->etc()
            )
        );
});

test('client show displays empty vehicles array when client has no vehicles', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('clients.show', $client))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clients/show')
            ->has('vehicles', 0)
        );
});
