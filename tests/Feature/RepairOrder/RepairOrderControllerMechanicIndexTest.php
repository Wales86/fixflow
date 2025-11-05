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

// Mechanic Index Endpoint Tests

test('guests are redirected to login when accessing mechanic index', function () {
    get(route('repair-orders.mechanic'))->assertRedirect(route('login'));
});

test('mechanic can view active repair orders', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::NEW]);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::IN_PROGRESS]);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::CLOSED]);

    actingAs($user)
        ->get(route('repair-orders.mechanic'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/mechanic-index')
            ->has('orders', 2)
            ->has('search')
        );
});

test('owner cannot view mechanic index', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('repair-orders.mechanic'))
        ->assertForbidden();
});

test('office cannot view mechanic index', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    actingAs($user)
        ->get(route('repair-orders.mechanic'))
        ->assertForbidden();
});

test('mechanic index excludes closed repair orders', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $activeOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::NEW]);
    $closedOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::CLOSED]);

    actingAs($user)
        ->get(route('repair-orders.mechanic'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/mechanic-index')
            ->has('orders', 1)
            ->where('orders.0.id', $activeOrder->id)
        );
});

test('mechanic index includes vehicle and client data', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create([
        'make' => 'Toyota',
        'model' => 'Corolla',
        'registration_number' => 'ABC123',
    ]);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::NEW]);

    actingAs($user)
        ->get(route('repair-orders.mechanic'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/mechanic-index')
            ->has('orders', 1)
            ->where('orders.0.vehicle.make', 'Toyota')
            ->where('orders.0.vehicle.model', 'Corolla')
            ->where('orders.0.vehicle.registration_number', 'ABC123')
            ->where('orders.0.client.first_name', 'John')
            ->where('orders.0.client.last_name', 'Doe')
        );
});

test('mechanic index search filters by vehicle registration', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle1 = Vehicle::factory()->for($client)->for($this->workshop)->create(['registration_number' => 'ABC123']);
    $vehicle2 = Vehicle::factory()->for($client)->for($this->workshop)->create(['registration_number' => 'XYZ789']);
    RepairOrder::factory()->for($vehicle1)->for($this->workshop)->create(['status' => RepairOrderStatus::NEW]);
    RepairOrder::factory()->for($vehicle2)->for($this->workshop)->create(['status' => RepairOrderStatus::NEW]);

    actingAs($user)
        ->get(route('repair-orders.mechanic', ['search' => 'ABC']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/mechanic-index')
            ->has('orders', 1)
            ->where('orders.0.vehicle.registration_number', 'ABC123')
            ->where('search', 'ABC')
        );
});

test('mechanic index search filters by client name', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client1 = Client::factory()->for($this->workshop)->create(['last_name' => 'Smith']);
    $client2 = Client::factory()->for($this->workshop)->create(['last_name' => 'Johnson']);
    $vehicle1 = Vehicle::factory()->for($client1)->for($this->workshop)->create();
    $vehicle2 = Vehicle::factory()->for($client2)->for($this->workshop)->create();
    RepairOrder::factory()->for($vehicle1)->for($this->workshop)->create(['status' => RepairOrderStatus::NEW]);
    RepairOrder::factory()->for($vehicle2)->for($this->workshop)->create(['status' => RepairOrderStatus::NEW]);

    actingAs($user)
        ->get(route('repair-orders.mechanic', ['search' => 'Smith']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/mechanic-index')
            ->has('orders', 1)
            ->where('orders.0.client.last_name', 'Smith')
        );
});

test('mechanic index search filters by problem description', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'status' => RepairOrderStatus::NEW,
        'problem_description' => 'Engine problem',
    ]);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'status' => RepairOrderStatus::NEW,
        'problem_description' => 'Brake issue',
    ]);

    actingAs($user)
        ->get(route('repair-orders.mechanic', ['search' => 'Engine']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/mechanic-index')
            ->has('orders', 1)
            ->where('orders.0.problem_description', 'Engine problem')
        );
});

test('mechanic index orders are scoped to current workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $otherWorkshop = Workshop::factory()->create();
    $client1 = Client::factory()->for($this->workshop)->create();
    $client2 = Client::factory()->for($otherWorkshop)->create();
    $vehicle1 = Vehicle::factory()->for($client1)->for($this->workshop)->create();
    $vehicle2 = Vehicle::factory()->for($client2)->for($otherWorkshop)->create();

    RepairOrder::factory()->for($vehicle1)->for($this->workshop)->create(['status' => RepairOrderStatus::NEW]);
    RepairOrder::factory()->for($vehicle2)->for($otherWorkshop)->create(['status' => RepairOrderStatus::NEW]);

    actingAs($user)
        ->get(route('repair-orders.mechanic'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/mechanic-index')
            ->has('orders', 1)
        );
});

test('mechanic index returns all active statuses except closed', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::NEW]);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::DIAGNOSIS]);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::AWAITING_CONTACT]);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::AWAITING_PARTS]);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::IN_PROGRESS]);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::READY_FOR_PICKUP]);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::CLOSED]);

    actingAs($user)
        ->get(route('repair-orders.mechanic'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/mechanic-index')
            ->has('orders', 6)
        );
});

test('user without role cannot access mechanic index', function () {
    $user = User::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.mechanic'))
        ->assertForbidden();
});
