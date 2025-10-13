<?php

namespace App\Enums;

enum RepairOrderStatus: string
{
    case New = 'new';
    case Diagnosis = 'diagnosis';
    case AwaitingContact = 'awaiting_contact';
    case AwaitingParts = 'awaiting_parts';
    case InProgress = 'in_progress';
    case ReadyForPickup = 'ready_for_pickup';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Nowe',
            self::Diagnosis => 'Diagnoza',
            self::AwaitingContact => 'Wymaga kontaktu',
            self::AwaitingParts => 'Czeka na części',
            self::InProgress => 'W naprawie',
            self::ReadyForPickup => 'Gotowe do odbioru',
            self::Closed => 'Zamknięte',
        };
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
