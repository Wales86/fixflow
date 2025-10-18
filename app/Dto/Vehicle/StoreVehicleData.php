<?php

namespace App\Dto\Vehicle;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StoreVehicleData extends Data
{
    public function __construct(
        #[Required, IntegerType, Exists('clients', 'id')]
        public int $client_id,
        #[Required, StringType, Max(255)]
        public string $make,
        #[Required, StringType, Max(255)]
        public string $model,
        #[Required, IntegerType, Min(1900), Max(2100)]
        public int $year,
        #[Required, StringType, Max(17)]
        public string $vin,
        #[Required, StringType, Max(20)]
        public string $registration_number,
    ) {}
}
