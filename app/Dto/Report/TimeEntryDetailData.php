<?php

namespace App\Dto\Report;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TimeEntryDetailData extends Data
{
    public function __construct(
        public int $id,
        public Carbon $date,
        public int $durationMinutes,
        public ?string $description,
    ) {}
}
