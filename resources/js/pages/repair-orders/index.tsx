import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Zlecenia naprawcze',
        href: '/repair-orders',
    },
];

export default function RepairOrdersIndex({
    tableData,
    filters,
    statusOptions,
}: App.Dto.RepairOrder.RepairOrderIndexPagePropsData) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Zlecenia naprawcze" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">Zlecenia naprawcze</h1>
                </div>
                <div>
                    <p className="text-muted-foreground">
                        Komponent w budowie. Otrzymane dane: {tableData.data.length} zleceń,{' '}
                        {statusOptions.length} opcji statusów.
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
