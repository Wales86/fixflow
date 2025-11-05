<?php

namespace App\Dto\TimeTracking;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TimeEntryEditData extends Data
{
    public function __construct(
        public int $id,
        public int $repair_order_id,
        public int $mechanic_id,
        public int $duration_minutes,
        public ?string $description,
        public TimeEntryMechanicData $mechanic
    ) {}
}
