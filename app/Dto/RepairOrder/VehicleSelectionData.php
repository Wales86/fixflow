<?php

namespace App\Dto\RepairOrder;

use App\Models\Vehicle;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class VehicleSelectionData extends Data
{
    public function __construct(
        public int $id,
        public string $display_name,
        public string $registration_number,
        public string $client_name,
    ) {}

    public static function fromModel(Vehicle $vehicle): self
    {
        return new self(
            id: $vehicle->id,
            display_name: $vehicle->display_name,
            registration_number: $vehicle->registration_number,
            client_name: $vehicle->client->name,
        );
    }
}
