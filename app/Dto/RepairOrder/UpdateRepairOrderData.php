<?php

namespace App\Dto\RepairOrder;

use App\Enums\RepairOrderStatus;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UpdateRepairOrderData extends Data
{
    public function __construct(
        #[Nullable, StringType]
        public ?string $description = null,
        #[Nullable, Enum(RepairOrderStatus::class)]
        public ?RepairOrderStatus $status = null,
    ) {}
}
