<?php

namespace App\Dto\RepairOrder;

use App\Dto\Common\ActivityLogData;
use App\Dto\InternalNote\InternalNoteData;
use App\Dto\TimeTracking\TimeEntryData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RepairOrderShowPagePropsData extends Data
{
    public function __construct(
        public RepairOrderShowData $order,
        #[DataCollectionOf(TimeEntryData::class)]
        public DataCollection $time_entries,
        #[DataCollectionOf(InternalNoteData::class)]
        public DataCollection $internal_notes,
        #[DataCollectionOf(ActivityLogData::class)]
        public DataCollection $activity_log,
    ) {}
}
