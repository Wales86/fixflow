import { RepairOrdersCardsGrid } from '@/components/repair-orders/repair-orders-cards-grid';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

export default function MechanicRepairOrdersIndex({
    orders,
    search,
}: App.Dto.RepairOrder.MechanicRepairOrderIndexPagePropsData) {
    const { t } = useLaravelReactI18n();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('active_repair_orders'),
            href: '/repair-orders/mechanic',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('active_repair_orders')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">
                        {t('active_repair_orders')}
                    </h1>
                </div>
                <RepairOrdersCardsGrid orders={orders} search={search} />
            </div>
        </AppLayout>
    );
}
