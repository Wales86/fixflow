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

test('guests are redirected to login when accessing show', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    get(route('repair-orders.show', $repairOrder))->assertRedirect(route('login'));
});

test('owner can view repair order details', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.show', $repairOrder))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/show')
            ->has('order')
            ->has('order.id')
            ->has('order.status')
            ->has('order.problem_description')
            ->has('order.vehicle')
            ->has('order.client')
            ->has('order.images')
            ->has('time_entries')
            ->has('internal_notes')
            ->has('activity_log')
        );
});

test('office can view repair order details', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.show', $repairOrder))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/show')
            ->has('order')
            ->has('time_entries')
            ->has('internal_notes')
            ->has('activity_log')
        );
});

test('mechanic can view repair order details', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.show', $repairOrder))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/show')
            ->has('order')
        );
});

test('show page includes vehicle and client data', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone_number' => '123456789',
    ]);
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create([
        'make' => 'Toyota',
        'model' => 'Corolla',
        'registration_number' => 'ABC123',
    ]);
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.show', $repairOrder))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/show')
            ->where('order.vehicle.make', 'Toyota')
            ->where('order.vehicle.model', 'Corolla')
            ->where('order.vehicle.registration_number', 'ABC123')
            ->where('order.client.first_name', 'John')
            ->where('order.client.last_name', 'Doe')
            ->where('order.client.phone_number', '123456789')
        );
});

test('show page includes time entries with mechanic data', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $mechanic = Mechanic::factory()->for($this->workshop)->create([
        'first_name' => 'Mike',
        'last_name' => 'Smith',
    ]);
    TimeEntry::factory()->for($repairOrder)->for($mechanic)->create([
        'duration_minutes' => 120,
        'description' => 'Engine repair',
    ]);

    actingAs($user)
        ->get(route('repair-orders.show', $repairOrder))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('repair-orders/show')
            ->has('time_entries', 1)
            ->where('time_entries.0.duration_minutes', 120)
            ->where('time_entries.0.description', 'Engine repair')
            ->where('time_entries.0.mechanic.first_name', 'Mike')
            ->where('time_entries.0.mechanic.last_name', 'Smith')
        );
});

test('user cannot view repair order from another workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherClient = Client::factory()->for($otherWorkshop)->create();
    $otherVehicle = Vehicle::factory()->for($otherClient)->for($otherWorkshop)->create();
    $otherRepairOrder = RepairOrder::factory()->for($otherVehicle)->for($otherWorkshop)->create();

    actingAs($user)
        ->get(route('repair-orders.show', $otherRepairOrder))
        ->assertNotFound();
});

test('show returns 404 for non-existent repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->get(route('repair-orders.show', 99999))
        ->assertNotFound();
});

test('user without role cannot view repair order', function () {
    $user = User::factory()->for($this->workshop)->create();

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('repair-orders.show', $repairOrder))
        ->assertForbidden();
});
