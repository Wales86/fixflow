<?php

namespace App\Dto\Vehicle;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class VehicleClientData extends Data
{
    public function __construct(
        public int $id,
        public string $first_name,
        public ?string $last_name,
    ) {}
}
