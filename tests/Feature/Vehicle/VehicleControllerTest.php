<?php

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
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
            ->has('tableData.data')
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
            ->has('tableData.data')
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
            ->has('tableData.data', 3)
            ->has('tableData.data.0', fn ($vehicle) => $vehicle
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
                ->etc()
            )
            ->has('tableData.links')
            ->has('tableData.current_page')
            ->has('tableData.per_page')
            ->has('tableData.total')
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
            ->has('tableData.data', 1)
            ->where('tableData.data.0.make', 'Toyota')
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
            ->has('tableData.data', 1)
            ->where('tableData.data.0.model', 'Civic')
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
            ->has('tableData.data', 1)
            ->where('tableData.data.0.registration_number', 'ABC123')
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
            ->has('tableData.data', 1)
            ->where('tableData.data.0.vin', '1HGBH41JXMN109186')
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
            ->where('tableData.data.0.make', 'Ford')
            ->where('tableData.data.1.make', 'Honda')
            ->where('tableData.data.2.make', 'Toyota')
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
            ->where('tableData.data.0.year', 2022)
            ->where('tableData.data.1.year', 2020)
            ->where('tableData.data.2.year', 2018)
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
            ->has('tableData.data', 1)
            ->where('tableData.data.0.make', 'Toyota')
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
            ->has('tableData.data', 1)
            ->where('tableData.data.0.repair_orders_count', 0)
        );
});

test('user without role cannot access vehicles list', function () {
    $user = User::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('vehicles.index'))
        ->assertForbidden();
});

test('guests are redirected to login page when accessing create', function () {
    get(route('vehicles.create'))->assertRedirect(route('login'));
});

test('authenticated users without proper role are forbidden from create', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->get(route('vehicles.create'))
        ->assertForbidden();
});

test('user without role cannot access vehicles create', function () {
    $user = User::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('vehicles.create'))
        ->assertForbidden();
});

test('owner can view create vehicle form', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('vehicles.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/create')
            ->has('clients')
            ->has('preselectedClientId')
        );
});

test('office can view create vehicle form', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    actingAs($user)
        ->get(route('vehicles.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/create')
            ->has('clients')
            ->has('preselectedClientId')
        );
});

test('create form includes all clients from workshop', function () {
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

    actingAs($user)
        ->get(route('vehicles.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/create')
            ->has('clients', 2)
            ->has('clients.0', fn ($client) => $client
                ->has('id')
                ->has('name')
            )
        );
});

test('create form clients are sorted by last name then first name', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    Client::factory()->for($this->workshop)->create([
        'first_name' => 'Zbigniew',
        'last_name' => 'Kowalski',
    ]);
    Client::factory()->for($this->workshop)->create([
        'first_name' => 'Anna',
        'last_name' => 'Nowak',
    ]);
    Client::factory()->for($this->workshop)->create([
        'first_name' => 'Adam',
        'last_name' => 'Kowalski',
    ]);

    actingAs($user)
        ->get(route('vehicles.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/create')
            ->has('clients', 3)
            ->where('clients.0.name', 'Adam Kowalski')
            ->where('clients.1.name', 'Zbigniew Kowalski')
            ->where('clients.2.name', 'Anna Nowak')
        );
});

test('create form only includes clients from current workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();

    Client::factory()->for($this->workshop)->create(['first_name' => 'John', 'last_name' => 'Doe']);
    Client::factory()->for($otherWorkshop)->create(['first_name' => 'Jane', 'last_name' => 'Smith']);

    actingAs($user)
        ->get(route('vehicles.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/create')
            ->has('clients', 1)
            ->where('clients.0.name', 'John Doe')
        );
});

test('create form accepts preselected_client_id parameter', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('vehicles.create', ['preselected_client_id' => $client->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/create')
            ->where('preselectedClientId', $client->id)
        );
});

test('create form preselected_client_id is null when not provided', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('vehicles.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/create')
            ->where('preselectedClientId', null)
        );
});

test('create form validates preselected_client_id must be integer', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('vehicles.create', ['preselected_client_id' => 'not-an-integer']))
        ->assertSessionHasErrors('preselected_client_id');
});

test('create form validates preselected_client_id must exist in database', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('vehicles.create', ['preselected_client_id' => 99999]))
        ->assertSessionHasErrors('preselected_client_id');
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
