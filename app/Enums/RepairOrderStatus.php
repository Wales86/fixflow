<?php

namespace App\Enums;

enum RepairOrderStatus: string
{
    case NEW = 'new';
    case DIAGNOSIS = 'diagnosis';
    case AWAITING_CONTACT = 'awaiting_contact';
    case AWAITING_PARTS = 'awaiting_parts';
    case IN_PROGRESS = 'in_progress';
    case READY_FOR_PICKUP = 'ready_for_pickup';
    case CLOSED = 'closed';

    public function label(): string
    {
        return __("repair_orders.statuses.{$this->value}");
    }

    public static function options(): array
    {
        return array_map(
            fn (self $status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ],
            self::cases()
        );
    }
}
