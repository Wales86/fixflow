<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkshopSeeder extends Seeder
{
    public function run(): void
    {
        // Create workshop
        $workshop = Workshop::create([
            'name' => 'Auto-Serwis Kowalski',
        ]);

        // Make this workshop the current tenant
        $workshop->makeCurrent();

        // Create owner user for the workshop
        $owner = User::create([
            'workshop_id' => $workshop->id,
            'name' => 'Jan Kowalski',
            'email' => 'jan.kowalski@fixflow.pl',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Assign Owner role
        $owner->assignRole(UserRole::OWNER->value);
    }
}
