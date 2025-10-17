<?php

namespace App\Dto\Vehicle;

use App\Dto\Common\FiltersData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class VehicleIndexPagePropsData extends Data
{
    public function __construct(
        public LengthAwarePaginator $vehicles,
        public FiltersData $filters,
    ) {}
}
