<?php

use App\Models\Client;
use App\Models\User;
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
