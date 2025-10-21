<?php

namespace App\Enums;

use App\Models\Client;
use App\Models\RepairOrder;
use App\Models\Vehicle;

enum NotableType: string
{
    case RepairOrder = 'repair_order';
    case Client = 'client';
    case Vehicle = 'vehicle';

    public function modelClass(): string
    {
        return match ($this) {
            self::RepairOrder => RepairOrder::class,
            self::Client => Client::class,
            self::Vehicle => Vehicle::class,
        };
    }

    public static function fromModelClass(string $modelClass): self
    {
        return match ($modelClass) {
            RepairOrder::class => self::RepairOrder,
            Client::class => self::Client,
            Vehicle::class => self::Vehicle,
            default => throw new \InvalidArgumentException("Unknown model class: {$modelClass}"),
        };
    }
}
