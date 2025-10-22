<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'Owner';
    case OFFICE = 'Office';
    case MECHANIC = 'Mechanic';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Właściciel',
            self::OFFICE => 'Biuro',
            self::MECHANIC => 'Mechanik',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn (self $role) => [
                'value' => $role->value,
                'label' => $role->label(),
            ],
            self::cases()
        );
    }
}
