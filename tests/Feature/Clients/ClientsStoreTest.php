<?php

use App\Models\Client;
use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

const VALID_DATA = [
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
    $this->workshop = Workshop::factory()->create();
    $this->otherWorkshop = Workshop::factory()->create();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('unauthenticated users are redirected to login', function () {
    post(route('clients.store'), VALID_DATA)
        ->assertRedirect(route('login'));
});

test('authenticated user with Owner role can store client', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Owner');

    $response = actingAs($user)
        ->post(route('clients.store'), VALID_DATA);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Klient został dodany');

    assertDatabaseHas('clients', [
        'workshop_id' => $this->workshop->id,
        'last_name' => 'Kowalski',
        'first_name' => 'Jan',
        'phone_number' => '+48123456789',
        'email' => 'jan.kowalski@example.com',
    ]);
});

test('authenticated user with Office role can store client', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Office');

    $response = actingAs($user)
        ->post(route('clients.store'), VALID_DATA);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Klient został dodany');

    assertDatabaseHas('clients', [
        'workshop_id' => $this->workshop->id,
        'last_name' => 'Kowalski',
        'first_name' => 'Jan',
    ]);
});

test('authenticated user with Mechanic role cannot store client', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Mechanic');

    actingAs($user)
        ->post(route('clients.store'), VALID_DATA)
        ->assertForbidden();
});

test('authenticated user without any role cannot store client', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);

    actingAs($user)
        ->post(route('clients.store'), VALID_DATA)
        ->assertForbidden();
});

test('client is automatically associated with user workshop', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('clients.store'), VALID_DATA);

    $client = Client::where('last_name', 'Kowalski')->first();

    expect($client->workshop_id)->toBe($this->workshop->id);
});

test('validation fails when last_name is missing', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Owner');

    $invalidData = VALID_DATA;
    unset($invalidData['last_name']);

    actingAs($user)
        ->post(route('clients.store'), $invalidData)
        ->assertSessionHasErrors(['last_name']);
});

test('validation fails when first_name is missing', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Owner');

    $invalidData = VALID_DATA;
    unset($invalidData['first_name']);

    actingAs($user)
        ->post(route('clients.store'), $invalidData)
        ->assertSessionHasErrors(['first_name']);
});

test('validation fails when phone_number is missing', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Owner');

    $invalidData = VALID_DATA;
    unset($invalidData['phone_number']);

    actingAs($user)
        ->post(route('clients.store'), $invalidData)
        ->assertSessionHasErrors(['phone_number']);
});

test('validation fails when email is invalid', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Owner');

    $invalidData = VALID_DATA;
    $invalidData['email'] = 'invalid-email';

    actingAs($user)
        ->post(route('clients.store'), $invalidData)
        ->assertSessionHasErrors(['email']);
});

test('can store client with minimal required data', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Owner');

    $minimalData = [
        'last_name' => 'Nowak',
        'first_name' => 'Anna',
        'phone_number' => '+48987654321',
    ];

    $response = actingAs($user)
        ->post(route('clients.store'), $minimalData);

    $response->assertRedirect();

    assertDatabaseHas('clients', [
        'workshop_id' => $this->workshop->id,
        'last_name' => 'Nowak',
        'first_name' => 'Anna',
        'phone_number' => '+48987654321',
        'email' => null,
    ]);
});

test('redirects to clients.index after successful store', function () {
    $user = User::factory()->create(['workshop_id' => $this->workshop->id]);
    $user->assignRole('Owner');

    $response = actingAs($user)
        ->post(route('clients.store'), VALID_DATA);

    $response->assertRedirect(route('clients.index'));
    $response->assertSessionHas('success', 'Klient został dodany');
});
