import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import { type ClientData } from '@/types/generated';

interface ClientsEditProps {
    client: ClientData;
}

export default function ClientsEdit({ client }: ClientsEditProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Klienci',
            href: '/clients',
        },
        {
            title: `${client.first_name} ${client.last_name || ''}`.trim(),
            href: `/clients/${client.id}`,
        },
        {
            title: 'Edycja',
            href: `/clients/${client.id}/edit`,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edycja - ${client.first_name} ${client.last_name || ''}`.trim()} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    Edycja klienta: {client.first_name} {client.last_name}
                </h1>
                <p>Formularz edycji klienta będzie tutaj.</p>
            </div>
        </AppLayout>
    );
}
