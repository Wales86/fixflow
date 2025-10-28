<?php

namespace App\Dto\User;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CreateUserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role,
    ) {}
}
