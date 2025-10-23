<?php

namespace App\Dto\RepairOrder;

use App\Enums\RepairOrderStatus;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MechanicRepairOrderCardData extends Data
{
    public function __construct(
        public int $id,
        public RepairOrderStatus $status,
        public string $problem_description,
        public int $total_time_minutes,
        public string $created_at,
        public RepairOrderVehicleData $vehicle,
        public RepairOrderClientData $client,
    ) {}
}
