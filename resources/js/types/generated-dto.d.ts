declare namespace App.Dto.Client {
export type ClientData = {
id: number;
first_name: string;
last_name: string | null;
phone_number: string;
email: string | null;
address_street: string | null;
address_city: string | null;
address_postal_code: string | null;
address_country: string | null;
created_at: string;
};
export type ClientListItemData = {
id: number;
first_name: string;
last_name: string | null;
phone_number: string;
email: string | null;
vehicles_count: number;
};
export type ClientShowPagePropsData = {
client: App.Dto.Client.ClientData;
vehicles: Array<App.Dto.Vehicle.VehicleData>;
};
export type StoreClientData = {
last_name: string;
first_name: string;
phone_number: string;
email?: string;
address_street?: string;
address_city?: string;
address_postal_code?: string;
address_country?: string;
};
export type UpdateClientData = {
last_name: string;
first_name: string;
phone_number: string;
email?: string;
address_street?: string;
address_city?: string;
address_postal_code?: string;
address_country?: string;
};
}
declare namespace App.Dto.Common {
export type FilterableTablePagePropsData = {
tableData: any;
filters: App.Dto.Common.FiltersData;
};
export type FiltersData = {
search: string | null;
sort: string | null;
direction: string | null;
};
}
declare namespace App.Dto.Dashboard {
export type DashboardData = {
activeOrdersCount: number;
pendingOrdersCount: number;
todayTimeEntriesTotal: number;
recentOrders: Array<App.Dto.Dashboard.RecentOrderData>;
};
export type RecentOrderData = {
id: number;
vehicle: string;
client: string;
status: string;
created_at: string;
};
}
declare namespace App.Dto.Vehicle {
export type VehicleClientData = {
id: number;
first_name: string;
last_name: string | null;
};
export type VehicleData = {
id: number;
make: string;
model: string;
year: number;
registration_number: string;
vin: string;
repair_orders_count: number | null;
client: App.Dto.Vehicle.VehicleClientData | null;
};
export type VehicleIndexPagePropsData = {
vehicles: any;
filters: App.Dto.Common.FiltersData;
};
}
declare namespace App.Enums {
export type RepairOrderStatus = 'new' | 'diagnosis' | 'awaiting_contact' | 'awaiting_parts' | 'in_progress' | 'ready_for_pickup' | 'closed';
export type UserRole = 'Owner' | 'Office' | 'Mechanic';
}
