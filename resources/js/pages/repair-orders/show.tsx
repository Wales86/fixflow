import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';

interface RepairOrderShowProps
    extends App.Dto.RepairOrder.RepairOrderShowPagePropsData {}

export default function RepairOrderShow({
    order,
    time_entries,
    internal_notes,
    activity_log,
    can_edit,
}: RepairOrderShowProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Zlecenia naprawy',
            href: '/repair-orders',
        },
        {
            title: `Zlecenie #${order.id}`,
            href: `/repair-orders/${order.id}`,
        },
    ];

    const handleEditClick = () => {
        router.visit(`/repair-orders/${order.id}/edit`);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Zlecenie naprawy #${order.id}`} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Zlecenie naprawy #{order.id}
                        </h1>
                        <p className="text-muted-foreground">
                            {order.vehicle.make} {order.vehicle.model} (
                            {order.vehicle.registration_number})
                        </p>
                    </div>
                    {can_edit && (
                        <Button onClick={handleEditClick}>
                            Edytuj zlecenie
                        </Button>
                    )}
                </div>

                <Tabs defaultValue="details" className="w-full">
                    <TabsList>
                        <TabsTrigger value="details">Szczegóły</TabsTrigger>
                        <TabsTrigger value="time-entries">
                            Czas pracy ({time_entries.length})
                        </TabsTrigger>
                        <TabsTrigger value="notes">
                            Notatki ({internal_notes.length})
                        </TabsTrigger>
                        <TabsTrigger value="history">
                            Historia zmian ({activity_log.length})
                        </TabsTrigger>
                    </TabsList>

                    <TabsContent value="details">
                        <div className="rounded-lg border bg-card p-6">
                            <p className="text-muted-foreground">
                                Szczegóły zlecenia - do zaimplementowania
                            </p>
                        </div>
                    </TabsContent>

                    <TabsContent value="time-entries">
                        <div className="rounded-lg border bg-card p-6">
                            <p className="text-muted-foreground">
                                Wpisy czasu pracy - do zaimplementowania
                            </p>
                        </div>
                    </TabsContent>

                    <TabsContent value="notes">
                        <div className="rounded-lg border bg-card p-6">
                            <p className="text-muted-foreground">
                                Notatki wewnętrzne - do zaimplementowania
                            </p>
                        </div>
                    </TabsContent>

                    <TabsContent value="history">
                        <div className="rounded-lg border bg-card p-6">
                            <p className="text-muted-foreground">
                                Historia zmian - do zaimplementowania
                            </p>
                        </div>
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    );
}
