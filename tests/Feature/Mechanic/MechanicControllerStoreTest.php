<?php

use App\Models\Mechanic;
use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

const VALID_MECHANIC_DATA = [
    'first_name' => 'Jan',
    'last_name' => 'Kowalski',
    'is_active' => true,
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

test('guests are redirected to login when storing mechanic', function () {
    post(route('mechanics.store'), VALID_MECHANIC_DATA)
        ->assertRedirect(route('login'));
});

test('owner can store mechanic', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $response = actingAs($user)
        ->post(route('mechanics.store'), VALID_MECHANIC_DATA);

    $response->assertRedirect(route('mechanics.index'));
    $response->assertSessionHas('success', 'Mechanik został dodany');

    assertDatabaseHas('mechanics', [
        'workshop_id' => $this->workshop->id,
        'first_name' => 'Jan',
        'last_name' => 'Kowalski',
        'is_active' => true,
    ]);
});

test('office can store mechanic', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $response = actingAs($user)
        ->post(route('mechanics.store'), VALID_MECHANIC_DATA);

    $response->assertRedirect(route('mechanics.index'));
    $response->assertSessionHas('success', 'Mechanik został dodany');

    assertDatabaseHas('mechanics', [
        'workshop_id' => $this->workshop->id,
        'first_name' => 'Jan',
        'last_name' => 'Kowalski',
    ]);
});

test('mechanic cannot store mechanic', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->post(route('mechanics.store'), VALID_MECHANIC_DATA)
        ->assertForbidden();
});

test('user without role cannot store mechanic', function () {
    $user = User::factory()->for($this->workshop)->create();

    actingAs($user)
        ->post(route('mechanics.store'), VALID_MECHANIC_DATA)
        ->assertForbidden();
});

test('mechanic is automatically associated with user workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('mechanics.store'), VALID_MECHANIC_DATA);

    $mechanic = Mechanic::where('last_name', 'Kowalski')->first();

    expect($mechanic->workshop_id)->toBe($this->workshop->id);
});

test('store validation fails when first_name is missing', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $invalidData = VALID_MECHANIC_DATA;
    unset($invalidData['first_name']);

    actingAs($user)
        ->post(route('mechanics.store'), $invalidData)
        ->assertSessionHasErrors(['first_name']);
});

test('store validation fails when last_name is missing', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $invalidData = VALID_MECHANIC_DATA;
    unset($invalidData['last_name']);

    actingAs($user)
        ->post(route('mechanics.store'), $invalidData)
        ->assertSessionHasErrors(['last_name']);
});

test('store validation fails when first_name exceeds max length', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $invalidData = VALID_MECHANIC_DATA;
    $invalidData['first_name'] = str_repeat('a', 256);

    actingAs($user)
        ->post(route('mechanics.store'), $invalidData)
        ->assertSessionHasErrors(['first_name']);
});

test('store validation fails when last_name exceeds max length', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $invalidData = VALID_MECHANIC_DATA;
    $invalidData['last_name'] = str_repeat('a', 256);

    actingAs($user)
        ->post(route('mechanics.store'), $invalidData)
        ->assertSessionHasErrors(['last_name']);
});

test('store validation fails when is_active is not boolean', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $invalidData = VALID_MECHANIC_DATA;
    $invalidData['is_active'] = 'invalid';

    actingAs($user)
        ->post(route('mechanics.store'), $invalidData)
        ->assertSessionHasErrors(['is_active']);
});

test('can store mechanic with minimal required data', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $minimalData = [
        'first_name' => 'Anna',
        'last_name' => 'Nowak',
    ];

    $response = actingAs($user)
        ->post(route('mechanics.store'), $minimalData);

    $response->assertRedirect(route('mechanics.index'));

    assertDatabaseHas('mechanics', [
        'workshop_id' => $this->workshop->id,
        'first_name' => 'Anna',
        'last_name' => 'Nowak',
        'is_active' => true,
    ]);
});

test('can store inactive mechanic', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $data = [
        'first_name' => 'Piotr',
        'last_name' => 'Wiśniewski',
        'is_active' => false,
    ];

    $response = actingAs($user)
        ->post(route('mechanics.store'), $data);

    $response->assertRedirect(route('mechanics.index'));

    assertDatabaseHas('mechanics', [
        'workshop_id' => $this->workshop->id,
        'first_name' => 'Piotr',
        'last_name' => 'Wiśniewski',
        'is_active' => false,
    ]);
});
