<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Get the workshop
        $workshop = Workshop::first();

        if (! $workshop) {
            $this->command->error('No workshop found. Run WorkshopSeeder first.');

            return;
        }

        // Create office user
        $office = User::create([
            'workshop_id' => $workshop->id,
            'name' => 'Anna Nowak',
            'email' => 'anna.nowak@fixflow.pl',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Assign Office role
        $office->assignRole(UserRole::OFFICE->value);

        // Create mechanic user (shared account for all mechanics)
        $mechanic = User::create([
            'workshop_id' => $workshop->id,
            'name' => 'Mechanicy',
            'email' => 'mechanicy@fixflow.pl',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Assign Mechanic role
        $mechanic->assignRole(UserRole::MECHANIC->value);
    }
}
