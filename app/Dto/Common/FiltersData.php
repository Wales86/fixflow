<?php

namespace App\Dto\Common;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FiltersData extends Data
{
    public function __construct(
        public ?string $search = null,
        public ?string $sort = null,
        public ?string $direction = null,
    ) {}
}
