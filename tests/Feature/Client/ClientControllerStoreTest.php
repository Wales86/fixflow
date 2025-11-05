<?php

use App\Models\Client;
use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

const VALID_CLIENT_DATA = [
    'last_name' => 'Kowalski',
    'first_name' => 'Jan',
    'phone_number' => '+48123456789',
    'email' => 'jan.kowalski@example.com',
    'address_street' => 'ul. Testowa 123',
    'address_city' => 'Warszawa',
    'address_postal_code' => '00-001',
    'address_country' => 'Polska',
];

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

// ============================================================================
// STORE TESTS
// ============================================================================

test('guests are redirected to login when storing client', function () {
    post(route('clients.store'), VALID_CLIENT_DATA)
        ->assertRedirect(route('login'));
});

test('owner can store client', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $response = actingAs($user)
        ->post(route('clients.store'), VALID_CLIENT_DATA);

    $response->assertRedirect(route('clients.index'));
    $response->assertSessionHas('success', 'Klient został dodany');

    assertDatabaseHas('clients', [
        'workshop_id' => $this->workshop->id,
        'last_name' => 'Kowalski',
        'first_name' => 'Jan',
        'phone_number' => '+48123456789',
        'email' => 'jan.kowalski@example.com',
    ]);
});

test('office can store client', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $response = actingAs($user)
        ->post(route('clients.store'), VALID_CLIENT_DATA);

    $response->assertRedirect(route('clients.index'));
    $response->assertSessionHas('success', 'Klient został dodany');

    assertDatabaseHas('clients', [
        'workshop_id' => $this->workshop->id,
        'last_name' => 'Kowalski',
        'first_name' => 'Jan',
    ]);
});

test('mechanic cannot store client', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->post(route('clients.store'), VALID_CLIENT_DATA)
        ->assertForbidden();
});

test('user without role cannot store client', function () {
    $user = User::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('clients.store'), VALID_CLIENT_DATA)
        ->assertForbidden();
});

test('client is automatically associated with user workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('clients.store'), VALID_CLIENT_DATA);

    $client = Client::where('last_name', 'Kowalski')->first();

    expect($client->workshop_id)->toBe($this->workshop->id);
});

test('store validation fails when last_name is missing', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $invalidData = VALID_CLIENT_DATA;
    unset($invalidData['last_name']);

    actingAs($user)
        ->post(route('clients.store'), $invalidData)
        ->assertSessionHasErrors(['last_name']);
});

test('store validation fails when first_name is missing', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $invalidData = VALID_CLIENT_DATA;
    unset($invalidData['first_name']);

    actingAs($user)
        ->post(route('clients.store'), $invalidData)
        ->assertSessionHasErrors(['first_name']);
});

test('store validation fails when phone_number is missing', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $invalidData = VALID_CLIENT_DATA;
    unset($invalidData['phone_number']);

    actingAs($user)
        ->post(route('clients.store'), $invalidData)
        ->assertSessionHasErrors(['phone_number']);
});

test('store validation fails when email is invalid', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $invalidData = VALID_CLIENT_DATA;
    $invalidData['email'] = 'invalid-email';

    actingAs($user)
        ->post(route('clients.store'), $invalidData)
        ->assertSessionHasErrors(['email']);
});

test('can store client with minimal required data', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $minimalData = [
        'last_name' => 'Nowak',
        'first_name' => 'Anna',
        'phone_number' => '+48987654321',
    ];

    $response = actingAs($user)
        ->post(route('clients.store'), $minimalData);

    $response->assertRedirect(route('clients.index'));

    assertDatabaseHas('clients', [
        'workshop_id' => $this->workshop->id,
        'last_name' => 'Nowak',
        'first_name' => 'Anna',
        'phone_number' => '+48987654321',
        'email' => null,
    ]);
});
