import { ClientsDataTable } from '@/components/clients/clients-data-table';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Klienci',
        href: '/clients',
    },
];

export default function ClientsIndex({
    tableData,
    filters,
}: App.Dto.Common.FilterableTablePagePropsData) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Klienci" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">
                        Klienci
                    </h1>
                    <Button asChild>
                        <Link href="/clients/create">
                            <Plus className="mr-2 size-4" />
                            Dodaj klienta
                        </Link>
                    </Button>
                </div>
                <ClientsDataTable tableData={tableData} filters={filters} />
            </div>
        </AppLayout>
    );
}
