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

test('guests are redirected to login when attempting to update repair order', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    patch(route('repair-orders.update', $repairOrder), [
        'description' => 'Updated description',
    ])->assertRedirect(route('login'));
});

test('user without proper role cannot update repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'description' => 'Updated description',
        ])
        ->assertForbidden();
});

test('owner can update repair order description', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'problem_description' => 'Original description',
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'description' => 'Updated description',
        ])
        ->assertRedirect(route('repair-orders.index'))
        ->assertSessionHas('success', __('repair_orders.messages.updated'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'problem_description' => 'Updated description',
    ]);
});

test('office can update repair order description', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'problem_description' => 'Original description',
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'description' => 'Updated description',
        ])
        ->assertRedirect(route('repair-orders.index'))
        ->assertSessionHas('success', __('repair_orders.messages.updated'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'problem_description' => 'Updated description',
    ]);
});

test('owner can update repair order status', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'status' => RepairOrderStatus::NEW,
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'status' => RepairOrderStatus::IN_PROGRESS->value,
        ])
        ->assertRedirect(route('repair-orders.index'))
        ->assertSessionHas('success', __('repair_orders.messages.updated'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'status' => RepairOrderStatus::IN_PROGRESS->value,
    ]);
});

test('owner can update both description and status', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'problem_description' => 'Original description',
        'status' => RepairOrderStatus::NEW,
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'description' => 'Updated description',
            'status' => RepairOrderStatus::DIAGNOSIS->value,
        ])
        ->assertRedirect(route('repair-orders.index'))
        ->assertSessionHas('success', __('repair_orders.messages.updated'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'problem_description' => 'Updated description',
        'status' => RepairOrderStatus::DIAGNOSIS->value,
    ]);
});

test('partial update with only description works', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'problem_description' => 'Original description',
        'status' => RepairOrderStatus::IN_PROGRESS,
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'description' => 'Only description updated',
        ])
        ->assertRedirect(route('repair-orders.index'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'problem_description' => 'Only description updated',
        'status' => RepairOrderStatus::IN_PROGRESS->value,
    ]);
});

test('partial update with only status works', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'problem_description' => 'Original description',
        'status' => RepairOrderStatus::NEW,
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'status' => RepairOrderStatus::CLOSED->value,
        ])
        ->assertRedirect(route('repair-orders.index'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'problem_description' => 'Original description',
        'status' => RepairOrderStatus::CLOSED->value,
    ]);
});

test('updating repair order validates status must be valid enum value', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'status' => 'invalid_status',
        ])
        ->assertSessionHasErrors('status');
});

test('updating repair order validates description must be string', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'description' => 12345,
        ])
        ->assertSessionHasErrors('description');
});

test('user cannot update repair order from another workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherClient = Client::factory()->for($otherWorkshop)->create();
    $otherVehicle = Vehicle::factory()->for($otherClient)->for($otherWorkshop)->create();
    $otherRepairOrder = RepairOrder::factory()->for($otherVehicle)->for($otherWorkshop)->create();

    actingAs($user)
        ->patch(route('repair-orders.update', $otherRepairOrder), [
            'description' => 'Trying to update',
        ])
        ->assertNotFound();
});

test('update returns 404 for non-existent repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->patch(route('repair-orders.update', 99999), [
            'description' => 'Trying to update',
        ])
        ->assertNotFound();
});

test('user without role cannot update repair order', function () {
    $user = User::factory()->for($this->workshop)->create();

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'description' => 'Updated description',
        ])
        ->assertForbidden();
});
