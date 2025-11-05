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

test('guests are redirected to login when attempting to delete repair order', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $this->delete(route('repair-orders.destroy', $repairOrder))
        ->assertRedirect(route('login'));
});

test('user without proper role cannot delete repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->delete(route('repair-orders.destroy', $repairOrder))
        ->assertForbidden();

    assertDatabaseHas('repair_orders', ['id' => $repairOrder->id, 'deleted_at' => null]);
});

test('owner can delete repair order without time entries', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->delete(route('repair-orders.destroy', $repairOrder))
        ->assertRedirect(route('repair-orders.index'))
        ->assertSessionHas('success', __('repair_orders.messages.deleted'));

    $this->assertSoftDeleted('repair_orders', ['id' => $repairOrder->id]);
});

test('cannot delete repair order with time entries', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $mechanic = Mechanic::factory()->for($this->workshop)->create();
    TimeEntry::factory()->for($repairOrder)->for($mechanic)->create();

    actingAs($user)
        ->delete(route('repair-orders.destroy', $repairOrder))
        ->assertRedirect()
        ->assertSessionHas('error', __('repair_orders.messages.cannot_delete_with_time_entries'));

    assertDatabaseHas('repair_orders', ['id' => $repairOrder->id, 'deleted_at' => null]);
});

test('user cannot delete repair order from another workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherClient = Client::factory()->for($otherWorkshop)->create();
    $otherVehicle = Vehicle::factory()->for($otherClient)->for($otherWorkshop)->create();
    $otherRepairOrder = RepairOrder::factory()->for($otherVehicle)->for($otherWorkshop)->create();

    actingAs($user)
        ->delete(route('repair-orders.destroy', $otherRepairOrder))
        ->assertNotFound();
});

test('delete returns 404 for non-existent repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->delete(route('repair-orders.destroy', 99999))
        ->assertNotFound();
});

test('user without role cannot delete repair order', function () {
    $user = User::factory()->for($this->workshop)->create();

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->delete(route('repair-orders.destroy', $repairOrder))
        ->assertForbidden();
});
