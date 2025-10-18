<?php

namespace App\Dto\Vehicle;

use App\Dto\Client\ClientSelectOptionData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class VehicleEditPagePropsData extends Data
{
    public function __construct(
        public VehicleData $vehicle,
        #[DataCollectionOf(ClientSelectOptionData::class)]
        public DataCollection $clients,
    ) {}
}
