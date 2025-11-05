<?php

namespace App\Dto\Report;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MechanicPerformanceStatsData extends Data
{
    public function __construct(
        public int $mechanicId,
        public string $mechanic,
        public int $totalMinutes,
        public int $ordersCompleted,
        public float $avgTimePerOrder,
    ) {}
}
