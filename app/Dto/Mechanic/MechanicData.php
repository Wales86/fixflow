<?php

namespace App\Dto\Mechanic;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MechanicData extends Data
{
    public function __construct(
        public int $id,
        public string $first_name,
        public string $last_name,
        public bool $is_active,
        public ?int $time_entries_count,
        public string $created_at,
    ) {}
}
