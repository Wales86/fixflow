<?php

namespace App\Dto\Mechanic;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StoreMechanicData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $first_name,
        #[Required, StringType, Max(255)]
        public string $last_name,
        #[BooleanType]
        public bool $is_active = true,
    ) {}
}
