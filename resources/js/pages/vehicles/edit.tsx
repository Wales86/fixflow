import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

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
                <Card>
                    <CardHeader>
                        <CardTitle>Formularz edycji pojazdu</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p>VIN: {vehicle.vin}</p>
                        <p>Dostępnych klientów: {clients.length}</p>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
