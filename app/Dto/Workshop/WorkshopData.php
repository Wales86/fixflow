<?php

namespace App\Dto\Workshop;

use App\Models\Workshop;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class WorkshopData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $created_at,
    ) {}

    public static function fromModel(Workshop $workshop): self
    {
        return new self(
            id: $workshop->id,
            name: $workshop->name,
            created_at: $workshop->created_at->format('Y-m-d H:i:s'),
        );
    }
}