<?php

namespace App\Dto\Common;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ActivityLogData extends Data
{
    public function __construct(
        public int $id,
        public string $description,
        public ?string $event,
        public ?array $properties,
        public string $created_at,
        public ?ActivityLogCauserData $causer = null,
    ) {}
}
