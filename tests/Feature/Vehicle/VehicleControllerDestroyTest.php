<?php

use App\Models\Client;
use App\Models\RepairOrder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('owner can delete a vehicle without repair orders', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->delete(route('vehicles.destroy', $vehicle))
        ->assertRedirect(route('clients.show', $client))
        ->assertSessionHas('success', 'Pojazd został usunięty');

    $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
});

test('office cannot delete vehicle', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->delete(route('vehicles.destroy', $vehicle))
        ->assertForbidden();

    assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'deleted_at' => null]);
});

test('cannot delete vehicle with active repair orders', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->delete(route('vehicles.destroy', $vehicle))
        ->assertRedirect()
        ->assertSessionHas('error', 'Nie można usunąć pojazdu z aktywnymi zleceniami');

    assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'deleted_at' => null]);
});

test('unauthenticated user cannot delete vehicle', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    $this->delete(route('vehicles.destroy', $vehicle))
        ->assertRedirect(route('login'));

    assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'deleted_at' => null]);
});
