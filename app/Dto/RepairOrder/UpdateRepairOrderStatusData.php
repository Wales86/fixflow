<?php

namespace App\Dto\RepairOrder;

use App\Enums\RepairOrderStatus;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UpdateRepairOrderStatusData extends Data
{
    public function __construct(
        #[Required, Enum(RepairOrderStatus::class)]
        public readonly RepairOrderStatus $status,
    ) {}
}
