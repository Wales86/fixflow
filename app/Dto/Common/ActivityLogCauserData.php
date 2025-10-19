<?php

namespace App\Dto\Common;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ActivityLogCauserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $type,
    ) {}
}
