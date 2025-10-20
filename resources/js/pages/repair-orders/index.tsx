import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { RepairOrdersDataTable } from '@/components/repair-orders/repair-orders-data-table';
import { useLaravelReactI18n } from 'laravel-react-i18n';

export default function RepairOrdersIndex({
    tableData,
    filters,
    statusOptions,
}: App.Dto.RepairOrder.RepairOrderIndexPagePropsData) {
    const { t } = useLaravelReactI18n();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('repair_orders'),
            href: '/repair-orders',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('repair_orders')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">{t('repair_orders')}</h1>
                    <Button asChild>
                        <Link href="/repair-orders/create">
                            <Plus className="mr-2 size-4" />
                            {t('add_repair_order')}
                        </Link>
                    </Button>
                </div>
                <RepairOrdersDataTable
                    tableData={tableData}
                    filters={filters}
                    statusOptions={statusOptions}
                />
            </div>
        </AppLayout>
    );
}
