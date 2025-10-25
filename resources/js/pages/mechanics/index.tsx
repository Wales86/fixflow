import { Head, Link } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Plus } from 'lucide-react';

import { MechanicsDataTable } from '@/components/mechanics/mechanics-data-table';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { usePermission } from '@/lib/permissions';
import { create } from '@/routes/mechanics';
import { type BreadcrumbItem } from '@/types';

export default function MechanicsIndex({
    tableData,
    filters,
}: App.Dto.Common.FilterableTablePagePropsData) {
    const { t } = useLaravelReactI18n();
    const canCreate = usePermission('create_mechanics');

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('mechanics.title'),
            href: '/mechanics',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('mechanics.title')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">
                        {t('mechanics.title')}
                    </h1>
                    {canCreate && (
                        <Button asChild>
                            <Link href={create().url}>
                                <Plus className="mr-2 size-4" />
                                {t('mechanics.add_mechanic')}
                            </Link>
                        </Button>
                    )}
                </div>
                <MechanicsDataTable tableData={tableData} filters={filters} />
            </div>
        </AppLayout>
    );
}
