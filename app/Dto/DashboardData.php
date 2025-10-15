<?php

namespace App\Dto;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class DashboardData extends Data
{
    public function __construct(
        public int $activeOrdersCount,
        public int $pendingOrdersCount,
        public int $todayTimeEntriesTotal,
        #[DataCollectionOf(RecentOrderData::class)]
        public DataCollection $recentOrders,
    ) {}
}
