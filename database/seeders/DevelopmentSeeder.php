<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Seed development/testing data.
     * This seeder should NOT be run in production.
     */
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            MechanicsSeeder::class,
            ClientsSeeder::class,
            VehiclesSeeder::class,
            RepairOrdersSeeder::class,
        ]);
    }
}
