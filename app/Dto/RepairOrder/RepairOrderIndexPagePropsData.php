<?php

namespace App\Dto\RepairOrder;

use App\Dto\Common\FilterableTablePagePropsData;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RepairOrderIndexPagePropsData extends FilterableTablePagePropsData
{
    public array $statusOptions;
}
