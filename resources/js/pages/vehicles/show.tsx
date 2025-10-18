import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';


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
            title: `${vehicle.year} ${vehicle.make} ${vehicle.model}`,
            href: `/vehicles/${vehicle.id}`,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${vehicle.year} ${vehicle.make} ${vehicle.model}`} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">
                        {vehicle.year} {vehicle.make} {vehicle.model}
                    </h1>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    <div className="rounded-lg border bg-card p-4 text-card-foreground shadow-sm">
                        <h2 className="mb-4 text-lg font-semibold">
                            Dane pojazdu
                        </h2>
                        <dl className="space-y-2">
                            <div className="flex justify-between">
                                <dt className="text-muted-foreground">
                                    Numer rejestracyjny:
                                </dt>
                                <dd className="font-medium">
                                    {vehicle.registration_number}
                                </dd>
                            </div>
                            <div className="flex justify-between">
                                <dt className="text-muted-foreground">VIN:</dt>
                                <dd className="font-medium">{vehicle.vin}</dd>
                            </div>
                            <div className="flex justify-between">
                                <dt className="text-muted-foreground">Rok:</dt>
                                <dd className="font-medium">{vehicle.year}</dd>
                            </div>
                        </dl>
                    </div>

                    {vehicle.client && (
                        <div className="rounded-lg border bg-card p-4 text-card-foreground shadow-sm">
                            <h2 className="mb-4 text-lg font-semibold">
                                Właściciel
                            </h2>
                            <dl className="space-y-2">
                                <div className="flex justify-between">
                                    <dt className="text-muted-foreground">
                                        Imię:
                                    </dt>
                                    <dd className="font-medium">
                                        {vehicle.client.first_name}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-muted-foreground">
                                        Nazwisko:
                                    </dt>
                                    <dd className="font-medium">
                                        {vehicle.client.last_name}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    )}
                </div>

                <div className="rounded-lg border bg-card p-4 text-card-foreground shadow-sm">
                    <h2 className="mb-4 text-lg font-semibold">
                        Historia napraw
                    </h2>
                    <p className="text-muted-foreground">
                        Komponent tabeli napraw będzie tutaj (w następnym kroku)
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
