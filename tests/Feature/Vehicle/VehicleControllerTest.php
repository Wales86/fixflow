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
