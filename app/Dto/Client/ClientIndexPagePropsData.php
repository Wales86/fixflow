<?php

namespace App\Dto\Client;

use App\Dto\Common\FiltersData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ClientIndexPagePropsData extends Data
{
    public function __construct(
        public LengthAwarePaginator $clients,
        public FiltersData $filters,
    ) {}
}
