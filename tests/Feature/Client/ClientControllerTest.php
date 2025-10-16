<?php

use App\Models\Client;
use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();
});

test('guests are redirected to login page', function () {
    get(route('clients.index'))->assertRedirect(route('login'));
});

test('authenticated users without proper role are forbidden', function () {
    $mechanicRole = Role::firstOrCreate(['name' => 'Mechanic']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($mechanicRole);

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
            ->has('clients.data')
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
            ->has('clients.data')
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
            ->has('clients.data', 3)
            ->has('clients.data.0', fn ($client) => $client
                ->has('id')
                ->has('first_name')
                ->has('last_name')
                ->has('phone_number')
                ->has('email')
                ->has('vehicles_count')
            )
            ->has('clients.links')
            ->has('clients.current_page')
            ->has('clients.per_page')
            ->has('clients.total')
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
            ->has('clients.data', 1)
            ->where('clients.data.0.first_name', 'John')
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
            ->has('clients.data', 1)
            ->where('clients.data.0.email', 'john@example.com')
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
            ->where('clients.data.0.first_name', 'Alice')
            ->where('clients.data.1.first_name', 'Bob')
            ->where('clients.data.2.first_name', 'Charlie')
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

test('guests cannot access client edit page', function () {
    $client = Client::factory()->for($this->workshop)->create();

    get(route('clients.edit', $client))->assertRedirect(route('login'));
});

test('authenticated users without proper role cannot access client edit page', function () {
    $mechanicRole = Role::firstOrCreate(['name' => 'Mechanic']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($mechanicRole);

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('clients.edit', $client))
        ->assertForbidden();
});

test('owner can access client edit page', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('clients.edit', $client))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clients/edit')
            ->has('client')
            ->where('client.id', $client->id)
            ->where('client.first_name', $client->first_name)
            ->where('client.last_name', $client->last_name)
            ->where('client.phone_number', $client->phone_number)
            ->where('client.email', $client->email)
            ->where('client.address_street', $client->address_street)
            ->where('client.address_city', $client->address_city)
            ->where('client.address_postal_code', $client->address_postal_code)
            ->where('client.address_country', $client->address_country)
        );
});

test('office can access client edit page', function () {
    $officeRole = Role::firstOrCreate(['name' => 'Office']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($officeRole);

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('clients.edit', $client))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clients/edit')
            ->has('client')
            ->where('client.id', $client->id)
        );
});

test('accessing non-existent client returns 404', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    actingAs($user)
        ->get(route('clients.edit', 99999))
        ->assertNotFound();
});

test('guests cannot update client', function () {
    $client = Client::factory()->for($this->workshop)->create();

    put(route('clients.update', $client), [
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'phone_number' => '123456789',
    ])->assertRedirect(route('login'));
});

test('authenticated users without proper role cannot update client', function () {
    $mechanicRole = Role::firstOrCreate(['name' => 'Mechanic']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($mechanicRole);

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->put(route('clients.update', $client), [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'phone_number' => '123456789',
        ])
        ->assertForbidden();
});

test('owner can update client', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    $client = Client::factory()->for($this->workshop)->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone_number' => '111222333',
        'email' => 'john@example.com',
    ]);

    actingAs($user)
        ->put(route('clients.update', $client), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone_number' => '999888777',
            'email' => 'jane@example.com',
            'address_street' => '123 Main St',
            'address_city' => 'New York',
            'address_postal_code' => '10001',
            'address_country' => 'USA',
        ])
        ->assertRedirect(route('clients.index'))
        ->assertSessionHas('success', 'Klient został zaktualizowany');

    $client->refresh();

    expect($client->first_name)->toBe('Jane');
    expect($client->last_name)->toBe('Smith');
    expect($client->phone_number)->toBe('999888777');
    expect($client->email)->toBe('jane@example.com');
    expect($client->address_street)->toBe('123 Main St');
    expect($client->address_city)->toBe('New York');
    expect($client->address_postal_code)->toBe('10001');
    expect($client->address_country)->toBe('USA');
});

test('office can update client', function () {
    $officeRole = Role::firstOrCreate(['name' => 'Office']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($officeRole);

    $client = Client::factory()->for($this->workshop)->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    actingAs($user)
        ->put(route('clients.update', $client), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone_number' => '999888777',
        ])
        ->assertRedirect(route('clients.index'))
        ->assertSessionHas('success');

    expect($client->fresh()->first_name)->toBe('Jane');
});

test('updating non-existent client returns 404', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    actingAs($user)
        ->put(route('clients.update', 99999), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone_number' => '123456789',
        ])
        ->assertNotFound();
});

test('update validation fails for missing required fields', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->put(route('clients.update', $client), [])
        ->assertSessionHasErrors(['first_name', 'last_name', 'phone_number']);
});

test('update validation fails for invalid email', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->put(route('clients.update', $client), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone_number' => '123456789',
            'email' => 'invalid-email',
        ])
        ->assertSessionHasErrors('email');
});

test('update validation fails for fields exceeding max length', function () {
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole($ownerRole);

    $client = Client::factory()->for($this->workshop)->create();

    actingAs($user)
        ->put(route('clients.update', $client), [
            'first_name' => str_repeat('a', 256),
            'last_name' => 'Doe',
            'phone_number' => '123456789',
        ])
        ->assertSessionHasErrors('first_name');
});
