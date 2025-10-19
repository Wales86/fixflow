import { Head } from '@inertiajs/react';

import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Zlecenia naprawcze',
        href: '/repair-orders',
    },
    {
        title: 'Nowe zlecenie',
        href: '/repair-orders/create',
    },
];

export default function RepairOrdersCreate({
    vehicles,
    statuses,
    preselected_vehicle_id,
}: App.Dto.RepairOrder.RepairOrderCreatePageData) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dodaj Nowe Zlecenie Naprawcze" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    Dodaj Nowe Zlecenie Naprawcze
                </h1>
                <div>
                    <p className="text-muted-foreground">
                        Komponent w budowie. Otrzymane dane: {vehicles.length}{' '}
                        pojazdów, {statuses.length} opcji statusów
                        {preselected_vehicle_id &&
                            `, preselekcja pojazdu #${preselected_vehicle_id}`}
                        .
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
