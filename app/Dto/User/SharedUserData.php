<?php

namespace App\Dto\User;

use App\Dto\Workshop\WorkshopData;
use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SharedUserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $created_at,
        public WorkshopData $workshop,
        public array $roles,
        public array $permissions,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            roles: $user->roles->pluck('name')->toArray(),
            permissions: $user->getAllPermissions()->pluck('name')->toArray(),
            created_at: $user->created_at->format('Y-m-d H:i:s'),
            workshop: WorkshopData::fromModel($user->workshop),
        );
    }
}
