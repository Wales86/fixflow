import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

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
    clients: App.Dto.Client.ClientForListData[];
    preselectedClientId: number | null;
}

export default function VehiclesCreate({
    clients,
    preselectedClientId,
}: VehiclesCreateProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Nowy pojazd" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    Dodaj nowy pojazd
                </h1>
                <Card>
                    <CardHeader>
                        <CardTitle>Formularz pojazdu</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p>Dostępnych klientów: {clients.length}</p>
                        {preselectedClientId && (
                            <p>Wybrany klient ID: {preselectedClientId}</p>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
