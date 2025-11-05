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

test('guests are redirected to login when attempting to update repair order status', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    patch(route('repair-orders.update-status', $repairOrder), [
        'status' => RepairOrderStatus::IN_PROGRESS->value,
    ])->assertRedirect(route('login'));
});

test('user without proper role cannot update repair order status', function () {
    $user = User::factory()->for($this->workshop)->create();
    // User has no role assigned

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->patch(route('repair-orders.update-status', $repairOrder), [
            'status' => RepairOrderStatus::IN_PROGRESS->value,
        ])
        ->assertForbidden();
});

test('owner can update repair order status using dedicated endpoint', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'status' => RepairOrderStatus::NEW,
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update-status', $repairOrder), [
            'status' => RepairOrderStatus::IN_PROGRESS->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('repair_orders.messages.status_updated'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'status' => RepairOrderStatus::IN_PROGRESS->value,
    ]);
});

test('office can update repair order status using dedicated endpoint', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'status' => RepairOrderStatus::DIAGNOSIS,
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update-status', $repairOrder), [
            'status' => RepairOrderStatus::READY_FOR_PICKUP->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('repair_orders.messages.status_updated'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'status' => RepairOrderStatus::READY_FOR_PICKUP->value,
    ]);
});

test('updating status validates status is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->patch(route('repair-orders.update-status', $repairOrder), [])
        ->assertSessionHasErrors('status');
});

test('updating status validates status must be valid enum value', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->patch(route('repair-orders.update-status', $repairOrder), [
            'status' => 'invalid_status',
        ])
        ->assertSessionHasErrors('status');
});

test('user cannot update status of repair order from another workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherClient = Client::factory()->for($otherWorkshop)->create();
    $otherVehicle = Vehicle::factory()->for($otherClient)->for($otherWorkshop)->create();
    $otherRepairOrder = RepairOrder::factory()->for($otherVehicle)->for($otherWorkshop)->create();

    actingAs($user)
        ->patch(route('repair-orders.update-status', $otherRepairOrder), [
            'status' => RepairOrderStatus::CLOSED->value,
        ])
        ->assertNotFound();
});

test('update status returns 404 for non-existent repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->patch(route('repair-orders.update-status', 99999), [
            'status' => RepairOrderStatus::CLOSED->value,
        ])
        ->assertNotFound();
});

test('user without role cannot update repair order status', function () {
    $user = User::factory()->for($this->workshop)->create();

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->patch(route('repair-orders.update-status', $repairOrder), [
            'status' => RepairOrderStatus::IN_PROGRESS->value,
        ])
        ->assertForbidden();
});
