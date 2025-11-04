<?php

namespace App\Dto\RepairOrder;

use App\Enums\RepairOrderStatus;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UpdateRepairOrderStatusData extends Data
{
    public function __construct(
        #[Required, Enum(RepairOrderStatus::class)]
        public readonly RepairOrderStatus $status,
        #[Nullable, Exists('mechanics', 'id')]
        public readonly ?int $mechanic_id,
    ) {}
}
