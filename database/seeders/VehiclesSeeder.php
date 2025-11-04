<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Vehicle;
use App\Models\Workshop;
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

        if ($clients->count() < 9) {
            $this->command->error('Not enough clients found. Run ClientsSeeder first.');

            return;
        }

        // Create 9 vehicles (one for each of the first 9 clients)
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
            [
                'client_id' => $clients[2]->id,
                'make' => 'Honda',
                'model' => 'Civic',
                'year' => 2019,
                'vin' => '2HGFC2F59JH123456',
                'registration_number' => 'GD 34567',
            ],
            [
                'client_id' => $clients[3]->id,
                'make' => 'BMW',
                'model' => '3 Series',
                'year' => 2021,
                'vin' => 'WBA8E9G57JNU12345',
                'registration_number' => 'PO 45678',
            ],
            [
                'client_id' => $clients[4]->id,
                'make' => 'Ford',
                'model' => 'Focus',
                'year' => 2017,
                'vin' => '1FADP3F2XHL123456',
                'registration_number' => 'WR 56789',
            ],
            [
                'client_id' => $clients[5]->id,
                'make' => 'Audi',
                'model' => 'A4',
                'year' => 2022,
                'vin' => 'WAUENAF4XMN123456',
                'registration_number' => 'LD 67890',
            ],
            [
                'client_id' => $clients[6]->id,
                'make' => 'Mercedes-Benz',
                'model' => 'C-Class',
                'year' => 2020,
                'vin' => 'WDDWK4JB8KF123456',
                'registration_number' => 'SZ 78901',
            ],
            [
                'client_id' => $clients[7]->id,
                'make' => 'Opel',
                'model' => 'Astra',
                'year' => 2019,
                'vin' => 'W0L0SDL08L1234567',
                'registration_number' => 'BG 89012',
            ],
            [
                'client_id' => $clients[8]->id,
                'make' => 'Renault',
                'model' => 'Clio',
                'year' => 2018,
                'vin' => 'VF1RFB00012345678',
                'registration_number' => 'LU 90123',
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            Vehicle::create([
                'workshop_id' => $workshop->id,
                ...$vehicleData,
            ]);
        }
    }
}
