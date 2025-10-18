import { Head } from '@inertiajs/react';

import AppLayout from '@/layouts/app-layout';
import { VehicleForm } from '@/components/vehicles/vehicle-form';
import { type BreadcrumbItem } from '@/types';

interface VehiclesEditProps {
    vehicle: App.Dto.Vehicle.VehicleData;
    clients: App.Dto.Client.ClientSelectOptionData[];
}

export default function VehiclesEdit({ vehicle, clients }: VehiclesEditProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Pojazdy',
            href: '/vehicles',
        },
        {
            title: vehicle.registration_number,
            href: `/vehicles/${vehicle.id}`,
        },
        {
            title: 'Edytuj',
            href: `/vehicles/${vehicle.id}/edit`,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edytuj ${vehicle.registration_number}`} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    Edytuj pojazd: {vehicle.make} {vehicle.model}
                </h1>
                <VehicleForm vehicle={vehicle} clients={clients} />
            </div>
        </AppLayout>
    );
}
