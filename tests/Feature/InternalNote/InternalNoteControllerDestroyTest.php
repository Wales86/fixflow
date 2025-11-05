<?php

use App\Models\Client;
use App\Models\InternalNote;
use App\Models\RepairOrder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('guests are redirected to login when attempting to delete internal note', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $internalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Note to delete',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);

    delete(route('internal-notes.destroy', $internalNote))
        ->assertRedirect(route('login'));
});

test('office cannot delete internal note', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $internalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Note content',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);

    actingAs($user)
        ->delete(route('internal-notes.destroy', $internalNote))
        ->assertForbidden();
});

test('mechanic cannot delete internal note', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $ownerUser = User::factory()->for($this->workshop)->create();
    $ownerUser->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $internalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Note content',
        'author_id' => $ownerUser->id,
        'author_type' => get_class($ownerUser),
    ]);

    actingAs($user)
        ->delete(route('internal-notes.destroy', $internalNote))
        ->assertForbidden();
});

test('owner can delete an internal note', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $internalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Note to delete',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);

    actingAs($user)
        ->delete(route('internal-notes.destroy', $internalNote))
        ->assertRedirect()
        ->assertSessionHas('success', __('internal_notes.messages.deleted'));

    assertDatabaseMissing('internal_notes', [
        'id' => $internalNote->id,
    ]);
});

test('user cannot delete internal note from another workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherUser = User::factory()->for($otherWorkshop)->create();
    $otherUser->assignRole('Owner');

    $otherClient = Client::factory()->for($otherWorkshop)->create();
    $otherVehicle = Vehicle::factory()->for($otherClient)->for($otherWorkshop)->create();
    $otherRepairOrder = RepairOrder::factory()->for($otherVehicle)->for($otherWorkshop)->create();

    $otherInternalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $otherRepairOrder->id,
        'content' => 'Other workshop note',
        'author_id' => $otherUser->id,
        'author_type' => get_class($otherUser),
    ]);

    actingAs($user)
        ->delete(route('internal-notes.destroy', $otherInternalNote))
        ->assertForbidden();
});

test('deleting internal note returns 404 for non-existent note', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->delete(route('internal-notes.destroy', 99999))
        ->assertNotFound();
});

test('user without role cannot delete internal note', function () {
    $user = User::factory()->for($this->workshop)->create();

    $ownerUser = User::factory()->for($this->workshop)->create();
    $ownerUser->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $internalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Note content',
        'author_id' => $ownerUser->id,
        'author_type' => get_class($ownerUser),
    ]);

    actingAs($user)
        ->delete(route('internal-notes.destroy', $internalNote))
        ->assertForbidden();
});
