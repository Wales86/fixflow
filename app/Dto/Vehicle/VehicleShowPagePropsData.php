<?php

namespace App\Dto\Vehicle;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class VehicleShowPagePropsData extends Data
{
    public function __construct(
        public VehicleData $vehicle,
        public LengthAwarePaginator $repair_orders,
    ) {}
}
