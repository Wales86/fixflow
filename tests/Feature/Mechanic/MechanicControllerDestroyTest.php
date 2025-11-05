<?php

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

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('guests are redirected to login when attempting to delete mechanic', function () {
    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    delete(route('mechanics.destroy', $mechanic))
        ->assertRedirect(route('login'));

    assertDatabaseHas('mechanics', ['id' => $mechanic->id, 'deleted_at' => null]);
});

test('owner can delete a mechanic without time entries', function () {
    $owner = User::factory()->for($this->workshop)->create()->assignRole('Owner');
    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    actingAs($owner)
        ->delete(route('mechanics.destroy', $mechanic))
        ->assertRedirect(route('mechanics.index'))
        ->assertSessionHas('success', __('mechanics.messages.deleted'));

    $this->assertSoftDeleted('mechanics', ['id' => $mechanic->id]);
});

test('office user cannot delete a mechanic', function () {
    $office = User::factory()->for($this->workshop)->create()->assignRole('Office');
    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    actingAs($office)
        ->delete(route('mechanics.destroy', $mechanic))
        ->assertForbidden();

    assertDatabaseHas('mechanics', ['id' => $mechanic->id, 'deleted_at' => null]);
});

test('cannot delete mechanic with associated time entries', function () {
    $owner = User::factory()->for($this->workshop)->create()->assignRole('Owner');
    $mechanic = Mechanic::factory()->for($this->workshop)->create();
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->create([
        'workshop_id' => $this->workshop->id,
        'vehicle_id' => $vehicle->id,
    ]);
    TimeEntry::factory()->for($repairOrder)->for($mechanic)->create();

    actingAs($owner)
        ->delete(route('mechanics.destroy', $mechanic))
        ->assertRedirect()
        ->assertSessionHas('error', __('mechanics.messages.cannot_delete_with_time_entries'));

    assertDatabaseHas('mechanics', ['id' => $mechanic->id, 'deleted_at' => null]);
});

test('attempting to delete non-existent mechanic returns 404', function () {
    $owner = User::factory()->for($this->workshop)->create()->assignRole('Owner');

    actingAs($owner)
        ->delete(route('mechanics.destroy', 99999))
        ->assertNotFound();
});

test('mechanic from another workshop returns 404 due to global scope', function () {
    $anotherWorkshop = Workshop::factory()->create();
    $mechanicFromAnotherWorkshop = Mechanic::factory()->for($anotherWorkshop)->create();

    $owner = User::factory()->for($this->workshop)->create()->assignRole('Owner');

    actingAs($owner)
        ->delete(route('mechanics.destroy', $mechanicFromAnotherWorkshop))
        ->assertNotFound();
});

test('user without role cannot delete a mechanic', function () {
    $user = User::factory()->for($this->workshop)->create();
    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    actingAs($user)
        ->delete(route('mechanics.destroy', $mechanic))
        ->assertForbidden();

    assertDatabaseHas('mechanics', ['id' => $mechanic->id, 'deleted_at' => null]);
});
