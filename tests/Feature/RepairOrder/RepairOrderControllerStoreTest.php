<?php

use App\Enums\RepairOrderStatus;
use App\Models\Client;
use App\Models\Mechanic;
use App\Models\RepairOrder;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('guests are redirected to login when attempting to store repair order', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    post(route('repair-orders.store'), [
        'vehicle_id' => $vehicle->id,
        'description' => 'Engine not starting',
    ])->assertRedirect(route('login'));
});

test('user without proper role cannot store repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $vehicle->id,
            'description' => 'Engine not starting',
        ])
        ->assertForbidden();
});

test('owner can store a repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $vehicle->id,
            'description' => 'Engine not starting properly',
        ])
        ->assertRedirect(route('repair-orders.index'))
        ->assertSessionHas('success', __('repair_orders.messages.created'));

    assertDatabaseHas('repair_orders', [
        'workshop_id' => $this->workshop->id,
        'vehicle_id' => $vehicle->id,
        'problem_description' => 'Engine not starting properly',
        'status' => RepairOrderStatus::NEW->value,
    ]);
});

test('office can store a repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $vehicle->id,
            'description' => 'Brake pads replacement needed',
        ])
        ->assertRedirect(route('repair-orders.index'))
        ->assertSessionHas('success', __('repair_orders.messages.created'));

    assertDatabaseHas('repair_orders', [
        'workshop_id' => $this->workshop->id,
        'vehicle_id' => $vehicle->id,
        'problem_description' => 'Brake pads replacement needed',
        'status' => RepairOrderStatus::NEW->value,
    ]);
});

test('storing repair order validates vehicle_id is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'description' => 'Engine problem',
        ])
        ->assertSessionHasErrors('vehicle_id');
});

test('storing repair order validates description is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $vehicle->id,
        ])
        ->assertSessionHasErrors('description');
});

test('storing repair order validates vehicle_id must exist in current workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherClient = Client::factory()->for($otherWorkshop)->create();
    $otherVehicle = Vehicle::factory()->for($otherClient)->for($otherWorkshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $otherVehicle->id,
            'description' => 'Engine problem',
        ])
        ->assertSessionHasErrors('vehicle_id');
});

test('storing repair order validates vehicle_id must be integer', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => 'not-an-integer',
            'description' => 'Engine problem',
        ])
        ->assertSessionHasErrors('vehicle_id');
});

test('storing repair order validates vehicle_id must exist', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => 99999,
            'description' => 'Engine problem',
        ])
        ->assertSessionHasErrors('vehicle_id');
});

test('storing repair order validates description must be string', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $vehicle->id,
            'description' => 12345,
        ])
        ->assertSessionHasErrors('description');
});

test('user without role cannot store repair order', function () {
    $user = User::factory()->for($this->workshop)->create();

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $vehicle->id,
            'description' => 'Engine problem',
        ])
        ->assertForbidden();
});
