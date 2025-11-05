import { Head } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

import { RepairOrderForm } from '@/components/repair-orders/repair-order-form';
import AppLayout from '@/layouts/app-layout';
import { useRepairOrdersListUrl } from '@/lib/permissions';
import { type BreadcrumbItem } from '@/types';

export default function RepairOrdersEdit({
    repairOrder,
    vehicles,
    statuses,
}: App.Dto.RepairOrder.RepairOrderEditPagePropsData) {
    const { t } = useLaravelReactI18n();
    const repairOrdersListUrl = useRepairOrdersListUrl();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('repair_orders'),
            href: repairOrdersListUrl,
        },
        {
            title: t('edit'),
            href: '#',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${t('edit_repair_order')} #${repairOrder.id}`} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    {t('edit_repair_order')} #{repairOrder.id}
                </h1>
                <RepairOrderForm
                    isEditMode
                    initialData={repairOrder}
                    vehicles={vehicles}
                    statuses={statuses}
                />
            </div>
        </AppLayout>
    );
}
