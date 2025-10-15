<?php

namespace App\Dto\Client;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ClientListItemData extends Data
{
    public function __construct(
        public int $id,
        public string $first_name,
        public ?string $last_name,
        public string $phone_number,
        public ?string $email,
        public int $vehicles_count,
    ) {}
}
