<?php

namespace App\Dto\InternalNote;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StoreInternalNoteData extends Data
{
    public function __construct(
        #[Required, StringType]
        public string $notable_type,
        #[Required, IntegerType]
        public int $notable_id,
        #[Required, StringType, Max(5000)]
        public string $content,
    ) {}
}
