<?php

namespace App\Dto\RepairOrder;

use App\Enums\RepairOrderStatus;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RepairOrderData extends Data
{
    public function __construct(
        public int $id,
        public int $vehicle_id,
        public RepairOrderStatus $status,
        public string $problem_description,
        public ?string $started_at,
        public ?string $finished_at,
        public int $total_time_minutes,
        public string $created_at,
    ) {}
}
