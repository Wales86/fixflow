<?php

namespace App\Enums;

enum UserRole: string
{
    case Owner = 'Owner';
    case Office = 'Office';
    case Mechanic = 'Mechanic';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Właściciel',
            self::Office => 'Biuro',
            self::Mechanic => 'Mechanik',
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
