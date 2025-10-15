<?php

namespace App\Dto\Dashboard;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RecentOrderData extends Data
{
    public function __construct(
        public int $id,
        public string $vehicle,
        public string $client,
        public string $status,
        public string $created_at,
    ) {}
}
