import { Head } from '@inertiajs/react';

import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Zlecenia naprawcze',
        href: '/repair-orders',
    },
    {
        title: 'Edycja',
        href: '#',
    },
];

export default function RepairOrdersEdit({
    repairOrder,
    vehicles,
    statuses,
}: App.Dto.RepairOrder.RepairOrderEditPagePropsData) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edycja Zlecenia Naprawczego" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    Edycja Zlecenia Naprawczego #{repairOrder.id}
                </h1>
                <div>
                    <p className="text-muted-foreground">
                        Komponent w budowie. Otrzymane dane: zlecenie #
                        {repairOrder.id} ({repairOrder.status}), {vehicles.length}{' '}
                        pojazdów, {statuses.length} opcji statusów,{' '}
                        {repairOrder.images.length} obrazów.
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
