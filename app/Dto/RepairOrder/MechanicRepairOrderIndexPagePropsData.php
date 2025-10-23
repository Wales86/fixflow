<?php

namespace App\Dto\RepairOrder;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MechanicRepairOrderIndexPagePropsData extends Data
{
    public function __construct(
        /** @var DataCollection<int, MechanicRepairOrderCardData> */
        public DataCollection $orders,
        public ?string $search = null,
    ) {}
}
