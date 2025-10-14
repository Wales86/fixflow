<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Seed workshop with owner user
        $this->call([
            WorkshopSeeder::class,
        ]);

        // Seed additional users, mechanics, clients, and vehicles
        $this->call([
            UsersSeeder::class,
            MechanicsSeeder::class,
            ClientsSeeder::class,
            VehiclesSeeder::class,
        ]);
    }
}
