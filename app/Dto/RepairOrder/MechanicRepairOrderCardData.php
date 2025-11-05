<?php

namespace App\Dto\RepairOrder;

use App\Enums\RepairOrderStatus;
use App\Models\RepairOrder;
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

    public static function fromRepairOrder(RepairOrder $repairOrder): self
    {
        return new self(
            id: $repairOrder->id,
            status: $repairOrder->status,
            problem_description: $repairOrder->problem_description,
            total_time_minutes: $repairOrder->total_time_minutes,
            created_at: $repairOrder->created_at->toIso8601String(),
            vehicle: RepairOrderVehicleData::from($repairOrder->vehicle),
            client: RepairOrderClientData::from($repairOrder->vehicle->client),
        );
    }
}
