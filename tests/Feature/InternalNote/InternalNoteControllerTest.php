<?php

use App\Models\Client;
use App\Models\RepairOrder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('guests are redirected to login when attempting to store internal note', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    post(route('internal-notes.store'), [
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Internal note content',
    ])->assertRedirect(route('login'));
});

test('user without proper role cannot store internal note', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => RepairOrder::class,
            'notable_id' => $repairOrder->id,
            'content' => 'Internal note content',
        ])
        ->assertForbidden();
});

test('owner can store an internal note for repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => RepairOrder::class,
            'notable_id' => $repairOrder->id,
            'content' => 'This is an internal note for the repair order',
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('internal_notes.messages.created'));

    assertDatabaseHas('internal_notes', [
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'This is an internal note for the repair order',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);
});

test('office can store an internal note for repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => RepairOrder::class,
            'notable_id' => $repairOrder->id,
            'content' => 'Office note about the order',
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('internal_notes.messages.created'));

    assertDatabaseHas('internal_notes', [
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Office note about the order',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);
});

test('storing internal note validates content is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => RepairOrder::class,
            'notable_id' => $repairOrder->id,
        ])
        ->assertSessionHasErrors('content');
});

test('storing internal note validates content has max length of 5000 characters', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => RepairOrder::class,
            'notable_id' => $repairOrder->id,
            'content' => str_repeat('a', 5001),
        ])
        ->assertSessionHasErrors('content');
});

test('user cannot store internal note for repair order from another workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherClient = Client::factory()->for($otherWorkshop)->create();
    $otherVehicle = Vehicle::factory()->for($otherClient)->for($otherWorkshop)->create();
    $otherRepairOrder = RepairOrder::factory()->for($otherVehicle)->for($otherWorkshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => RepairOrder::class,
            'notable_id' => $otherRepairOrder->id,
            'content' => 'Trying to create note for another workshop',
        ])
        ->assertForbidden();
});

test('storing internal note validates notable_type is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_id' => $repairOrder->id,
            'content' => 'Some content',
        ])
        ->assertSessionHasErrors('notable_type');
});

test('storing internal note validates notable_id is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => RepairOrder::class,
            'content' => 'Some content',
        ])
        ->assertSessionHasErrors('notable_id');
});

test('storing internal note returns 404 for non-existent repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => RepairOrder::class,
            'notable_id' => 99999,
            'content' => 'Some content',
        ])
        ->assertNotFound();
});

test('storing internal note validates content must be string', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => RepairOrder::class,
            'notable_id' => $repairOrder->id,
            'content' => 12345,
        ])
        ->assertSessionHasErrors('content');
});

test('user without role cannot store internal note', function () {
    $user = User::factory()->for($this->workshop)->create();

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => RepairOrder::class,
            'notable_id' => $repairOrder->id,
            'content' => 'Some content',
        ])
        ->assertForbidden();
});
