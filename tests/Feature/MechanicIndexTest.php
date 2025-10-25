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

test('guests are redirected to the login page', function () {
    get(route('mechanics.index'))->assertRedirect(route('login'));
});

test('mechanics without proper permission are forbidden from viewing mechanics list', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->get(route('mechanics.index'))
        ->assertForbidden();
});

test('owner can view mechanics list', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    Mechanic::factory()->count(3)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('mechanics.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('mechanics/index')
            ->has('tableData')
            ->has('filters')
        );
});

test('office can view mechanics list', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    Mechanic::factory()->count(3)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('mechanics.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('mechanics/index')
            ->has('tableData')
            ->has('filters')
        );
});

test('mechanics list includes paginated data', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    Mechanic::factory()->count(5)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('mechanics.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('tableData.data', 5)
            ->has('tableData.links')
        );
});

test('mechanics are sorted alphabetically by first and last name', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $mechanicB = Mechanic::factory()->for($this->workshop)->create([
        'first_name' => 'Bob',
        'last_name' => 'Smith',
    ]);

    $mechanicA = Mechanic::factory()->for($this->workshop)->create([
        'first_name' => 'Alice',
        'last_name' => 'Johnson',
    ]);

    actingAs($user)
        ->get(route('mechanics.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('tableData.data.0.id', $mechanicA->id)
            ->where('tableData.data.1.id', $mechanicB->id)
        );
});

test('can filter mechanics by active status', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $activeMechanic = Mechanic::factory()->for($this->workshop)->create([
        'is_active' => true,
    ]);

    $inactiveMechanic = Mechanic::factory()->for($this->workshop)->create([
        'is_active' => false,
    ]);

    actingAs($user)
        ->get(route('mechanics.index', ['active' => true]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('tableData.data', 1)
            ->where('tableData.data.0.id', $activeMechanic->id)
        );
});

test('can filter mechanics by inactive status', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $activeMechanic = Mechanic::factory()->for($this->workshop)->create([
        'is_active' => true,
    ]);

    $inactiveMechanic = Mechanic::factory()->for($this->workshop)->create([
        'is_active' => false,
    ]);

    actingAs($user)
        ->get(route('mechanics.index', ['active' => false]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('tableData.data', 1)
            ->where('tableData.data.0.id', $inactiveMechanic->id)
        );
});

test('mechanics include time entries count', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $mechanic = Mechanic::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('mechanics.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('tableData.data.0.time_entries_count')
        );
});
