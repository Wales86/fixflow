import ClientForm from '@/components/clients/client-form';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

interface ClientsEditProps {
    client: App.Dto.Client.ClientData;
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
            <Head
                title={`Edycja - ${client.first_name} ${client.last_name || ''}`.trim()}
            />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    Edytuj klienta
                </h1>

                <Card>
                    <CardHeader>
                        <CardTitle>Dane klienta</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <ClientForm client={client} />
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
