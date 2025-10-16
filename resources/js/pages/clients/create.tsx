import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';

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
                <h1 className="text-2xl font-bold tracking-tight">Nowy klient</h1>
                <p>Formularz tworzenia klienta będzie tutaj.</p>
            </div>
        </AppLayout>
    );
}
