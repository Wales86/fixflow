import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface ClientsPageProps extends App.Dto.Client.ClientIndexPagePropsData {}

export default function ClientsIndex({ clients, filters }: ClientsPageProps) {
    return (
        <AppLayout>
            <Head title="Clients" />
            <div className="p-4">
                <h1>Clients</h1>
            </div>
        </AppLayout>
    );
}
