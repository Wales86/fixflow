<?php

namespace App\Dto\Common;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FilterableTablePagePropsData extends Data
{
    public function __construct(
        public LengthAwarePaginator $tableData,
        public FiltersData $filters,
    ) {}
}
