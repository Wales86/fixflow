<?php

namespace App\Dto\Report;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MechanicPerformanceReportData extends Data
{
    public function __construct(
        public int $totalMinutes,
        public int $ordersCompleted,
        public float $avgTimePerOrder,
        #[DataCollectionOf(RepairOrderDetailData::class)]
        public DataCollection $repairOrders,
    ) {}
}
