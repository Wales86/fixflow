<?php

namespace App\Dto;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class RegisterWorkshopData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $workshop_name,
        #[Required, StringType, Max(255)]
        public string $owner_name,
        #[Required, Email, Max(255), Unique('users', 'email')]
        public string $email,
        #[Required, StringType, Min(8), Confirmed]
        public string $password,
        #[Required, StringType]
        public string $password_confirmation,
    ) {}
}
