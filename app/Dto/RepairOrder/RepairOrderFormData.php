<?php

namespace App\Dto\RepairOrder;

use App\Dto\Common\MediaData;
use App\Enums\RepairOrderStatus;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RepairOrderFormData extends Data
{
    public function __construct(
        public int $id,
        public int $vehicle_id,
        public RepairOrderStatus $status,
        public string $problem_description,
        #[DataCollectionOf(MediaData::class)]
        public DataCollection $images,
    ) {}
}
