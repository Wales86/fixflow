<?php

namespace App\Dto\Client;

use App\Dto\Vehicle\VehicleData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ClientShowPagePropsData extends Data
{
    public function __construct(
        public ClientData $client,
        #[DataCollectionOf(VehicleData::class)]
        public DataCollection $vehicles,
    ) {}
}
