<?php

namespace App\Dto\Report;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MechanicSelectOptionData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
