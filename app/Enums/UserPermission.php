<?php

namespace App\Enums;

enum UserPermission: string
{
    // Dashboard permissions
    case VIEW_DASHBOARD = 'view_dashboard';

    // Client permissions
    case VIEW_CLIENTS = 'view_clients';
    case CREATE_CLIENTS = 'create_clients';
    case UPDATE_CLIENTS = 'update_clients';
    case DELETE_CLIENTS = 'delete_clients';

    // Vehicle permissions
    case VIEW_VEHICLES = 'view_vehicles';
    case CREATE_VEHICLES = 'create_vehicles';
    case UPDATE_VEHICLES = 'update_vehicles';
    case DELETE_VEHICLES = 'delete_vehicles';

    // Repair Order permissions
    case VIEW_REPAIR_ORDERS = 'view_repair_orders';
    case VIEW_REPAIR_ORDERS_MECHANIC = 'view_repair_orders_mechanic';
    case CREATE_REPAIR_ORDERS = 'create_repair_orders';
    case UPDATE_REPAIR_ORDERS = 'update_repair_orders';
    case DELETE_REPAIR_ORDERS = 'delete_repair_orders';
    case UPDATE_REPAIR_ORDER_STATUS = 'update_repair_order_status';

    // Internal Note permissions
    case VIEW_INTERNAL_NOTES = 'view_internal_notes';
    case CREATE_INTERNAL_NOTES = 'create_internal_notes';
    case UPDATE_INTERNAL_NOTES = 'update_internal_notes';
    case DELETE_INTERNAL_NOTES = 'delete_internal_notes';

    // Time Entry permissions
    case CREATE_TIME_ENTRIES = 'create_time_entries';
    case UPDATE_TIME_ENTRIES = 'update_time_entries';
    case DELETE_TIME_ENTRIES = 'delete_time_entries';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function clients(): array
    {
        return [
            self::VIEW_CLIENTS->value,
            self::CREATE_CLIENTS->value,
            self::UPDATE_CLIENTS->value,
            self::DELETE_CLIENTS->value,
        ];
    }

    public static function vehicles(): array
    {
        return [
            self::VIEW_VEHICLES->value,
            self::CREATE_VEHICLES->value,
            self::UPDATE_VEHICLES->value,
            self::DELETE_VEHICLES->value,
        ];
    }

    public static function repairOrders(): array
    {
        return [
            self::VIEW_REPAIR_ORDERS->value,
            self::CREATE_REPAIR_ORDERS->value,
            self::UPDATE_REPAIR_ORDERS->value,
            self::DELETE_REPAIR_ORDERS->value,
            self::UPDATE_REPAIR_ORDER_STATUS->value,
        ];
    }

    public static function internalNotes(): array
    {
        return [
            self::VIEW_INTERNAL_NOTES->value,
            self::CREATE_INTERNAL_NOTES->value,
            self::UPDATE_INTERNAL_NOTES->value,
            self::DELETE_INTERNAL_NOTES->value,
        ];
    }

    public static function timeEntries(): array
    {
        return [
            self::CREATE_TIME_ENTRIES->value,
            self::UPDATE_TIME_ENTRIES->value,
            self::DELETE_TIME_ENTRIES->value,
        ];
    }
}
