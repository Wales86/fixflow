import ClientForm from '@/components/clients/client-form';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Klienci',
        href: '/clients',
    },
    {
        title: 'Nowy klient',
        href: '/clients/create',
    },
];

export default function ClientsCreate() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Nowy klient" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    Dodaj nowego klienta
                </h1>

                <Card>
                    <CardHeader>
                        <CardTitle>Dane klienta</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <ClientForm />
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
