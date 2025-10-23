import repairOrders from '@/routes/repair-orders';
import { type SharedData, User } from '@/types';
import { usePage } from '@inertiajs/react';

export function hasPermission(
    user: User | undefined,
    permission: string,
): boolean {
    return user?.permissions?.includes(permission) ?? false;
}

/**
 * Returns the appropriate repair orders list URL based on user permissions.
 * Users with 'view_repair_orders' permission see the full list (/repair-orders).
 * Others (mechanics) see the mechanic-specific list (/repair-orders/mechanic).
 */
export function useRepairOrdersListUrl(): string {
    const { auth } = usePage<SharedData>().props;

    return hasPermission(auth.user, 'view_repair_orders')
        ? repairOrders.index().url
        : repairOrders.mechanic().url;
}
