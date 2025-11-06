import { Head } from '@inertiajs/react';

import { VehicleForm } from '@/components/vehicles/vehicle-form';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pojazdy',
        href: '/vehicles',
    },
    {
        title: 'Nowy pojazd',
        href: '/vehicles/create',
    },
];

interface VehiclesCreateProps {
    clients: App.Dto.Client.ClientSelectOptionData[];
    preselectedClientId?: number;
}

export default function VehiclesCreate({
    clients,
    preselectedClientId,
}: VehiclesCreateProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dodaj Nowy Pojazd" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    Dodaj Nowy Pojazd
                </h1>
                <VehicleForm
                    clients={clients}
                    preselectedClientId={preselectedClientId}
                />
            </div>
        </AppLayout>
    );
}
