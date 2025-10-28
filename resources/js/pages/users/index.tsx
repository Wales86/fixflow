import { Head, Link } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Plus } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { UsersDataTable } from '@/components/users/users-data-table';
import AppLayout from '@/layouts/app-layout';
import { usePermission } from '@/lib/permissions';
import { type BreadcrumbItem } from '@/types';

export default function UsersIndex({
    tableData,
    filters,
}: App.Dto.Common.FilterableTablePagePropsData) {
    const { t } = useLaravelReactI18n();
    const canCreate = usePermission('create_users');

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('users.title'),
            href: '/users',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('users.title')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">
                        {t('users.title')}
                    </h1>
                    {canCreate && (
                        <Button asChild>
                            <Link href="/users/create">
                                <Plus className="mr-2 size-4" />
                                {t('users.add_user')}
                            </Link>
                        </Button>
                    )}
                </div>
                <UsersDataTable tableData={tableData} filters={filters} />
            </div>
        </AppLayout>
    );
}
