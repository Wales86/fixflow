declare namespace App.Dto {
export type DashboardData = {
activeOrdersCount: number;
pendingOrdersCount: number;
todayTimeEntriesTotal: number;
recentOrders: Array<App.Dto.RecentOrderData>;
};
export type RecentOrderData = {
id: number;
vehicle: string;
client: string;
status: string;
created_at: string;
};
}
declare namespace App.Enums {
export type RepairOrderStatus = 'new' | 'diagnosis' | 'awaiting_contact' | 'awaiting_parts' | 'in_progress' | 'ready_for_pickup' | 'closed';
export type UserRole = 'Owner' | 'Office' | 'Mechanic';
}
