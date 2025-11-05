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
use function Pest\Laravel\patch;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

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
