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

// ============================================================================
// INDEX TESTS
// ============================================================================

test('guests are redirected to login page when accessing index', function () {
    get(route('clients.index'))->assertRedirect(route('login'));
});

test('authenticated users without proper role are forbidden from index', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->get(route('clients.index'))
        ->assertForbidden();
});

test('owner can view clients list', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    actingAs($user)
        ->get(route('clients.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clients/index')
            ->has('tableData.data')
            ->has('filters')
        );
});

test('office can view clients list', function () {
    $officeRole = Role::firstOrCreate(['name' => 'Office']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($officeRole);

    actingAs($user)
        ->get(route('clients.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clients/index')
            ->has('tableData.data')
            ->has('filters')
        );
});

test('clients list includes pagination and vehicle count', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    $clients = Client::factory()->for($this->workshop)->count(3)->create();

    actingAs($user)
        ->get(route('clients.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clients/index')
            ->has('tableData.data', 3)
            ->has('tableData.data.0', fn ($client) => $client
                ->has('id')
                ->has('first_name')
                ->has('last_name')
                ->has('phone_number')
                ->has('email')
                ->has('vehicles_count')
            )
            ->has('tableData.links')
            ->has('tableData.current_page')
            ->has('tableData.per_page')
            ->has('tableData.total')
        );
});

test('search filters clients by name', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    Client::factory()->for($this->workshop)->create(['first_name' => 'John', 'last_name' => 'Doe']);
    Client::factory()->for($this->workshop)->create(['first_name' => 'Jane', 'last_name' => 'Smith']);

    actingAs($user)
        ->get(route('clients.index', ['search' => 'John']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clients/index')
            ->has('tableData.data', 1)
            ->where('tableData.data.0.first_name', 'John')
            ->where('filters.search', 'John')
        );
});

test('search filters clients by email', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    Client::factory()->for($this->workshop)->create(['email' => 'john@example.com']);
    Client::factory()->for($this->workshop)->create(['email' => 'jane@example.com']);

    actingAs($user)
        ->get(route('clients.index', ['search' => 'john@']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clients/index')
            ->has('tableData.data', 1)
            ->where('tableData.data.0.email', 'john@example.com')
        );
});

test('sort parameter orders clients correctly', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    Client::factory()->for($this->workshop)->create(['first_name' => 'Charlie']);
    Client::factory()->for($this->workshop)->create(['first_name' => 'Alice']);
    Client::factory()->for($this->workshop)->create(['first_name' => 'Bob']);

    actingAs($user)
        ->get(route('clients.index', ['sort' => 'first_name', 'direction' => 'asc']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clients/index')
            ->where('tableData.data.0.first_name', 'Alice')
            ->where('tableData.data.1.first_name', 'Bob')
            ->where('tableData.data.2.first_name', 'Charlie')
            ->where('filters.sort', 'first_name')
            ->where('filters.direction', 'asc')
        );
});

test('invalid sort parameter fails validation', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    actingAs($user)
        ->get(route('clients.index', ['sort' => 'invalid_column']))
        ->assertSessionHasErrors('sort');
});

test('invalid direction parameter fails validation', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    actingAs($user)
        ->get(route('clients.index', ['direction' => 'invalid']))
        ->assertSessionHasErrors('direction');
});
