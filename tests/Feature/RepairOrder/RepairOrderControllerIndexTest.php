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

test('guests are redirected to login page when accessing index', function () {
    get(route('repair-orders.index'))->assertRedirect(route('login'));
});

test('mechanic cannot view repair orders index', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->get(route('repair-orders.index'))
        ->assertForbidden();
});

test('owner can view repair orders list', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('repair-orders.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/index')
            ->has('tableData.data')
            ->has('filters')
            ->has('statusOptions')
        );
});

test('office can view repair orders list', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    actingAs($user)
        ->get(route('repair-orders.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/index')
            ->has('tableData.data')
            ->has('filters')
        );
});

test('repair orders list includes pagination and related data', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->count(3)->create();

    actingAs($user)
        ->get(route('repair-orders.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/index')
            ->has('tableData.data', 3)
            ->has('tableData.data.0', fn ($repairOrder) => $repairOrder
                ->has('id')
                ->has('status')
                ->has('problem_description')
                ->has('started_at')
                ->has('finished_at')
                ->has('total_time_minutes')
                ->has('created_at')
                ->has('vehicle')
                ->has('vehicle.id')
                ->has('vehicle.make')
                ->has('vehicle.model')
                ->has('vehicle.registration_number')
                ->has('client')
                ->has('client.id')
                ->has('client.first_name')
                ->has('client.phone_number')
                ->etc()
            )
            ->has('tableData.links')
            ->has('tableData.current_page')
            ->has('tableData.per_page')
            ->has('tableData.total')
        );
});

test('status filter filters repair orders by status', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::NEW]);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::IN_PROGRESS]);

    actingAs($user)
        ->get(route('repair-orders.index', ['status' => 'new']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/index')
            ->has('tableData.data', 1)
            ->where('tableData.data.0.status', 'new')
            ->where('filters.status', 'new')
        );
});

test('search filters repair orders by problem description', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['problem_description' => 'Engine problem']);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['problem_description' => 'Brake issue']);

    actingAs($user)
        ->get(route('repair-orders.index', ['search' => 'Engine']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/index')
            ->has('tableData.data', 1)
            ->where('tableData.data.0.problem_description', 'Engine problem')
            ->where('filters.search', 'Engine')
        );
});

test('search filters repair orders by vehicle make', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle1 = Vehicle::factory()->for($client)->for($this->workshop)->create(['make' => 'Toyota']);
    $vehicle2 = Vehicle::factory()->for($client)->for($this->workshop)->create(['make' => 'Honda']);
    RepairOrder::factory()->for($vehicle1)->for($this->workshop)->create();
    RepairOrder::factory()->for($vehicle2)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.index', ['search' => 'Toyota']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/index')
            ->has('tableData.data', 1)
            ->where('tableData.data.0.vehicle.make', 'Toyota')
        );
});

test('search filters repair orders by client name', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client1 = Client::factory()->for($this->workshop)->create(['first_name' => 'John', 'last_name' => 'Doe']);
    $client2 = Client::factory()->for($this->workshop)->create(['first_name' => 'Jane', 'last_name' => 'Smith']);
    $vehicle1 = Vehicle::factory()->for($client1)->for($this->workshop)->create();
    $vehicle2 = Vehicle::factory()->for($client2)->for($this->workshop)->create();
    RepairOrder::factory()->for($vehicle1)->for($this->workshop)->create();
    RepairOrder::factory()->for($vehicle2)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.index', ['search' => 'John']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/index')
            ->has('tableData.data', 1)
            ->where('tableData.data.0.client.first_name', 'John')
        );
});

test('sort parameter orders repair orders correctly by created_at', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $order1 = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['created_at' => now()->subDays(3)]);
    $order2 = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['created_at' => now()->subDays(1)]);
    $order3 = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['created_at' => now()->subDays(2)]);

    actingAs($user)
        ->get(route('repair-orders.index', ['sort' => 'created_at', 'direction' => 'asc']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/index')
            ->where('tableData.data.0.id', $order1->id)
            ->where('tableData.data.1.id', $order3->id)
            ->where('tableData.data.2.id', $order2->id)
            ->where('filters.sort', 'created_at')
            ->where('filters.direction', 'asc')
        );
});

test('invalid sort parameter fails validation', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('repair-orders.index', ['sort' => 'invalid_column']))
        ->assertSessionHasErrors('sort');
});

test('invalid direction parameter fails validation', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('repair-orders.index', ['direction' => 'invalid']))
        ->assertSessionHasErrors('direction');
});

test('invalid status parameter fails validation', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('repair-orders.index', ['status' => 'invalid_status']))
        ->assertSessionHasErrors('status');
});

test('repair orders are scoped to current workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $client1 = Client::factory()->for($this->workshop)->create();
    $client2 = Client::factory()->for($otherWorkshop)->create();
    $vehicle1 = Vehicle::factory()->for($client1)->for($this->workshop)->create();
    $vehicle2 = Vehicle::factory()->for($client2)->for($otherWorkshop)->create();

    RepairOrder::factory()->for($vehicle1)->for($this->workshop)->create(['problem_description' => 'My workshop order']);
    RepairOrder::factory()->for($vehicle2)->for($otherWorkshop)->create(['problem_description' => 'Other workshop order']);

    actingAs($user)
        ->get(route('repair-orders.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/index')
            ->has('tableData.data', 1)
            ->where('tableData.data.0.problem_description', 'My workshop order')
        );
});

test('user without role cannot access repair orders list', function () {
    $user = User::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.index'))
        ->assertForbidden();
});
