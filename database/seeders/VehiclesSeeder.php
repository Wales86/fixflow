<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\RepairOrder;
use App\Models\Vehicle;
use App\Models\Workshop;
use App\Enums\RepairOrderStatus;
use Illuminate\Database\Seeder;

class VehiclesSeeder extends Seeder
{
    public function run(): void
    {
        // Get the workshop
        $workshop = Workshop::first();

        if (! $workshop) {
            $this->command->error('No workshop found. Run WorkshopSeeder first.');

            return;
        }

        // Get the clients
        $clients = Client::where('workshop_id', $workshop->id)->get();

        if ($clients->count() < 2) {
            $this->command->error('Not enough clients found. Run ClientsSeeder first.');

            return;
        }

        // Create 2 vehicles
        $vehicles = [
            [
                'client_id' => $clients[0]->id,
                'make' => 'Volkswagen',
                'model' => 'Golf',
                'year' => 2018,
                'vin' => 'WVWZZZ1KZAW123456',
                'registration_number' => 'WW 12345',
            ],
            [
                'client_id' => $clients[1]->id,
                'make' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2020,
                'vin' => '2T1BURHE5KC123789',
                'registration_number' => 'KR 67890',
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            $vehicle = Vehicle::create([
                'workshop_id' => $workshop->id,
                ...$vehicleData,
            ]);

            // Create one repair order for each vehicle
            RepairOrder::factory()->for($vehicle)->for($workshop)->create([
                'status' => RepairOrderStatus::NEW->value,
            ]);
        }
    }
}
