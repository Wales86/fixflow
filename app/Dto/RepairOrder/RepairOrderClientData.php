<?php

namespace App\Dto\RepairOrder;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RepairOrderClientData extends Data
{
    public function __construct(
        public int $id,
        public string $first_name,
        public ?string $last_name,
        public string $phone_number,
    ) {}
}
