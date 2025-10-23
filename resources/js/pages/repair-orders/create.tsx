import { Head } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

import { RepairOrderForm } from '@/components/repair-orders/repair-order-form';
import AppLayout from '@/layouts/app-layout';
import { useRepairOrdersListUrl } from '@/lib/permissions';
import { type BreadcrumbItem } from '@/types';

export default function RepairOrdersCreate({
    vehicles,
    statuses,
    preselected_vehicle_id,
}: App.Dto.RepairOrder.RepairOrderCreatePageData) {
    const { t } = useLaravelReactI18n();
    const repairOrdersListUrl = useRepairOrdersListUrl();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('repair_orders'),
            href: repairOrdersListUrl,
        },
        {
            title: t('new_repair_order'),
            href: '/repair-orders/create',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('new_repair_order')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    {t('new_repair_order')}
                </h1>
                <RepairOrderForm
                    vehicles={vehicles}
                    statuses={statuses}
                    preselectedVehicleId={preselected_vehicle_id}
                />
            </div>
        </AppLayout>
    );
}
