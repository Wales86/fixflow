<?php

namespace App\Dto\InternalNote;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class InternalNoteData extends Data
{
    public function __construct(
        public int $id,
        public int $repair_order_id,
        public string $content,
        public int $author_id,
        public string $author_type,
        public string $created_at,
        public ?InternalNoteAuthorData $author = null,
    ) {}
}
