<?php

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
    get(route('users.index'))->assertRedirect(route('login'));
});

test('users without proper permission are forbidden from viewing users list', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Mechanic');

    actingAs($user)
        ->get(route('users.index'))
        ->assertForbidden();
});

test('owner can view users list', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    User::factory()->count(3)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('users/index')
            ->has('tableData')
            ->has('filters')
        );
});

test('office can view users list', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Office');

    User::factory()->count(3)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('users/index')
            ->has('tableData')
            ->has('filters')
        );
});

test('users list includes paginated data', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    User::factory()->count(5)->for($this->workshop)->create();

    actingAs($user)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('tableData.data', 5)
            ->has('tableData.links')
        );
});

test('users are sorted alphabetically by name', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $userB = User::factory()->for($this->workshop)->create([
        'name' => 'Bob Smith',
    ]);

    $userA = User::factory()->for($this->workshop)->create([
        'name' => 'Alice Johnson',
    ]);

    actingAs($user)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('tableData.data.0.id', $userA->id)
            ->where('tableData.data.1.id', $userB->id)
        );
});

test('users are scoped to current workshop', function () {
    $user = User::factory()->for($this->workshop)->create([
        'name' => 'Alice Owner',
    ]);
    $user->assignRole('Owner');

    $otherWorkshop = Workshop::factory()->create();
    $otherUser = User::factory()->for($otherWorkshop)->create([
        'name' => 'Should Not Appear',
    ]);

    $workshopUser = User::factory()->for($this->workshop)->create([
        'name' => 'Bob User',
    ]);

    actingAs($user)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('tableData.data', 1)
            ->where('tableData.data.0.id', $workshopUser->id)
        );
});

test('users include roles in response', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    $otherUser = User::factory()->for($this->workshop)->create();
    $otherUser->assignRole('Owner');

    actingAs($user)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('tableData.data.0.roles')
            ->where('tableData.data.0.roles.0', 'Owner')
        );
});

test('users include all required fields', function () {
    $user = User::factory()->for($this->workshop)->create();
    $user->assignRole('Owner');

    User::factory()->for($this->workshop)->create();

    actingAs($user)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('tableData.data.0.id')
            ->has('tableData.data.0.name')
            ->has('tableData.data.0.email')
            ->has('tableData.data.0.roles')
            ->has('tableData.data.0.created_at')
        );
});
