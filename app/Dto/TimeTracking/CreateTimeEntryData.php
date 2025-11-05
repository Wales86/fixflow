<?php

namespace App\Dto\TimeTracking;

use Spatie\LaravelData\Data;

class CreateTimeEntryData extends Data
{
    public function __construct(
        public int $repair_order_id,
        public int $mechanic_id,
        public int $duration_hours_input,
        public int $duration_minutes_input,
        public ?string $description,
    ) {}
}
