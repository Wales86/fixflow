import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { RepairOrdersTable } from '@/components/vehicles/repair-orders-table';
import { VehicleDetailsCard } from '@/components/vehicles/vehicle-details-card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';

interface VehicleShowProps {
    vehicle: App.Dto.Vehicle.VehicleData;
    repair_orders: PaginatedData<App.Dto.RepairOrder.RepairOrderData>;
}

export default function VehicleShow({
    vehicle,
    repair_orders,
}: VehicleShowProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Pojazdy',
            href: '/vehicles',
        },
        {
            title: `${vehicle.make} ${vehicle.model} ${vehicle.year}`,
            href: `/vehicles/${vehicle.id}`,
        },
    ];

    const handleEditClick = () => {
        router.visit(`/vehicles/${vehicle.id}/edit`);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${vehicle.make} ${vehicle.model} ${vehicle.year}`} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">
                        {vehicle.make} {vehicle.model} {vehicle.year}
                    </h1>
                    <Button onClick={handleEditClick}>Edytuj pojazd</Button>
                </div>

                <Tabs defaultValue="vehicle-details" className="w-full">
                    <TabsList>
                        <TabsTrigger value="vehicle-details">
                            Dane pojazdu
                        </TabsTrigger>
                        <TabsTrigger value="repair-history">
                            Historia napraw
                        </TabsTrigger>
                    </TabsList>
                    <TabsContent value="vehicle-details">
                        <VehicleDetailsCard vehicle={vehicle} />
                    </TabsContent>
                    <TabsContent value="repair-history">
                        <RepairOrdersTable
                            repairOrders={
                                repair_orders as PaginatedData<App.Dto.RepairOrder.RepairOrderData>
                            }
                        />
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    );
}
