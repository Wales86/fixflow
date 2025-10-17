import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { VehiclesDataTable } from '@/components/vehicles/vehicles-data-table';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pojazdy',
        href: '/vehicles',
    },
];

interface VehiclesPageProps {
    vehicles: PaginatedResponse<App.Dto.Vehicle.VehicleData>;
    filters: App.Dto.Common.FiltersData;
}

export default function VehiclesIndex({ vehicles, filters }: VehiclesPageProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Pojazdy" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">Pojazdy</h1>
                    <Button asChild>
                        <Link href="/vehicles/create">
                            <Plus className="mr-2 size-4" />
                            Dodaj pojazd
                        </Link>
                    </Button>
                </div>
                <VehiclesDataTable vehicles={vehicles} filters={filters} />
            </div>
        </AppLayout>
    );
}
