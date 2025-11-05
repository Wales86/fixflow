<?php

namespace App\Dto\InternalNote;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UpdateInternalNoteData extends Data
{
    public function __construct(
        #[Required, StringType, Max(5000)]
        public string $content,
    ) {}
}
