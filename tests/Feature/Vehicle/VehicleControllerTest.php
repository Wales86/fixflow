<?php

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();
});

test('guests are redirected to login page when accessing index', function () {
    get(route('vehicles.index'))->assertRedirect(route('login'));
});

test('authenticated users without proper role are forbidden from index', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->get(route('vehicles.index'))
        ->assertForbidden();
});

test('owner can view vehicles list', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('vehicles.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/index')
            ->has('vehicles.data')
            ->has('filters')
        );
});

test('office can view vehicles list', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    actingAs($user)
        ->get(route('vehicles.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/index')
            ->has('vehicles.data')
            ->has('filters')
        );
});

test('vehicles list includes pagination and client data', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    Vehicle::factory()->for($client)->for($this->workshop)->count(3)->create();

    actingAs($user)
        ->get(route('vehicles.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/index')
            ->has('vehicles.data', 3)
            ->has('vehicles.data.0', fn ($vehicle) => $vehicle
                ->has('id')
                ->has('make')
                ->has('model')
                ->has('year')
                ->has('registration_number')
                ->has('vin')
                ->has('repair_orders_count')
                ->has('client')
                ->has('client.id')
                ->has('client.first_name')
                ->has('client.last_name')
            )
            ->has('vehicles.links')
            ->has('vehicles.current_page')
            ->has('vehicles.per_page')
            ->has('vehicles.total')
        );
});

test('search filters vehicles by make', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    Vehicle::factory()->for($client)->for($this->workshop)->create(['make' => 'Toyota', 'model' => 'Corolla']);
    Vehicle::factory()->for($client)->for($this->workshop)->create(['make' => 'Honda', 'model' => 'Civic']);

    actingAs($user)
        ->get(route('vehicles.index', ['search' => 'Toyota']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/index')
            ->has('vehicles.data', 1)
            ->where('vehicles.data.0.make', 'Toyota')
            ->where('filters.search', 'Toyota')
        );
});

test('search filters vehicles by model', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    Vehicle::factory()->for($client)->for($this->workshop)->create(['make' => 'Toyota', 'model' => 'Corolla']);
    Vehicle::factory()->for($client)->for($this->workshop)->create(['make' => 'Honda', 'model' => 'Civic']);

    actingAs($user)
        ->get(route('vehicles.index', ['search' => 'Civic']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/index')
            ->has('vehicles.data', 1)
            ->where('vehicles.data.0.model', 'Civic')
        );
});

test('search filters vehicles by registration number', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    Vehicle::factory()->for($client)->for($this->workshop)->create(['registration_number' => 'ABC123']);
    Vehicle::factory()->for($client)->for($this->workshop)->create(['registration_number' => 'XYZ789']);

    actingAs($user)
        ->get(route('vehicles.index', ['search' => 'ABC']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/index')
            ->has('vehicles.data', 1)
            ->where('vehicles.data.0.registration_number', 'ABC123')
        );
});

test('search filters vehicles by VIN', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    Vehicle::factory()->for($client)->for($this->workshop)->create(['vin' => '1HGBH41JXMN109186']);
    Vehicle::factory()->for($client)->for($this->workshop)->create(['vin' => '2HGBH41JXMN109187']);

    actingAs($user)
        ->get(route('vehicles.index', ['search' => '1HGBH']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/index')
            ->has('vehicles.data', 1)
            ->where('vehicles.data.0.vin', '1HGBH41JXMN109186')
        );
});

test('sort parameter orders vehicles correctly by make', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    Vehicle::factory()->for($client)->for($this->workshop)->create(['make' => 'Toyota']);
    Vehicle::factory()->for($client)->for($this->workshop)->create(['make' => 'Honda']);
    Vehicle::factory()->for($client)->for($this->workshop)->create(['make' => 'Ford']);

    actingAs($user)
        ->get(route('vehicles.index', ['sort' => 'make', 'direction' => 'asc']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/index')
            ->where('vehicles.data.0.make', 'Ford')
            ->where('vehicles.data.1.make', 'Honda')
            ->where('vehicles.data.2.make', 'Toyota')
            ->where('filters.sort', 'make')
            ->where('filters.direction', 'asc')
        );
});

test('sort parameter orders vehicles correctly by year', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    Vehicle::factory()->for($client)->for($this->workshop)->create(['year' => 2020]);
    Vehicle::factory()->for($client)->for($this->workshop)->create(['year' => 2018]);
    Vehicle::factory()->for($client)->for($this->workshop)->create(['year' => 2022]);

    actingAs($user)
        ->get(route('vehicles.index', ['sort' => 'year', 'direction' => 'desc']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/index')
            ->where('vehicles.data.0.year', 2022)
            ->where('vehicles.data.1.year', 2020)
            ->where('vehicles.data.2.year', 2018)
            ->where('filters.sort', 'year')
            ->where('filters.direction', 'desc')
        );
});

test('invalid sort parameter fails validation', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('vehicles.index', ['sort' => 'invalid_column']))
        ->assertSessionHasErrors('sort');
});

test('invalid direction parameter fails validation', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('vehicles.index', ['direction' => 'invalid']))
        ->assertSessionHasErrors('direction');
});

test('vehicles are scoped to current workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $client1 = Client::factory()->for($this->workshop)->create();
    $client2 = Client::factory()->for($otherWorkshop)->create();

    Vehicle::factory()->for($client1)->for($this->workshop)->create(['make' => 'Toyota']);
    Vehicle::factory()->for($client2)->for($otherWorkshop)->create(['make' => 'Honda']);

    actingAs($user)
        ->get(route('vehicles.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/index')
            ->has('vehicles.data', 1)
            ->where('vehicles.data.0.make', 'Toyota')
        );
});

test('vehicles list shows repair orders count', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('vehicles.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/index')
            ->has('vehicles.data', 1)
            ->where('vehicles.data.0.repair_orders_count', 0)
        );
});

test('user without role cannot access vehicles list', function () {
    $user = User::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('vehicles.index'))
        ->assertForbidden();
});
