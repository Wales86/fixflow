<?php

namespace App\Dto\Report;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RepairOrderDetailData extends Data
{
    public function __construct(
        public int $id,
        public string $vehicleInfo,
        public string $status,
        public ?string $startedAt,
        public ?string $finishedAt,
        public int $totalMinutes,
        #[DataCollectionOf(TimeEntryDetailData::class)]
        public DataCollection $timeEntries,
    ) {}
}
