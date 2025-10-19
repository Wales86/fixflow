<?php

namespace App\Dto\Common;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SelectOptionData extends Data
{
    public function __construct(
        public string $value,
        public string $label,
    ) {}
}
