import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';

export default function ClientShow({
    client,
    vehicles,
}: App.Dto.Client.ClientShowPagePropsData) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Klienci',
            href: '/clients',
        },
        {
            title: `${client.first_name} ${client.last_name || ''}`.trim(),
            href: `/clients/${client.id}`,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${client.first_name} ${client.last_name || ''}`.trim()} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    {client.first_name} {client.last_name}
                </h1>
                <p>Client details and vehicles will be displayed here.</p>
            </div>
        </AppLayout>
    );
}
