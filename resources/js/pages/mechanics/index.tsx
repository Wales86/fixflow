import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mechanicy',
        href: '/mechanics',
    },
];

export default function MechanicsIndex({
    tableData,
    filters,
}: App.Dto.Common.FilterableTablePagePropsData) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Mechanicy" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">
                        Mechanicy
                    </h1>
                </div>
                <div className="rounded-lg border bg-card p-4">
                    <pre>{JSON.stringify(tableData, null, 2)}</pre>
                </div>
            </div>
        </AppLayout>
    );
}
