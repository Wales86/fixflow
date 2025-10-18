<?php

namespace App\Dto\Client;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ClientForListData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
