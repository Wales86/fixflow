<?php

namespace Database\Seeders;

use App\Models\Mechanic;
use App\Models\Workshop;
use Illuminate\Database\Seeder;

class MechanicsSeeder extends Seeder
{
    public function run(): void
    {
        // Get the workshop
        $workshop = Workshop::first();

        if (! $workshop) {
            $this->command->error('No workshop found. Run WorkshopSeeder first.');

            return;
        }

        // Create 3 mechanics with Polish names
        $mechanics = [
            [
                'first_name' => 'Piotr',
                'last_name' => 'Wiśniewski',
                'is_active' => true,
            ],
            [
                'first_name' => 'Marek',
                'last_name' => 'Lewandowski',
                'is_active' => true,
            ],
            [
                'first_name' => 'Tomasz',
                'last_name' => 'Dąbrowski',
                'is_active' => true,
            ],
        ];

        foreach ($mechanics as $mechanicData) {
            Mechanic::create([
                'workshop_id' => $workshop->id,
                ...$mechanicData,
            ]);
        }
    }
}
