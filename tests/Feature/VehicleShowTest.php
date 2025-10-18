<?php

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\RepairOrder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (UserRole::cases() as $role) {
        Role::create(['name' => $role->value]);
    }
});

test('guests cannot view vehicle details', function () {
    $workshop = Workshop::factory()->create();
    $client = Client::factory()->create(['workshop_id' => $workshop->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $workshop->id,
        'client_id' => $client->id,
    ]);

    $this->get(route('vehicles.show', $vehicle))
        ->assertRedirect(route('login'));
});

test('authenticated owner can view vehicle details from their workshop', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create(['workshop_id' => $workshop->id]);
    $user->assignRole('Owner');

    $client = Client::factory()->create(['workshop_id' => $workshop->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $workshop->id,
        'client_id' => $client->id,
    ]);

    $this->actingAs($user)
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
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create(['workshop_id' => $workshop->id]);
    $user->assignRole('Office');

    $client = Client::factory()->create(['workshop_id' => $workshop->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $workshop->id,
        'client_id' => $client->id,
    ]);

    $this->actingAs($user)
        ->get(route('vehicles.show', $vehicle))
        ->assertOk();
});

test('authenticated mechanic can view vehicle details from their workshop', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create(['workshop_id' => $workshop->id]);
    $user->assignRole('Mechanic');

    $client = Client::factory()->create(['workshop_id' => $workshop->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $workshop->id,
        'client_id' => $client->id,
    ]);

    $this->actingAs($user)
        ->get(route('vehicles.show', $vehicle))
        ->assertOk();
});

test('authenticated user cannot view vehicle from another workshop', function () {
    $workshop1 = Workshop::factory()->create();
    $workshop2 = Workshop::factory()->create();

    $user = User::factory()->create(['workshop_id' => $workshop1->id]);
    $user->assignRole('Owner');

    $client = Client::factory()->create(['workshop_id' => $workshop2->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $workshop2->id,
        'client_id' => $client->id,
    ]);

    expect($user->can('view', $vehicle))->toBeFalse();
});

test('vehicle details page includes paginated repair orders', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create(['workshop_id' => $workshop->id]);
    $user->assignRole('Owner');

    $client = Client::factory()->create(['workshop_id' => $workshop->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $workshop->id,
        'client_id' => $client->id,
    ]);

    RepairOrder::factory()->count(3)->create([
        'workshop_id' => $workshop->id,
        'vehicle_id' => $vehicle->id,
    ]);

    $this->actingAs($user)
        ->get(route('vehicles.show', $vehicle))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/show')
            ->has('repair_orders.data', 3)
            ->has('repair_orders.links')
        );
});

test('repair orders are sorted by creation date descending', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create(['workshop_id' => $workshop->id]);
    $user->assignRole('Owner');

    $client = Client::factory()->create(['workshop_id' => $workshop->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $workshop->id,
        'client_id' => $client->id,
    ]);

    $oldOrder = RepairOrder::factory()->create([
        'workshop_id' => $workshop->id,
        'vehicle_id' => $vehicle->id,
        'created_at' => now()->subDays(5),
    ]);

    $newOrder = RepairOrder::factory()->create([
        'workshop_id' => $workshop->id,
        'vehicle_id' => $vehicle->id,
        'created_at' => now()->subDays(1),
    ]);

    $this->actingAs($user)
        ->get(route('vehicles.show', $vehicle))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('vehicles/show')
            ->where('repair_orders.data.0.id', $newOrder->id)
            ->where('repair_orders.data.1.id', $oldOrder->id)
        );
});

test('vehicle show returns 404 for non-existent vehicle', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create(['workshop_id' => $workshop->id]);
    $user->assignRole('Owner');

    $this->actingAs($user)
        ->get(route('vehicles.show', 99999))
        ->assertNotFound();
});
