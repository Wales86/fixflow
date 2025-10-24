<?php

use App\Models\Client;
use App\Models\InternalNote;
use App\Models\RepairOrder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
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

test('guests are redirected to login when attempting to store internal note', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    post(route('internal-notes.store'), [
        'notable_type' => 'repair_order',
        'notable_id' => $repairOrder->id,
        'content' => 'Internal note content',
    ])->assertRedirect(route('login'));
});

test('mechanic cannot store internal note with inactive mechanic', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $inactiveMechanic = \App\Models\Mechanic::factory()->for($this->workshop)->create([
        'is_active' => false,
    ]);

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => 'repair_order',
            'notable_id' => $repairOrder->id,
            'content' => 'Internal note content',
            'mechanic_id' => $inactiveMechanic->id,
        ])
        ->assertSessionHasErrors('mechanic_id');
});

test('owner can store an internal note for repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => 'repair_order',
            'notable_id' => $repairOrder->id,
            'content' => 'This is an internal note for the repair order',
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('internal_notes.messages.created'));

    assertDatabaseHas('internal_notes', [
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'This is an internal note for the repair order',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);
});

test('office can store an internal note for repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => 'repair_order',
            'notable_id' => $repairOrder->id,
            'content' => 'Office note about the order',
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('internal_notes.messages.created'));

    assertDatabaseHas('internal_notes', [
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Office note about the order',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);
});

test('mechanic can store an internal note for repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $mechanic = \App\Models\Mechanic::factory()->for($this->workshop)->create([
        'is_active' => true,
    ]);

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => 'repair_order',
            'notable_id' => $repairOrder->id,
            'content' => 'Mechanic note about the repair work',
            'mechanic_id' => $mechanic->id,
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('internal_notes.messages.created'));

    assertDatabaseHas('internal_notes', [
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Mechanic note about the repair work',
        'author_id' => $mechanic->id,
        'author_type' => \App\Models\Mechanic::class,
    ]);
});

test('storing internal note validates content is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => 'repair_order',
            'notable_id' => $repairOrder->id,
        ])
        ->assertSessionHasErrors('content');
});

test('storing internal note validates content has max length of 5000 characters', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => 'repair_order',
            'notable_id' => $repairOrder->id,
            'content' => str_repeat('a', 5001),
        ])
        ->assertSessionHasErrors('content');
});

test('user cannot store internal note for repair order from another workshop', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherClient = Client::factory()->for($otherWorkshop)->create();
    $otherVehicle = Vehicle::factory()->for($otherClient)->for($otherWorkshop)->create();
    $otherRepairOrder = RepairOrder::factory()->for($otherVehicle)->for($otherWorkshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => 'repair_order',
            'notable_id' => $otherRepairOrder->id,
            'content' => 'Trying to create note for another workshop',
        ])
        ->assertNotFound();
});

test('storing internal note validates notable_type is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_id' => $repairOrder->id,
            'content' => 'Some content',
        ])
        ->assertSessionHasErrors('notable_type');
});

test('storing internal note validates notable_id is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => 'repair_order',
            'content' => 'Some content',
        ])
        ->assertSessionHasErrors('notable_id');
});

test('storing internal note returns 404 for non-existent repair order', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => 'repair_order',
            'notable_id' => 99999,
            'content' => 'Some content',
        ])
        ->assertNotFound();
});

test('storing internal note validates content must be string', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => 'repair_order',
            'notable_id' => $repairOrder->id,
            'content' => 12345,
        ])
        ->assertSessionHasErrors('content');
});

test('user without role cannot store internal note', function () {
    $user = User::factory()->for($this->workshop)->create();

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    actingAs($user)
        ->post(route('internal-notes.store'), [
            'notable_type' => 'repair_order',
            'notable_id' => $repairOrder->id,
            'content' => 'Some content',
        ])
        ->assertForbidden();
});

// Update tests
test('guests are redirected to login when attempting to update internal note', function () {
    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $internalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Original content',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);

    patch(route('internal-notes.update', $internalNote), [
        'content' => 'Updated content',
    ])->assertRedirect(route('login'));
});

test('user without proper role cannot update internal note', function () {
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
        'content' => 'Original content',
        'author_id' => $ownerUser->id,
        'author_type' => get_class($ownerUser),
    ]);

    actingAs($user)
        ->patch(route('internal-notes.update', $internalNote), [
            'content' => 'Updated content',
        ])
        ->assertForbidden();
});

test('owner can update an internal note', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $internalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Original content',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);

    actingAs($user)
        ->patch(route('internal-notes.update', $internalNote), [
            'content' => 'Updated internal note content',
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('internal_notes.messages.updated'));

    assertDatabaseHas('internal_notes', [
        'id' => $internalNote->id,
        'content' => 'Updated internal note content',
    ]);
});

test('office can update an internal note', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $internalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Original office note',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);

    actingAs($user)
        ->patch(route('internal-notes.update', $internalNote), [
            'content' => 'Updated office note',
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('internal_notes.messages.updated'));

    assertDatabaseHas('internal_notes', [
        'id' => $internalNote->id,
        'content' => 'Updated office note',
    ]);
});

test('updating internal note validates content is required', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $internalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Original content',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);

    actingAs($user)
        ->patch(route('internal-notes.update', $internalNote), [])
        ->assertSessionHasErrors('content');
});

test('updating internal note validates content has max length of 5000 characters', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $internalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Original content',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);

    actingAs($user)
        ->patch(route('internal-notes.update', $internalNote), [
            'content' => str_repeat('a', 5001),
        ])
        ->assertSessionHasErrors('content');
});

test('user cannot update internal note from another workshop', function () {
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
        ->patch(route('internal-notes.update', $otherInternalNote), [
            'content' => 'Trying to update note from another workshop',
        ])
        ->assertForbidden();
});

test('updating internal note returns 404 for non-existent note', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    actingAs($user)
        ->patch(route('internal-notes.update', 99999), [
            'content' => 'Some content',
        ])
        ->assertNotFound();
});

test('updating internal note validates content must be string', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $internalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Original content',
        'author_id' => $user->id,
        'author_type' => get_class($user),
    ]);

    actingAs($user)
        ->patch(route('internal-notes.update', $internalNote), [
            'content' => 12345,
        ])
        ->assertSessionHasErrors('content');
});

test('user without role cannot update internal note', function () {
    $user = User::factory()->for($this->workshop)->create();

    $ownerUser = User::factory()->for($this->workshop)->create();
    $ownerUser->assignRole('Owner');

    $client = Client::factory()->for($this->workshop)->create();
    $vehicle = Vehicle::factory()->for($client)->for($this->workshop)->create();
    $repairOrder = RepairOrder::factory()->for($vehicle)->for($this->workshop)->create();

    $internalNote = InternalNote::create([
        'notable_type' => RepairOrder::class,
        'notable_id' => $repairOrder->id,
        'content' => 'Original content',
        'author_id' => $ownerUser->id,
        'author_type' => get_class($ownerUser),
    ]);

    actingAs($user)
        ->patch(route('internal-notes.update', $internalNote), [
            'content' => 'Updated content',
        ])
        ->assertForbidden();
});

// Delete tests
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
