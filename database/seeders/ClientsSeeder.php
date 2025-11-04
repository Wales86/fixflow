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

        // Create 10 clients with Polish names
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
            [
                'first_name' => 'Anna',
                'last_name' => 'Nowak',
                'phone_number' => '+48 603 456 789',
                'email' => 'anna.nowak@example.pl',
                'address_street' => 'ul. Główna 22',
                'address_city' => 'Gdańsk',
                'address_postal_code' => '80-123',
                'address_country' => 'Polska',
            ],
            [
                'first_name' => 'Piotr',
                'last_name' => 'Kowalewski',
                'phone_number' => '+48 604 567 890',
                'email' => 'piotr.kowalewski@example.pl',
                'address_street' => 'ul. Lipowa 5',
                'address_city' => 'Poznań',
                'address_postal_code' => '60-456',
                'address_country' => 'Polska',
            ],
            [
                'first_name' => 'Ewa',
                'last_name' => 'Wiśniewska',
                'phone_number' => '+48 605 678 901',
                'email' => 'ewa.wisniewska@example.pl',
                'address_street' => 'ul. Dębowa 12',
                'address_city' => 'Wrocław',
                'address_postal_code' => '50-789',
                'address_country' => 'Polska',
            ],
            [
                'first_name' => 'Marek',
                'last_name' => 'Zieliński',
                'phone_number' => '+48 606 789 012',
                'email' => 'marek.zielinski@example.pl',
                'address_street' => 'ul. Brzozowa 7',
                'address_city' => 'Łódź',
                'address_postal_code' => '90-234',
                'address_country' => 'Polska',
            ],
            [
                'first_name' => 'Barbara',
                'last_name' => 'Szymankowska',
                'phone_number' => '+48 607 890 123',
                'email' => 'barbara.szymankowska@example.pl',
                'address_street' => 'ul. Kasztanowa 18',
                'address_city' => 'Szczecin',
                'address_postal_code' => '70-567',
                'address_country' => 'Polska',
            ],
            [
                'first_name' => 'Tomasz',
                'last_name' => 'Wójtowicz',
                'phone_number' => '+48 608 901 234',
                'email' => 'tomasz.wojtowicz@example.pl',
                'address_street' => 'ul. Akacjowa 9',
                'address_city' => 'Bydgoszcz',
                'address_postal_code' => '85-890',
                'address_country' => 'Polska',
            ],
            [
                'first_name' => 'Magdalena',
                'last_name' => 'Krawczyk',
                'phone_number' => '+48 609 012 345',
                'email' => 'magdalena.krawczyk@example.pl',
                'address_street' => 'ul. Wierzbowa 14',
                'address_city' => 'Lublin',
                'address_postal_code' => '20-123',
                'address_country' => 'Polska',
            ],
            [
                'first_name' => 'Jan',
                'last_name' => 'Pawłowski',
                'phone_number' => '+48 610 123 456',
                'email' => 'jan.pawlowski@example.pl',
                'address_street' => 'ul. Bukowa 3',
                'address_city' => 'Katowice',
                'address_postal_code' => '40-456',
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
