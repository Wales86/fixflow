<?php

namespace App\Dto\Dashboard;

use App\Models\RepairOrder;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RecentOrderData extends Data
{
    public function __construct(
        public int $id,
        public string $vehicle,
        public string $client,
        public string $status,
        public string $created_at,
    ) {}

    public static function fromRepairOrder(RepairOrder $order): self
    {
        return new self(
            id: $order->id,
            vehicle: "{$order->vehicle->make} {$order->vehicle->model} {$order->vehicle->year}",
            client: "{$order->client->first_name} {$order->client->last_name}",
            status: $order->status->value,
            created_at: $order->created_at->toISOString(),
        );
    }
}
