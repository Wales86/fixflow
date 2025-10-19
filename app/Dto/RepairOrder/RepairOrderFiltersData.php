<?php

namespace App\Dto\RepairOrder;

use App\Dto\Common\FiltersData;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RepairOrderFiltersData extends FiltersData
{
    public ?string $status = null;
}
