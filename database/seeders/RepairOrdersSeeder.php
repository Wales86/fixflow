<?php

namespace Database\Seeders;

use App\Enums\RepairOrderStatus;
use App\Models\InternalNote;
use App\Models\Mechanic;
use App\Models\RepairOrder;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RepairOrdersSeeder extends Seeder
{
    public function run(): void
    {
        // Get the workshop
        $workshop = Workshop::first();

        if (! $workshop) {
            $this->command->error('No workshop found. Run WorkshopSeeder first.');

            return;
        }

        // Get the vehicles
        $vehicles = Vehicle::where('workshop_id', $workshop->id)->get();

        if ($vehicles->isEmpty()) {
            $this->command->error('No vehicles found. Run VehiclesSeeder first.');

            return;
        }

        // Get mechanics and users for time entries and internal notes
        $mechanics = Mechanic::where('workshop_id', $workshop->id)->get();
        $users = User::where('workshop_id', $workshop->id)->get();

        if ($mechanics->isEmpty() || $users->isEmpty()) {
            $this->command->error('No mechanics or users found. Run MechanicsSeeder and UsersSeeder first.');

            return;
        }

        // Create repair orders with different statuses and dates
        $repairOrdersData = [
            [
                'vehicle_index' => 0,
                'problem_description' => 'Wyciek oleju silnikowego',
                'status' => RepairOrderStatus::CLOSED,
                'started_at' => Carbon::now()->subDays(30),
                'finished_at' => Carbon::now()->subDays(25),
                'time_entries' => [
                    ['mechanic_index' => 0, 'duration_minutes' => 240, 'description' => 'Zdiagnozowano wyciek oleju w bloku silnika'],
                    ['mechanic_index' => 1, 'duration_minutes' => 180, 'description' => 'Wymieniono uszczelki silnika'],
                ],
                'internal_note' => 'Klient poprosił o przyspieszenie naprawy ze względu na nadchodzącą podróż.',
            ],
            [
                'vehicle_index' => 1,
                'problem_description' => 'Wymiana klocków hamulcowych',
                'status' => RepairOrderStatus::READY_FOR_PICKUP,
                'started_at' => Carbon::now()->subDays(15),
                'finished_at' => null,
                'time_entries' => [
                    ['mechanic_index' => 2, 'duration_minutes' => 120, 'description' => 'Wymieniono przednie klocki hamulcowe'],
                    ['mechanic_index' => 0, 'duration_minutes' => 90, 'description' => 'Wymieniono tylne klocki hamulcowe'],
                ],
            ],
            [
                'vehicle_index' => 2,
                'problem_description' => 'Klimatyzacja nie działa',
                'status' => RepairOrderStatus::IN_PROGRESS,
                'started_at' => Carbon::now()->subDays(5),
                'finished_at' => null,
                'time_entries' => [
                    ['mechanic_index' => 1, 'duration_minutes' => 60, 'description' => 'Sprawdzono układ klimatyzacji'],
                ],
                'internal_note' => 'Części na zamówieniu, oczekiwana dostawa za 3 dni.',
            ],
            [
                'vehicle_index' => 3,
                'problem_description' => 'Wymiana paska rozrządu',
                'status' => RepairOrderStatus::AWAITING_PARTS,
                'started_at' => Carbon::now()->subDays(10),
                'finished_at' => null,
                'time_entries' => [
                    ['mechanic_index' => 2, 'duration_minutes' => 45, 'description' => 'Zamówiono zestaw paska rozrządu'],
                ],
            ],
            [
                'vehicle_index' => 4,
                'problem_description' => 'Diagnostyka kontrolki check engine',
                'status' => RepairOrderStatus::DIAGNOSIS,
                'started_at' => Carbon::now()->subDays(2),
                'finished_at' => null,
                'time_entries' => [
                    ['mechanic_index' => 0, 'duration_minutes' => 30, 'description' => 'Podłączono narzędzie diagnostyczne'],
                ],
            ],
            [
                'vehicle_index' => 5,
                'problem_description' => 'Naprawa zawieszenia',
                'status' => RepairOrderStatus::CLOSED,
                'started_at' => Carbon::now()->subDays(20),
                'finished_at' => Carbon::now()->subDays(18),
                'time_entries' => [
                    ['mechanic_index' => 1, 'duration_minutes' => 300, 'description' => 'Wymieniono elementy przedniego zawieszenia'],
                    ['mechanic_index' => 2, 'duration_minutes' => 150, 'description' => 'Wyregulowano koła'],
                ],
            ],
            [
                'vehicle_index' => 6,
                'problem_description' => 'Wymiana akumulatora',
                'status' => RepairOrderStatus::NEW,
                'started_at' => Carbon::now()->subDays(1),
                'finished_at' => null,
                'time_entries' => [], // No time entries for NEW status
            ],
            [
                'vehicle_index' => 7,
                'problem_description' => 'Wymiana oleju w skrzyni biegów',
                'status' => RepairOrderStatus::AWAITING_CONTACT,
                'started_at' => Carbon::now()->subDays(7),
                'finished_at' => null,
                'time_entries' => [
                    ['mechanic_index' => 0, 'duration_minutes' => 75, 'description' => 'Odpompowano stary olej przekładniowy'],
                ],
            ],
            [
                'vehicle_index' => 8,
                'problem_description' => 'Rotacja i wyważanie opon',
                'status' => RepairOrderStatus::CLOSED,
                'started_at' => Carbon::now()->subDays(14),
                'finished_at' => Carbon::now()->subDays(13),
                'time_entries' => [
                    ['mechanic_index' => 1, 'duration_minutes' => 90, 'description' => 'Przekręcono i wyważono wszystkie opony'],
                ],
            ],
        ];

        foreach ($repairOrdersData as $orderData) {
            $vehicle = $vehicles[$orderData['vehicle_index']];

            // Set the creation time to the start time of the repair to ensure chronological order
            Carbon::setTestNow($orderData['started_at']);

            $repairOrder = RepairOrder::create([
                'workshop_id' => $workshop->id,
                'vehicle_id' => $vehicle->id,
                'status' => $orderData['status'],
                'problem_description' => $orderData['problem_description'],
                'started_at' => $orderData['started_at'],
                'finished_at' => $orderData['finished_at'],
            ]);

            // If the order is closed, set the updated_at to the finished_at time
            if ($repairOrder->finished_at) {
                $repairOrder->updated_at = $repairOrder->finished_at;
                $repairOrder->save();
            }

            // Create time entries for orders with status other than NEW
            if ($orderData['status'] !== RepairOrderStatus::NEW) {
                foreach ($orderData['time_entries'] as $timeEntryData) {
                    // Mock time entry creation time to be slightly after the repair order start
                    $timeEntryCreationTime = $orderData['started_at']->copy()->addHours(rand(1, 8));
                    // Ensure the creation time is not after the finish time for closed orders
                    if ($orderData['finished_at'] && $timeEntryCreationTime->isAfter($orderData['finished_at'])) {
                        $timeEntryCreationTime = $orderData['started_at']->copy()->addMinutes(rand(15, 60));
                    }
                    Carbon::setTestNow($timeEntryCreationTime);

                    TimeEntry::create([
                        'repair_order_id' => $repairOrder->id,
                        'mechanic_id' => $mechanics[$timeEntryData['mechanic_index']]->id,
                        'duration_minutes' => $timeEntryData['duration_minutes'],
                        'description' => $timeEntryData['description'],
                    ]);
                }
            }

            // Add internal note if it exists
            if (isset($orderData['internal_note'])) {
                Carbon::setTestNow($orderData['started_at']->copy()->addMinutes(rand(5, 60)));
                InternalNote::create([
                    'notable_type' => RepairOrder::class,
                    'notable_id' => $repairOrder->id,
                    'content' => $orderData['internal_note'],
                    'author_id' => $users->first()->id,
                    'author_type' => User::class,
                ]);
            }
        }

        // Reset the mocked time
        Carbon::setTestNow();
    }
}
