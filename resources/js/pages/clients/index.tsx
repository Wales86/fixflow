import { Head, Link } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Plus } from 'lucide-react';

import { ClientsDataTable } from '@/components/clients/clients-data-table';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { usePermission } from '@/lib/permissions';
import { type BreadcrumbItem } from '@/types';

export default function ClientsIndex({
    tableData,
    filters,
}: App.Dto.Common.FilterableTablePagePropsData) {
    const { t } = useLaravelReactI18n();
    const canCreate = usePermission('create_clients');

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('clients.title'),
            href: '/clients',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('clients.title')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">
                        {t('clients.title')}
                    </h1>
                    {canCreate && (
                        <Button asChild>
                            <Link href="/clients/create">
                                <Plus className="mr-2 size-4" />
                                {t('add_client')}
                            </Link>
                        </Button>
                    )}
                </div>
                <ClientsDataTable tableData={tableData} filters={filters} />
            </div>
        </AppLayout>
    );
}
