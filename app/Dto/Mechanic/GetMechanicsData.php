<?php

namespace App\Dto\Mechanic;

use Spatie\LaravelData\Data;

class GetMechanicsData extends Data
{
    public function __construct(
        public ?bool $active = null,
    ) {}
}
