<?php

use App\Models\Client;
use App\Models\RepairOrder;
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

test('guests cannot view vehicle details', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    get(route('vehicles.show', $vehicle))
        ->assertRedirect(route('login'));
});

test('authenticated owner can view vehicle details from their workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('vehicles.show', $vehicle))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/show')
            ->has('vehicle')
            ->where('vehicle.id', $vehicle->id)
            ->where('vehicle.make', $vehicle->make)
            ->where('vehicle.model', $vehicle->model)
            ->where('vehicle.year', $vehicle->year)
            ->where('vehicle.registration_number', $vehicle->registration_number)
            ->where('vehicle.vin', $vehicle->vin)
            ->has('vehicle.client')
            ->where('vehicle.client.id', $client->id)
            ->has('repair_orders')
        );
});

test('authenticated office user can view vehicle details from their workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('vehicles.show', $vehicle))
        ->assertOk();
});

test('authenticated mechanic can view vehicle details from their workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('vehicles.show', $vehicle))
        ->assertOk();
});

test('authenticated user cannot view vehicle from another workshop', function () {
    $otherWorkshop = Workshop::factory()->create();

    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($otherWorkshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($otherWorkshop)->create();

    expect($user->can('view', $vehicle))->toBeFalse();
});

test('vehicle details page includes paginated repair orders', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    RepairOrder::factory()->count(3)->create([
        'workshop_id' => $this->workshop->id,
        'vehicle_id' => $vehicle->id,
    ]);

    actingAs($user)
        ->get(route('vehicles.show', $vehicle))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/show')
            ->has('repair_orders.data', 3)
            ->has('repair_orders.links')
        );
});

test('repair orders are sorted by creation date descending', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    $oldOrder = RepairOrder::factory()->create([
        'workshop_id' => $this->workshop->id,
        'vehicle_id' => $vehicle->id,
        'created_at' => now()->subDays(5),
    ]);

    $newOrder = RepairOrder::factory()->create([
        'workshop_id' => $this->workshop->id,
        'vehicle_id' => $vehicle->id,
        'created_at' => now()->subDays(1),
    ]);

    actingAs($user)
        ->get(route('vehicles.show', $vehicle))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/show')
            ->where('repair_orders.data.0.id', $newOrder->id)
            ->where('repair_orders.data.1.id', $oldOrder->id)
        );
});

test('vehicle show returns 404 for non-existent vehicle', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('vehicles.show', 99999))
        ->assertNotFound();
});
