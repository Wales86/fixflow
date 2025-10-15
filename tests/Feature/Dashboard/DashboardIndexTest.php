<?php

use App\Enums\RepairOrderStatus;
use App\Models\Client;
use App\Models\Mechanic;
use App\Models\RepairOrder;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;


beforeEach(function () {
    $this->workshop1 = Workshop::factory()->create(['name' => 'Workshop 1']);
    $this->workshop2 = Workshop::factory()->create(['name' => 'Workshop 2']);

    $this->user1 = User::factory()->create(['workshop_id' => $this->workshop1->id]);
    $this->user2 = User::factory()->create(['workshop_id' => $this->workshop2->id]);
});

// Authentication Tests
test('unauthenticated users are redirected to login', function () {
    get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('authenticated users can access dashboard', function () {
    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('dashboard/index'));
});

// Multi-Tenancy Tests
test('dashboard only shows data from authenticated user workshop', function () {
    // Create data for Workshop 1
    $client1 = Client::factory()->create(['workshop_id' => $this->workshop1->id]);
    $vehicle1 = Vehicle::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'client_id' => $client1->id,
    ]);
    RepairOrder::factory()->count(3)->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle1->id,
        'status' => RepairOrderStatus::InProgress,
    ]);

    // Create data for Workshop 2
    $client2 = Client::factory()->create(['workshop_id' => $this->workshop2->id]);
    $vehicle2 = Vehicle::factory()->create([
        'workshop_id' => $this->workshop2->id,
        'client_id' => $client2->id,
    ]);
    RepairOrder::factory()->count(5)->create([
        'workshop_id' => $this->workshop2->id,
        'vehicle_id' => $vehicle2->id,
        'status' => RepairOrderStatus::InProgress,
    ]);

    // User 1 should only see 3 orders from Workshop 1
    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) =>
            $page->component('dashboard/index')
                ->where('activeOrdersCount', 3)
        );

    // User 2 should only see 5 orders from Workshop 2
    actingAs($this->user2)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) =>
            $page->component('dashboard/index')
                ->where('activeOrdersCount', 5)
        );
});

test('dashboard does not leak data between workshops', function () {
    $client1 = Client::factory()->create(['workshop_id' => $this->workshop1->id]);
    $vehicle1 = Vehicle::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'client_id' => $client1->id,
        'make' => 'Toyota',
        'model' => 'Corolla',
        'year' => 2020,
    ]);
    RepairOrder::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle1->id,
        'status' => RepairOrderStatus::InProgress,
    ]);

    $client2 = Client::factory()->create(['workshop_id' => $this->workshop2->id]);
    $vehicle2 = Vehicle::factory()->create([
        'workshop_id' => $this->workshop2->id,
        'client_id' => $client2->id,
        'make' => 'Honda',
        'model' => 'Civic',
        'year' => 2021,
    ]);
    RepairOrder::factory()->create([
        'workshop_id' => $this->workshop2->id,
        'vehicle_id' => $vehicle2->id,
        'status' => RepairOrderStatus::InProgress,
    ]);

    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertInertia(function ($page) {
            $page->component('dashboard/index')
                ->has('recentOrders', 1)
                ->where('recentOrders.0.vehicle', 'Toyota Corolla 2020');

            // Ensure no Honda Civic from Workshop 2
            $recentOrders = $page->toArray()['props']['recentOrders'];
            expect($recentOrders)->not->toContain(fn ($order) => str_contains($order['vehicle'], 'Honda'));
        });
});

// Active Orders Count Tests
test('active orders count excludes closed orders', function () {
    $client = Client::factory()->create(['workshop_id' => $this->workshop1->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'client_id' => $client->id,
    ]);

    RepairOrder::factory()->count(5)->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle->id,
        'status' => RepairOrderStatus::InProgress,
    ]);

    RepairOrder::factory()->count(3)->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle->id,
        'status' => RepairOrderStatus::Closed,
    ]);

    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) =>
            $page->component('dashboard/index')
                ->where('activeOrdersCount', 5)
        );
});

test('active orders count includes all non-closed statuses', function () {
    $client = Client::factory()->create(['workshop_id' => $this->workshop1->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'client_id' => $client->id,
    ]);

    $nonClosedStatuses = [
        RepairOrderStatus::New,
        RepairOrderStatus::Diagnosis,
        RepairOrderStatus::AwaitingContact,
        RepairOrderStatus::AwaitingParts,
        RepairOrderStatus::InProgress,
        RepairOrderStatus::ReadyForPickup,
    ];

    foreach ($nonClosedStatuses as $status) {
        RepairOrder::factory()->create([
            'workshop_id' => $this->workshop1->id,
            'vehicle_id' => $vehicle->id,
            'status' => $status,
        ]);
    }

    RepairOrder::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle->id,
        'status' => RepairOrderStatus::Closed,
    ]);

    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) =>
            $page->component('dashboard/index')
                ->where('activeOrdersCount', 6)
        );
});

// Pending Orders Count Tests
test('pending orders count only includes ready for pickup status', function () {
    $client = Client::factory()->create(['workshop_id' => $this->workshop1->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'client_id' => $client->id,
    ]);

    RepairOrder::factory()->count(3)->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle->id,
        'status' => RepairOrderStatus::ReadyForPickup,
    ]);

    RepairOrder::factory()->count(5)->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle->id,
        'status' => RepairOrderStatus::InProgress,
    ]);

    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) =>
            $page->component('dashboard/index')
                ->where('pendingOrdersCount', 3)
        );
});

// Today's Time Entries Tests
test('today time entries total only includes entries from today', function () {
    $client = Client::factory()->create(['workshop_id' => $this->workshop1->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'client_id' => $client->id,
    ]);
    $repairOrder = RepairOrder::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle->id,
    ]);
    $mechanic = Mechanic::factory()->create(['workshop_id' => $this->workshop1->id]);

    // Create today's entries
    TimeEntry::factory()->create([
        'repair_order_id' => $repairOrder->id,
        'mechanic_id' => $mechanic->id,
        'duration_minutes' => 60,
        'created_at' => now(),
    ]);

    TimeEntry::factory()->create([
        'repair_order_id' => $repairOrder->id,
        'mechanic_id' => $mechanic->id,
        'duration_minutes' => 45,
        'created_at' => now(),
    ]);

    // Create yesterday's entry (should not be included)
    TimeEntry::factory()->create([
        'repair_order_id' => $repairOrder->id,
        'mechanic_id' => $mechanic->id,
        'duration_minutes' => 120,
        'created_at' => now()->subDay(),
    ]);

    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) =>
            $page->component('dashboard/index')
                ->where('todayTimeEntriesTotal', 105)
        );
});

test('today time entries respects workshop multi-tenancy', function () {
    // Workshop 1 data
    $client1 = Client::factory()->create(['workshop_id' => $this->workshop1->id]);
    $vehicle1 = Vehicle::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'client_id' => $client1->id,
    ]);
    $repairOrder1 = RepairOrder::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle1->id,
    ]);
    $mechanic1 = Mechanic::factory()->create(['workshop_id' => $this->workshop1->id]);

    TimeEntry::factory()->create([
        'repair_order_id' => $repairOrder1->id,
        'mechanic_id' => $mechanic1->id,
        'duration_minutes' => 60,
        'created_at' => now(),
    ]);

    // Workshop 2 data
    $client2 = Client::factory()->create(['workshop_id' => $this->workshop2->id]);
    $vehicle2 = Vehicle::factory()->create([
        'workshop_id' => $this->workshop2->id,
        'client_id' => $client2->id,
    ]);
    $repairOrder2 = RepairOrder::factory()->create([
        'workshop_id' => $this->workshop2->id,
        'vehicle_id' => $vehicle2->id,
    ]);
    $mechanic2 = Mechanic::factory()->create(['workshop_id' => $this->workshop2->id]);

    TimeEntry::factory()->create([
        'repair_order_id' => $repairOrder2->id,
        'mechanic_id' => $mechanic2->id,
        'duration_minutes' => 90,
        'created_at' => now(),
    ]);

    // User 1 should only see 60 minutes
    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) =>
            $page->component('dashboard/index')
                ->where('todayTimeEntriesTotal', 60)
        );

    // User 2 should only see 90 minutes
    actingAs($this->user2)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) =>
            $page->component('dashboard/index')
                ->where('todayTimeEntriesTotal', 90)
        );
});

// Recent Orders Tests
test('recent orders are limited to 10 items', function () {
    $client = Client::factory()->create(['workshop_id' => $this->workshop1->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'client_id' => $client->id,
    ]);

    RepairOrder::factory()->count(15)->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle->id,
    ]);

    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) =>
            $page->component('dashboard/index')
                ->has('recentOrders', 10)
        );
});

test('recent orders are sorted by updated_at descending', function () {
    $client = Client::factory()->create(['workshop_id' => $this->workshop1->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'client_id' => $client->id,
    ]);

    $order1 = RepairOrder::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle->id,
        'updated_at' => now()->subHours(3),
    ]);

    $order2 = RepairOrder::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle->id,
        'updated_at' => now()->subHour(),
    ]);

    $order3 = RepairOrder::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle->id,
        'updated_at' => now(),
    ]);

    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertInertia(function ($page) use ($order3, $order2, $order1) {
            $page->component('dashboard/index')
                ->has('recentOrders', 3)
                ->where('recentOrders.0.id', $order3->id)
                ->where('recentOrders.1.id', $order2->id)
                ->where('recentOrders.2.id', $order1->id);
        });
});

test('recent orders have correct data structure', function () {
    $client = Client::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'client_id' => $client->id,
        'make' => 'Toyota',
        'model' => 'Camry',
        'year' => 2022,
    ]);

    $repairOrder = RepairOrder::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle->id,
        'status' => RepairOrderStatus::InProgress,
    ]);

    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertInertia(function ($page) use ($repairOrder) {
            $page->component('dashboard/index')
                ->has('recentOrders.0', fn ($order) =>
                    $order->where('id', $repairOrder->id)
                        ->where('vehicle', 'Toyota Camry 2022')
                        ->where('client', 'John Doe')
                        ->where('status', 'W naprawie')
                        ->has('created_at')
                );
        });
});

// Empty State Tests
test('dashboard displays zeros when no data exists', function () {
    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) =>
            $page->component('dashboard/index')
                ->where('activeOrdersCount', 0)
                ->where('pendingOrdersCount', 0)
                ->where('todayTimeEntriesTotal', 0)
                ->has('recentOrders', 0)
        );
});

test('dashboard handles workshop with only closed orders', function () {
    $client = Client::factory()->create(['workshop_id' => $this->workshop1->id]);
    $vehicle = Vehicle::factory()->create([
        'workshop_id' => $this->workshop1->id,
        'client_id' => $client->id,
    ]);

    RepairOrder::factory()->count(5)->create([
        'workshop_id' => $this->workshop1->id,
        'vehicle_id' => $vehicle->id,
        'status' => RepairOrderStatus::Closed,
    ]);

    actingAs($this->user1)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) =>
            $page->component('dashboard/index')
                ->where('activeOrdersCount', 0)
                ->where('pendingOrdersCount', 0)
                ->has('recentOrders', 5) // Closed orders still appear in recent orders
        );
});
