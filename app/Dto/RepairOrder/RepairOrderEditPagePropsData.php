<?php

namespace App\Dto\RepairOrder;

use App\Dto\Common\SelectOptionData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RepairOrderEditPagePropsData extends Data
{
    public function __construct(
        public RepairOrderFormData $repairOrder,
        #[DataCollectionOf(VehicleSelectionData::class)]
        public DataCollection $vehicles,
        #[DataCollectionOf(SelectOptionData::class)]
        public DataCollection $statuses,
    ) {}
}
