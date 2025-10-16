import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { ClientsDataTable } from '@/components/clients/clients-data-table';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Klienci',
        href: '/clients',
    },
];

interface ClientsPageProps {
    clients: PaginatedResponse<App.Dto.Client.ClientListItemData>;
    filters: App.Dto.Client.FiltersData;
}

export default function ClientsIndex({ clients, filters }: ClientsPageProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Klienci" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">Klienci</h1>
                    <Button asChild>
                        <Link href="/clients/create">
                            <Plus className="mr-2 size-4" />
                            Dodaj klienta
                        </Link>
                    </Button>
                </div>
                <ClientsDataTable clients={clients} filters={filters} />
            </div>
        </AppLayout>
    );
}
