<?php

namespace App\Dto\Client;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ClientData extends Data
{
    public function __construct(
        public int $id,
        public string $first_name,
        public ?string $last_name,
        public string $phone_number,
        public ?string $email,
        public ?string $address_street,
        public ?string $address_city,
        public ?string $address_postal_code,
        public ?string $address_country,
        public string $created_at,
    ) {}
}
