<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === PRODUCTION SEEDERS ===
        // These seeders contain essential data and should ALWAYS run

        // Seed roles and permissions first (REQUIRED)
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // === DEVELOPMENT SEEDERS ===
        // These seeders contain test data and should ONLY run in development/local environments
        // if (! app()->environment('production')) {
            $this->call([
                WorkshopSeeder::class,
            ]);

            $this->call([
                DevelopmentSeeder::class,
            ]);
        // }
    }
}
