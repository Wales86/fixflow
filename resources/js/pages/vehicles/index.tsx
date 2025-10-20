import { Button } from '@/components/ui/button';
import { VehiclesDataTable } from '@/components/vehicles/vehicles-data-table';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pojazdy',
        href: '/vehicles',
    },
];

export default function VehiclesIndex({
    tableData,
    filters,
}: App.Dto.Common.FilterableTablePagePropsData) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Pojazdy" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">
                        Pojazdy
                    </h1>
                    <Button asChild>
                        <Link href="/vehicles/create">
                            <Plus className="mr-2 size-4" />
                            Dodaj pojazd
                        </Link>
                    </Button>
                </div>
                <VehiclesDataTable tableData={tableData} filters={filters} />
            </div>
        </AppLayout>
    );
}
