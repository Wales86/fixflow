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

test('guests are redirected to login page when accessing create', function () {
    get(route('repair-orders.create'))->assertRedirect(route('login'));
});

test('authenticated users without proper role are forbidden from create', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->get(route('repair-orders.create'))
        ->assertForbidden();
});

test('owner can view repair order create form', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('repair-orders.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/create')
            ->has('vehicles')
            ->has('statuses')
            ->where('preselected_vehicle_id', null)
        );
});

test('office can view repair order create form', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    actingAs($user)
        ->get(route('repair-orders.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/create')
            ->has('vehicles')
            ->has('statuses')
        );
});

test('create form includes vehicles from current workshop only', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client1 = Client::factory()->for($this->workshop)->create();
    $vehicle1 = Vehicle::factory()->for($client1)->for($this->workshop)->create();

    $otherWorkshop = Workshop::factory()->create();
    $client2 = Client::factory()->for($otherWorkshop)->create();
    $vehicle2 = Vehicle::factory()->for($client2)->for($otherWorkshop)->create();

    actingAs($user)
        ->get(route('repair-orders.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/create')
            ->has('vehicles', 1)
            ->where('vehicles.0.id', $vehicle1->id)
            ->has('vehicles.0', fn ($vehicle) => $vehicle
                ->has('id')
                ->has('display_name')
                ->has('registration_number')
                ->has('client_name')
            )
        );
});

test('create form includes all repair order statuses', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('repair-orders.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/create')
            ->has('statuses', 7)
            ->has('statuses.0', fn ($status) => $status
                ->has('value')
                ->has('label')
            )
        );
});

test('create form preselects vehicle when preselected_vehicle_id is provided', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.create', ['preselected_vehicle_id' => $vehicle->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/create')
            ->where('preselected_vehicle_id', $vehicle->id)
        );
});

test('create form fails validation with invalid preselected_vehicle_id', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('repair-orders.create', ['preselected_vehicle_id' => 999999]))
        ->assertSessionHasErrors('preselected_vehicle_id');
});

test('user without role cannot access create form', function () {
    $user = User::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.create'))
        ->assertForbidden();
});
