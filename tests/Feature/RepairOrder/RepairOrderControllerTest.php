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

test('authenticated users without proper role are forbidden from index', function () {
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
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::New]);
    RepairOrder::factory()->for($vehicle)->for($this->workshop)->create(['status' => RepairOrderStatus::InProgress]);

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
            ->has('can_edit')
            ->has('can_delete')
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
            ->where('can_edit', true)
            ->where('can_delete', false)
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
            ->where('can_edit', false)
            ->where('can_delete', false)
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

test('guests are redirected to login when attempting to store repair order', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    post(route('repair-orders.store'), [
        'vehicle_id' => $vehicle->id,
        'description' => 'Engine not starting',
    ])->assertRedirect(route('login'));
});

test('user without proper role cannot store repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $vehicle->id,
            'description' => 'Engine not starting',
        ])
        ->assertForbidden();
});

test('owner can store a repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $vehicle->id,
            'description' => 'Engine not starting properly',
        ])
        ->assertRedirect(route('repair-orders.index'))
        ->assertSessionHas('success', __('repair_orders.messages.created'));

    assertDatabaseHas('repair_orders', [
        'workshop_id' => $this->workshop->id,
        'vehicle_id' => $vehicle->id,
        'problem_description' => 'Engine not starting properly',
        'status' => RepairOrderStatus::New->value,
    ]);
});

test('office can store a repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $vehicle->id,
            'description' => 'Brake pads replacement needed',
        ])
        ->assertRedirect(route('repair-orders.index'))
        ->assertSessionHas('success', __('repair_orders.messages.created'));

    assertDatabaseHas('repair_orders', [
        'workshop_id' => $this->workshop->id,
        'vehicle_id' => $vehicle->id,
        'problem_description' => 'Brake pads replacement needed',
        'status' => RepairOrderStatus::New->value,
    ]);
});

test('storing repair order validates vehicle_id is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'description' => 'Engine problem',
        ])
        ->assertSessionHasErrors('vehicle_id');
});

test('storing repair order validates description is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $vehicle->id,
        ])
        ->assertSessionHasErrors('description');
});

test('storing repair order validates vehicle_id must exist in current workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherClient = Client::factory()->for($otherWorkshop)->create();
    $otherVehicle = Vehicle::factory()->for($otherClient)->for($otherWorkshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $otherVehicle->id,
            'description' => 'Engine problem',
        ])
        ->assertSessionHasErrors('vehicle_id');
});

test('storing repair order validates vehicle_id must be integer', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => 'not-an-integer',
            'description' => 'Engine problem',
        ])
        ->assertSessionHasErrors('vehicle_id');
});

test('storing repair order validates vehicle_id must exist', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => 99999,
            'description' => 'Engine problem',
        ])
        ->assertSessionHasErrors('vehicle_id');
});

test('storing repair order validates description must be string', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $vehicle->id,
            'description' => 12345,
        ])
        ->assertSessionHasErrors('description');
});

test('user without role cannot store repair order', function () {
    $user = User::factory()->for($this->workshop)->create();

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('repair-orders.store'), [
            'vehicle_id' => $vehicle->id,
            'description' => 'Engine problem',
        ])
        ->assertForbidden();
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
        'status' => RepairOrderStatus::New,
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'status' => RepairOrderStatus::InProgress->value,
        ])
        ->assertRedirect(route('repair-orders.index'))
        ->assertSessionHas('success', __('repair_orders.messages.updated'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'status' => RepairOrderStatus::InProgress->value,
    ]);
});

test('owner can update both description and status', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'problem_description' => 'Original description',
        'status' => RepairOrderStatus::New,
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'description' => 'Updated description',
            'status' => RepairOrderStatus::Diagnosis->value,
        ])
        ->assertRedirect(route('repair-orders.index'))
        ->assertSessionHas('success', __('repair_orders.messages.updated'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'problem_description' => 'Updated description',
        'status' => RepairOrderStatus::Diagnosis->value,
    ]);
});

test('partial update with only description works', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'problem_description' => 'Original description',
        'status' => RepairOrderStatus::InProgress,
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'description' => 'Only description updated',
        ])
        ->assertRedirect(route('repair-orders.index'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'problem_description' => 'Only description updated',
        'status' => RepairOrderStatus::InProgress->value,
    ]);
});

test('partial update with only status works', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'problem_description' => 'Original description',
        'status' => RepairOrderStatus::New,
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update', $repairOrder), [
            'status' => RepairOrderStatus::Closed->value,
        ])
        ->assertRedirect(route('repair-orders.index'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'problem_description' => 'Original description',
        'status' => RepairOrderStatus::Closed->value,
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

test('guests are redirected to login when attempting to update repair order status', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    patch(route('repair-orders.update-status', $repairOrder), [
        'status' => RepairOrderStatus::InProgress->value,
    ])->assertRedirect(route('login'));
});

test('user without proper role cannot update repair order status', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->patch(route('repair-orders.update-status', $repairOrder), [
            'status' => RepairOrderStatus::InProgress->value,
        ])
        ->assertForbidden();
});

test('owner can update repair order status using dedicated endpoint', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'status' => RepairOrderStatus::New,
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update-status', $repairOrder), [
            'status' => RepairOrderStatus::InProgress->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('repair_orders.messages.status_updated'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'status' => RepairOrderStatus::InProgress->value,
    ]);
});

test('office can update repair order status using dedicated endpoint', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create([
        'status' => RepairOrderStatus::Diagnosis,
    ]);

    actingAs($user)
        ->patch(route('repair-orders.update-status', $repairOrder), [
            'status' => RepairOrderStatus::ReadyForPickup->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('repair_orders.messages.status_updated'));

    assertDatabaseHas('repair_orders', [
        'id' => $repairOrder->id,
        'status' => RepairOrderStatus::ReadyForPickup->value,
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
            'status' => RepairOrderStatus::Closed->value,
        ])
        ->assertNotFound();
});

test('update status returns 404 for non-existent repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->patch(route('repair-orders.update-status', 99999), [
            'status' => RepairOrderStatus::Closed->value,
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
            'status' => RepairOrderStatus::InProgress->value,
        ])
        ->assertForbidden();
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
