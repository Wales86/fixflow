<?php

namespace App\Dto\TimeTracking;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MechanicSelectOptionData extends Data
{
    public function __construct(
        public int $id,
        public string $full_name,
        public bool $is_active,
    ) {}
}
