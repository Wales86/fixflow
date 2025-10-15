<?php

use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new workshop and owner can register', function () {
    // Create Owner role (normally done in seeding)
    Role::create(['name' => 'Owner']);

    $response = $this->post(route('register'), [
        'workshop_name' => 'Test Workshop',
        'owner_name' => 'Test Owner',
        'email' => 'owner@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    // Assert user is authenticated
    $this->assertAuthenticated();

    // Assert redirect to dashboard
    $response->assertRedirect(route('dashboard', absolute: false));

    // Assert workshop was created
    $this->assertDatabaseHas('workshops', [
        'name' => 'Test Workshop',
    ]);

    // Assert user was created with correct data
    $user = User::where('email', 'owner@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Test Owner')
        ->and($user->workshop_id)->not->toBeNull();

    // Assert user has Owner role
    expect($user->hasRole('Owner'))->toBeTrue();

    // Assert workshop relationship
    expect($user->workshop->name)->toBe('Test Workshop');
});

test('registration requires workshop name', function () {
    $response = $this->post(route('register'), [
        'owner_name' => 'Test Owner',
        'email' => 'owner@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['workshop_name']);
});

test('registration requires owner name', function () {
    $response = $this->post(route('register'), [
        'workshop_name' => 'Test Workshop',
        'email' => 'owner@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['owner_name']);
});

test('registration requires email', function () {
    $response = $this->post(route('register'), [
        'workshop_name' => 'Test Workshop',
        'owner_name' => 'Test Owner',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('registration requires valid email', function () {
    $response = $this->post(route('register'), [
        'workshop_name' => 'Test Workshop',
        'owner_name' => 'Test Owner',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('registration requires unique email', function () {
    // Create existing user
    $workshop = Workshop::create(['name' => 'Existing Workshop']);
    User::create([
        'workshop_id' => $workshop->id,
        'name' => 'Existing User',
        'email' => 'existing@example.com',
        'password' => 'password123',
    ]);

    $response = $this->post(route('register'), [
        'workshop_name' => 'New Workshop',
        'owner_name' => 'New Owner',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('registration requires password', function () {
    $response = $this->post(route('register'), [
        'workshop_name' => 'Test Workshop',
        'owner_name' => 'Test Owner',
        'email' => 'owner@example.com',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('registration requires password confirmation', function () {
    $response = $this->post(route('register'), [
        'workshop_name' => 'Test Workshop',
        'owner_name' => 'Test Owner',
        'email' => 'owner@example.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('registration requires matching password confirmation', function () {
    $response = $this->post(route('register'), [
        'workshop_name' => 'Test Workshop',
        'owner_name' => 'Test Owner',
        'email' => 'owner@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different-password',
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('registration requires minimum 8 character password', function () {
    $response = $this->post(route('register'), [
        'workshop_name' => 'Test Workshop',
        'owner_name' => 'Test Owner',
        'email' => 'owner@example.com',
        'password' => 'pass',
        'password_confirmation' => 'pass',
    ]);

    $response->assertSessionHasErrors(['password']);
});
