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
    export type ClientSelectOptionData = {
        id: number;
        name: string;
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
    export type ActivityLogCauserData = {
        id: number;
        name: string;
        type: string;
    };
    export type ActivityLogData = {
        id: number;
        description: string;
        event: string | null;
        properties: Array<any> | null;
        created_at: string;
        causer: App.Dto.Common.ActivityLogCauserData | null;
    };
    export type FilterableTablePagePropsData = {
        tableData: any;
        filters: App.Dto.Common.FiltersData;
    };
    export type FiltersData = {
        search: string | null;
        sort: string | null;
        direction: string | null;
    };
    export type MediaData = {
        id: number;
        name: string;
        url: string;
        mime_type: string;
        size: number;
    };
    export type SelectOptionData = {
        value: string;
        label: string;
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
declare namespace App.Dto.InternalNote {
    export type InternalNoteAuthorData = {
        id: number;
        name: string;
        type: string;
    };
    export type InternalNoteData = {
        id: number;
        repair_order_id: number;
        content: string;
        author_id: number;
        author_type: string;
        created_at: string;
        author: App.Dto.InternalNote.InternalNoteAuthorData | null;
    };
}
declare namespace App.Dto.RepairOrder {
    export type RepairOrderClientData = {
        id: number;
        first_name: string;
        last_name: string | null;
        phone_number: string;
    };
    export type RepairOrderCreatePageData = {
        vehicles: Array<App.Dto.RepairOrder.VehicleSelectionData>;
        statuses: Array<App.Dto.Common.SelectOptionData>;
        preselected_vehicle_id: number | null;
    };
    export type RepairOrderData = {
        id: number;
        vehicle_id: number;
        status: App.Enums.RepairOrderStatus;
        problem_description: string;
        started_at: string | null;
        finished_at: string | null;
        total_time_minutes: number;
        created_at: string;
    };
    export type RepairOrderEditPagePropsData = {
        repairOrder: App.Dto.RepairOrder.RepairOrderFormData;
        vehicles: Array<App.Dto.RepairOrder.VehicleSelectionData>;
        statuses: Array<App.Dto.Common.SelectOptionData>;
    };
    export type RepairOrderFiltersData = {
        status: string | null;
        search: string | null;
        sort: string | null;
        direction: string | null;
    };
    export type RepairOrderFormData = {
        id: number;
        vehicle_id: number;
        status: App.Enums.RepairOrderStatus;
        problem_description: string;
        images: Array<App.Dto.Common.MediaData>;
    };
    export type RepairOrderIndexPagePropsData = {
        statusOptions: Array<any>;
        tableData: any;
        filters: App.Dto.Common.FiltersData;
    };
    export type RepairOrderListItemData = {
        id: number;
        status: App.Enums.RepairOrderStatus;
        problem_description: string;
        started_at: string | null;
        finished_at: string | null;
        total_time_minutes: number;
        created_at: string;
        vehicle: App.Dto.RepairOrder.RepairOrderVehicleData;
        client: App.Dto.RepairOrder.RepairOrderClientData;
    };
    export type RepairOrderShowData = {
        id: number;
        vehicle_id: number;
        status: App.Enums.RepairOrderStatus;
        problem_description: string;
        started_at: string | null;
        finished_at: string | null;
        total_time_minutes: number;
        created_at: string;
        vehicle: App.Dto.RepairOrder.RepairOrderVehicleData;
        client: App.Dto.RepairOrder.RepairOrderClientData;
        images: Array<App.Dto.Common.MediaData>;
    };
    export type RepairOrderShowPagePropsData = {
        order: App.Dto.RepairOrder.RepairOrderShowData;
        time_entries: Array<App.Dto.TimeTracking.TimeEntryData>;
        internal_notes: Array<App.Dto.InternalNote.InternalNoteData>;
        activity_log: Array<App.Dto.Common.ActivityLogData>;
        can_edit: boolean;
        can_delete: boolean;
    };
    export type RepairOrderVehicleData = {
        id: number;
        make: string;
        model: string;
        registration_number: string;
    };
    export type StoreRepairOrderData = {
        vehicle_id: number;
        description: string;
        attachments: Array<any> | null;
    };
    export type UpdateRepairOrderData = {
        description: string | null;
        status: App.Enums.RepairOrderStatus | null;
    };
    export type UpdateRepairOrderStatusData = {
        status: App.Enums.RepairOrderStatus;
    };
    export type VehicleSelectionData = {
        id: number;
        display_name: string;
        registration_number: string;
        client_name: string;
    };
}
declare namespace App.Dto.TimeTracking {
    export type TimeEntryData = {
        id: number;
        repair_order_id: number;
        mechanic_id: number;
        duration_minutes: number;
        duration_hours: number;
        description: string | null;
        created_at: string;
        mechanic: App.Dto.TimeTracking.TimeEntryMechanicData | null;
    };
    export type TimeEntryMechanicData = {
        id: number;
        first_name: string;
        last_name: string;
    };
}
declare namespace App.Dto.Vehicle {
    export type StoreVehicleData = {
        client_id: number;
        make: string;
        model: string;
        year: number;
        vin: string;
        registration_number: string;
    };
    export type UpdateVehicleData = {
        client_id: number;
        make: string;
        model: string;
        year: number;
        vin: string;
        registration_number: string;
    };
    export type VehicleClientData = {
        id: number;
        first_name: string;
        last_name: string | null;
    };
    export type VehicleData = {
        id: number;
        client_id: number;
        make: string;
        model: string;
        year: number;
        registration_number: string;
        vin: string;
        repair_orders_count: number | null;
        client: App.Dto.Vehicle.VehicleClientData | null;
    };
    export type VehicleEditPagePropsData = {
        vehicle: App.Dto.Vehicle.VehicleData;
        clients: Array<App.Dto.Client.ClientSelectOptionData>;
    };
    export type VehicleShowPagePropsData = {
        vehicle: App.Dto.Vehicle.VehicleData;
        repair_orders: any;
    };
}
declare namespace App.Enums {
    export type RepairOrderStatus =
        | 'new'
        | 'diagnosis'
        | 'awaiting_contact'
        | 'awaiting_parts'
        | 'in_progress'
        | 'ready_for_pickup'
        | 'closed';
    export type UserRole = 'Owner' | 'Office' | 'Mechanic';
}
