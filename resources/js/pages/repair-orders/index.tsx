import { RepairOrdersDataTable } from '@/components/repair-orders/repair-orders-data-table';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { useRepairOrdersListUrl } from '@/lib/permissions';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Plus, RefreshCw } from 'lucide-react';

export default function RepairOrdersIndex({
    tableData,
    filters,
    statusOptions,
}: App.Dto.RepairOrder.RepairOrderIndexPagePropsData) {
    const { t } = useLaravelReactI18n();
    const repairOrdersListUrl = useRepairOrdersListUrl();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('repair_orders'),
            href: repairOrdersListUrl,
        },
    ];

    const handleRefresh = () => {
        const params: Record<string, string> = {};
        const filtersWithStatus = filters as typeof filters & {
            status?: string | null;
        };
        if (filtersWithStatus.status) {
            params.status = filtersWithStatus.status;
        }
        if (filters.search) {
            params.search = filters.search;
        }
        if (filters.sort) {
            params.sort = filters.sort;
        }
        if (filters.direction) {
            params.direction = filters.direction;
        }

        router.get(window.location.pathname, params, {
            preserveState: true,
            preserveScroll: true,
            only: ['tableData', 'filters', 'statusOptions'],
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('repair_orders')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">
                        {t('repair_orders')}
                    </h1>
                    <div className="flex items-center gap-2">
                        <Button asChild>
                            <Link href="/repair-orders/create">
                                <Plus className="mr-2 size-4" />
                                {t('add_repair_order')}
                            </Link>
                        </Button>
                        <Button
                            variant="outline"
                            size="icon"
                            onClick={handleRefresh}
                            title={t('refresh')}
                        >
                            <RefreshCw className="h-4 w-4" />
                        </Button>
                    </div>
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
