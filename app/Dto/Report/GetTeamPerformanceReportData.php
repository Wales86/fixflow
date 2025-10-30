<?php

namespace App\Dto\Report;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class GetTeamPerformanceReportData extends Data
{
    public readonly Carbon $startDate;

    public readonly Carbon $endDate;

    public function __construct(
        ?string $start_date = null,
        ?string $end_date = null,
    ) {
        // Default to last 30 days if not provided
        $this->startDate = $start_date
            ? Carbon::parse($start_date)->startOfDay()
            : now()->subDays(30)->startOfDay();

        $this->endDate = $end_date
            ? Carbon::parse($end_date)->endOfDay()
            : now()->endOfDay();
    }
}
