<?php

namespace App\Dto\Report;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MechanicPerformanceStatsData extends Data
{
    public function __construct(
        public string $mechanic,
        public int $totalHours,
        public int $ordersCompleted,
        public float $avgTimePerOrder,
    ) {}
}
