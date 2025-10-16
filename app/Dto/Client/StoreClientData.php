<?php

namespace App\Dto\Client;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StoreClientData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $last_name,
        #[Required, StringType, Max(255)]
        public string $first_name,
        #[Required, StringType, Max(50)]
        public string $phone_number,
        #[Email, Max(255)]
        public string|Optional $email,
        #[StringType, Max(255)]
        public string|Optional $address_street,
        #[StringType, Max(255)]
        public string|Optional $address_city,
        #[StringType, Max(20)]
        public string|Optional $address_postal_code,
        #[StringType, Max(100)]
        public string|Optional $address_country,
    ) {}
}
