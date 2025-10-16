import { ClientDetailsCard } from '@/components/clients/client-details-card';
import { ClientVehiclesTable } from '@/components/clients/client-vehicles-table';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';

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

    const handleEditClick = () => {
        router.visit(`/clients/${client.id}/edit`);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head
                title={`${client.first_name} ${client.last_name || ''}`.trim()}
            />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">
                        {client.first_name} {client.last_name}
                    </h1>
                    <Button onClick={handleEditClick}>Edytuj klienta</Button>
                </div>

                <Tabs defaultValue="client-details" className="w-full">
                    <TabsList>
                        <TabsTrigger value="client-details">
                            Dane klienta
                        </TabsTrigger>
                        <TabsTrigger value="vehicles">Pojazdy</TabsTrigger>
                    </TabsList>
                    <TabsContent value="client-details">
                        <ClientDetailsCard client={client} />
                    </TabsContent>
                    <TabsContent value="vehicles">
                        <ClientVehiclesTable vehicles={vehicles} />
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    );
}
