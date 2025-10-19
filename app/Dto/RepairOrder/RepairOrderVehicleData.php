<?php

namespace App\Dto\RepairOrder;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RepairOrderVehicleData extends Data
{
    public function __construct(
        public int $id,
        public string $make,
        public string $model,
        public string $registration_number,
    ) {}
}
