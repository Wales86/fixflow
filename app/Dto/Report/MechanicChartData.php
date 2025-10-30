<?php

namespace App\Dto\Report;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MechanicChartData extends Data
{
    public function __construct(
        public string $name,
        public int $hours,
    ) {}
}
