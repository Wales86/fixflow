<?php

namespace App\Dto\Report;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TeamPerformanceReportData extends Data
{
    public function __construct(
        public int $totalMinutes,
        public int $totalOrders,
        public int $activeMechanics,
        #[DataCollectionOf(MechanicChartData::class)]
        public DataCollection $chartData,
        #[DataCollectionOf(MechanicPerformanceStatsData::class)]
        public DataCollection $tableData,
    ) {}
}
