<?php

namespace App\Dto\Vehicle;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class VehicleData extends Data
{
    public function __construct(
        public int $id,
        public string $make,
        public string $model,
        public int $year,
        public string $registration_number,
        public string $vin,
        public ?int $repair_orders_count,
    ) {}
}
