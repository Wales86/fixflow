<?php

namespace App\Dto\Common;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MediaData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $url,
        public string $mime_type,
        public int $size,
    ) {}
}
