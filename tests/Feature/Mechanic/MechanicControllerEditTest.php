<?php

use App\Models\Mechanic;
use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    /** @var Workshop $this->workshop */
    $this->workshop = Workshop::factory()->create();
    $this->workshop->makeCurrent();

    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Office']);
    Role::firstOrCreate(['name' => 'Mechanic']);
});

test('guests are redirected to login when accessing edit', function () {
    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    get(route('mechanics.edit', $mechanic))
        ->assertRedirect(route('login'));
});

test('owner can access mechanic edit page', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    $mechanic = Mechanic::factory()->for($this->workshop)->create([
        'first_name' => 'Jan',
        'last_name' => 'Kowalski',
    ]);

    actingAs($owner)
        ->get(route('mechanics.edit', $mechanic))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('mechanics/edit')
            ->has('mechanic')
            ->where('mechanic.id', $mechanic->id)
            ->where('mechanic.first_name', 'Jan')
            ->where('mechanic.last_name', 'Kowalski')
        );
});

test('office can access mechanic edit page', function () {
    $office = User::factory()->for($this->workshop)->create();
    $office->assignRole('Office');

    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    actingAs($office)
        ->get(route('mechanics.edit', $mechanic))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('mechanics/edit'));
});

test('mechanic cannot access mechanic edit page', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('mechanics.edit', $mechanic))
        ->assertForbidden();
});

test('user without role cannot access mechanic edit page', function () {
    $user = User::factory()->for($this->workshop)->create();

    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('mechanics.edit', $mechanic))
        ->assertForbidden();
});

test('mechanic from another workshop returns 404 due to global scope', function () {
    $anotherWorkshop = Workshop::factory()->create();
    $mechanicFromAnotherWorkshop = Mechanic::factory()->for($anotherWorkshop)->create();

    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->get(route('mechanics.edit', $mechanicFromAnotherWorkshop))
        ->assertNotFound();
});

test('returns 404 when mechanic does not exist', function () {
    $owner = User::factory()->for($this->workshop)->create();
    $owner->assignRole('Owner');

    actingAs($owner)
        ->get(route('mechanics.edit', 99999))
        ->assertNotFound();
});
