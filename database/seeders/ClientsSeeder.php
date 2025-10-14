<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Workshop;
use Illuminate\Database\Seeder;

class ClientsSeeder extends Seeder
{
    public function run(): void
    {
        // Get the workshop
        $workshop = Workshop::first();

        if (! $workshop) {
            $this->command->error('No workshop found. Run WorkshopSeeder first.');

            return;
        }

        // Create 2 clients with Polish names
        $clients = [
            [
                'first_name' => 'Krzysztof',
                'last_name' => 'Wójcik',
                'phone_number' => '+48 601 234 567',
                'email' => 'krzysztof.wojcik@example.pl',
                'address_street' => 'ul. Kwiatowa 15',
                'address_city' => 'Warszawa',
                'address_postal_code' => '01-234',
                'address_country' => 'Polska',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Kamiński',
                'phone_number' => '+48 602 345 678',
                'email' => 'maria.kaminski@example.pl',
                'address_street' => 'ul. Słoneczna 8',
                'address_city' => 'Kraków',
                'address_postal_code' => '30-567',
                'address_country' => 'Polska',
            ],
        ];

        foreach ($clients as $clientData) {
            Client::create([
                'workshop_id' => $workshop->id,
                ...$clientData,
            ]);
        }
    }
}
