<?php

use App\Models\Client;
use App\Models\User;
use App\Models\Workshop;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

test('vehicle store validation returns polish error messages', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create(['workshop_id' => $workshop->id]);
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user->assignRole($ownerRole);

    actingAs($user);

    $response = postJson(route('vehicles.store'), []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'client_id' => 'Pole klient jest wymagane.',
            'make' => 'Pole marka jest wymagane.',
            'model' => 'Pole model jest wymagane.',
            'year' => 'Pole rok jest wymagane.',
            'vin' => 'Pole VIN jest wymagane.',
            'registration_number' => 'Pole numer rejestracyjny jest wymagane.',
        ]);
});

test('vehicle year validation returns polish error messages', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create(['workshop_id' => $workshop->id]);
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user->assignRole($ownerRole);
    $client = Client::factory()->create(['workshop_id' => $workshop->id]);

    actingAs($user);

    $response = postJson(route('vehicles.store'), [
        'client_id' => $client->id,
        'make' => 'Toyota',
        'model' => 'Corolla',
        'year' => 1800,
        'vin' => '1HGBH41JXMN109186',
        'registration_number' => 'ABC123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'year' => 'Rok produkcji musi być większy lub równy 1900.',
        ]);
});

test('vehicle client exists validation returns polish error message', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create(['workshop_id' => $workshop->id]);
    $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $user->assignRole($ownerRole);

    actingAs($user);

    $response = postJson(route('vehicles.store'), [
        'client_id' => 99999,
        'make' => 'Toyota',
        'model' => 'Corolla',
        'year' => 2020,
        'vin' => '1HGBH41JXMN109186',
        'registration_number' => 'ABC123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'client_id' => 'Wybrany klient nie istnieje.',
        ]);
});
