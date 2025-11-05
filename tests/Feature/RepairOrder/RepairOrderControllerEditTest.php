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

test('guests are redirected to login when accessing edit', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    get(route('repair-orders.edit', $repairOrder))->assertRedirect(route('login'));
});

test('authenticated users without proper role are forbidden from edit', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.edit', $repairOrder))
        ->assertForbidden();
});

test('owner can view repair order edit form', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.edit', $repairOrder))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/edit')
            ->has('repairOrder')
            ->has('repairOrder.id')
            ->has('repairOrder.vehicle_id')
            ->has('repairOrder.status')
            ->has('repairOrder.problem_description')
            ->has('repairOrder.images')
            ->has('vehicles')
            ->has('statuses')
        );
});

test('office can view repair order edit form', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.edit', $repairOrder))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/edit')
            ->has('repairOrder')
            ->has('vehicles')
            ->has('statuses')
        );
});

test('edit form returns repair order with images', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.edit', $repairOrder))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/edit')
            ->where('repairOrder.id', $repairOrder->id)
            ->where('repairOrder.vehicle_id', $repairOrder->vehicle_id)
            ->where('repairOrder.status', $repairOrder->status->value)
            ->where('repairOrder.problem_description', $repairOrder->problem_description)
            ->has('repairOrder.images')
        );
});

test('edit form includes vehicles from current workshop only', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client1 = Client::factory()->for($this->workshop)->create();
    $vehicle1 = Vehicle::factory()->for($client1)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle1)->for($this->workshop)->create();

    $otherWorkshop = Workshop::factory()->create();
    $client2 = Client::factory()->for($otherWorkshop)->create();
    $vehicle2 = Vehicle::factory()->for($client2)->for($otherWorkshop)->create();

    actingAs($user)
        ->get(route('repair-orders.edit', $repairOrder))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/edit')
            ->has('vehicles', 1)
            ->where('vehicles.0.id', $vehicle1->id)
        );
});

test('edit form includes all repair order statuses', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.edit', $repairOrder))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/edit')
            ->has('statuses', 7)
            ->has('statuses.0', fn ($status) => $status
                ->has('value')
                ->has('label')
            )
        );
});

test('user cannot edit repair order from another workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherClient = Client::factory()->for($otherWorkshop)->create();
    $otherVehicle = Vehicle::factory()->for($otherClient)->for($otherWorkshop)->create();
    $otherRepairOrder = RepairOrder::factory()->for($otherVehicle)->for($otherWorkshop)->create();

    actingAs($user)
        ->get(route('repair-orders.edit', $otherRepairOrder))
        ->assertNotFound();
});

test('edit returns 404 for non-existent repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('repair-orders.edit', 99999))
        ->assertNotFound();
});

test('user without role cannot access edit form', function () {
    $user = User::factory()->for($this->workshop)->create();

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.edit', $repairOrder))
        ->assertForbidden();
});
