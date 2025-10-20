<?php

namespace App\Dto\InternalNote;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class InternalNoteAuthorData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $type,
    ) {}
}
