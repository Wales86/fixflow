<?php

namespace App\Dto\TimeTracking;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TimeEntryMechanicData extends Data
{
    public function __construct(
        public int $id,
        public string $first_name,
        public string $last_name,
    ) {}
}
