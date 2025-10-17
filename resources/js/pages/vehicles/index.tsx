import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface VehiclesPageProps {
    vehicles: PaginatedResponse<App.Dto.Vehicle.VehicleData>;
    filters: App.Dto.Common.FiltersData;
}

export default function VehiclesIndex({}: VehiclesPageProps) {
    return (
        <AppLayout>
            <Head title="Pojazdy" />
            <div>Vehicles Index</div>
        </AppLayout>
    );
}
