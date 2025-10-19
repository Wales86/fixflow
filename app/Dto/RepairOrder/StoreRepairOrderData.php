<?php

namespace App\Dto\RepairOrder;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StoreRepairOrderData extends Data
{
    public function __construct(
        #[Required, IntegerType, Exists('vehicles', 'id')]
        public int $vehicle_id,
        #[Required, StringType]
        public string $description,
        #[Nullable, ArrayType]
        public ?array $attachments = null,
    ) {}
}
